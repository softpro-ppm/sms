<?php

namespace App\Services;

class SignatureImageService
{
    /**
     * Get signature path with transparent background. Processes the source
     * to remove light/grey background if needed.
     */
    public static function getTransparentSignaturePath(): ?string
    {
        $sourcePath = public_path('images/signature.png');
        $cachePath = storage_path('app/signature-transparent.png');

        if (!file_exists($sourcePath)) {
            return null;
        }

        try {
            // Reprocess if source is newer than cache
            if (!file_exists($cachePath) || filemtime($sourcePath) > filemtime($cachePath)) {
                if (!self::makeBackgroundTransparent($sourcePath, $cachePath)) {
                    return $sourcePath;
                }
            }
        } catch (\Throwable $e) {
            return $sourcePath;
        }

        return file_exists($cachePath) ? $cachePath : $sourcePath;
    }

    /**
     * Make light/white/grey pixels transparent using GD
     */
    protected static function makeBackgroundTransparent(string $sourcePath, string $destPath): bool
    {
        $img = @imagecreatefrompng($sourcePath);
        if (!$img) {
            $img = @imagecreatefromjpeg($sourcePath);
        }
        if (!$img) {
            return false;
        }

        imagealphablending($img, false);
        imagesavealpha($img, true);

        $width = imagesx($img);
        $height = imagesy($img);

        // Threshold: pixels with R,G,B all above this become transparent (removes grey/white)
        $threshold = 220;

        for ($x = 0; $x < $width; $x++) {
            for ($y = 0; $y < $height; $y++) {
                $rgb = imagecolorat($img, $x, $y);
                $r = ($rgb >> 16) & 0xFF;
                $g = ($rgb >> 8) & 0xFF;
                $b = $rgb & 0xFF;

                if ($r >= $threshold && $g >= $threshold && $b >= $threshold) {
                    $transparent = imagecolorallocatealpha($img, 255, 255, 255, 127);
                    imagesetpixel($img, $x, $y, $transparent);
                }
            }
        }

        $result = imagepng($img, $destPath);
        imagedestroy($img);

        return $result;
    }
}
