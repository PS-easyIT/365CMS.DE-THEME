# cms-newspaper

Version: 0.1.0  
Status: Legacy static prototype (not yet a full 365CMS theme)

## Current State

This directory currently contains a static file:

- `newspaper.html`

There is no `theme.json`, no `functions.php`, and no CMS template structure yet.

## Migration Goal (v3)

To bring `cms-newspaper` to 365CMS v3 compatibility, the following steps are required:

1. Create `theme.json` with templates, metadata, and Customizer schema.
2. Split `newspaper.html` into `header.php`, `home.php`, `footer.php`, and optional section partials.
3. Add `functions.php` with:
   - hook registration (`head`, `before_footer`, `init`, `cms_init`)
   - asset loading via deferred scripts and safe escaped URLs
   - customizer CSS variable output
4. Move inline styles/scripts into dedicated `style.css` and `js/navigation.js`.
5. Add accessibility and responsive hardening (`focus-visible`, reduced motion, keyboard nav).

## Notes

`cms-newspaper` is intentionally documented now so it can be upgraded in a controlled next pass without losing context.
