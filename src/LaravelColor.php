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
     * Get hex code from red, green and blue.
     *
     * @param  int  $red
     * @param  int  $green
     * @param  int  $blue
     * @return string
     */
    public function rgbToHex(int $red, int $green, int $blue): string
    {
        return '#'
            .str_pad(dechex($red), 2, '0', STR_PAD_LEFT)
            .str_pad(dechex($green), 2, '0', STR_PAD_LEFT)
            .str_pad(dechex($blue), 2, '0', STR_PAD_LEFT);
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
     * Get hex8 code from red, green, blue and alpha.
     *
     * @param  int  $red
     * @param  int  $green
     * @param  int  $blue
     * @param  int  $alpha
     * @return string
     */
    public function rgbaToHex8(int $red, int $green, int $blue, float $alpha = 1): string
    {
        $hex = $this->rgbToHex($red, $green, $blue);

        // NOTE: Assuming the following: $alpha 1~100 ? x / 100 = 0.x : 0.x
        $alpha = $alpha > 1 ? ($alpha / 100) : $alpha;
        $alpha = $alpha * 255;

        return $hex.str_pad(dechex($alpha), 2, '0', STR_PAD_LEFT);
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

        $s = floatval($this->max) === 0.0 ? 0 : $this->diff / $this->max;

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

    /**
     * @param  string  $color1_hex
     * @param  string  $color2_hex
     * @param  int  $percentage
     * @return string
     * @throws \Emotality\LaravelColor\LaravelColorException
     */
    public function mix(string $color1_hex, string $color2_hex, int $percentage = 50): string
    {
        $percentage = $percentage < 0 ? 0 : ($percentage > 100 ? 100 : $percentage);
        $factor = $percentage / 100;

        $color1rgb = self::getRGBA($color1_hex);
        $color1 = [$color1rgb->r, $color1rgb->g, $color1rgb->b];

        $color2rgb = self::getRGBA($color2_hex);
        $color2 = [$color2rgb->r, $color2rgb->g, $color2rgb->b];

        for ($i = 0; $i < 3; $i++) {
            $color[$i] = round($color1[$i] + $factor * ($color2[$i] - $color1[$i]));
        }

        return $this->rgbToHex($color[0], $color[1], $color[2]);
    }

    /**
     * @param  int  $percentage
     * @param  string|null  $hex
     * @return string
     * @throws \Emotality\LaravelColor\LaravelColorException
     */
    public function tint(int $percentage = 10, string $hex = null): string
    {
        return $this->mix(($hex ?? $this->hex), 'ffffff', $percentage);
    }

    /**
     * @param  int  $count
     * @param  string|null  $hex
     * @return array
     */
    public function getTints(int $count = 20, string $hex = null): array
    {
        $percentage = 100 / $count;
        $factor = 0;

        while ($factor <= 100) {
            $colors[$factor] = self::tint($factor, $hex);
            $factor = $factor + $percentage;
        }

        return $colors;
    }

    /**
     * @param  int  $percentage
     * @param  string|null  $hex
     * @return string
     * @throws \Emotality\LaravelColor\LaravelColorException
     */
    public function shade(int $percentage = 10, string $hex = null): string
    {
        return $this->mix(($hex ?? $this->hex), '000000', $percentage);
    }

    /**
     * @param  int  $count
     * @param  string|null  $hex
     * @return array
     */
    public function getShades(int $count = 20, string $hex = null): array
    {
        $percentage = 100 / $count;
        $factor = 0;

        while ($factor <= 100) {
            $colors[$factor] = self::shade($factor, $hex);
            $factor = $factor + $percentage;
        }

        return $colors;
    }

    /**
     * Return all info about the parsed color in a JSON string format.
     *
     * @param  int  $flags json_decode() flags.
     * @return string
     */
    public function toJson(int $flags = 0): string
    {
        return json_encode($this->all(), $flags);
    }

    /**
     * Return all info about the parsed color in an array format.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return json_decode($this->toJson(), true);
    }

    /**
     * Return all info about the parsed color in an object format.
     *
     * @return object
     */
    public function toObject(): object
    {
        return json_decode($this->toJson(), false);
    }
}
