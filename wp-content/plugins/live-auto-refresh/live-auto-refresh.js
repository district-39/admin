(function() {
	const params = new Proxy(new URLSearchParams(window.location.search), {
		get: (searchParams, prop) => searchParams.get(prop),
	});
	if(params.perron_auto_refresh_status){
		document.location.href=document.location.pathname;
	}

    var lastHash = '';
    var status = parseInt(autoRefresh.status);
    var lastChangeTime = Date.now();
    var postModifiedTime = parseInt(autoRefresh.postModifiedTime);
    if (status) {
		console.info('%c*** LIVE AUTO REFRESH is monitoring for file changes ***', 'color:black;background:yellow;');
        var interval = typeof autoRefresh.interval === 'number' && autoRefresh.interval >= 500 ? autoRefresh.interval : 1234;
        var timeout = typeof autoRefresh.timeout === 'number' && autoRefresh.timeout >= 1 ? autoRefresh.timeout : 10;
        var intervalId = setInterval(function() {
            if (Date.now() - lastChangeTime > timeout * 60 * 1000) {
                clearInterval(intervalId);
				var toolbarbutton = document.getElementById("wp-admin-bar-autorefresh");
				if (toolbarbutton){
					toolbarbutton.className = "autorefreshbuttonpaused";
					toolbarbutton.getElementsByClassName('ab-item')[0].removeAttribute('onclick');
					toolbarbutton.getElementsByClassName('ab-item')[0].href = document.location.pathname;
				}
				console.log('%c*** LIVE AUTO REFRESH has stopped monitoring after timeout. Reload to restart monitoring ***', 'color:black;background:orange;');
                return;
            }
			

			
            var data = { action: 'auto_refresh', nonce: autoRefresh.nonce };
            fetch(autoRefresh.ajaxurl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: Object.keys(data).map(key => key + '=' + encodeURIComponent(data[key])).join('&')
            })
            .then(response => response.text())
            .then(response => {
                response = JSON.parse(response);
				//console.log(response);
				
				if (postModifiedTime && postModifiedTime !== parseInt(response.postModifiedTime)) {
					console.warn('%c*** LIVE AUTO REFRESH detected a save! ***', 'color:white;background:green;');
					clearInterval(intervalId);
					postModifiedTime = parseInt(response.postModifiedTime);
					location.reload();
				}
				
				if (lastHash && lastHash !== response.hash) {
                    if (response.changedFile.endsWith('.css')) {
						console.warn('%c*** LIVE AUTO REFRESH detected a style change! ***', 'color:white;background:green;');
                        reloadStylesheets();
                    } else {
						console.warn('%c*** LIVE AUTO REFRESH detected a file change! ***', 'color:white;background:green;');
                        location.reload();
                    }
                    lastChangeTime = Date.now();
					postModifiedTime = parseInt(response.postModifiedTime);
                }
                lastHash = response.hash;
            });
        }, interval);
    }else{
		console.info('%c*** LIVE AUTO REFRESH is disabled ***', 'color:white;background:red;');
	}
})();

// Throttle reloads to prevent rapid-fire reloads
var lastReloadTime = 0;
function reloadStylesheets() {
    var now = Date.now();
    if (now - lastReloadTime < 1000) return; // Only allow once per second
    lastReloadTime = now;

    // Collect links into a static array to avoid live collection issues
    var links = Array.prototype.slice.call(document.getElementsByTagName("link"));
    links.forEach(function(link) {
        if (link.rel === "stylesheet" && link.href) {
            var cleanHref = link.href.replace(/([?&]_lr=\d+)/g, "");
            var sep = cleanHref.indexOf('?') !== -1 ? '&' : '?';
            var newHref = cleanHref + sep + '_lr=' + Date.now();

            var newLink = document.createElement('link');
            newLink.rel = 'stylesheet';
            newLink.href = newHref;
            newLink.media = link.media || 'all';
            newLink.type = link.type || 'text/css';

            // Insert new link just before the old one
            link.parentNode.insertBefore(newLink, link);

            var removed = false;
            function cleanup() {
                if (!removed && link.parentNode) {
                    link.parentNode.removeChild(link);
                    removed = true;
                }
            }
            newLink.onload = cleanup;
            // Fallback: remove old link after 5 seconds even if onload fails
            setTimeout(cleanup, 5000);
        }
    });
}