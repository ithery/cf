# Cres JS - Observer


### elementRendered

```javascript
window.addEventListener('cresenity:loaded',()=>{
    cresenity.observer.elementRendered('.my-element',(element) => {

        element.innerHTML = element.innerHTML + '|rendered dari js';
    });
});
```
