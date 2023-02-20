# Laravel Color

<p>
    <a href="https://packagist.org/packages/emotality/laravel-color"><img src="https://img.shields.io/packagist/l/emotality/laravel-color" alt="License"></a>
    <a href="https://packagist.org/packages/emotality/laravel-color"><img src="https://img.shields.io/packagist/v/emotality/laravel-color" alt="Latest Version"></a>
    <a href="https://packagist.org/packages/emotality/laravel-color"><img src="https://img.shields.io/packagist/dt/emotality/laravel-color" alt="Total Downloads"></a>
</p>

A Laravel package to retrieve information from colors, convert and more.

## Requirements

- PHP 7.3+
- Laravel 8+

## Installation

```bash
composer require emotality/laravel-color
```

## Usage

```php
use Emotality\LaravelColor\Color;

$value = Color::parse('#ff2830')->functionName();
// or
$color = Color::parse('#ff2830');
$value1 = $color->functionName();
$value2 = $color->functionName();
```

Functions can also be called directly without parsing the color with `parse()`:

```php
use Emotality\LaravelColor\Color;

$value = Color::functionName('#ff2830');
```

As this is a Facade, it can just be called without an import like this:

```php
$value = \Color::functionName('#ff2830');
```

This package also supports `hex8` which includes the alpha:

```php
$color = \Color::parse('#ff28307f');
$alpha = $color->rgba()->alpha; // 0.498 (50%)
```


### Examples:

```php
$black = '#000';
$is_dark = \Color::isDark($black); // true
$font_color = \Color::fontColor($black); // #ffffff
```

```php
// #ff2830 has a brightness of 41 :
$font_color = \Color::fontColor('#ff2830', 30); // #000000
$font_color = \Color::fontColor('#ff2830', 50); // #ffffff
```

```html
<!-- 
For colors where `isDark()` is true, `fontColor()` will return `#ffffff`
and colors where `isLight()` is true, `fontColor()` will return `#000000`. 
-->
<a href="#" style="background:#000; color:{{ \Color::fontColor('#000') }};">
    ...
</a>
```

## Functions

```php
function hex(): string;
function hex8(): string;
function rgb(string $hex = null): object;
function rgba(string $hex = null): object;
function hsl(string $hex = null): object;
function hsv(string $hex = null): object;
function hue(string $hex = null): int;
function value(string $hex = null): int;
function luminance(string $hex = null): float;
function lightness(string $hex = null): int;
function brightness(string $hex = null): int;
function isDark(string $hex = null, int $brightness = null): bool;
function isLight(string $hex = null, int $brightness = null): bool;
function fontColor(string $hex = null, int $brightness = null): string;
function toJson(): string;
function toArray(): array;
function toObject(): object;
// more coming soon!
```

Please see all functions [here](https://github.com/emotality/laravel-color/blob/1.x/src/Interfaces/ColorFunctions.php).

#### toArray() for `#ff28307f` :

```php
array:20 [
  "hex" => "#ff2830"
  "hexdec" => 16721968
  "hex8" => "#ff28307f"
  "hex8dec" => 4280823935
  "red" => 1               // out of 1
  "green" => 0.15686       // out of 1
  "blue" => 0.18824        // out of 1
  "alpha" => 0.498         // out of 1
  "rgb" => array:3 [
    "red" => 255           // out of 255
    "green" => 40          // out of 255
    "blue" => 48           // out of 255
  ]
  "rgba" => array:4 [
    "red" => 255           // out of 255
    "green" => 40          // out of 255
    "blue" => 48           // out of 255
    "alpha" => 0.498       // out of 1
  ]
  "hsl" => array:3 [
    "hue" => 358           // out of 360
    "saturation" => 100    // out of 100
    "lightness" => 58      // out of 100
  ]
  "hsv" => array:3 [
    "hue" => 358           // out of 360
    "saturation" => 84     // out of 100
    "value" => 100         // out of 100
  ]
  "hue" => 358             // out of 360
  "value" => 100           // out of 100
  "luminance" => 22.99     // out of 100
  "lightness" => 58        // out of 100
  "brightness" => 41       // out of 100
  "dark" => true
  "light" => false
  "font_color" => "#ffffff"
]
```

## License

laravel-color is released under the MIT license. See [LICENSE](https://github.com/emotality/laravel-color/blob/master/LICENSE) for details.
