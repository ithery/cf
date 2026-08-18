# View

Views separate your presentation logic from your controller logic. They contain the HTML and Blade template directives that render the final output sent to the browser.

Views are stored in the `views/` directory of your application (`application/{app_code}/default/views/`).

---

### Creating Views

Create a view by placing a file with the `.blade.php` extension in your `views/` directory. The `.blade.php` extension tells the framework to process the file using the Blade template engine.

A simple view might look like this:

**`views/greeting.blade.php`**
```html
<html>
    <body>
        <h1>Hello, {{ $name }}</h1>
    </body>
</html>
```

---

### Rendering Views

Return a view from your controller using the `c::view()` helper:

```php
<?php
public function index() {
    return c::view('greeting', ['name' => 'James']);
}
```

You can also use `CView::make()`:

```php
return CView::make('greeting', ['name' => 'James']);
```

---

### Nested View Directories

Views can be organized into subdirectories. Use dot notation to reference them.

For example, a view stored at `views/admin/profile.blade.php` is referenced as:

```php
return c::view('admin.profile', $data);
```

More nesting levels work the same way:

```php
// views/docs/nav.blade.php
return c::view('docs.nav', ['navs' => $navs]);

// views/demo/page/view/simple/index.blade.php
return c::view('demo.page.view.simple.index', $data);
```

---

### Checking If a View Exists

Use `CView::exists()` to check if a view is available before rendering:

```php
if (CView::exists('admin.profile')) {
    return c::view('admin.profile');
}
```

---

### Passing Data to Views

#### As an Array

Pass data as the second argument — an associative array of key/value pairs. Each key becomes a variable available in the view:

```php
return c::view('greeting', ['name' => 'Victoria', 'age' => 30]);
```

In the view:
```html
<h1>Hello, {{ $name }}</h1>
<p>Age: {{ $age }}</p>
```

#### Using the with() Method

Chain `with()` calls to pass data one value at a time:

```php
return c::view('greeting')
    ->with('name', 'Victoria')
    ->with('occupation', 'Astronaut');
```

#### Sharing Data Across All Views

Use `CView::factory()->share()` to make data available to every view. This is typically done in your `bootstrap.php`:

```php
CView::factory()->share('appName', c::config('app.title'));
```

In any view:
```html
<title>{{ $appName }}</title>
```

---

### Using Views with CApp

The most common pattern in CF is using views through the `CApp` page builder. CApp handles the full page layout (theme, navigation, assets), and you set which view to render:

```php
<?php
public function index() {
    $app = c::app();
    $app->title('Dashboard');
    $app->setView('dashboard');

    return $app;
}
```

You can also add a view as an element inside CApp:

```php
<?php
public function members() {
    $app = c::app();
    $app->title('Members');

    $app->addView('member', [
        'members' => MemberModel::all(),
    ]);

    return $app;
}
```

---

### Blade Directives

Cresenity uses the Blade template engine (same syntax as Laravel). Here are the most common directives:

#### Displaying Data

```html
<!-- Escaped output (safe from XSS) -->
{{ $name }}

<!-- Unescaped output (use with caution) -->
{!! $html !!}

<!-- Default value if variable is not set -->
{{ $name ?? 'Guest' }}
```

#### Conditionals

```html
@if ($user)
    <p>Welcome, {{ $user->name }}</p>
@elseif ($guest)
    <p>Welcome, Guest</p>
@else
    <p>Please log in</p>
@endif

@unless ($user->isAdmin())
    <p>You are not an admin</p>
@endunless

@isset($name)
    <p>{{ $name }}</p>
@endisset

@empty($records)
    <p>No records found</p>
@endempty
```

#### Loops

```html
@foreach ($users as $user)
    <p>{{ $user->name }}</p>
@endforeach

@forelse ($users as $user)
    <p>{{ $user->name }}</p>
@empty
    <p>No users found</p>
@endforelse

@for ($i = 0; $i < 10; $i++)
    <p>Item {{ $i }}</p>
@endfor

@while ($condition)
    <p>Looping...</p>
@endwhile
```

The `$loop` variable is available inside `@foreach` and `@forelse`:

```html
@foreach ($users as $user)
    @if ($loop->first) <strong> @endif
    {{ $user->name }}
    @if ($loop->first) </strong> @endif
    @unless ($loop->last) , @endunless
@endforeach
```

#### Including Sub-Views

```html
<!-- Include a partial -->
@include('shared.header')

<!-- Include with additional data -->
@include('shared.alert', ['type' => 'danger', 'message' => 'Error!'])

<!-- Include if exists -->
@includeIf('custom.sidebar')

<!-- Include based on condition -->
@includeWhen($user->isAdmin(), 'admin.toolbar')
```

#### Layouts and Sections

**`views/layouts/base.blade.php`**
```html
<html>
<head>
    <title>@yield('title') - My App</title>
</head>
<body>
    <nav>@include('shared.nav')</nav>
    <main>
        @yield('content')
    </main>
</body>
</html>
```

**`views/home.blade.php`**
```html
@extends('layouts.base')

@section('title', 'Home')

@section('content')
    <h1>Welcome</h1>
    <p>This is the home page.</p>
@endsection
```

#### Raw PHP

```html
@php
    $total = array_sum($prices);
@endphp
<p>Total: {{ $total }}</p>
```

---

### CApp Blade Directives

When using views inside a CApp theme, these special directives are available:

```html
<!-- Include CApp CSS assets -->
@CAppStyles

<!-- Include CApp JavaScript assets -->
@CAppScripts
```

These are typically placed in the theme layout, not in individual views.
