<?php

namespace Emotality\LaravelColor;

class LaravelColor
{
    /**
     * The color in hex string format.
     *
     * @var string
     */
    public $hex = '000000';

    /**
     * The color's hex value for Red.
     *
     * @var int
     */
    protected $red = 0;

    /**
     * The color's hex value for Green.
     *
     * @var int
     */
    protected $green = 0;

    /**
     * The color's hex value for Blue.
     *
     * @var int
     */
    protected $blue = 0;

    /**
     * The lowest (darkest) color component's value.
     *
     * @var int
     */
    protected $min = 0;

    /**
     * The highest (lightest) color component's value.
     *
     * @var int
     */
    protected $max = 0;

    /**
     * Original RGB values.
     *
     * @var object
     */
    protected $rgb;

    /**
     * The color components.
     *
     * @var array<string, int>
     */
    protected $components = [];

    /** @var int Percentage to determine if a color is dark. */
    const BRIGHTNESS_PERCENT = 50;

    /** @var string Light font color. */
    const FONT_LIGHT = '#ffffff';

    /** @var string Dark font color. */
    const FONT_DARK = '#000000';

    /**
     * LaravelColor constructor.
     *
     * @param  string|null  $hex
     * @return void
     */
    public function __construct(string $hex = null)
    {
        $this->rgb = (object) [];

        if ($hex) {
            $this->parse($hex);
        }
    }

    /**
     * @param  string  $hex
     * @return $this
     */
    public function parse(string $hex) : self
    {
        $this->hex = $hex = trim($hex, '#');

        if (strlen($hex) == 3) {
            $this->hex = $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
        }

        $r = $this->rgb->r = hexdec($hex[0].$hex[1]);
        $g = $this->rgb->g = hexdec($hex[2].$hex[3]);
        $b = $this->rgb->b = hexdec($hex[4].$hex[5]);

        $this->red = $r / 255;
        $this->green = $g / 255;
        $this->blue = $b / 255;

        $this->min = min($this->red, $this->green, $this->blue);
        $this->max = max($this->red, $this->green, $this->blue);

        $this->components = [
            'red'   => $this->red,
            'green' => $this->green,
            'blue'  => $this->blue,
        ];

        return $this;
    }

    /**
     * Red. Green. Blue.
     *
     * @param  string|null  $hex
     * @return object
     */
    public function rgb(string $hex = null) : object
    {
        if ($hex) {
            $this->parse($hex);
        }

        return (object) [
            'red'   => $this->rgb->r,
            'green' => $this->rgb->g,
            'blue'  => $this->rgb->b,
        ];
    }

    /**
     * Hue. Saturation. Lightness.
     *
     * @param  string|null  $hex
     * @return object
     * @url https://www.had2know.org/technology/hsl-rgb-color-converter.html
     */
    public function hsl(string $hex = null) : object
    {
        if ($hex) {
            $this->parse($hex);
        }

        $h = $l = ($this->max + $this->min) / 2;
        $d = $this->max - $this->min;

        if ($this->max == $this->min) {
            $h = $s = 0;
        } else {
            $s = $d / (1 - abs(2 * $l - 1));

            switch ($this->max) {
                case $this->red:
                    $h = ($this->green - $this->blue) / $d + ($this->green < $this->blue ? 6 : 0);
                    break;
                case $this->green:
                    $h = ($this->blue - $this->red) / $d + 2;
                    break;
                case $this->blue:
                    $h = ($this->red - $this->green) / $d + 4;
                    break;
            }

            $h /= 6;
        }

        return (object) [
            'hue'        => intval(round($h * 360)),
            'saturation' => intval(round($s * 100)),
            'lightness'  => intval(round($l * 100)),
        ];
    }

    /**
     * Hue. Saturation. Value.
     *
     * @param  string|null  $hex
     * @return object
     * @url https://www.had2know.org/technology/hsv-rgb-conversion-formula-calculator.html
     */
    public function hsv(string $hex = null) : object
    {
        if ($hex) {
            $this->parse($hex);
        }

        $h = $v = $this->max;
        $d = $this->max - $this->min;
        $s = $this->max === 0 ? 0 : $d / $this->max;

        if ($this->max == $this->min) {
            $h = 0;
        } else {
            switch ($this->max) {
                case $this->red:
                    $h = ($this->green - $this->blue) / $d + ($this->green < $this->blue ? 6 : 0);
                    break;
                case $this->green:
                    $h = ($this->blue - $this->red) / $d + 2;
                    break;
                case $this->blue:
                    $h = ($this->red - $this->green) / $d + 4;
                    break;
            }

            $h /= 6;
        }

        return (object) [
            'hue'        => intval(round($h * 360)),
            'saturation' => intval(round($s * 100)),
            'value'      => intval(round($v * 100)),
        ];
    }

    /**
     * Color relative luminance.
     *
     * @param  string|null  $hex
     * @return float
     */
    public function luminance(string $hex = null) : float
    {
        if ($hex) {
            $this->parse($hex);
        }

        foreach ($this->components as $color => $value) {
            if ($value <= 0.04045) {
                $components[$color] = $value / 12.92;
            } else {
                $components[$color] = pow((($value + 0.055) / 1.055), 2.4);
            }
        }

        // Calculate relative luminance using ITU-R BT. 709 coefficients
        $luminance_perc = (($components['red'] * 0.2126) + ($components['green'] * 0.7152) + ($components['blue'] * 0.0722)) * 100;

        return round($luminance_perc, 2);
    }

    /**
     * @param  string|null  $hex
     * @return int
     */
    public function lightness(string $hex = null) : int
    {
        if ($hex) {
            $this->parse($hex);
        }

        return intval(round(($this->min + $this->max) * 100 / 2));
    }

    /**
     * @param  string|null  $hex
     * @return int
     */
    public function brightness(string $hex = null) : int
    {
        if ($hex) {
            $this->parse($hex);
        }

        return intval(round($this->red * 299 + $this->green * 587 + $this->blue * 114) / 10);
    }

    /**
     * If the specified color is dark.
     *
     * @param  string|null  $hex
     * @return bool
     */
    public function isDark(string $hex = null) : bool
    {
        return $this->brightness($hex) < self::BRIGHTNESS_PERCENT;
    }

    /**
     * If the specified color is light.
     *
     * @param  string|null  $hex
     * @return bool
     */
    public function isLight(string $hex = null) : bool
    {
        return ! $this->isDark($hex);
    }

    /**
     * Foreground font color if specified
     * color is the background.
     *
     * @param  string|null  $hex
     * @return string
     */
    public function fontColor(string $hex = null) : string
    {
        return $this->isDark($hex) ? self::FONT_LIGHT : self::FONT_DARK;
    }

    /**
     * @return array
     */
    public function toArray() : array
    {
        return [
            'hex'        => '#'.$this->hex,
            'red'        => $this->red,
            'green'      => $this->green,
            'blue'       => $this->blue,
            'min'        => $this->min,
            'max'        => $this->max,
            'luminance'  => $this->luminance(),
            'lightness'  => $this->lightness(),
            'brightness' => $this->brightness(),
            'dark'       => $this->isDark(),
            'light'      => $this->isLight(),
            'rgb'        => $this->rgb(),
            'hsl'        => $this->hsl(),
            'hsv'        => $this->hsv(),
        ];
    }

    /**
     * @return string
     */
    public function __toString() : string
    {
        return json_encode($this->toArray(), JSON_PRETTY_PRINT);
    }
}
