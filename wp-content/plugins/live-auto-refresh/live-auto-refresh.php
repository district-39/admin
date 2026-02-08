<?php
/*
 * Plugin Name: Live Auto Refresh (Hot Reload / Live Reload for WordPress Developers)
 * Description: Instantly reloads the browser when any theme file code is edited during development or when a content edit is saved.
 * Plugin URI:  https://www.andrewperron.com/live-auto-refresh/
 * Version:     3.2.1
 * Author:      Andrew Perron
 * Author URI:  https://www.andrewperron.com/live-auto-refresh/
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: perron
 * Domain Path: /languages
 */
 
 if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
 
function perron_live_auto_refresh() {
    $js_file = plugin_dir_path(__FILE__) . 'live-auto-refresh.min.js';
	$js_ver = file_exists($js_file) ? filemtime($js_file) : false;
	wp_enqueue_script( 'perron-live-auto-refresh', plugins_url( 'live-auto-refresh.min.js', __FILE__ ), array(), $js_ver, true );
}
add_action( 'wp_enqueue_scripts', 'perron_live_auto_refresh' );

// Helper: Collect files recursively with extension and subpath filter
function perron_collect_files($base_dir, $allowed_exts = array(), $subpath_filter = '') {
    $results = array();
    if (!is_dir($base_dir)) return $results;
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($base_dir, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::SELF_FIRST
    );
    foreach ($iterator as $file) {
        if ($file->isFile()) {
            $path = $file->getPathname();
            $ext = pathinfo($path, PATHINFO_EXTENSION);
            if ((empty($allowed_exts) || in_array($ext, $allowed_exts)) &&
                (empty($subpath_filter) || str_contains(wp_normalize_path($path), $subpath_filter))) {
                $results[$path] = filemtime($path);
            }
        }
    }
    return $results;
}

function perron_get_theme_files_hash() {
    // --- v3.0: Monitor theme and selected plugin files ---
    $theme_dir = WP_CONTENT_DIR;
    // Get monitor mode from option
    $monitor_all_files = get_option('perron_monitor_all_files', 0);
    $allowed_exts = array('php', 'js', 'css');
    if ($monitor_all_files) {
        $allowed_exts = array(); // Empty means allow all
    }
    $hashes = array();
    // Theme files
    $content_dir_relative = trailingslashit(str_replace(wp_normalize_path(ABSPATH), '/', wp_normalize_path(WP_CONTENT_DIR)));
    $theme_path = $content_dir_relative . 'themes/' . get_template();
    $hashes += perron_collect_files(WP_CONTENT_DIR, $allowed_exts, $theme_path);
    // Plugin files
    $selected_plugins = get_option('perron_auto_refresh_plugins', array());
    $plugin_base_dir = WP_PLUGIN_DIR;
    foreach ($selected_plugins as $plugin_rel_path) {
        $plugin_dir = dirname($plugin_base_dir . '/' . $plugin_rel_path);
        $hashes += perron_collect_files($plugin_dir, $allowed_exts);
    }
    $changedFile = '';
    $oldHashes = get_option('perron_theme_files_hashes', array());
    foreach ($hashes as $filename => $hash) {
        if (!isset($oldHashes[$filename]) || $oldHashes[$filename] !== $hash) {
            $changedFile = $filename;
            break;
        }
    }
    update_option('perron_theme_files_hashes', $hashes);
    return array(
        'hash' => md5(implode('', $hashes)),
        'changedFile' => $changedFile,
        'postModifiedTime' => get_option('perron_post_modified_time'),
    );
}

function perron_auto_refresh_ajax() {
	check_ajax_referer('perron_auto_refresh_nonce', 'nonce');
	$themeFilesHash = perron_get_theme_files_hash();
	echo wp_json_encode(array(
		'hash' => $themeFilesHash['hash'],
		'changedFile' => $themeFilesHash['changedFile'],
		'postModifiedTime' => $themeFilesHash['postModifiedTime'],
	));
	wp_die();
}
add_action( 'wp_ajax_auto_refresh', 'perron_auto_refresh_ajax' );
add_action( 'wp_ajax_nopriv_auto_refresh', 'perron_auto_refresh_ajax' );

function perron_auto_refresh_localize_script() {
	wp_localize_script(
		'perron-live-auto-refresh',
		'autoRefresh',
		array(
			'ajaxurl' => esc_url(admin_url( 'admin-ajax.php' )),
			'status' => (int) get_option('perron_auto_refresh_status', 1),
			'postModifiedTime' => (int) get_option('perron_post_modified_time'),
			'nonce' => wp_create_nonce('perron_auto_refresh_nonce'),
			'interval' => (int) get_option('perron_auto_refresh_interval', 1234),
			'timeout' => (int) get_option('perron_auto_refresh_timeout', 10),
		)
	);
}
add_action( 'wp_enqueue_scripts', 'perron_auto_refresh_localize_script' );

function perron_auto_refresh_save_post($post_id) {
    update_option('perron_post_modified_time', time());
}
add_action( 'save_post', 'perron_auto_refresh_save_post' );
if (class_exists('ACF')) {
	add_action( 'acf/save_post', 'perron_auto_refresh_save_post', 20 );
}

// Elementor: Trigger auto-refresh on Elementor document save
if ( class_exists('Elementor\Plugin') ) {
    add_action( 'elementor/document/save_data', function( $document ) {
        update_option('perron_post_modified_time', time());
    }, 10, 1 );
}

// Beaver Builder: Trigger auto-refresh before layout is saved
if ( class_exists('FLBuilderModel') ) {
    add_action( 'fl_builder_before_save_layout', function( $layout_id, $data ) {
        update_option('perron_post_modified_time', time());
    }, 10, 2 );
}

// Bricks Builder: Trigger auto-refresh on builder save
if ( class_exists('Bricks') ) {
    add_action( 'bricks/builder/save_data', function( $data, $post_id ) {
        update_option('perron_post_modified_time', time());
    }, 10, 2 );
}

// Extensibility: Allow custom hooks to trigger auto-refresh
$custom_hooks = apply_filters('perron_auto_refresh_custom_hooks', []);
foreach ($custom_hooks as $hook) {
    add_action($hook, 'perron_auto_refresh_save_post', 10, 2);
}

function perron_auto_refresh_toolbar_link($wp_admin_bar) {
	if( !is_super_admin() || !is_admin_bar_showing() || is_admin() ) return;
	$status = get_option('perron_auto_refresh_status', 1);
	if ($status) {
		$title = __('Auto Refresh');
		$new_status = 0;
		$autorefreshbuttonclass = "autorefreshbuttonenabled";
	} else {
		$title = __('Auto Refresh');
		$new_status = 1;
		$autorefreshbuttonclass = "autorefreshbuttondisabled";
	}
	$args = array(
		'id' => 'autorefresh',
		'title' => $title,
		'href' => wp_nonce_url(add_query_arg(array('perron_auto_refresh_status'=>$new_status)), 'perron_auto_refresh_toggle', 'toggle_nonce'),
		'meta' => array(
			'class' => $autorefreshbuttonclass,
		)
	);
	if (isset($_GET['perron_auto_refresh_status']) && isset($_GET['toggle_nonce']) && wp_verify_nonce($_GET['toggle_nonce'], 'perron_auto_refresh_toggle')) {
		update_option('perron_auto_refresh_status', intval($_GET['perron_auto_refresh_status']));
		wp_safe_redirect(remove_query_arg(array('perron_auto_refresh_status','toggle_nonce')));
		exit;
	}
	$wp_admin_bar->add_node($args);
}
add_action('admin_bar_menu', 'perron_auto_refresh_toolbar_link', 999);

function perron_auto_refresh_toolbar_style() {
	echo '<style>/*AUTO REFRESH*/.autorefreshbuttonenabled a{background:green !important;color:white !important;}.autorefreshbuttonpaused a{background:orange !important;color:white !important;}.autorefreshbuttondisabled a{background:red !important;color:white !important;}/*.autorefreshbuttonenabled a:after{content:" ON";}.autorefreshbuttondisabled a:after{content:" OFF";}*/</style>';
}
add_action('wp_head', 'perron_auto_refresh_toolbar_style', 100);

function perron_action_links( $links ) {
	$settings_url = admin_url('options-general.php?page=perron-live-auto-refresh');
	$settings_link = '<a href="' . esc_url($settings_url) . '">Settings</a>';
	$donate_link = '<a href="https://paypal.me/perronuk/" target="_blank">Donate</a>';

	array_unshift($links, $settings_link);
	$links[] = $donate_link;
	return $links;
}
add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), 'perron_action_links', 10, 1 );




register_deactivation_hook(__FILE__, 'perron_auto_refresh_deactivate');

// Settings page for usability
add_action('admin_menu', function() {
	add_options_page(
		'Live Auto Refresh Settings',
		'Live Auto Refresh',
		'manage_options',
		'perron-live-auto-refresh',
		'perron_live_auto_refresh_settings_page'
	);
});

function perron_live_auto_refresh_settings_page() {
	if ( ! function_exists( 'get_plugins' ) ) {
		require_once ABSPATH . 'wp-admin/includes/plugin.php';
	}
	if (!current_user_can('manage_options')) return;
	if (
		isset($_POST['perron_auto_refresh_status']) ||
		isset($_POST['perron_auto_refresh_interval']) ||
		isset($_POST['perron_auto_refresh_timeout']) ||
		isset($_POST['perron_auto_refresh_plugins']) ||
		isset($_POST['perron_monitor_all_files'])
	) {
		check_admin_referer('perron_live_auto_refresh_settings');
		update_option('perron_auto_refresh_status', intval($_POST['perron_auto_refresh_status']));
		$interval = isset($_POST['perron_auto_refresh_interval']) ? max(500, intval($_POST['perron_auto_refresh_interval'])) : 1234;
		update_option('perron_auto_refresh_interval', $interval);
		$timeout = isset($_POST['perron_auto_refresh_timeout']) ? max(1, intval($_POST['perron_auto_refresh_timeout'])) : 10;
		update_option('perron_auto_refresh_timeout', $timeout);
		// Save selected plugins
		$selected_plugins = isset($_POST['perron_auto_refresh_plugins']) && is_array($_POST['perron_auto_refresh_plugins']) ? array_map('sanitize_text_field', $_POST['perron_auto_refresh_plugins']) : array();
		update_option('perron_auto_refresh_plugins', $selected_plugins);
		// Save monitoring mode
		$monitor_all_files = isset($_POST['perron_monitor_all_files']) ? intval($_POST['perron_monitor_all_files']) : 0;
		update_option('perron_monitor_all_files', $monitor_all_files);
		echo '<div class="updated"><p>Settings saved.</p></div>';
	}
	$status = get_option('perron_auto_refresh_status', 1);
	$interval = get_option('perron_auto_refresh_interval', 1234);
	$timeout = get_option('perron_auto_refresh_timeout', 10);
	$selected_plugins = get_option('perron_auto_refresh_plugins', array());
	$all_plugins = function_exists('get_plugins') ? get_plugins() : array();
	$monitor_all_files = get_option('perron_monitor_all_files', 0);
	?>
	<div class="wrap">
		<h1>Live Auto Refresh Settings</h1>
		<form method="post">
			<?php wp_nonce_field('perron_live_auto_refresh_settings'); ?>
			<table class="form-table">
				<tr valign="top">
					<th scope="row">Enable Auto Refresh</th>
					<td><input type="checkbox" name="perron_auto_refresh_status" value="1" <?php checked($status, 1); ?> /></td>
				</tr>
				<tr valign="top">
					<th scope="row">Refresh Interval (ms)</th>
					<td>
						<input type="number" name="perron_auto_refresh_interval" value="<?php echo esc_attr($interval); ?>" min="500" step="1" />
						<small>(minimum 500ms)</small><br />
						<em><small>Lower values make changes appear more immediate, but may overload low resource servers. If you experience server issues, increase this value.</small></em>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">Timeout (minutes)</th>
					<td><input type="number" name="perron_auto_refresh_timeout" value="<?php echo esc_attr($timeout); ?>" min="1" step="1" /> <small>(monitoring will stop after this many minutes of inactivity, default 10)</small></td>
				</tr>
				<tr valign="top">
					<th scope="row">Monitor Plugin Files (beta)</th>
					<td>
						<fieldset>
							<legend class="screen-reader-text"><span>Select plugins to monitor for file changes</span></legend>
							<?php
							if (!empty($all_plugins)) {
								foreach ($all_plugins as $plugin_path => $plugin_data) {
									$checked = in_array($plugin_path, $selected_plugins) ? 'checked' : '';
									echo '<label><input type="checkbox" name="perron_auto_refresh_plugins[]" value="' . esc_attr($plugin_path) . '" ' . $checked . '> ' . esc_html($plugin_data['Name']) . ' (' . esc_html($plugin_path) . ')</label><br />';
								}
							} else {
								echo '<em>No plugins found.</em>';
							}
							?>
						</fieldset>
						<small>Tick plugins to monitor for file changes (PHP, JS, CSS). Useful for plugin development. <strong>Experimental</strong>.</small>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">File Types to Monitor</th>
					<td>
						<fieldset>
							<label>
								<input type="radio" name="perron_monitor_all_files" value="0" <?php checked($monitor_all_files, 0); ?>> Monitor only <code>php</code>, <code>js</code>, <code>css</code> files (Recommended, Default)
							</label><br>
							<label>
								<input type="radio" name="perron_monitor_all_files" value="1" <?php checked($monitor_all_files, 1); ?>> Monitor <strong>all file types</strong> (e.g. images, json, txt, etc)<br>
								<span style="color:#d35400;font-size:90%">May reduce performance, so an increased Refresh Interval is recommended.</span>
							</label>
						</fieldset>
					</td>
				</tr>
			</table>
			<?php submit_button(); ?>
		</form>
	</div>
	<?php
}

function perron_auto_refresh_deactivate() {
    delete_option('perron_theme_files_hashes');
    delete_option('perron_auto_refresh_status');
    delete_option('perron_post_modified_time');
}