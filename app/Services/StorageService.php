<?php
namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class StorageService
{
    public function uploadImage(UploadedFile $file, string $directory = 'uploads'): string
    {
        $filename = Str::random(40).'.'.$file->getClientOriginalExtension();

        $disk = config('filesystems.default');

        if ($disk === 's3') {
            return Storage::disk('s3')->putFileAs($directory, $file, $filename, 'public');
        } else if ($disk === 'wasabi') {
            return Storage::disk('wasabi')->putFileAs($directory, $file, $filename, 'public');
        } else {
            return Storage::disk('local')->putFileAs("public/$directory", $file, $filename);
        }
    }

    public function deleteImage(string $path): bool
    {
        $disk = config('filesystems.default');

        if ($disk === 's3' || $disk === 'wasabi') {
            return Storage::disk($disk)->delete($path);
        } else {
            return Storage::disk('local')->delete("public/$path");
        }
    }

    public function getImageUrl(string $path): string
    {
        $disk = config('filesystems.default');

        if ($disk === 's3' || $disk === 'wasabi') {
            return Storage::disk($disk)->url($path);
        } else {
            return Storage::disk('local')->url("public/$path");
        }
    }
}
