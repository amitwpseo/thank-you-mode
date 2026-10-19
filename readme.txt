=== Thank You Mode ===
Tags: thank you, maintenance, coming soon, landing page, event
Requires at least: 5.8
Tested up to: 6.8
Requires PHP: 7.4
Stable tag: 2.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

One-click "Thank You / Stay Tuned" page with 6 premium designs. Hides the rest of the site from the public while you stay logged in.

== Description ==

Thank You Mode shows a beautiful, branded Thank You page to your visitors and hides the rest of the site from the public, while administrators keep seeing the full site. Ideal after events, launches, sales and seasonal closures.

**Features**

* 6 premium designs with live preview (desktop and mobile)
* Logo picker (Media Library), headline, message, contact email, optional button
* Color pickers and optional background image with overlay
* Schedule: turn on and off automatically
* Redirect other pages to the front page or show the page on the same URL
* Keep chosen pages public, allow other logged-in users or specific IP addresses
* 200 OK or 503 response code, optional noindex
* Blocks REST API and XML-RPC for logged-out visitors while active
* Option to disable Google Fonts, custom CSS field
* Non-destructive - nothing is deleted or changed in your content

== Installation ==

1. Upload the `thank-you-mode` folder to `/wp-content/plugins/`, or install the zip via Plugins > Add New > Upload Plugin.
2. Activate the plugin.
3. Go to Settings > Thank You Mode, choose a design and save.
4. Purge any cache plugin or CDN.

== Frequently Asked Questions ==

= Will this hurt my SEO? =
Redirects are temporary (302) and the page returns 200 OK, so search engines keep your real pages. Use the 503 option for short maintenance.

= How do I see the normal site? =
Log in as an administrator. Add ?tym_preview=1 to any address to see what visitors see.

= How do I turn it off? =
Untick "Enabled" in Settings > Thank You Mode, or deactivate the plugin.

== Changelog ==

= 2.0.0 =
* New general-purpose release: renamed to Thank You Mode, no site-specific defaults.
* New: live preview, tabbed settings, background image, schedule, always-public pages, IP allow-list, other-logged-in-users option, 503 mode, noindex, Google Fonts switch, custom CSS.
* Carries over settings from the earlier private build automatically.
