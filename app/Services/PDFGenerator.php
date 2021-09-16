<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use \Imagick;


class PDFGenerator
{
    // DO NOT change this config.
    // Pass override config when calling download() method.
    private static $config = [
        'delay' => 10,
        'fullpage' => 1,
        'viewport' => '1768x2000',
    ];

    public static function prepareUrl($url, $params = [])
    {
        if (strpos($url, '?') === false) {
            $url .= '?';
        } else {
            $url .= '&';
        }

        if (empty($params) || !is_array($params)) {
            $params = [];
        }

        $params = array_merge($params, ['print' => true]);

        return urlencode($url) . http_build_query($params, '', '%26');
    }

    public static function getScreenshot($url, $params = [], $config = [])
    {
        try {
            $url = self::prepareUrl($url, $params);

            if (!empty($config) && is_array($config)) {
                $config = array_merge(self::$config, $config);
            } else {
                $config = self::$config;
            }

            $parts = [];
            foreach ($config as $key => $value) {
                $parts[] = "$key=$value";
            }

            $query = implode("&", $parts);

            $accessKey = '74ddd6e88bf925224ebb63ea141e98f1';

            $serviceUrl = "http://api.screenshotlayer.com/api/capture?access_key={$accessKey}&url={$url}&{$query}";

            $file = file_get_contents($serviceUrl);

            return $file;
        } catch (\Throwable $th) {
            dd($th);
        }
    }

    public static function download($url, $params = [], $config = [])
    {
        $directory = 'images/dashboard/';
        $filename = $config['filename'] ?? (time() . '-Uptime Resume.pdf');
        $path = $directory . $filename;

        $file = self::getScreenshot($url, $params, $config);

        $image = new Imagick();
        $image->readImageBlob($file);
//        $image->rotateimage('white', 90);
//        $image->resizeImage(2550, 3300, Imagick::FILTER_LANCZOS, 1);
        $image->setImageFormat('pdf');
        $image->setImageFilename($filename);
        $image->setFilename($filename);
        /*
         * Save the image on S3.
         * Uncomment the following lines if we need to store the file on S3.
         */

    //    $outputFile = $image->getImageBlob();
    //    Storage::disk("s3")->put($path, $outputFile, "public");
    //    $url = Storage::disk('s3')->url($path);

        return response($image)->withHeaders([
            'Content-Type' => 'application/pdf',
            'Content-Description' => 'File Transfer',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'filename' => $filename,
        ]);
    }
}