<?php

/** Decode and re-encode uploads; never publish the original uploaded bytes. */
class SponsorsBlockImage
{
    public const MAX_BYTES = 5242880;
    public const MAX_PIXELS = 12000000;
    public const BACKGROUND_TOLERANCE = 10;

    public static function resize($path, $crop = true)
    {
        if (!function_exists('imagecreatefromstring')) {
            throw new RuntimeException('plugins.block.sponsorsBlock.gdRequired');
        }
        $size = @getimagesize($path);
        if (!$size || !in_array($size[2], array(IMAGETYPE_PNG, IMAGETYPE_JPEG, IMAGETYPE_GIF), true)
            || filesize($path) > self::MAX_BYTES || $size[0] * $size[1] > self::MAX_PIXELS
            || $size[0] < 1 || $size[1] < 1) {
            throw new RuntimeException('plugins.block.sponsorsBlock.invalidImage');
        }
        $source = @imagecreatefromstring(file_get_contents($path));
        if (!$source) {
            throw new RuntimeException('plugins.block.sponsorsBlock.invalidImage');
        }
        list($left, $top, $width, $height) = $crop
            ? self::contentBounds($source)
            : array(0, 0, $size[0], $size[1]);
        $outputHeight = max(1, (int) round($height * 150 / $width));
        if ($outputHeight > 3000) {
            imagedestroy($source);
            throw new RuntimeException('plugins.block.sponsorsBlock.invalidImage');
        }
        $target = imagecreatetruecolor(150, $outputHeight);
        imagealphablending($target, false);
        imagesavealpha($target, true);
        imagefill($target, 0, 0, imagecolorallocatealpha($target, 0, 0, 0, 127));
        imagecopyresampled($target, $source, 0, 0, $left, $top, 150, $outputHeight, $width, $height);
        ob_start();
        imagepng($target);
        $contents = ob_get_clean();
        imagedestroy($source);
        imagedestroy($target);
        return $contents;
    }

    /** Trim only empty outer margins, keeping a one-pixel safety margin. */
    private static function contentBounds($image)
    {
        $width = imagesx($image);
        $height = imagesy($image);
        $background = self::backgroundColor($image);
        $left = $width;
        $top = $height;
        $right = $bottom = -1;
        $visibleLeft = $width;
        $visibleTop = $height;
        $visibleRight = $visibleBottom = -1;
        $hasTransparency = false;
        $trueColor = imageistruecolor($image);
        for ($y = 0; $y < $height; $y++) {
            for ($x = 0; $x < $width; $x++) {
                $pixel = imagecolorat($image, $x, $y);
                if ($trueColor) {
                    $alpha = ($pixel >> 24) & 127;
                    $red = ($pixel >> 16) & 255;
                    $green = ($pixel >> 8) & 255;
                    $blue = $pixel & 255;
                } else {
                    // GIF and indexed PNG pixels are palette indexes, not RGB values.
                    $color = imagecolorsforindex($image, $pixel);
                    $alpha = $color['alpha'];
                    $red = $color['red'];
                    $green = $color['green'];
                    $blue = $color['blue'];
                }
                $hasTransparency = $hasTransparency || $alpha > 0;
                if ($alpha === 127) {
                    continue;
                }
                $visibleLeft = min($visibleLeft, $x);
                $visibleTop = min($visibleTop, $y);
                $visibleRight = max($visibleRight, $x);
                $visibleBottom = max($visibleBottom, $y);
                // Ignore pixels close to the detected background, including JPEG noise.
                if ($background !== null
                    && abs($red - $background['red']) <= self::BACKGROUND_TOLERANCE
                    && abs($green - $background['green']) <= self::BACKGROUND_TOLERANCE
                    && abs($blue - $background['blue']) <= self::BACKGROUND_TOLERANCE) {
                    continue;
                }
                $left = min($left, $x);
                $top = min($top, $y);
                $right = max($right, $x);
                $bottom = max($bottom, $y);
            }
        }
        if ($hasTransparency) {
            $left = $visibleLeft;
            $top = $visibleTop;
            $right = $visibleRight;
            $bottom = $visibleBottom;
        }
        // Entirely blank images retain their dimensions.
        if ($right < 0) {
            return array(0, 0, $width, $height);
        }
        $left = max(0, $left - 1);
        $top = max(0, $top - 1);
        $right = min($width - 1, $right + 1);
        $bottom = min($height - 1, $bottom + 1);
        return array($left, $top, $right - $left + 1, $bottom - $top + 1);
    }

    /** Require agreement between at least three corners before trimming an opaque background. */
    private static function backgroundColor($image)
    {
        $right = imagesx($image) - 1;
        $bottom = imagesy($image) - 1;
        $corners = array();
        foreach (array(array(0, 0), array($right, 0), array(0, $bottom), array($right, $bottom)) as $point) {
            $corners[] = imagecolorsforindex($image, imagecolorat($image, $point[0], $point[1]));
        }
        foreach ($corners as $candidate) {
            if ($candidate['alpha'] !== 0) {
                continue;
            }
            $matches = 0;
            foreach ($corners as $color) {
                if ($color['alpha'] === 0
                    && abs($color['red'] - $candidate['red']) <= self::BACKGROUND_TOLERANCE
                    && abs($color['green'] - $candidate['green']) <= self::BACKGROUND_TOLERANCE
                    && abs($color['blue'] - $candidate['blue']) <= self::BACKGROUND_TOLERANCE) {
                    $matches++;
                }
            }
            if ($matches >= 3) {
                return $candidate;
            }
        }
        // Ambiguous corners may be artwork or a nonuniform background: retain the image.
        return null;
    }

}
