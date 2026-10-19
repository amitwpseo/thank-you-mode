# Thank You Mode

> One-click **"Thank You / Stay Tuned"** landing page for any WordPress site. Pick one of 6 premium designs, make it yours, and hide the rest of the site from the public - while you stay logged in and see everything.

Perfect after **events, conventions, launches, sales, seasonal closures** and any time you want a clean, branded "thank you" page instead of a half-empty site.

![WordPress](https://img.shields.io/badge/WordPress-5.8%2B-blue) ![PHP](https://img.shields.io/badge/PHP-7.4%2B-777bb3) ![License](https://img.shields.io/badge/license-GPL--2.0--or--later-green)

---

## Features

- **6 premium designs** - Vault Classic, Arcade Neon, Collector's Ticket, Comic Pop, Vegas Marquee, Minimal Editorial
- **Live preview** inside the settings page (desktop and mobile views)
- **Full branding control** - logo (Media Library picker), headline, message, contact email, optional button
- **Color pickers** for background, accent and highlight (or keep each design's own palette)
- **Background image** with adjustable dark overlay
- **Schedule** - switch on and off automatically at a chosen date/time (site timezone)
- **Smart visibility rules**
  - redirect every other URL to the front page, or serve the page on the same URL
  - keep chosen pages public (e.g. `/privacy-policy/`)
  - let other logged-in users and/or allow-listed IPs see the real site
  - choose `200 OK` (Thank You page) or `503` (short maintenance)
- **Security** - blocks the REST API and XML-RPC for logged-out visitors while active
- **SEO friendly** - temporary (302) redirects, canonical to the front page, optional `noindex`
- **Privacy** - optionally turn off Google Fonts
- **Custom CSS** field for fine-tuning
- **Non-destructive** - your pages, posts and settings are never changed. Deactivate and the site is back instantly.
- Admin-bar indicator so you never forget it is on

## Installation

1. Download the latest `thank-you-mode.zip` from the [Releases](../../releases) page (or use **Code -> Download ZIP** and zip the `thank-you-mode` folder).
2. In WordPress go to **Plugins -> Add New -> Upload Plugin**, choose the zip, then **Install Now** and **Activate**.
3. Go to **Settings -> Thank You Mode**, pick a design, edit the text, and press **Save Changes**.
4. Purge your cache plugin / CDN once.

It is **ON as soon as you activate it**. Turn it off any time with the switch at the top of the settings page.

## How it works

- Visitors who are not logged in see only the Thank You page, served from the front page.
- Logged-in administrators see the normal site. Add `?tym_preview=1` to any address to see exactly what visitors see.
- The page is standalone (no theme header, menu or footer), so it works with any theme and any page builder.

## Settings overview

| Tab | What you can do |
| --- | --- |
| **Design** | Choose a design, set colors, add a background image |
| **Content** | Logo, headline, message, contact, button, design-specific text |
| **Visibility & Rules** | Schedule, redirect behaviour, response code, always-public pages, who still sees the real site, REST/XML-RPC blocking, noindex |
| **Advanced** | Browser tab title, Google Fonts on/off, custom CSS |

## Developer hooks

```php
// Modify settings at runtime
add_filter( 'tym_settings', function ( $settings ) {
	$settings['headline'] = 'See you next year!';
	return $settings;
} );

// Override whether Thank You Mode is active
add_filter( 'tym_is_active', function ( $active, $settings ) {
	return $active && ! is_user_logged_in();
}, 10, 2 );
```

## FAQ

**Will this hurt my SEO?**
The redirects are temporary (302) and the Thank You page returns `200 OK`, so search engines keep your real pages in their index. For short maintenance use the `503` option. Restore the site when you are ready and rankings recover normally.

**Does it work with caching plugins?**
Yes. The page sends no-cache headers and the `DONOTCACHEPAGE` flag. Purge your cache once after activating or changing settings.

**Can my team still see the site?**
Administrators always can. You can also allow all logged-in users, or specific IP addresses.

**Does it delete anything?**
No. Deactivate the plugin (or switch it off) and everything is exactly as you left it. Deleting the plugin removes only its own settings.

**It works behind Cloudflare - why does the IP list not match?**
The IP allow-list uses the address the server reports. Behind a CDN/proxy that can be the CDN's address.

## Changelog

See [CHANGELOG.md](CHANGELOG.md).

## Credits

Built by **[BLSS - Best Local SEO Services & Development](https://bestlocalseoservices.com)**.

## License

GPL-2.0-or-later. See [LICENSE](LICENSE).
