<?php

namespace App\Services;

use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;

class QrCodeService
{
    /**
     * Generate an SVG QR Code string for the given content.
     *
     * @param string $content
     * @param int $size
     * @param int $margin
     * @param bool $cleanXmlHeader
     * @return string Valid SVG markup
     */
    public function generateSvg(string $content, int $size = 200, int $margin = 1, bool $cleanXmlHeader = true): string
    {
        $renderer = new ImageRenderer(
            new RendererStyle($size, $margin),
            new SvgImageBackEnd()
        );

        $writer = new Writer($renderer);
        $svg = $writer->writeString($content);

        if ($cleanXmlHeader) {
            $svg = preg_replace('/<\?xml[^>]*\?>\s*/i', '', $svg);
        }

        return trim($svg);
    }

    /**
     * Generate a Base64 SVG data URI for embedding in src attribute.
     */
    public function generateDataUri(string $content, int $size = 200, int $margin = 1): string
    {
        $svg = $this->generateSvg($content, $size, $margin, false);

        return 'data:image/svg+xml;base64,' . base64_encode($svg);
    }
}
