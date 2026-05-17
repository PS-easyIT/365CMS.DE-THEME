# LogiLink Theme

Version: 1.0.1  
Target: 365CMS v3.x.x, PHP 8.4

## Purpose

`LogiLink` is a logistics and transport theme focused on fast operational workflows:

- shipment tracking
- KPI-heavy dashboard sections
- compact navigation with quick actions
- status-driven visual language

## Technical Notes

- Theme hooks are registered for both `init` and `cms_init`.
- Customizer values are mapped to CSS variables in `functions.php`.
- Typography settings from `theme.json` are translated into valid font stacks.
- Status colors (`warehouse`, `picked`, `transit`, `delivered`, `delayed`, `returned`) are controlled centrally via Customizer.

## Security and Accessibility

- Header and home URLs are rendered with escaped output.
- `type="button"` is set on non-submit action buttons.
- Visible keyboard focus states are enabled via `:focus-visible`.
- Motion-heavy reveal effects are skipped when `prefers-reduced-motion: reduce` is active.

## Performance

- Google Fonts URLs are sanitized before output.
- Navigation JS is deferred.
- Scroll listener is passive.
- IntersectionObserver is only used when available and when reduced motion is not requested.
