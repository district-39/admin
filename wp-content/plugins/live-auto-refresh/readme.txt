=== Live Auto Refresh (Hot Reload / Live Reload for WordPress Developers) ===
Contributors: perron
Donate link: https://paypal.me/perronuk/
Tags: reload, refresh, live reload, hot reload, auto refresh
Requires at least: 4.7
Tested up to: 6.8.1
Stable tag: 3.2.1
Requires PHP: 7.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Instantly reloads the browser when any theme file code is edited during development or when a content edit is saved.

== Description ==

**THIS PLUGIN IS FOR USE DURING THE DEVELOPMENT OF WORDPRESS THEMES**

Live Auto Refresh helps developers instantly see changes made to theme code or content by automatically reloading the browser. No manual refresh is needed.

This greatly enhances the productivity and accuracy of web development. It allows the developer to test and debug the website in real time and to instantly view how the content appears on the front end of all loaded logged in browsers and devices. With this feature, the developer can always ensure that the website matches the intended design and functionality.

This plugin is only active for logged-in administrators and does not affect regular site visitors.

**🆕 MAJOR v3 UPDATES ~ New in this version:**
- Usability: A settings page is available under Settings → Live Auto Refresh
- Settings: option to monitor all file types, or default (php, css, js).
- BETA: Settings for monitoring selected plugin(s) for changes during development.
- BETA: Support for monitoring of non standard folder paths.
eg. /app/ not /wp-content/ in Roots Bedrock.
- BETA: Support for popular page builders monitoring of content saves.
- Extensibility: Developers can add support for additional builders via the `perron_auto_refresh_custom_hooks` filter
- Performance: File change detection uses faster file modification times.
- Security: All AJAX requests are protected by nonces, and all user input is properly sanitized.

== Installation ==

1. Install to your site from the WordPress Plugin Directory
2. Activate the plugin through the Plugins menu in WordPress
3. Use the new settings page (Settings → Live Auto Refresh) to enable or disable auto refresh as needed.

== Frequently Asked Questions ==

= What files does this monitor for code changes? =

By default, only .php, .js, and .css files within the active theme and child theme are monitored for changes. This improves performance and avoids unnecessary checks on other files.

= Do style changes reload the page? =

Changes to CSS files will hot reload to display the updates styling without reloading the HTML DOM.

= What content saves trigger a refresh? =

Editor saves to the wordpress page or post content.

= How are PHP errors handled? =

If WP_DEBUG is enabled then any non critical error messages will display.
Critical errors will prevent the plugin running, so a manual reload will be required once the code error is fixed to restart monitoring.

= Supports popular page builders content save monitoring =

Auto refresh triggers on content save in these builders:
- WP Block Editor (Gutenberg)
- Elementor
- Beaver Builder
- Bricks Builder
- WPBakery
- Divi
- Visual Composer

= How do I use the new settings page? =

Go to Settings → Live Auto Refresh in your WordPress admin dashboard. You can enable or disable auto refresh here, or use the admin bar toggle for quick access. The settings page also allows you to monitor all file types, or default (php, css, js). You can also select which plugins to monitor for changes during development. Interval and timeout options are also available.

= Advanced extensibility: =

Developers can add support for additional builder save hooks using the `perron_auto_refresh_custom_hooks` filter. Example:

    add_filter('perron_auto_refresh_custom_hooks', function($hooks) {
        $hooks[] = 'my_custom_builder_save_hook';
        return $hooks;
    });

= Do you accept donations? =

This plugin saves developers valuable time, so any donations are greatly appreciated!
[DONATE](https://paypal.me/perronuk/)

== Screenshots ==

1. Admin Bar toggle to enable or disable the monitoring.
2. Console notifications of active monitoring, file change detection, style detection.
3. Disables monitoring after a defined timeout only if no changes have been detected.

== Changelog ==

= 3.2.1 =
* inline toolbar button styling bugfix

= 3.2 =
* improved style change hot reloading

= 3.1 =
* improved style change hot reloading

= 3.0 =
* Settings option to monitor all file types, or default (php, css, js).
* BETA support for monitoring selected plugin during development.
* BETA support for monitoring non standard folder paths.
eg. /app/ not /wp-content/ in Roots Bedrock.
* BETA: for popular page builders monitoring of content saves
* Extensibility: Developers can add support for additional builders via the `perron_auto_refresh_custom_hooks` filter

= 2.2 =
* Title change to Live Auto Refresh (Hot Reload / Live Reload for Wordpress Developers)

= 2.1 =
* Added settings link to the plugin page.
* Fixed versioning for js file.

= 2.0 =
* Added settings page for enabling/disabling the plugin and to change the refresh interval.
* Added settings option to alter the monitoring timeout.
* Updated WP tested version to 6.8.1

= 1.1 =
* Updated WP tested version to 6.7.1

= 1.0 =
* Initial version