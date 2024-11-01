=== SLRedirectPlugIn ===
Contributors: Steffen Liersch
Donate link: http://www.steffen-liersch.de/wordpress/
Tags: liersch, seo, move, forward, migrate, redirect, redirections, relocated, location, header, wp_redirect, 301, 302, 404
Requires at least: 2.8.1
Tested up to: 2.9.1
Stable tag: 1.0

This plug-in can generate redirections to relocated pages or posts.
Furthermore an alternative implementation of wp_redirect can be installed
for the case that the correct HTTP status code is not sent.

== Description ==

This plug-in can generate redirections to relocated pages or posts. Not found
documents are searched by name only. If found, the request will be permanently
redirected to the new location (returning HTTP status code 301). This option
helps, for instance, if the permalink structure has changed.

Furthermore an alternative implementation of wp_redirect can be installed
for the case that the correct HTTP status code is not sent. This modified
implementation of function wp_redirect does not care about FastCGI. As a
result the correct HTTP status code is returned. Permanent redirections
will be enabled (HTTP status code 301). Please be careful with this option.
Use it at your own risk.

If you have any questions or if you need more information, please visit
[http://www.steffen-liersch.de/wordpress/](http://www.steffen-liersch.de/wordpress/ "Steffen Liersch Software Solutions").

== Installation ==

1. Upload `sl-redirect-plugin.php` to the `/wp-content/plugins/` directory
1. Activate the plug-in through the 'Plugins' menu in WordPress
1. Call the setup 'SL::Redirect' through the 'Settings' menu

== Changelog ==

= Version 1.0 (2010-02-11) =
* Initial release
