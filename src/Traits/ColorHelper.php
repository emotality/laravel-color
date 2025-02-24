<?php

namespace Emotality\LaravelColor\Traits;

use Emotality\LaravelColor\Color;
use Emotality\LaravelColor\LaravelColorException;

trait ColorHelper
{
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
     * The color in hex8 string format.
     *
     * @var string
     */
    protected $hex8 = '000000ff';

    /**
     * The color in hex8 decimal format.
     *
     * @var int
     */
    protected $hex8dec = 0;

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
     * The color's hex value for Alpha.
     *
     * @var float
     */
    protected $alpha = 1;

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
     * The difference between the highest and lowest values.
     *
     * @var float
     */
    protected $diff = 0;

    /**
     * Original RGBA values.
     *
     * @var object
     */
    protected $rgba;

    /**
     * Default options.
     *
     * @var array<string, string|int>
     */
    protected static $default_options = [
        Color::BRIGHT_PERC      => 50,
        Color::DARK_FONT_COLOR  => '#ffffff',
        Color::LIGHT_FONT_COLOR => '#000000',
        Color::OUTPUT           => Color::OUTPUT_HEX,
        Color::HEX_CASING       => Color::HEX_LOWER,
    ];

    /**
     * Configured options.
     *
     * @var array<string, mixed>
     */
    protected static $options = [];

    /**
     * Supported output formats.
     *
     * @var array<int, string>
     */
    protected static $supported_outputs = [
        Color::OUTPUT_HEX, // Default
        Color::OUTPUT_HEX8,
        Color::OUTPUT_RGB,
        Color::OUTPUT_RGBA,
    ];

    /**
     * Supported hex casings
     *
     * @var array<int, string>
     */
    protected static $supported_hex_casings = [
        Color::HEX_LOWER, // Default
        Color::HEX_UPPER,
    ];

    private static function setDefaultOptions(): void
    {
        self::$options = self::$default_options;
    }

    /**
     * @return $this
     * @throws \Emotality\LaravelColor\LaravelColorException
     */
    private function setColorProps(string $hex): self
    {
        $rgba = self::getRGBA($hex);

        $this->hex = $rgba->hex6;
        $this->hex8 = $rgba->hex8;
        $this->hexdec = hexdec($this->hex);
        $this->hex8dec = hexdec($this->hex8);

        $this->rgba->r = $rgba->r;
        $this->rgba->g = $rgba->g;
        $this->rgba->b = $rgba->b;
        $this->rgba->a = $this->alpha = round($rgba->a, 3);

        $this->red = round($this->rgba->r / 255, 5);
        $this->green = round($this->rgba->g / 255, 5);
        $this->blue = round($this->rgba->b / 255, 5);

        $this->min = min($this->red, $this->green, $this->blue);
        $this->max = max($this->red, $this->green, $this->blue);
        $this->diff = $this->max - $this->min;

        return $this;
    }

    /**
     * @throws \Emotality\LaravelColor\LaravelColorException
     */
    private static function getRGBA(string $hex): object
    {
        $hex = self::cleanHex($hex);

        $hex6 = $hex->hex6;
        $hex8 = $hex->hex8;

        $r = hexdec($hex8[0].$hex8[1]);
        $g = hexdec($hex8[2].$hex8[3]);
        $b = hexdec($hex8[4].$hex8[5]);
        $a = hexdec($hex8[6].$hex8[7]) / 255;

        return (object) compact('hex6', 'hex8', 'r', 'g', 'b', 'a');
    }

    private static function setHexCasing(string $hex): string
    {
        return (self::$options[Color::HEX_CASING] === Color::HEX_UPPER)
            ? strtoupper($hex)
            : strtolower($hex);
    }

    /**
     * @throws \Emotality\LaravelColor\LaravelColorException
     */
    private static function cleanHex(string $hex): object
    {
        $hex = trim($hex, '#');

        if (preg_match('/^[A-Fa-f0-9]{6}$/', $hex)) {
            $hex6 = $hex;
            $hex8 = $hex6.'ff';
        } elseif (preg_match('/^[A-Fa-f0-9]{3}$/', $hex)) {
            $hex6 = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
            $hex8 = $hex6.'ff';
        } elseif (preg_match('/^[A-Fa-f0-9]{8}$/', $hex)) {
            $hex6 = substr($hex, 0, 6);
            $hex8 = $hex;
        } elseif (preg_match('/^[A-Fa-f0-9]{4}$/', $hex)) {
            $hex6 = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
            $hex8 = $hex6.$hex[3].$hex[3];
        } else {
            throw new LaravelColorException(sprintf('Invalid hex color! [#%s]', $hex));
        }

        $hex6 = self::setHexCasing($hex6);
        $hex8 = self::setHexCasing($hex8);

        return (object) compact('hex6', 'hex8');
    }

    private function output(?string $hex = null, ?string $output = null): string
    {
        $selected_output = $output ?? self::$options[Color::OUTPUT];

        if (! in_array($selected_output, self::$supported_outputs)) {
            throw new LaravelColorException(sprintf('Unsupported output selected! [%s]', $selected_output));
        }

        $rgba = self::getRGBA($hex ?? $this->hex);

        switch ($selected_output) {
            default:
            case Color::OUTPUT_HEX:
                return '#'.$rgba->hex6;
            case Color::OUTPUT_HEX8:
                return '#'.$rgba->hex8;
            case Color::OUTPUT_RGB:
                return sprintf('rgb(%d, %d, %d)', $rgba->r, $rgba->g, $rgba->b);
            case Color::OUTPUT_RGBA:
                return sprintf('rgba(%d, %d, %d, %.3f)', $rgba->r, $rgba->g, $rgba->b, $rgba->a);
        }
    }

    /**
     * Return all info about the parsed color.
     *
     * @return array<string, mixed>
     */
    private function all(): array
    {
        return [
            'hex'        => $this->hex(),
            'hexdec'     => $this->hexdec,
            'hex8'       => $this->hex8(),
            'hex8dec'    => $this->hex8dec,
            'red'        => $this->red,
            'green'      => $this->green,
            'blue'       => $this->blue,
            'alpha'      => $this->alpha,
            'rgb'        => $this->rgb(),
            'rgba'       => $this->rgba(),
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
            'shades'     => $this->getShades(),
            'tints'      => $this->getTints(),
        ];
    }
}
