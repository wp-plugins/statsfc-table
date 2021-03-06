=== StatsFC Table ===
Contributors: willjw
Donate link:
Tags: widget, football, soccer, premier league, uefa, champions league, europa league
Requires at least: 3.3
Tested up to: 4.2.2
Stable tag: 1.9.3
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

This widget will place a football league table on your website.

== Description ==

Add a football league table to your WordPress website. To request a key sign up for your free trial at [statsfc.com](https://statsfc.com).

For a demo, check out [wp.statsfc.com/league-table/](http://wp.statsfc.com/league-table/).

== Installation ==

1. Upload the `statsfc-table` folder and all files to the `/wp-content/plugins/` directory
2. Activate the widget through the 'Plugins' menu in WordPress
3. Drag the widget to the relevant sidebar on the 'Widgets' page in WordPress
4. Set the StatsFC key and any other options. If you don't have a key, sign up for free at [statsfc.com](https://statsfc.com)

You can also use the `[statsfc-table]` shortcode, with the following options:

- `key` (required): Your StatsFC key
- `competition` (required): Competition key, e.g., `EPL`
- `group` (optional): Competition group, e.g., `A`, `B`
- `type` (optional): Type of league table, `full` or `mini`
- `highlight` (optional): Name of the team you want to highlight, e.g., `Liverpool`
- `rows` (optional, *used in conjunction with `highlight`*): Number of rows you want to show, e.g., `3`, `5`
- `date` (optional): For a back-dated league table, e.g., `2013-12-31`
- `show_badges` (optional): Display team badges, `true` or `false`
- `show_form` (optional): Show form of last 6 matches, `true` or `false`
- `default_css` (optional): Use the default widget styles, `true` or `false`

== Frequently asked questions ==



== Screenshots ==



== Changelog ==

**1.0.1**: Swapped club crests for shirts.

**1.0.2**: Fixed possible CSS overlaps.

**1.0.3**: Changed 'Highlight' option from a textbox to a dropdown.

**1.0.4**: Load images from CDN.

**1.1.0**: Updated team badges for 2013/14.

**1.1.1**: Use cURL to fetch API data if possible.

**1.1.2**: Fixed possible cURL bug.

**1.1.3**: Added fopen fallback if cURL request fails.

**1.1.4**: More reliable team icons.

**1.1.5**: Added timestamp for debugging.

**1.2**: Opened up to UEFA Champions League and UEFA Europa League group stages groups.

**1.2.1**: Tweaked error message.

**1.3**: Updated to use the new API. Removed support for European competition groups for now.

**1.3.1**: Use API to get competition list dynamically.

**1.4**: Added an option to show team form.

**1.5**: Added a `date` parameter.

**1.6**: Added `[statsfc-table]` shortcode.

**1.6.1**: Fixed shortcode bug.

**1.7**: Added a `rows` parameter, which will work in association with `highlight`.

**1.7.2**: Updated team badges.

**1.7.3**: Default `default_css` parameter to `true`

**1.7.4**: Added badge class for each team

**1.7.5**: Use built-in WordPress HTTP API functions

**1.7.6**: Added `group` parameter

**1.8**: Enabled ad-support

**1.8.1**: Allow more discrete ads for ad-supported accounts

**1.9**: Added `show_badges` parameter

**1.9.1**: Fixed bug with boolean options

**1.9.2**: Fixed bug saving 'Show badges' option

**1.9.3**: Fixed "Invalid domain" bug caused by referal domain

== Upgrade notice ==

