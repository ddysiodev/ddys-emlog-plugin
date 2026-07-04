# DDYS API Emlog Pro Plugin

English | [简体中文](README.zh-CN.md)

Official Emlog Pro plugin for the [DDYS](https://ddys.io/) API. It adds frontend plugin pages, article shortcodes, a local JSON proxy, caching, diagnostics, and a server-side request form without exposing the API Key in the browser.

- Repository: [ddysiodev/ddys-emlog-plugin](https://github.com/ddysiodev/ddys-emlog-plugin)
- GitHub Release: [v0.1.0](https://github.com/ddysiodev/ddys-emlog-plugin/releases/tag/v0.1.0)
- Download ZIP: [ddys-emlog-plugin-v0.1.0.zip](https://github.com/ddysiodev/ddys-emlog-plugin/releases/download/v0.1.0/ddys-emlog-plugin-v0.1.0.zip)
- Plugin directory: `ddys_open`
- Target: Emlog Pro plugin system
- Distribution: GitHub Release ZIP

## Features

- Admin settings for API Base URL, source site URL, API Key, timeout, cache TTLs, default count, theme, layout, navigation, and request form.
- Admin diagnostics for connection tests, cache status, cache clearing, and endpoint inspection.
- Generator for shortcodes, frontend page links, and local proxy URLs.
- Frontend pages for latest, hot, search, calendar, movie detail, collections, collection detail, and requests.
- Article shortcode rendering for `[ddys_*]` tags.
- Local JSON proxy under the Emlog site domain, keeping the API Key server-side.
- Server-side request submission with nonce validation, rate limiting, field validation, and clear errors.
- Per-endpoint file caching with separate TTLs for dictionaries, fresh lists, details, and community data.
- Safety checks for Emlog entry guards, Input parameter reading, route allowlists, parameter allowlists, escaped output, media URL validation, timeouts, and sensitive settings.

## Installation

1. Download `ddys-emlog-plugin-v0.1.0.zip` from Releases.
2. In the Emlog Pro admin panel, open `Plugins -> Upload`, then upload the zip.
3. Or unzip manually and upload `ddys_open` to `content/plugins/ddys_open`.
4. Enable “低端影视 API” in the plugin list.
5. Open the plugin settings page and configure API Base URL, cache TTLs, display options, and navigation.
6. To enable the request form, set an API Key and run the connection test.

## Frontend Routes

Default dynamic entries:

```text
?plugin=ddys_open
?plugin=ddys_open&view=hot
?plugin=ddys_open&view=search
?plugin=ddys_open&view=calendar
?plugin=ddys_open&view=movie&slug=this-tempting-madness
?plugin=ddys_open&view=collections
?plugin=ddys_open&view=collection&slug=editor-choice
?plugin=ddys_open&view=requests
```

When Emlog Pro pretty URLs are enabled, the plugin homepage can also be opened at:

```text
/plugin/ddys_open
```

Other views can append query parameters:

```text
/plugin/ddys_open?view=hot
/plugin/ddys_open?view=movie&slug=this-tempting-madness
```

Local proxy examples:

```text
?plugin=ddys_open&action=api&route=latest&limit=6
?plugin=ddys_open&action=api&route=movie&slug=this-tempting-madness
?plugin=ddys_open&action=api&route=collections&page=1
```

Request form endpoint:

```text
?plugin=ddys_open&action=request-submit
```

## Shortcodes

```text
[ddys_latest limit="12"]
[ddys_hot limit="10"]
[ddys_search]
[ddys_suggest q="interstellar" limit="8"]
[ddys_calendar year="2026" month="7"]
[ddys_movie slug="this-tempting-madness"]
[ddys_sources slug="this-tempting-madness"]
[ddys_related slug="this-tempting-madness"]
[ddys_comments slug="this-tempting-madness"]
[ddys_collections page="1"]
[ddys_collection slug="editor-choice"]
[ddys_shares page="1"]
[ddys_share id="1"]
[ddys_requests page="1"]
[ddys_activities page="1"]
[ddys_user username="demo"]
[ddys_types]
[ddys_genres]
[ddys_regions]
[ddys_request_form]
```

Full movie list example:

```text
[ddys_movies type="movie" genre="drama" region="us" year="2026" sort="latest" page="1" per_page="12"]
```

## Cache

Cache files are stored in:

```text
content/plugins/ddys_open/cache
```

The plugin writes `.htaccess` and `index.html` to reduce direct access risk. Cache can be cleared from the admin settings page.

## Pretty URLs

Emlog Pro already provides plugin page routing. After enabling pretty URLs, confirm `/plugin/ddys_open` works. Nginx and Apache rules should follow the official Emlog Pro rewrite rules; this plugin does not require overwriting core PHP files.

## Development Check

Run from the repository root:

```powershell
node tools/check.mjs
```

The check covers Emlog plugin structure, main file metadata, frontend page, settings page, shortcode coverage, proxy, request form, cache, safety wording, icon dimensions, and sensitive files.
