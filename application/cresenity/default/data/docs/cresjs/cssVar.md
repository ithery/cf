# Cres JS - CSS Variables

Cresenity automatically sets CSS custom properties (variables) that update dynamically based on the browser window.

### --cres-window-height

The current window height, updated on resize. Useful for dynamic layouts that need to adapt to viewport changes:

```html
<div style="height: calc(var(--cres-window-height) / 2)">
    This div is always 50% of the window height
</div>
```

```html
<div style="min-height: var(--cres-window-height)">
    Full viewport height container
</div>
```

Unlike `100vh` which can be unreliable on mobile browsers (due to address bar), `--cres-window-height` reflects the actual visible viewport.
