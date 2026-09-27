<?php
namespace App\Services;

use Picqer\Barcode\BarcodeGeneratorSVG;

/**
 * Wraps picqer/php-barcode-generator to render a CODE-128 barcode as inline SVG,
 * usable directly inside a Blade view (product labels, PDF invoices).
 */
class BarcodeService
{
    public static function svg(string $code, int $widthFactor = 2, int $height = 40): string
    {
        $generator = new BarcodeGeneratorSVG();

        return $generator->getBarcode($code, $generator::TYPE_CODE_128, $widthFactor, $height);
    }
}
