<?php

namespace Emotality\LaravelColor;

class LaravelColor
{
    /** @var string Key for bright percentage. */
    public const brightPercentage = 'bright_percentage';

    /** @var string Key for light font color. */
    public const lightColorFont = 'font_light';

    /** @var string Key for dark font color. */
    public const darkColorFont = 'font_dark';

    /**
     * The color in hex string format.
     *
     * @var string
     */
    protected $hex = '000000';

    /**
     * The color in hex decimal format.
     *
     * @var int
     */
    protected $hexdec = 0;

    /**
     * Original RGB values.
     *
     * @var object
     */
    protected $rgb;

    /**
     * The color's hex value for Red.
     *
     * @var float
     */
    protected $red = 0;

    /**
     * The color's hex value for Green.
     *
     * @var float
     */
    protected $green = 0;

    /**
     * The color's hex value for Blue.
     *
     * @var float
     */
    protected $blue = 0;

    /**
     * The lowest (darkest) color component's value.
     *
     * @var float
     */
    protected $min = 0;

    /**
     * The highest (lightest) color component's value.
     *
     * @var float
     */
    protected $max = 0;

    /**
     * @var float
     */
    private $d = 0;

    /**
     * The color components.
     *
     * @var array<string, int>
     */
    protected $components = [
        'red'   => 0,
        'green' => 0,
        'blue'  => 0,
    ];

    /**
     * The options.
     *
     * @var array<string, mixed>
     */
    protected $options = [
        self::brightPercentage => 60,
        self::darkColorFont    => '#ffffff',
        self::lightColorFont   => '#000000',
    ];

    /**
     * LaravelColor constructor.
     *
     * @param  string|null  $hex The hex color code to parse, with or without hashtag.
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
     * @param  string  $hex The hex color code to parse, with or without hashtag.
     * @param  array<string, mixed>|null  $options
     * @return $this
     */
    public function parse(string $hex, array $options = null): self
    {
        if ($options) {
            $this->set($options);
        }

        $this->hex = $hex = strtolower(trim($hex, '#'));

        if (strlen($hex) === 3) {
            $this->hex = $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
        }

        if (! preg_match('/^[A-Fa-f0-9]{6}$/', $hex)) {
            throw new LaravelColorException(sprintf('Invalid hex color! [#%s]', $hex));
        }

        $r = $this->rgb->r = hexdec($hex[0].$hex[1]);
        $g = $this->rgb->g = hexdec($hex[2].$hex[3]);
        $b = $this->rgb->b = hexdec($hex[4].$hex[5]);

        $this->red = $r / 255;
        $this->green = $g / 255;
        $this->blue = $b / 255;

        $this->hexdec = hexdec($hex);
        $this->min = min($this->red, $this->green, $this->blue);
        $this->max = max($this->red, $this->green, $this->blue);
        $this->d = $this->max - $this->min;

        $this->components = [
            'red'   => $this->red,
            'green' => $this->green,
            'blue'  => $this->blue,
        ];

        return $this;
    }

    /**
     * Set/modify parsing options.
     *
     * @param  array<string, mixed>  $options The options you want to set.
     * @return $this
     */
    public function set(array $options): self
    {
        foreach ($options as $key => $value) {
            if (isset($this->options[$key]) && ! empty($value)) {
                $this->options[$key] = $value;
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
            'red'   => $this->rgb->r,
            'green' => $this->rgb->g,
            'blue'  => $this->rgb->b,
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
        $s = ($this->max == $this->min) ? 0 : $this->d / (1 - abs(2 * $l - 1));

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

        $s = $this->max === 0 ? 0 : $this->d / $this->max;

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
            $d = $this->max - $this->min;

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

        foreach ($this->components as $color => $value) {
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
     * @param  int|null  $brightness The percentage of brightness to measure against, default is 60.
     * @return bool
     */
    public function isDark(string $hex = null, int $brightness = null): bool
    {
        return $this->brightness($hex) < ($brightness ?? $this->options[self::brightPercentage]);
    }

    /**
     * If the parsed color is lighter than specified brightness percentage.
     *
     * @param  string|null  $hex The hex color code to parse, with or without hashtag.
     * @param  int|null  $brightness The percentage of brightness to measure against, default is 60.
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
     * @param  int|null  $brightness The percentage of brightness to measure against, default is 60.
     * @return string
     */
    public function fontColor(string $hex = null, int $brightness = null): string
    {
        $key = $this->isDark($hex, $brightness) ? self::darkColorFont : self::lightColorFont;

        return '#'.ltrim($this->options[$key], '#');
    }

    /**
     * Return all info about the parsed color in an array format.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'hex'        => $this->hex(),
            'hexdec'     => $this->hexdec,
            'red'        => $this->red,
            'green'      => $this->green,
            'blue'       => $this->blue,
            'rgb'        => $this->rgb(),
            'hsl'        => $this->hsl(),
            'hsv'        => $this->hsv(),
            'hue'        => $this->hue(),
            'value'      => $this->value(),
            'luminance'  => $this->luminance(),
            'lightness'  => $this->lightness(),
            'brightness' => $this->brightness(),
            'dark'       => $this->isDark(),
            'light'      => $this->isLight(),
            'font_color' => $this->fontColor(),
        ];
    }

    /**
     * Return all info about the parsed color in an object format.
     *
     * @return object
     */
    public function toObject(): object
    {
        return (object) $this->toArray();
    }

    /**
     * Return all info about the parsed color in a JSON string format.
     *
     * @param  int  $flags json_decode() flags.
     * @return string
     */
    public function toJson(int $flags = 0): string
    {
        return json_encode($this->toArray(), $flags);
    }

    /**
     * Return all info about the parsed color in a string format.
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->toJson();
    }
}
