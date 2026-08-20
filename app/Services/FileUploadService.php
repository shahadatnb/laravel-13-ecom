<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileUploadService
{
    /**
     * Upload a file to the specified disk and folder.
     */
    public function upload(UploadedFile $file, string $folder = 'uploads', string $disk = 'public'): ?string
    {
        if (! $file) {
            return null;
        }

        $filename = $this->generateUniqueFilename($file);
        $path = $folder.'/'.$filename;

        Storage::disk($disk)->put($path, file_get_contents($file));

        return $path;
    }

    /**
     * Upload multiple files to the specified disk and folder.
     */
    public function uploadMultiple(array $files, string $folder = 'uploads', string $disk = 'public'): array
    {
        $paths = [];

        foreach ($files as $file) {
            if ($file instanceof UploadedFile) {
                $paths[] = $this->upload($file, $folder, $disk);
            }
        }

        return array_filter($paths);
    }

    /**
     * Delete a file from the specified disk.
     */
    public function delete(?string $path, string $disk = 'public'): bool
    {
        if (! $path) {
            return false;
        }

        if (Storage::disk($disk)->exists($path)) {
            return Storage::disk($disk)->delete($path);
        }

        return false;
    }

    /**
     * Delete multiple files from the specified disk.
     */
    public function deleteMultiple(array $paths, string $disk = 'public'): void
    {
        foreach ($paths as $path) {
            $this->delete($path, $disk);
        }
    }

    /**
     * Check if a file exists.
     */
    public function exists(string $path, string $disk = 'public'): bool
    {
        return Storage::disk($disk)->exists($path);
    }

    /**
     * Get the file URL.
     */
    public function url(string $path, string $disk = 'public'): string
    {
        return Storage::disk($disk)->url($path);
    }

    /**
     * Generate a unique filename.
     */
    protected function generateUniqueFilename(UploadedFile $file): string
    {
        $extension = $file->getClientOriginalExtension();
        $randomString = Str::random(10);
        $timestamp = time();

        return "{$timestamp}_{$randomString}.{$extension}";
    }

    /**
     * Move a file from one location to another.
     */
    public function move(string $fromPath, string $toPath, string $disk = 'public'): bool
    {
        if (! Storage::disk($disk)->exists($fromPath)) {
            return false;
        }

        $content = Storage::disk($disk)->get($fromPath);
        Storage::disk($disk)->put($toPath, $content);
        Storage::disk($disk)->delete($fromPath);

        return true;
    }
}
