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
        imagefill($image, 0, 0, imagecolorallocatealpha($image, 0, 0, 0, 127));
        imagefilledrectangle($image, 100, 50, 500, 180, imagecolorallocate($image, 20, 80, 180));
        call_user_func('image' . $format, $image, $path);
        imagedestroy($image);
        clearstatcache(true, $path);
        $result = SponsorsBlockImage::resize($path);
        $size = getimagesizefromstring($result);
        check($size[0] === 150 && $size[1] === 60 && $size[2] === IMAGETYPE_PNG, $format . ': wrong dimensions or format');
        if ($format === 'png') {
            $decoded = imagecreatefromstring($result);
            check((imagecolorat($decoded, 0, 0) >> 24 & 127) === 127, 'Transparency was lost');
            imagedestroy($decoded);
        }
        echo "PASS: $format resized to 150 x 60 PNG\n";
    }
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
