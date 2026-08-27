<?php

namespace App\Libraries;

use App\Models\ProductPhotoModel;
use CodeIgniter\HTTP\Files\UploadedFile;

/**
 * Stores product photos on disk, one directory per product code.
 *
 * SECURITY: uploads land inside the web root, so nothing here trusts the
 * client. The filename is generated (never taken from the upload), the
 * extension comes from the detected image type rather than the submitted name,
 * and the bytes must actually decode as an image before they are kept.
 * public/uploads/.htaccess additionally refuses to execute anything in the tree.
 */
class ProductPhotoStore
{
    public const MAX_PHOTOS    = 6;
    public const MAX_BYTES     = 5 * 1024 * 1024; // 5 MB
    public const MAX_DIMENSION = 8000;            // px per side

    /** Detected image type => extension. Anything else is rejected. */
    private const ALLOWED = [
        IMAGETYPE_JPEG => 'jpg',
        IMAGETYPE_PNG  => 'png',
        IMAGETYPE_WEBP => 'webp',
        IMAGETYPE_GIF  => 'gif',
    ];

    private string $error = '';
    private bool $replaced = false;

    public function error(): string
    {
        return $this->error;
    }

    /** True when the last upload overwrote the final slot instead of adding one. */
    public function replacedLast(): bool
    {
        return $this->replaced;
    }

    public static function dirFor(string $code): string
    {
        return FCPATH . 'uploads/products/' . $code;
    }

    public static function urlFor(string $code, string $filename): string
    {
        return base_url('uploads/products/' . $code . '/' . $filename);
    }

    /**
     * Add a photo to a product.
     *
     * At MAX_PHOTOS the newest upload replaces the last photo in the list —
     * the older ones are left alone.
     *
     * @return bool True when stored.
     */
    public function add(array $product, UploadedFile $file): bool
    {
        $this->error    = '';
        $this->replaced = false;

        if (!$file->isValid()) {
            $this->error = $file->getErrorString();

            return false;
        }
        if ($file->getSize() > self::MAX_BYTES) {
            $this->error = 'Image must be 5 MB or smaller.';

            return false;
        }

        // getimagesize() only reads the header — it will happily report a
        // 28-byte file starting with "GIF89a" as a huge GIF. Treat it as a
        // first filter, then prove the pixels really decode below.
        $info = @getimagesize($file->getTempName());
        if ($info === false || !isset(self::ALLOWED[$info[2]])) {
            $this->error = 'That file is not a JPG, PNG, WebP or GIF image.';

            return false;
        }

        [$width, $height] = $info;
        if ($width < 1 || $height < 1 || $width > self::MAX_DIMENSION || $height > self::MAX_DIMENSION) {
            $this->error = 'Image dimensions are out of range (max '
                . self::MAX_DIMENSION . 'px per side).';

            return false;
        }

        $ext = self::ALLOWED[$info[2]];

        $code = $product['code'] ?? '';
        if (!preg_match('/^[A-Z0-9]{6}$/', $code)) {
            $this->error = 'That product has no valid product code.';

            return false;
        }

        $dir = self::dirFor($code);
        if (!is_dir($dir) && !mkdir($dir, 0755, true) && !is_dir($dir)) {
            $this->error = 'Could not create the photo folder.';

            return false;
        }

        $model  = new ProductPhotoModel();
        $photos = $model->forProduct((int) $product['id']);

        // Generated name — the submitted filename never touches the disk.
        $name = bin2hex(random_bytes(8)) . '.' . $ext;
        $dest = $dir . DIRECTORY_SEPARATOR . $name;

        // Re-encode rather than copy. Anything smuggled alongside the pixels —
        // PHP in a polyglot, EXIF payloads — does not survive being decoded and
        // written out again, so what lands on disk is only image data.
        if (!$this->reencode($file->getTempName(), $dest, $info[2])) {
            $this->error = 'That image could not be read. Try re-saving it and uploading again.';

            return false;
        }

        if (count($photos) >= self::MAX_PHOTOS) {
            // Full: overwrite the last slot, keeping its position. The origin
            // marking is reset too — it described the previous image.
            $last = $photos[count($photos) - 1];
            $this->unlink($dir . DIRECTORY_SEPARATOR . $last['filename']);
            $model->update($last['id'], [
                'filename'   => $name,
                'is_ai'      => 0,
                'created_at' => date('Y-m-d H:i:s'),
            ]);
            $this->replaced = true;

            return true;
        }

        $model->insert([
            'product_id' => (int) $product['id'],
            'filename'   => $name,
            'sort_order' => $photos === [] ? 0 : ((int) $photos[count($photos) - 1]['sort_order'] + 1),
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        return true;
    }

    public function delete(array $product, int $photoId): bool
    {
        $model = new ProductPhotoModel();
        $photo = $model->where('id', $photoId)
            ->where('product_id', (int) $product['id'])
            ->first();

        if (!$photo) {
            $this->error = 'That photo does not belong to this product.';

            return false;
        }

        $this->unlink(self::dirFor($product['code']) . DIRECTORY_SEPARATOR . $photo['filename']);
        $model->delete($photoId);

        return true;
    }

    /**
     * Decode the upload and write it out again in the same format.
     *
     * This is the step that makes a polyglot harmless: only decoded pixels are
     * written, so bytes that were never part of the image are dropped.
     */
    private function reencode(string $src, string $dest, int $type): bool
    {
        $img = match ($type) {
            IMAGETYPE_JPEG => @imagecreatefromjpeg($src),
            IMAGETYPE_PNG  => @imagecreatefrompng($src),
            IMAGETYPE_WEBP => @imagecreatefromwebp($src),
            IMAGETYPE_GIF  => @imagecreatefromgif($src),
            default        => false,
        };

        if (!$img) {
            return false;
        }

        // Keep transparency intact for the formats that support it.
        if ($type === IMAGETYPE_PNG || $type === IMAGETYPE_WEBP || $type === IMAGETYPE_GIF) {
            imagealphablending($img, false);
            imagesavealpha($img, true);
        }

        $ok = match ($type) {
            IMAGETYPE_JPEG => imagejpeg($img, $dest, 88),
            IMAGETYPE_PNG  => imagepng($img, $dest, 6),
            IMAGETYPE_WEBP => imagewebp($img, $dest, 88),
            IMAGETYPE_GIF  => imagegif($img, $dest),
            default        => false,
        };

        imagedestroy($img);

        return (bool) $ok;
    }

    /** Removes a file, guarding against anything outside the product folder. */
    private function unlink(string $path): void
    {
        $real = realpath($path);
        $root = realpath(FCPATH . 'uploads/products');

        if ($real !== false && $root !== false && str_starts_with($real, $root) && is_file($real)) {
            @unlink($real);
        }
    }
}
