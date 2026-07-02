<?php

namespace App\Services;

use thiagoalessio\TesseractOCR\TesseractOCR;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class KtpOcrService
{
    /**
     * Simpan file foto KTP ke storage, lakukan preprocessing, lalu jalankan OCR.
     *
     * @return array{path:string, processed_path:?string, raw_text:string, parsed:array, glare_detected:bool}
     */
    public function process(UploadedFile $file): array
    {
        $path = $file->store('fpup/ktp', 'public');
        $fullPath = Storage::disk('public')->path($path);

        $glareDetected = $this->detectGlare($fullPath);

        // Siapkan beberapa varian gambar untuk dicoba OCR. Foto KTP laminasi
        // sering punya pantulan cahaya (glare) yang membuat satu varian
        // preprocessing saja tidak cukup — jadi kita coba beberapa dan pilih
        // hasil terbaik berdasarkan skor.
        $variants = $this->buildVariants($fullPath);

        $bestText = '';
        $bestScore = -1;
        $bestVariantPath = null;

        foreach ($variants as $variantPath) {
            $text = $this->runTesseractMultiPass($variantPath);
            $score = $this->scoreText($text);

            Log::debug('OCR variant result', ['variant' => basename($variantPath), 'score' => $score]);

            if ($score > $bestScore) {
                $bestScore = $score;
                $bestText = $text;
                $bestVariantPath = $variantPath;
            }
        }

        $parsed = $this->parse($bestText);

        // Simpan varian terbaik sebagai gambar debug, hapus sisanya
        $processedRelative = null;
        foreach ($variants as $variantPath) {
            if ($variantPath === $bestVariantPath) {
                $processedRelative = 'fpup/ktp/processed_' . basename($variantPath);
                Storage::disk('public')->put($processedRelative, file_get_contents($variantPath));
            }
            @unlink($variantPath);
        }

        Log::info('KTP OCR result', [
            'path' => $path,
            'glare_detected' => $glareDetected,
            'best_score' => $bestScore,
            'raw_text' => $bestText,
        ]);

        return [
            'path'           => $path,
            'processed_path' => $processedRelative,
            'raw_text'       => $bestText,
            'parsed'         => $parsed,
            'glare_detected' => $glareDetected,
        ];
    }

    /**
     * Bangun beberapa varian gambar untuk dicoba OCR satu per satu.
     * Mengembalikan array path file sementara (PNG).
     */
    protected function buildVariants(string $sourcePath): array
    {
        $variants = [];

        if (extension_loaded('imagick')) {
            try {
                $variants[] = $this->variantImagickNormal($sourcePath);
                $variants[] = $this->variantImagickAdaptiveThreshold($sourcePath);
                return array_filter($variants);
            } catch (\Throwable $e) {
                Log::warning('Imagick preprocessing gagal, fallback ke GD', ['error' => $e->getMessage()]);
                $variants = [];
            }
        }

        if (extension_loaded('gd')) {
            try {
                $variants[] = $this->variantGdNormal($sourcePath);
                return array_filter($variants);
            } catch (\Throwable $e) {
                Log::warning('GD preprocessing gagal, OCR jalan di file asli', ['error' => $e->getMessage()]);
            }
        }

        // Tidak ada ekstensi gambar tersedia — OCR langsung di file asli
        return [$sourcePath];
    }

    /**
     * Varian 1: grayscale + normalize + sharpen (preprocessing standar).
     * Bagus untuk foto yang cukup rata pencahayaannya.
     */
    protected function variantImagickNormal(string $sourcePath): string
    {
        $tmpOut = sys_get_temp_dir() . '/' . uniqid('ktp_v1_') . '.png';

        $img = new \Imagick($sourcePath);
        $img->autoOrient();

        $width = $img->getImageWidth();
        if ($width < 1600) {
            $scale = 1600 / max($width, 1);
            $img->resizeImage(
                (int) round($width * $scale),
                (int) round($img->getImageHeight() * $scale),
                \Imagick::FILTER_LANCZOS,
                1
            );
        }

        $img->setImageColorspace(\Imagick::COLORSPACE_GRAY);
        $img->normalizeImage();
        $img->sharpenImage(0, 1);
        $img->setImageFormat('png');
        $img->writeImage($tmpOut);
        $img->clear();
        $img->destroy();

        return $tmpOut;
    }

    /**
     * Varian 2: adaptive thresholding (binarisasi lokal) — jauh lebih tahan
     * terhadap pencahayaan tidak rata / glare dibanding normalize global,
     * karena threshold dihitung per-area kecil, bukan untuk seluruh gambar.
     * Ini varian yang biasanya menyelamatkan foto KTP laminasi mengkilap.
     */
    protected function variantImagickAdaptiveThreshold(string $sourcePath): string
    {
        $tmpOut = sys_get_temp_dir() . '/' . uniqid('ktp_v2_') . '.png';

        $img = new \Imagick($sourcePath);
        $img->autoOrient();

        $width = $img->getImageWidth();
        if ($width < 1600) {
            $scale = 1600 / max($width, 1);
            $img->resizeImage(
                (int) round($width * $scale),
                (int) round($img->getImageHeight() * $scale),
                \Imagick::FILTER_LANCZOS,
                1
            );
        }

        $img->setImageColorspace(\Imagick::COLORSPACE_GRAY);

        // Adaptive threshold: bandingkan tiap piksel dengan rata-rata area
        // lokal di sekitarnya (blur besar sebagai "local mean"), bukan rata-rata
        // global. Ini menetralkan efek terang-gelap tidak rata akibat glare.
        $blurred = clone $img;
        $blurred->blurImage(0, 25); // gaussian blur radius besar = local mean

        $img->compositeImage($blurred, \Imagick::COMPOSITE_MINUS_DST, 0, 0);
        $img->levelImage(0.45 * \Imagick::getQuantum(), 1.0, 0.55 * \Imagick::getQuantum());
        $img->contrastImage(true);

        $img->setImageFormat('png');
        $img->writeImage($tmpOut);
        $img->clear();
        $img->destroy();

        return $tmpOut;
    }

    protected function variantGdNormal(string $sourcePath): string
    {
        $tmpOut = sys_get_temp_dir() . '/' . uniqid('ktp_gd_') . '.png';

        $info = getimagesize($sourcePath);
        if (!$info) {
            throw new \RuntimeException('File gambar tidak terbaca oleh GD');
        }

        $mime = $info['mime'];
        $src = match ($mime) {
            'image/jpeg' => imagecreatefromjpeg($sourcePath),
            'image/png'  => imagecreatefrompng($sourcePath),
            default      => throw new \RuntimeException("Format $mime tidak didukung GD"),
        };

        if (!$src) {
            throw new \RuntimeException('Gagal membuka gambar dengan GD');
        }

        if ($mime === 'image/jpeg' && function_exists('exif_read_data')) {
            $exif = @exif_read_data($sourcePath);
            if (!empty($exif['Orientation'])) {
                $src = $this->fixOrientationGd($src, $exif['Orientation']);
            }
        }

        $width  = imagesx($src);
        $height = imagesy($src);

        if ($width < 1600) {
            $scale = 1600 / $width;
            $newW = (int) round($width * $scale);
            $newH = (int) round($height * $scale);
            $resized = imagecreatetruecolor($newW, $newH);
            imagecopyresampled($resized, $src, 0, 0, 0, 0, $newW, $newH, $width, $height);
            imagedestroy($src);
            $src = $resized;
        }

        imagefilter($src, IMG_FILTER_GRAYSCALE);
        imagefilter($src, IMG_FILTER_CONTRAST, -25);
        imagefilter($src, IMG_FILTER_SMOOTH, -4);

        imagepng($src, $tmpOut);
        imagedestroy($src);

        return $tmpOut;
    }

    protected function fixOrientationGd($image, int $orientation)
    {
        return match ($orientation) {
            3 => imagerotate($image, 180, 0),
            6 => imagerotate($image, -90, 0),
            8 => imagerotate($image, 90, 0),
            default => $image,
        };
    }

    /**
     * Deteksi sederhana apakah foto kemungkinan punya glare/pantulan cahaya:
     * hitung persentase piksel yang hampir putih total (>245 dari 255 di
     * grayscale). Foto KTP laminasi dengan pantulan biasanya punya area
     * cukup besar yang overexposed seperti ini.
     */
    protected function detectGlare(string $sourcePath): bool
    {
        if (!extension_loaded('gd')) {
            return false;
        }

        try {
            $info = getimagesize($sourcePath);
            if (!$info) {
                return false;
            }

            $mime = $info['mime'];
            $src = match ($mime) {
                'image/jpeg' => imagecreatefromjpeg($sourcePath),
                'image/png'  => imagecreatefrompng($sourcePath),
                default      => null,
            };

            if (!$src) {
                return false;
            }

            // Sample sebagian piksel saja (tiap 10px) demi performa
            $width  = imagesx($src);
            $height = imagesy($src);
            $total = 0;
            $overexposed = 0;

            for ($x = 0; $x < $width; $x += 10) {
                for ($y = 0; $y < $height; $y += 10) {
                    $rgb = imagecolorat($src, $x, $y);
                    $r = ($rgb >> 16) & 0xFF;
                    $g = ($rgb >> 8) & 0xFF;
                    $b = $rgb & 0xFF;
                    $gray = (int) round(($r + $g + $b) / 3);

                    $total++;
                    if ($gray > 245) {
                        $overexposed++;
                    }
                }
            }

            imagedestroy($src);

            if ($total === 0) {
                return false;
            }

            $ratio = $overexposed / $total;
            return $ratio > 0.12; // lebih dari 12% area sample overexposed → indikasi glare
        } catch (\Throwable $e) {
            return false;
        }
    }

    /**
     * Jalankan tesseract dengan beberapa kombinasi PSM dan pilih hasil terbaik.
     */
    protected function runTesseractMultiPass(string $imagePath): string
    {
        $psmList = [6, 4, 3, 11];

        $bestText = '';
        $bestScore = -1;

        foreach ($psmList as $psm) {
            $text = $this->runTesseractOnce($imagePath, $psm);
            $score = $this->scoreText($text);

            if ($score > $bestScore) {
                $bestScore = $score;
                $bestText = $text;
            }
        }

        return $bestText;
    }

    protected function runTesseractOnce(string $imagePath, int $psm): string
    {
        try {
            return (new TesseractOCR($imagePath))
                ->lang('ind', 'eng')
                ->psm($psm)
                ->run();
        } catch (\Throwable $e) {
            try {
                return (new TesseractOCR($imagePath))
                    ->lang('eng')
                    ->psm($psm)
                    ->run();
            } catch (\Throwable $e2) {
                Log::error('Tesseract gagal total', ['psm' => $psm, 'error' => $e2->getMessage()]);
                return '';
            }
        }
    }

    /**
     * Skor sederhana: hitung jumlah karakter alfanumerik yang valid.
     */
    protected function scoreText(string $text): int
    {
        $alnum = preg_replace('/[^A-Za-z0-9]/', '', $text);
        return strlen($alnum);
    }

    /**
     * Parsing teks hasil OCR KTP Indonesia ke field-field terstruktur.
     */
    public function parse(string $text): array
    {
        $lines = array_filter(array_map('trim', explode("\n", $text)));
        $clean = implode("\n", $lines);

        $result = [
            'no_ktp'        => null,
            'nama_pasien'   => null,
            'tgl_lahir'     => null,
            'jenis_kelamin' => null,
            'alamat'        => null,
            'kebangsaan'    => 'INDONESIA',
        ];

        // NIK: 16 digit, boleh ada spasi di tengah (OCR kadang menyisipkan
        // spasi salah letak, jadi kita hapus spasi dulu sebelum cari pola 16 digit)
        $noSpaceDigits = preg_replace('/(\d)\s+(\d)/', '$1$2', $clean);
        if (preg_match('/\b(\d{16})\b/', $noSpaceDigits, $m)) {
            $result['no_ktp'] = $m[1];
        }

        if (preg_match('/Nama\s*[:\-]?\s*([A-Z\' .]{3,})/i', $clean, $m)) {
            $result['nama_pasien'] = trim($m[1]);
        }

        if (preg_match('/(\d{2})[-\/](\d{2})[-\/](\d{4})/', $clean, $m)) {
            $result['tgl_lahir'] = "{$m[3]}-{$m[2]}-{$m[1]}";
        }

        if (preg_match('/LAKI[\s\-]?LAKI/i', $clean)) {
            $result['jenis_kelamin'] = 'Pria';
        } elseif (preg_match('/PEREMPUAN/i', $clean)) {
            $result['jenis_kelamin'] = 'Wanita';
        }

        if (preg_match('/Alamat\s*[:\-]?\s*(.+)/i', $clean, $m)) {
            $result['alamat'] = trim($m[1]);
        }

        return $result;
    }
}