<?php

namespace App\Services;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class QrCodeService
{
    /**
     * Generate or return static QR code image URL for an order code.
     */
    public static function generateForOrder(string $orderCode): string
    {
        $dir = public_path('qrcodes');
        if (! File::isDirectory($dir)) {
            File::makeDirectory($dir, 0755, true);
        }

        $filePath = "{$dir}/{$orderCode}.png";
        if (File::exists($filePath)) {
            return asset("qrcodes/{$orderCode}.png");
        }

        try {
            $escapedCode = escapeshellarg($orderCode);
            $escapedPath = escapeshellarg($filePath);
            $cmd = "python3 -c \"import qrcode; qr=qrcode.QRCode(box_size=8, border=2); qr.add_data({$escapedCode}); qr.make(fit=True); img=qr.make_image(fill_color='black', back_color='white'); img.save({$escapedPath})\"";
            @exec($cmd);

            if (File::exists($filePath)) {
                return asset("qrcodes/{$orderCode}.png");
            }
        } catch (\Throwable $e) {
            Log::warning('QrCodeService generation failed: '.$e->getMessage());
        }

        return 'https://api.qrserver.com/v1/create-qr-code/?size=250x250&data='.urlencode($orderCode);
    }

    /**
     * Generate or return static QR code image URL for a cafe table.
     */
    public static function generateForTable(\App\Models\Table $table, bool $force = false): string
    {
        $dir = public_path('qrcodes');
        if (! File::isDirectory($dir)) {
            File::makeDirectory($dir, 0755, true);
        }

        $safeNum = preg_replace('/[^a-zA-Z0-9_-]/', '_', $table->table_number);
        $filename = "tbl-{$table->id}-{$safeNum}.png";
        $filePath = "{$dir}/{$filename}";

        $baseUrl = url('/');
        if (app()->bound('request') && request()->getHost() && ! in_array(request()->getHost(), ['localhost', '127.0.0.1'])) {
            $baseUrl = request()->schemeAndHttpHost();
        } else {
            $lanIp = gethostbyname(gethostname());
            if ($lanIp && $lanIp !== '127.0.0.1' && filter_var($lanIp, FILTER_VALIDATE_IP)) {
                $port = app()->bound('request') && request()->getPort() ? ':'.request()->getPort() : ':8000';
                $baseUrl = 'http://'.$lanIp.$port;
            }
        }

        $targetUrl = rtrim($baseUrl, '/').'/table?qr='.$table->qr_code_token;

        if (! $force && File::exists($filePath)) {
            return asset("qrcodes/{$filename}");
        }

        try {
            $escapedData = escapeshellarg($targetUrl);
            $escapedPath = escapeshellarg($filePath);
            $cmd = "python3 -c \"import qrcode; qr=qrcode.QRCode(box_size=10, border=4); qr.add_data({$escapedData}); qr.make(fit=True); img=qr.make_image(fill_color='black', back_color='white'); img.save({$escapedPath})\"";
            @exec($cmd);

            if (File::exists($filePath)) {
                return asset("qrcodes/{$filename}").'?v='.time();
            }
        } catch (\Throwable $e) {
            Log::warning('QrCodeService generateForTable failed: '.$e->getMessage());
        }

        // Pure PHP/HTTP fallback if python3 is unavailable
        try {
            $qrUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=300x300&data='.urlencode($targetUrl);
            $imgData = @file_get_contents($qrUrl);
            if ($imgData && strlen($imgData) > 100) {
                File::put($filePath, $imgData);
                return asset("qrcodes/{$filename}").'?v='.time();
            }
        } catch (\Throwable $e) {
            Log::debug('QrCodeService online QR fallback failed: '.$e->getMessage());
        }

        return 'https://api.qrserver.com/v1/create-qr-code/?size=300x300&data='.urlencode($targetUrl);
    }

    /**
     * Generate or return physical path to complete table sticker PNG card.
     */
    public static function generateStickerForTable(\App\Models\Table $table, bool $force = false): string
    {
        $dir = public_path('qrcodes');
        if (! File::isDirectory($dir)) {
            File::makeDirectory($dir, 0755, true);
        }

        $safeNum = preg_replace('/[^a-zA-Z0-9_-]/', '_', $table->table_number);
        $filename = "sticker-tbl-{$table->id}-{$safeNum}.png";
        $filePath = "{$dir}/{$filename}";

        // Ensure base table QR image exists
        self::generateForTable($table, $force);
        $qrPath = "{$dir}/tbl-{$table->id}-{$safeNum}.png";

        if (! $force && File::exists($filePath)) {
            return $filePath;
        }

        try {
            $tableNum = escapeshellarg($table->table_number);
            $accessCode = escapeshellarg((string) ($table->access_code ?: '7K92A'));
            $escapedQr = escapeshellarg($qrPath);
            $escapedOut = escapeshellarg($filePath);
            $logoPath = public_path('assets/img/NongkrongId-blue.png');
            if (! File::exists($logoPath)) {
                $svgPath = public_path('assets/img/NongkrongId-blue.svg');
                if (File::exists($svgPath)) {
                    @exec("magick -background none -density 300 " . escapeshellarg($svgPath) . " " . escapeshellarg($logoPath));
                }
            }
            $escapedLogo = escapeshellarg($logoPath);

            $pythonScript = <<<PY
import os
from PIL import Image, ImageDraw, ImageFont

def get_font(size, bold=False, mono=False):
    if mono:
        candidates = [
            '/usr/share/fonts/liberation-mono-fonts/LiberationMono-Bold.ttf',
            '/usr/share/fonts/adwaita-mono-fonts/AdwaitaMono-Bold.ttf',
        ]
    elif bold:
        candidates = [
            '/usr/share/fonts/liberation-sans-fonts/LiberationSans-Bold.ttf',
            '/usr/share/fonts/adwaita-sans-fonts/AdwaitaSans-Bold.ttf',
            '/usr/share/fonts/dejavu-sans-fonts/DejaVuSans-Bold.ttf',
        ]
    else:
        candidates = [
            '/usr/share/fonts/liberation-sans-fonts/LiberationSans-Regular.ttf',
            '/usr/share/fonts/adwaita-sans-fonts/AdwaitaSans-Regular.ttf',
            '/usr/share/fonts/dejavu-sans-fonts/DejaVuSans.ttf',
        ]
    for c in candidates:
        if os.path.exists(c):
            return ImageFont.truetype(c, size)
    return ImageFont.load_default()

f_title = get_font(42, bold=True)
f_inst1 = get_font(21, bold=True)
f_inst2 = get_font(15, bold=False)
f_code_lbl = get_font(13, bold=True)
f_code_val = get_font(38, bold=True, mono=True)

img = Image.new('RGB', (600, 760), color='#ffffff')
draw = ImageDraw.Draw(img)

# Outer card border
draw.rounded_rectangle([16, 16, 584, 744], radius=28, outline='#0f172a', width=8)

# Center Brand Logo
logo_path = {$escapedLogo}
if os.path.exists(logo_path):
    logo = Image.open(logo_path).convert('RGBA')
    target_h = 40
    target_w = int(logo.width * (target_h / logo.height))
    logo = logo.resize((target_w, target_h), Image.Resampling.LANCZOS)
    logo_x = int((600 - target_w) / 2)
    img.paste(logo, (logo_x, 48), mask=logo)
else:
    f_header = get_font(26, bold=True)
    draw.text((300, 68), 'NONGKRONG.ID', fill='#0052cc', font=f_header, anchor='mm')

# Table title
draw.text((300, 118), 'MEJA ' + {$tableNum}, fill='#0f172a', font=f_title, anchor='mm')

# QR Frame
draw.rounded_rectangle([135, 150, 465, 480], radius=20, fill='#ffffff', outline='#0f172a', width=3)
if os.path.exists({$escapedQr}):
    qr_img = Image.open({$escapedQr}).resize((300, 300), Image.Resampling.LANCZOS)
    img.paste(qr_img, (150, 165))

# Instructions
draw.text((300, 520), 'Pindai QR untuk Pesan & Bayar', fill='#0f172a', font=f_inst1, anchor='mm')
draw.text((300, 550), 'Arahkan kamera HP Anda ke barcode di atas', fill='#64748b', font=f_inst2, anchor='mm')

# Access Code box
draw.rounded_rectangle([60, 585, 540, 695], radius=18, fill='#f1f5f9', outline='#cbd5e1', width=2)
draw.text((300, 620), 'ATAU MASUKKAN 5 DIGIT KODE MEJA:', fill='#64748b', font=f_code_lbl, anchor='mm')
draw.text((300, 665), {$accessCode}, fill='#0f172a', font=f_code_val, anchor='mm')

img.save({$escapedOut})
PY;
            $cmd = "python3 -c " . escapeshellarg($pythonScript);
            @exec($cmd);

            if (File::exists($filePath)) {
                return $filePath;
            }
        } catch (\Throwable $e) {
            Log::warning('generateStickerForTable python failed: '.$e->getMessage());
        }

        // Pure PHP GD fallback if python3/PIL is not installed
        if (self::generateStickerWithGd($filePath, $table->table_number, (string) ($table->access_code ?: '7K92A'), $qrPath)) {
            return $filePath;
        }

        return File::exists($qrPath) ? $qrPath : $filePath;
    }

    /**
     * Generate table sticker PNG using native PHP GD library (zero-dependency).
     */
    public static function generateStickerWithGd(string $filePath, string $tableNumber, string $accessCode, ?string $qrPath): bool
    {
        if (! extension_loaded('gd')) {
            return false;
        }

        try {
            $width = 600;
            $height = 760;
            $img = imagecreatetruecolor($width, $height);
            if (! $img) {
                return false;
            }

            // Colors
            $white = imagecolorallocate($img, 255, 255, 255);
            $navy = imagecolorallocate($img, 15, 23, 42);
            $slateLight = imagecolorallocate($img, 241, 245, 249);
            $slateBorder = imagecolorallocate($img, 203, 213, 225);
            $slateText = imagecolorallocate($img, 100, 116, 139);
            $brandBlue = imagecolorallocate($img, 0, 74, 173);

            imagefilledrectangle($img, 0, 0, $width, $height, $white);

            // Outer border
            imagesetthickness($img, 6);
            imagerectangle($img, 16, 16, $width - 16, $height - 16, $navy);

            // Header brand text
            imagestring($img, 5, 235, 50, 'NONGKRONG.ID', $brandBlue);

            // Table Title
            $titleText = 'MEJA ' . $tableNumber;
            imagestring($img, 5, 255, 100, $titleText, $navy);

            // QR frame
            imagesetthickness($img, 2);
            imagerectangle($img, 140, 150, 460, 470, $navy);

            // Embed QR image if available
            if ($qrPath && File::exists($qrPath)) {
                $qrImg = @imagecreatefrompng($qrPath) ?: @imagecreatefromstring((string) File::get($qrPath));
                if ($qrImg) {
                    imagecopyresampled($img, $qrImg, 150, 160, 0, 0, 300, 300, imagesx($qrImg), imagesy($qrImg));
                    imagedestroy($qrImg);
                }
            }

            // Instructions
            imagestring($img, 4, 175, 510, 'Pindai QR untuk Pesan & Bayar', $navy);
            imagestring($img, 2, 165, 535, 'Arahkan kamera HP Anda ke barcode di atas', $slateText);

            // Access Code box
            imagefilledrectangle($img, 80, 580, 520, 680, $slateLight);
            imagerectangle($img, 80, 580, 520, 680, $slateBorder);
            imagestring($img, 3, 160, 605, 'ATAU MASUKKAN 5 DIGIT KODE MEJA:', $slateText);
            imagestring($img, 5, 275, 635, $accessCode, $navy);

            $saved = imagepng($img, $filePath);
            imagedestroy($img);

            return $saved && File::exists($filePath);
        } catch (\Throwable $e) {
            Log::warning('generateStickerWithGd failed: '.$e->getMessage());
            return false;
        }
    }
}
