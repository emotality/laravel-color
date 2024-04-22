<?php

namespace Emotality\LaravelColor\Interfaces;

interface ColorFunctions
{
    /**
     * Get the hex color code with hashtag.
     *
     * @return string
     */
    public function hex(): string;

    /**
     * Get the hex color code with hashtag.
     *
     * @return string
     */
    public function hex8(): string;

    /**
     * Get red, green and blue from parsed color.
     *
     * @param  string|null  $hex The hex color code to parse, with or without hashtag.
     * @return object
     */
    public function rgb(string $hex = null): object;

    /**
     * Get hex code from red, green and blue.
     *
     * @param  int  $red
     * @param  int  $green
     * @param  int  $blue
     * @return string
     */
    public function rgbToHex(int $red, int $green, int $blue): string;

    /**
     * Get red, green, blue and alpha from parsed color.
     *
     * @param  string|null  $hex The hex color code to parse, with or without hashtag.
     * @return object
     */
    public function rgba(string $hex = null): object;

    /**
     * Get hex8 code from red, green, blue and alpha.
     *
     * @param  int  $red
     * @param  int  $green
     * @param  int  $blue
     * @param  float  $alpha
     * @return string
     */
    public function rgbaToHex8(int $red, int $green, int $blue, float $alpha = 1): string;

    /**
     * Get hue, saturation and lightness from parsed color.
     *
     * @param  string|null  $hex The hex color code to parse, with or without hashtag.
     * @return object
     * @url https://www.had2know.org/technology/hsl-rgb-color-converter.html
     * @url https://www.rapidtables.com/convert/color/rgb-to-hsl.html
     * @url https://bgrins.github.io/TinyColor/
     */
    public function hsl(string $hex = null): object;

    /**
     * Get hue, saturation and value from parsed color.
     *
     * @param  string|null  $hex The hex color code to parse, with or without hashtag.
     * @return object
     * @url https://www.had2know.org/technology/hsv-rgb-conversion-formula-calculator.html
     * @url https://www.rapidtables.com/convert/color/rgb-to-hsv.html
     * @url https://bgrins.github.io/TinyColor/
     */
    public function hsv(string $hex = null): object;

    /**
     * Get the hue from parsed color in a value out of 360.
     *
     * @param  string|null  $hex The hex color code to parse, with or without hashtag.
     * @return int
     */
    public function hue(string $hex = null): int;

    /**
     * Get the value from parsed color in a percentage value.
     *
     * @param  string|null  $hex The hex color code to parse, with or without hashtag.
     * @return int
     */
    public function value(string $hex = null): int;

    /**
     * Color relative luminance from parsed color in a percentage value.
     *
     * @param  string|null  $hex The hex color code to parse, with or without hashtag.
     * @return float
     */
    public function luminance(string $hex = null): float;

    /**
     * Get the lightness from parsed color in a percentage value.
     *
     * @param  string|null  $hex The hex color code to parse, with or without hashtag.
     * @return int
     */
    public function lightness(string $hex = null): int;

    /**
     * Get the brightness from parsed color in a percentage value.
     *
     * @param  string|null  $hex The hex color code to parse, with or without hashtag.
     * @return int
     */
    public function brightness(string $hex = null): int;

    /**
     * If the parsed color is darker than specified contrast percentage.
     *
     * @param  string|null  $hex The hex color code to parse, with or without hashtag.
     * @param  int|null  $contrast The percentage of contrast to measure against, default is 50.
     * @return bool
     */
    public function isDark(string $hex = null, int $contrast = null): bool;

    /**
     * If the parsed color is lighter than specified contrast percentage.
     *
     * @param  string|null  $hex The hex color code to parse, with or without hashtag.
     * @param  int|null  $contrast The percentage of contrast to measure against, default is 50.
     * @return bool
     */
    public function isLight(string $hex = null, int $contrast = null): bool;

    /**
     * Foreground font color if parsed color is the background.
     *
     * @param  string|null  $hex The hex color code to parse, with or without hashtag.
     * @param  int|null  $contrast The percentage of contrast to measure against, default is 50.
     * @return string
     */
    public function fontColor(string $hex = null, int $contrast = null): string;

    /**
     * @param  string  $color1_hex
     * @param  string  $color2_hex
     * @param  int  $percentage
     * @return string
     * @throws \Emotality\LaravelColor\LaravelColorException
     */
    public function mix(string $color1_hex, string $color2_hex, int $percentage = 50): string;

    /**
     * @param  int  $percentage
     * @param  string|null  $hex
     * @return string
     */
    public function tint(int $percentage = 10, string $hex = null): string;

    /**
     * @param  int  $count
     * @param  string|null  $hex
     * @return array
     */
    public function getTints(int $count = 20, string $hex = null): array;

    /**
     * @param  int  $percentage
     * @param  string|null  $hex
     * @return string
     */
    public function shade(int $percentage = 10, string $hex = null): string;

    /**
     * @param  int  $count
     * @param  string|null  $hex
     * @return array
     */
    public function getShades(int $count = 20, string $hex = null): array;

    /**
     * Return all info about the parsed color in a JSON string format.
     *
     * @param  int  $flags json_decode() flags.
     * @return string
     */
    public function toJson(int $flags = 0): string;

    /**
     * Return all info about the parsed color in an array format.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array;

    /**
     * Return all info about the parsed color in an object format.
     *
     * @return object
     */
    public function toObject(): object;
}