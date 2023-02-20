<?php

namespace Emotality\LaravelColor;

use Emotality\LaravelColor\Interfaces\ColorFunctions;
use Emotality\LaravelColor\Traits\ColorHelper;

/**
 * @implements \Emotality\LaravelColor\Interfaces\ColorFunctions
 */
class LaravelColor implements ColorFunctions
{
    use ColorHelper;

    /**
     * LaravelColor constructor.
     *
     * @param  string|null  $hex The hex color code to parse, with or without hashtag.
     * @param  array|null  $options The parsing options.
     * @return void
     */
    public function __construct(string $hex = null, array $options = null)
    {
        $this->rgba = (object) [
            'r' => 0,
            'g' => 0,
            'b' => 0,
            'a' => 1,
        ];

        $options ? $this->options($options) : self::setDefaultOptions();

        if ($hex) {
            $this->parse($hex);
        }
    }

    /**
     * @param  string  $hex The hex color code to parse, with or without hashtag.
     * @param  array<string, mixed>|null  $options
     * @return $this
     */
    public function parse(string $hex, array $options = null): self
    {
        self::setDefaultOptions();

        if ($options) {
            $this->options($options);
        }

        return $this->setColorProps($hex);
    }

    /**
     * Set/modify parsing options.
     *
     * @param  array<string, mixed>  $options The options you want to set.
     * @return $this
     */
    public function options(array $options): self
    {
        self::setDefaultOptions();

        foreach ($options as $key => $value) {
            if (key_exists($key, self::$options) && ! empty($value)) {
                self::$options[$key] = $value;
            }
        }

        return $this;
    }

    /**
     * Get the hex color code with hashtag.
     *
     * @return string
     */
    public function hex(): string
    {
        return '#'.ltrim($this->hex, '#');
    }

    /**
     * Get the hex color code with hashtag.
     *
     * @return string
     */
    public function hex8(): string
    {
        return '#'.ltrim($this->hex8, '#');
    }

    /**
     * Get red, green and blue from parsed color.
     *
     * @param  string|null  $hex The hex color code to parse, with or without hashtag.
     * @return object
     */
    public function rgb(string $hex = null): object
    {
        if ($hex) {
            $this->parse($hex);
        }

        return (object) [
            'red'   => $this->rgba->r,
            'green' => $this->rgba->g,
            'blue'  => $this->rgba->b,
        ];
    }

    /**
     * Get red, green, blue and alpha from parsed color.
     *
     * @param  string|null  $hex The hex color code to parse, with or without hashtag.
     * @return object
     */
    public function rgba(string $hex = null): object
    {
        if ($hex) {
            $this->parse($hex);
        }

        return (object) [
            'red'   => $this->rgba->r,
            'green' => $this->rgba->g,
            'blue'  => $this->rgba->b,
            'alpha' => $this->rgba->a,
        ];
    }

    /**
     * Get hue, saturation and lightness from parsed color.
     *
     * @param  string|null  $hex The hex color code to parse, with or without hashtag.
     * @return object
     * @url https://www.had2know.org/technology/hsl-rgb-color-converter.html
     * @url https://www.rapidtables.com/convert/color/rgb-to-hsl.html
     * @url https://bgrins.github.io/TinyColor/
     */
    public function hsl(string $hex = null): object
    {
        if ($hex) {
            $this->parse($hex);
        }

        $l = ($this->max + $this->min) / 2;
        $s = ($this->max == $this->min) ? 0 : $this->diff / (1 - abs(2 * $l - 1));

        return (object) [
            'hue'        => $this->hue(),
            'saturation' => intval(round($s * 100)),
            'lightness'  => $this->lightness(),
        ];
    }

    /**
     * Get hue, saturation and value from parsed color.
     *
     * @param  string|null  $hex The hex color code to parse, with or without hashtag.
     * @return object
     * @url https://www.had2know.org/technology/hsv-rgb-conversion-formula-calculator.html
     * @url https://www.rapidtables.com/convert/color/rgb-to-hsv.html
     * @url https://bgrins.github.io/TinyColor/
     */
    public function hsv(string $hex = null): object
    {
        if ($hex) {
            $this->parse($hex);
        }

        $s = $this->max === 0 ? 0 : $this->diff / $this->max;

        return (object) [
            'hue'        => $this->hue(),
            'saturation' => intval(round($s * 100)),
            'value'      => $this->value(),
        ];
    }

    /**
     * Get the hue from parsed color in a value out of 360.
     *
     * @param  string|null  $hex The hex color code to parse, with or without hashtag.
     * @return int
     */
    public function hue(string $hex = null): int
    {
        if ($hex) {
            $this->parse($hex);
        }

        if ($this->max !== $this->min) {
            switch ($this->max) {
                case $this->red:
                    $h = ($this->green - $this->blue) / $this->diff + ($this->green < $this->blue ? 6 : 0);
                    break;
                case $this->green:
                    $h = ($this->blue - $this->red) / $this->diff + 2;
                    break;
                case $this->blue:
                    $h = ($this->red - $this->green) / $this->diff + 4;
                    break;
            }

            $h /= 6;
        }

        return intval(round(($h ?? 0) * 360));
    }

    /**
     * Get the value from parsed color in a percentage value.
     *
     * @param  string|null  $hex The hex color code to parse, with or without hashtag.
     * @return int
     */
    public function value(string $hex = null): int
    {
        if ($hex) {
            $this->parse($hex);
        }

        return intval(round($this->max * 100));
    }

    /**
     * Color relative luminance from parsed color in a percentage value.
     *
     * @param  string|null  $hex The hex color code to parse, with or without hashtag.
     * @return float
     */
    public function luminance(string $hex = null): float
    {
        if ($hex) {
            $this->parse($hex);
        }

        $components = [
            'red'   => $this->red,
            'green' => $this->green,
            'blue'  => $this->blue,
        ];

        foreach ($components as $color => $value) {
            if ($value <= 0.04045) {
                $components[$color] = $value / 12.92;
            } else {
                $components[$color] = pow((($value + 0.055) / 1.055), 2.4);
            }
        }

        // ITU-R BT. 709
        $luminance_perc = (($components['red'] * 0.2126) + ($components['green'] * 0.7152) + ($components['blue'] * 0.0722)) * 100;

        return round($luminance_perc, 2);
    }

    /**
     * Get the lightness from parsed color in a percentage value.
     *
     * @param  string|null  $hex The hex color code to parse, with or without hashtag.
     * @return int
     */
    public function lightness(string $hex = null): int
    {
        if ($hex) {
            $this->parse($hex);
        }

        return intval(round(($this->min + $this->max) * 100 / 2));
    }

    /**
     * Get the brightness from parsed color in a percentage value.
     *
     * @param  string|null  $hex The hex color code to parse, with or without hashtag.
     * @return int
     */
    public function brightness(string $hex = null): int
    {
        if ($hex) {
            $this->parse($hex);
        }

        return intval(round($this->red * 299 + $this->green * 587 + $this->blue * 114) / 10);
    }

    /**
     * If the parsed color is darker than specified brightness percentage.
     *
     * @param  string|null  $hex The hex color code to parse, with or without hashtag.
     * @param  int|null  $brightness The percentage of brightness to measure against, default is 50.
     * @return bool
     */
    public function isDark(string $hex = null, int $brightness = null): bool
    {
        return $this->brightness($hex) < ($brightness ?? self::$options[Color::BRIGHT_PERC]);
    }

    /**
     * If the parsed color is lighter than specified brightness percentage.
     *
     * @param  string|null  $hex The hex color code to parse, with or without hashtag.
     * @param  int|null  $brightness The percentage of brightness to measure against, default is 50.
     * @return bool
     */
    public function isLight(string $hex = null, int $brightness = null): bool
    {
        return ! $this->isDark($hex, $brightness);
    }

    /**
     * Foreground font color if parsed color is the background.
     *
     * @param  string|null  $hex The hex color code to parse, with or without hashtag.
     * @param  int|null  $brightness The percentage of brightness to measure against, default is 50.
     * @return string
     */
    public function fontColor(string $hex = null, int $brightness = null): string
    {
        $key = $this->isDark($hex, $brightness)
            ? Color::DARK_FONT_COLOR
            : Color::LIGHT_FONT_COLOR;

        return $this->output(self::$options[$key]);
    }
}
