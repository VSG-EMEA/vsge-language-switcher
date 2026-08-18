# VSGE Language Switcher

VSGE Language Switcher provides the canonical dynamic WordPress block
`vsge/language-switcher` for Polylang-backed sites.

The block uses Polylang's public language APIs at render time. It can render a
dropdown, modal trigger, or the legacy dataset mode used by existing menus.
When Polylang is unavailable or returns no languages, it renders no switcher
and does not fatal.

Configuration is available at **Settings -> VSGE Language Switcher**. Existing
`VLS_REGIONS_MODE` and `VLS_REGIONS` constants remain supported with priority
over the WordPress setting. See [configuration documentation](docs/language-switcher-configuration.md)
and [runtime documentation](docs/language-switcher-runtime.md).

License: GPL-3.0-or-later. See [LICENSE.md](LICENSE.md).
