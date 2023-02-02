# Laravel Color

<p>
    <a href="https://packagist.org/packages/emotality/laravel-color"><img src="https://img.shields.io/packagist/l/emotality/laravel-color" alt="License"></a>
    <a href="https://packagist.org/packages/emotality/laravel-color"><img src="https://img.shields.io/packagist/v/emotality/laravel-color" alt="Latest Version"></a>
    <a href="https://packagist.org/packages/emotality/laravel-color"><img src="https://img.shields.io/packagist/dt/emotality/laravel-color" alt="Total Downloads"></a>
</p>

A Laravel package to retrieve information from colors, convert and more.

:warning: **NOTE:** This is a work in progress!

## Requirements

- PHP 7.2.5+
- Laravel 7+

## Installation

```bash
composer require emotality/laravel-color
```

## Usage

```php
use Emotality\LaravelColor\Color;

$color = Color::parse('#ff2830');

$dark = $color->isDark();
$rgb = $color->rgb();
$brightness = $color->brightness();

// Functions can also be called directly without parsing the color with `parse()`:
$light = Color::isLight('#ff2830');
$hsv = Color::hsv('#ff2830');
$brightness = Color::brightness('#ff2830');
```
As this is a Facade, it can be called without an import like this:

```php
$black = '#000';
$is_dark = \Color::isDark($black); // true
$font_color = \Color::fontColor($black); // #ffffff
```
```html
<!-- For colors where `isDark()` is true, `fontColor()` will return `#ffffff` -->
<a href="#" style="background:#000; color:{{ \Color::fontColor('#000') }};">...</a>
```

## Functions

```php
function rgb(string $hex = null) : object;
function hsl(string $hex = null) : object;
function hsv(string $hex = null) : object;
function luminance(string $hex = null) : float;
function lightness(string $hex = null) : int;
function brightness(string $hex = null) : int;
function isDark(string $hex = null) : bool;
function isLight(string $hex = null) : bool;
function fontColor(string $hex = null) : string;
function toArray() : array;
// more coming soon!
```

#### toArray() :

```php
array:14 [
  "hex" => "#ff2830"
  "red" => 1
  "green" => 0.15686274509804
  "blue" => 0.18823529411765
  "min" => 0.15686274509804
  "max" => 1
  "luminance" => 22.99
  "lightness" => 58
  "brightness" => 41
  "dark" => true
  "light" => false
  "rgb" => {
    +"red": 255
    +"green": 40
    +"blue": 48
  }
  "hsl" => {
    +"hue": 358
    +"saturation": 100
    +"lightness": 58
  }
  "hsv" => {
    +"hue": 358
    +"saturation": 84
    +"value": 100
  }
]
```

## License

laravel-color is released under the MIT license. See [LICENSE](https://github.com/emotality/laravel-color/blob/master/LICENSE) for details.
