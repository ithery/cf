# Helper cmsg

The `cmsg` helper class stores flash messages — notifications that are displayed once and
then discarded. It is a thin wrapper around `CApp_Message`.

### cmsg::add

The `cmsg::add` method adds a message. The first argument is the message type, which
determines how the message is styled when rendered:

```php
cmsg::add('success', 'Data saved');
cmsg::add('error', 'Unable to reach the server');
cmsg::add('warning', 'Only 5% of your quota remains');
cmsg::add('info', 'Synchronisation is scheduled for tonight');
```

Messages are typically added before redirecting:

```php
cmsg::add('success', 'Remote server saved');

return c::redirect('server/remote');
```

### cmsg::get

The `cmsg::get` method retrieves the messages of a given type without removing them:

```php
$messages = cmsg::get('error');
```

### cmsg::flash

The `cmsg::flash` method retrieves the messages of a given type and removes them, so they are
not displayed again on the next request:

```php
$messages = cmsg::flash('success');
```

### cmsg::flash_all

The `cmsg::flash_all` method retrieves and removes every message of every type:

```php
$messages = cmsg::flash_all();
```

### cmsg::clear

The `cmsg::clear` method discards the messages of a given type without reading them:

```php
cmsg::clear('error');
```

### cmsg::clear_all

The `cmsg::clear_all` method discards every message of every type:

```php
cmsg::clear_all();
```

## Display timing

Flash messages are stored in the session and rendered on the **next** request. Adding a
message and returning the current response in the same request will not display it until the
user navigates elsewhere.

For the same reason, assertions in tests should verify an action's outcome through the
response status code or the resulting database state rather than by searching the response
body for the message text.
