# Cres JS - Confirm


### Custom Confirm Handler

```javascript
window.addEventListener('cresenity:loaded',()=>{
    cresenity.setConfirmHandler((owner, setting, callback)=>{
        const confirmed = window.confirm('Sungguhan ta?');
        callback(confirmed);
    });
});
```
