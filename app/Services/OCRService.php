<?php

namespace App\Services;

use Imagick;
use thiagoalessio\TesseractOCR\TesseractOCR;

class OCRService
{
    public function preprocessImage(string $inputPath): string
    {
        $outputPath = storage_path('app/temp_ocr_image.png');

        $image = new Imagick($inputPath);
        $image->setImageResolution(300, 300);
        $image->resampleImage(300, 300, Imagick::FILTER_UNDEFINED, 1);
        $image->setImageFormat('png');
        // $image->setImageColorspace(\Imagick::COLORSPACE_GRAY);
        $image->normalizeImage();
        // $image->blurImage(1, 0.5);
        // $image->thresholdImage(0.5 * \Imagick::getQuantum());
        // $image->deskewImage(0.4);
        $image->resizeImage(
            $image->getImageWidth() * 2,
            $image->getImageHeight() * 2,
            Imagick::FILTER_LANCZOS,
            1
        );

        // Simpan gambar hasil preprocessing
        $image->writeImage($outputPath);

        // $cmd = "convert {$inputPath} -fill black -fuzz 30% +opaque '#FFFFFF' {$outputPath}";
        // exec($cmd);

        return $outputPath;
    }

    public function extractTextFromImage(string $imagePath): string
    {
        $preprocessedPath = $this->preprocessImage($imagePath);

        if (env('APP_ENV') == 'local') {
            return (new TesseractOCR($preprocessedPath))
                ->lang('eng+ind')
                ->psm(6)
                ->oem(1)
                ->tessdataDir('C:\Program Files\Tesseract-OCR\tessdata')
                ->run();
        } else {
            return (new TesseractOCR($preprocessedPath))
                ->executable('/usr/bin/tesseract')
                ->lang('eng+ind')
                ->psm(6)
                ->oem(1)
                ->tessdataDir('/usr/share/tesseract-ocr/4.00/tessdata')
                ->run();
        }
    }
}
