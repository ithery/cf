# CColor

`CColor` is a color-manipulation utility: parse a color in almost any CSS notation (hex, hex with alpha, `rgb()`, `rgba()`, `hsl()`, `hsla()`, `hsv()`, or a named CSS color like `red`), then lighten, darken, tint, shade, mix, saturate, or convert it to another notation with a fluent API. It's framework-level (`system/libraries/CColor.php` and `system/libraries/CColor/`), available anywhere without an import.

Typical use case: an app lets a company/tenant pick one brand color, and every derived shade used across the UI (hover state, subtle background, dark variant, text-on-color contrast) needs to be computed from that single color instead of hand-picking a palette.

---

### Creating a color

```php
$color = CColor::create('#1568b0');       // hex
$color = CColor::create('#1568b0ff');     // hex with alpha
$color = CColor::create('rgb(21,104,176)');
$color = CColor::create('rgba(21,104,176,0.5)');
$color = CColor::create('hsl(206,79%,39%)');
$color = CColor::create('red');           // named CSS color
```

`CColor::create()` guesses the format from the string and returns an instance of the matching `CColor_Format_*` class (`Hex`, `Hexa`, `Rgb`, `Rgba`, `Hsl`, `Hsla`, `Hsv`), all extending `CColor_FormatAbstract` and sharing the same manipulation API below. Plain 3/6/8-digit hex and named colors resolve to `Hex`/`Hexa`; everything else must be an explicit `rgb()`/`rgba()`/`hsl()`/`hsla()`/`hsv()` string or `CColor::create()` throws `CColor_Exception_AmbiguousColorStringException` (can't tell `hsv()` and `hsl()`'s 3-number form apart) or `CColor_Exception_InvalidColorException`.

---

### Manipulating a color

All of the following return a **new** color instance (same format as the one you called it on) - they don't mutate the original:

```php
$primary = CColor::create('#1568b0');

$primary->lighten(20);      // +20 lightness (HSL)
$primary->darken(20);       // -20 lightness (HSL)
$primary->saturate(15);     // +15 saturation (HSL)
$primary->desaturate(15);   // -15 saturation (HSL)
$primary->grayscale();      // fully desaturated
$primary->brighten(10);     // +10% brightness, blends toward white in RGB space
$primary->spin(40);         // rotate hue by 40 degrees

$primary->tint(90);         // mix 90% toward white - very light tint of the color
$primary->shade(30);        // mix 30% toward black - a darker shade
$primary->mix($other, 50);  // 50/50 linear blend with another CColor instance (RGB space)
$primary->mixInHsv($other, 50); // blend in HSV space (keeps hue/saturation more intact)

$primary->fade(50);         // set opacity to 50%
$primary->fadeIn(10);       // +10% opacity
$primary->fadeOut(10);      // -10% opacity

$primary->isLight();        // bool - true if the color reads as "light" (for text-on-bg contrast)
$primary->isDark();
```

`tint()`/`shade()` are the ones you want for deriving subtle backgrounds/borders from a single brand color - `tint(90)` gives you roughly the same effect as `rgba(brand, 0.1)` painted over a white page, but as a flat, no-transparency hex color.

### Converting / reading a color

```php
$primary->toHex();    // CColor_Format_Hex   -> (string) "#1568b0"
$primary->toHexa();   // CColor_Format_Hexa  -> (string) "#1568b0ff"
$primary->toRgb();    // CColor_Format_Rgb
$primary->toRgba();   // CColor_Format_Rgba
$primary->toHsl();    // CColor_Format_Hsl
$primary->toHsla();   // CColor_Format_Hsla
$primary->toHsv();    // CColor_Format_Hsv

(string) $primary->toHex();  // every format implements __toString(), so you can echo/interpolate directly

$rgb = $primary->toRgb();
$rgb->red();    // int 0-255
$rgb->green();
$rgb->blue();
```

Every `CColor_Format_*` class implements `__toString()`, so you rarely need to call `toHex()`/`toRgb()` just to print a value - string-interpolating the object already gives you its own notation (e.g. a `CColor_Format_Hex` interpolates as `#1568b0`, a `CColor_Format_Rgba` as `rgba(21,104,176,0.5)`). Call `toHex()`/`toRgb()`/etc. explicitly when you need to *convert* to a different notation, or need component access like `->red()`.

---

### Example: deriving a full theme palette from one brand color

This is the pattern used by `application/kiper`'s `page.blade.php` to turn a company's single `theme_color` setting into every CSS custom property the UI needs, replacing what used to be hand-rolled linear-interpolation PHP:

```php
$brand = CColor::create($companyThemeColor); // e.g. '#16a34a'

$primaryDark          = (string) $brand->darken(8)->toHex();
$primaryTextEmphasis  = (string) $brand->shade(40)->toHex();   // toward black, for text on a light bg
$primaryBgSubtle      = (string) $brand->tint(90)->toHex();    // very light bg (Bootstrap *-bg-subtle equivalent)
$primaryBorderSubtle  = (string) $brand->tint(75)->toHex();    // light border
$bodyBackground       = (string) $brand->tint(94)->toHex();    // near-white page background tinted with the brand hue

$rgb = $brand->toRgb();
$primaryRgb = $rgb->red() . ', ' . $rgb->green() . ', ' . $rgb->blue(); // for `rgba(var(--x-rgb), .1)` CSS
```

```html
<style>
    :root {
        --bs-primary: {{ $companyThemeColor }};
        --bs-primary-rgb: {{ $primaryRgb }};
        --bs-primary-text-emphasis: {{ $primaryTextEmphasis }};
        --bs-primary-bg-subtle: {{ $primaryBgSubtle }};
        --bs-primary-border-subtle: {{ $primaryBorderSubtle }};
        --ki-primary: {{ $companyThemeColor }};
        --ki-primary-dark: {{ $primaryDark }};
        --ki-bg-body: {{ $bodyBackground }};
    }
</style>
```

The rest of the app's CSS only ever reads these custom properties (`var(--ki-primary)`, `var(--ki-bg-body)`, ...) - a single `CColor::create($themeColor)` call at render time is enough to re-tint the entire UI consistently, light or dark backgrounds included, without maintaining a second hardcoded palette anywhere.

---

### Other helpers

- **`CColor::random($options = [])`** - returns a `CColor_Random` generator (`->toHex()`, `->toRgb()`, `->toHsl()`, ...) for producing random colors, with options like `hue` (`'red'`, `'blue'`, `'monochrome'`, ...) and `luminosity` (`'bright'`, `'dark'`, `'light'`, `'random'`) to constrain the range - handy for things like auto-assigning a distinct color per chart series or per user avatar.
- **`CColor::fromString($string, $options = [])`** - returns a `CColor_String` generator that deterministically derives a color from an arbitrary string (e.g. a username or ID), so the same input always produces the same color - useful for avatar background colors.
- **`CColor::css($bgHex, $fgHex)`** - returns a `CColor_Css` helper focused on accessibility: given a background/foreground pair it can compute contrast ratios (`brightnessDiff()`, `colorDiff()`) and pick a foreground color that meets a minimum contrast/brightness difference (`calcFG()`), plus standalone `lighten()`/`darken()`/`mix()` on raw hex strings.
