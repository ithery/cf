# Cres JS - Confirm

Simple Confirm

```javascript
cresenity.confirm((confirmed) => {
    if(confirmed) {
        //do something when user press Yes
    } else {
        //do something when user press No
    }
});
```

Advance Confirm
```javascript
cresenity.confirm({
    message: 'Apakah anda yakin?',
    confirmCallback: (confirmed) => {
        if(confirmed) {
            //do something when user press Yes
        } else {
            //do something when user press No
        }
    }
});
```

### Custom Confirm Handler

```javascript
window.addEventListener('cresenity:loaded',()=>{
    cresenity.setConfirmHandler((owner, setting, callback)=>{
        const confirmed = window.confirm('Apakah anda sudah benar-benar yakin?');
        callback(confirmed);
    });
});
```
