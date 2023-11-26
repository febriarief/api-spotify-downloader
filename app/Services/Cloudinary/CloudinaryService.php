<?php

namespace App\Services\Cloudinary;

class CloudinaryService
{
    /**
     * Get path info
     *
     * @author Febri Arief<febriarief6661@gmail.com>
     * @param  string $path
     * @return string[]|string
     */
    public static function path($path)
    {
        return pathinfo($path, PATHINFO_FILENAME);
    }

    /**
     * Do upload resource
     *
     * @author Febri Arief<febriarief6661@gmail.com>
     * @param  string   $image
     * @param  string   $filename
     * @param  string   $folder
     * @return string
     */
    public static function upload($image, $filename, $folder)
    {
        return cloudinary()->upload($image, [
            "public_id"     => self::path($filename),
            "folder"        => $folder,
            "resource_type" => 'auto'
        ])->getSecurePath();
    }

    /**
     * Delete resource
     *
     * @author Febri Arief<febriarief6661@gmail.com>
     * @param  string   $filepath
     * @return \Cloudinary\Api\ApiResponse
     */
    public static function delete($filepath, $folder)
    {
        return cloudinary()->destroy($folder . '/' .self::path($filepath), [
            "resource_type" => 'auto'
        ]);
    }
}
