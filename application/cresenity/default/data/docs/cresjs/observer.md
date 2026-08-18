# Cres JS - Observer

DOM mutation observers for detecting when elements are added to the page. This is the mechanism that powers auto-initialization of cres.js components (SelectTwo, Repeater, etc.).

### elementRendered

Execute a callback when an element matching a CSS selector is added to the DOM:

```javascript
window.addEventListener('cresenity:loaded', () => {
    cresenity.observer.elementRendered('.my-widget', (element) => {
        // Initialize the widget
        new MyWidget(element);
    });
});
```

This uses `MutationObserver` internally and fires for both initial page load and dynamically added elements (e.g. AJAX content, cloned rows in Repeater).

### elementReady

Similar to `elementRendered`, but only fires once per element:

```javascript
cresenity.observer.elementReady('#my-chart', (element) => {
    // Initialize chart - only runs once
});
```

### Use Cases

- Auto-initialize third-party plugins on dynamically loaded content
- Set up event listeners on elements that don't exist at page load
- React to DOM changes from AJAX reload operations
