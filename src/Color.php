<?php

namespace Emotality\LaravelColor;

use Illuminate\Support\Facades\Facade;

class Color extends Facade
{
    /** @var string Options key for bright percentage. */
    const BRIGHT_PERC = 'bright_percentage';

    /** @var string Options key for light font color. */
    const LIGHT_FONT_COLOR = 'font_light';

    /** @var string Options key for dark font color. */
    const DARK_FONT_COLOR = 'font_dark';

    /** @var string Options key for the color output format. */
    const OUTPUT = 'output';

    /** @var string Output key to output color in RGB format. */
    const OUTPUT_RGB = 'rgb';

    /** @var string Output key to output color in RGBA format. */
    const OUTPUT_RGBA = 'rgba';

    /** @var string Output key to output color in HEX format. */
    const OUTPUT_HEX = 'hex';

    /** @var string Output key to output color in HEX8 format. */
    const OUTPUT_HEX8 = 'hex8';

    /** @var string Options key for the hex output casing. */
    const HEX_CASING = 'hex_casing';

    /** @var string Hex key to output hex in lowercase. */
    const HEX_LOWER = 'hex_lowercase';

    /** @var string Hex key to output hex in uppercase. */
    const HEX_UPPER = 'hex_uppercase';

    /**
     * {@inheritDoc}
     */
    protected static function getFacadeAccessor()
    {
        return \Emotality\LaravelColor\LaravelColor::class;
    }
}
