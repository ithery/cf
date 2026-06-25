# Cres JS - Confirm

Show a confirmation dialog before executing an action.

### Simple Confirm

```javascript
cresenity.confirm((confirmed) => {
    if (confirmed) {
        // user pressed Yes
    }
});
```

### Advanced Confirm

Pass options for custom message and callbacks:

```javascript
cresenity.confirm({
    message: 'Are you sure?',
    confirmCallback: (confirmed) => {
        if (confirmed) {
            // user pressed Yes
        }
    }
});
```

### Custom Confirm Handler

Override the default confirmation dialog with your own implementation:

```javascript
window.addEventListener('cresenity:loaded', () => {
    cresenity.setConfirmHandler((owner, settings, callback) => {
        // Use any custom dialog library
        const confirmed = window.confirm(settings.message);
        callback(confirmed);
    });
});
```

This is useful for integrating custom modal libraries (SweetAlert2, etc.) as the confirmation dialog.
