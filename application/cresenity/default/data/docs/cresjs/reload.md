# Cres JS - Reload


```js
cresenity.reload(options);
//3.5.1
```

### Options

##### url (required)
Url ajax yang akan diload

```js
{
    url : '/app/ajax/reload',
}

```

##### selector (required)
Selector element yang akan direload

```js
{
    selector : '.element .tobe .selected',
}

```
##### reloadType (default:reload)
Jenis reload, parameter yang ada:

1. reload : replace inside current selector element
2. after : after selector element
3. before : before selector element
4. append : append inside current selector element
5. prepend: prepend inside current selector element

```js
{
    reloadType : 'reload',
}

```

##### onBlock (default:false)
callback function saat akan melakukan ajax
```js
{
    onBlock : () => {
        //do something to block here
        $(selector).addClass('loading');
    }
}

```

##### onUnblock (default:false)
callback function saat setelah ajax (success maupun error)

```js
{
    onUnblock : () => {
        //do something to unblock here
        $(selector).removeClass('loading');
    }
}

```

##### blockHtml (default:false)
blockHtml yang akan digunakan untuk memblock reload element

parameter `false` akan menggunakan default html:
```html
<div class="sk-wave sk-primary">
    <div class="sk-rect sk-rect1"></div>
    <div class="sk-rect sk-rect2"></div>
    <div class="sk-rect sk-rect3"></div>
    <div class="sk-rect sk-rect4"></div>
    <div class="sk-rect sk-rect5"></div>
</div>
```
##### method (default:'get')
Method yang akan digunakan untuk ajax

##### onComplete (default:false)
Callback saat ajax complete
```js
{
    onComplete : () => {

    }
}

```

##### onSuccess (default:false)
Callback saat ajax success, mempunyai parameter object data dari json response capp
```js
{
    onSuccess : (data) => {

    }
}

```

##### dataAdditional (default:{})
data yang akan dikirim untuk ajax
```js
{
    dataAdditional : {
        name: 'John',
        email: 'john@doe.com',
    }
}

```


### Events

##### reload:success
Event saat reload success

```js
    //when object cresenity not loaded
    window.addEventListener('cresenity:reload:success',(event) => {
        const cAppResponse = event.detail
    });

    //when object cresenity is loaded
    cresenity.on('reload:success',(event) => {
        const cAppResponse = event.detail
    });
```

##### reload:error
Event saat reload error

```js
    //when object cresenity not loaded
    window.addEventListener('cresenity:reload:error',(event) => {
        const xhr = event.detail.xhr; // ajax xhr object
        const ajaxOptions = event.detail.ajaxOption; // ajaxOptions object
        const error = event.error; // thrown error
        //do something to handle error
    });

    //when object cresenity is loaded
    cresenity.on('reload:error',(event) => {
        //do something to handle error
    });
```

##### reload:complete

```js
    //when object cresenity not loaded
    window.addEventListener('cresenity:reload:complete',(event) => {
        //no parameters passed on event, event.detail == null
        //do something to handle complete event
    });

    //when object cresenity is loaded
    cresenity.on('reload:complete',(event) => {
        //do something to handle complete event
    });
```
