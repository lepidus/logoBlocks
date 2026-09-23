<?php
/** Decode and re-encode uploads; never publish the original uploaded bytes. */
class SponsorsBlockImage {
    const MAX_BYTES = 5242880;
    const MAX_PIXELS = 12000000;

    static function resize($path) {
        if (!function_exists('imagecreatefromstring')) {
            throw new RuntimeException('plugins.block.sponsorsBlock.gdRequired');
        }
        $size = @getimagesize($path);
        if (!$size || !in_array($size[2], array(IMAGETYPE_PNG, IMAGETYPE_JPEG, IMAGETYPE_GIF), true)
            || filesize($path) > self::MAX_BYTES || $size[0] * $size[1] > self::MAX_PIXELS
            || $size[0] < 1 || $size[1] < 1) {
            throw new RuntimeException('plugins.block.sponsorsBlock.invalidImage');
        }
        $height = max(1, (int) round($size[1] * 150 / $size[0]));
        if ($height > 3000) {
            throw new RuntimeException('plugins.block.sponsorsBlock.invalidImage');
        }
        $source = @imagecreatefromstring(file_get_contents($path));
        if (!$source) {
            throw new RuntimeException('plugins.block.sponsorsBlock.invalidImage');
        }
        $target = imagecreatetruecolor(150, $height);
        imagealphablending($target, false);
        imagesavealpha($target, true);
        imagefill($target, 0, 0, imagecolorallocatealpha($target, 0, 0, 0, 127));
        imagecopyresampled($target, $source, 0, 0, 0, 0, 150, $height, $size[0], $size[1]);
        ob_start();
        imagepng($target);
        $contents = ob_get_clean();
        imagedestroy($source);
        imagedestroy($target);
        return $contents;
    }
}
