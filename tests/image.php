<?php
/** Run: php plugins/blocks/sponsors/tests/image.php */
require_once dirname(__DIR__) . '/SponsorsBlockImage.inc.php';

function check($condition, $message) {
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

$path = tempnam(sys_get_temp_dir(), 'sponsors-test-');
try {
    foreach (array('png', 'jpeg', 'gif') as $format) {
        $image = imagecreatetruecolor(600, 240);
        imagealphablending($image, false);
        imagesavealpha($image, true);
        imagefill($image, 0, 0, imagecolorallocate($image, 255, 255, 255));
        imagefilledrectangle($image, 100, 50, 500, 180, imagecolorallocate($image, 20, 80, 180));
        call_user_func('image' . $format, $image, $path);
        imagedestroy($image);
        clearstatcache(true, $path);
        $result = SponsorsBlockImage::resize($path);
        $size = getimagesizefromstring($result);
        check($size[0] === 150 && $size[1] >= 49 && $size[1] <= 52 && $size[2] === IMAGETYPE_PNG, $format . ': wrong dimensions or format');
        echo "PASS: $format white margins cropped and resized to PNG\n";
    }
    foreach (array(array(0, 0, 0), array(180, 25, 40), array(15, 160, 60), array(30, 40, 180), array(140, 140, 140)) as $background) {
        foreach (array('png', 'jpeg', 'gif') as $format) {
            $image = imagecreatetruecolor(300, 240);
            imagefill($image, 0, 0, imagecolorallocate($image, $background[0], $background[1], $background[2]));
            // Separated symbol and white text, like a logo on a black canvas.
            imagefilledrectangle($image, 110, 60, 189, 99, imagecolorallocate($image, 30, 100, 210));
            imagefilledrectangle($image, 70, 120, 229, 179, imagecolorallocate($image, 255, 255, 255));
            call_user_func('image' . $format, $image, $path);
            imagedestroy($image);
            clearstatcache(true, $path);
            $result = SponsorsBlockImage::resize($path);
            $size = getimagesizefromstring($result);
            // JPEG ringing can extend content a few pixels; lossless formats have exact bounds.
            $heightMatches = $format === 'jpeg' ? ($size[1] >= 105 && $size[1] <= 125) : $size[1] === 113;
            check($size[0] === 150 && $heightMatches, $format . ': colored background not cropped: ' . json_encode($background) . ' => ' . $size[0] . 'x' . $size[1]);
            $decoded = imagecreatefromstring($result);
            $symbol = imagecolorsforindex($decoded, imagecolorat($decoded, 75, 20));
            $text = imagecolorsforindex($decoded, imagecolorat($decoded, 75, 90));
            check($symbol['blue'] > 180 && $symbol['red'] < 60, 'Symbol lost');
            check(min($text['red'], $text['green'], $text['blue']) > 240, 'White text lost');
            imagedestroy($decoded);
        }
    }
    echo "PASS: black, red, green, blue and gray backgrounds cropped in PNG/JPEG/GIF; symbol and white text retained\n";

    $image = imagecreatetruecolor(200, 100);
    imagefill($image, 0, 0, imagecolorallocate($image, 255, 255, 255));
    imagefilledrectangle($image, 0, 0, 99, 99, imagecolorallocate($image, 0, 0, 0));
    imagepng($image, $path);
    imagedestroy($image);
    clearstatcache(true, $path);
    $size = getimagesizefromstring(SponsorsBlockImage::resize($path));
    check($size[0] === 150 && $size[1] === 75, 'Ambiguous background cropped');
    echo "PASS: ambiguous background retains original bounds\n";

    foreach (array('png', 'gif') as $format) {
        $image = imagecreatetruecolor(200, 100);
        imagealphablending($image, false);
        imagesavealpha($image, true);
        $transparent = imagecolorallocatealpha($image, 0, 0, 0, 127);
        imagefill($image, 0, 0, $transparent);
        imagecolortransparent($image, $transparent);
        // Include white artwork extending beyond the colored part.
        imagefilledrectangle($image, 50, 30, 149, 69, imagecolorallocate($image, 255, 255, 255));
        imagefilledrectangle($image, 80, 40, 120, 60, imagecolorallocate($image, 20, 80, 180));
        call_user_func('image' . $format, $image, $path);
        imagedestroy($image);
        clearstatcache(true, $path);
        $result = SponsorsBlockImage::resize($path);
        $size = getimagesizefromstring($result);
        check($size[0] === 150 && $size[1] === 62, $format . ': transparent margins or white artwork cropped incorrectly');
        $decoded = imagecreatefromstring($result);
        check((imagecolorat($decoded, 0, 0) >> 24 & 127) === 127, 'Transparency was lost');
        imagedestroy($decoded);
    }
    echo "PASS: transparent PNG/GIF cropped, white artwork and transparency preserved\n";

    foreach (array(array(255, 255, 255), array(0, 0, 0), array(0, 0, 0, 127)) as $background) {
        $image = imagecreatetruecolor(200, 100);
        imagealphablending($image, false);
        imagesavealpha($image, true);
        imagefill($image, 0, 0, imagecolorallocatealpha($image, $background[0], $background[1], $background[2], isset($background[3]) ? $background[3] : 0));
        imagepng($image, $path);
        imagedestroy($image);
        clearstatcache(true, $path);
        $size = getimagesizefromstring(SponsorsBlockImage::resize($path));
        check($size[0] === 150 && $size[1] === 75, 'Blank or full-bleed image changed proportions');
    }
    echo "PASS: blank and full-bleed images retain proportions\n";

    $image = imagecreatetruecolor(30, 15);
    imagepng($image, $path);
    imagedestroy($image);
    clearstatcache(true, $path);
    $size = getimagesizefromstring(SponsorsBlockImage::resize($path));
    check($size[0] === 150 && $size[1] === 75, 'Small logo not normalized');
    echo "PASS: small image normalized proportionally\n";

    foreach (array('<svg xmlns="http://www.w3.org/2000/svg"></svg>', '<?php echo "unsafe";', str_repeat('x', SponsorsBlockImage::MAX_BYTES + 1)) as $contents) {
        file_put_contents($path, $contents);
        clearstatcache(true, $path);
        $rejected = false;
        try {
            SponsorsBlockImage::resize($path);
        } catch (RuntimeException $e) {
            $rejected = true;
        }
        check($rejected, 'Invalid input accepted');
    }
    echo "PASS: SVG, non-image and oversized input rejected\n";
    $image = imagecreatetruecolor(1, 100);
    imagepng($image, $path);
    imagedestroy($image);
    clearstatcache(true, $path);
    $rejected = false;
    try {
        SponsorsBlockImage::resize($path);
    } catch (RuntimeException $e) {
        $rejected = true;
    }
    check($rejected, 'Excessive output height accepted');
    echo "PASS: excessive output height rejected\n";
} finally {
    unlink($path);
}
