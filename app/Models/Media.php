<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Media extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'original_name',
        'path',
        'url',
        'type',
        'mime_type',
        'size',
        'alt_text',
        'description',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'size' => 'integer',
            'sort_order' => 'integer',
        ];
    }

    /**
     * Get the human-readable file size.
     */
    public function getFormattedSizeAttribute(): string
    {
        $bytes = $this->size;
        $units = ['B', 'KB', 'MB', 'GB'];

        for ($i = 0; $bytes > 1024; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2).' '.$units[$i];
    }

    /**
     * Check if media is an image type.
     */
    public function getIsImageAttribute(): bool
    {
        return $this->type === 'image';
    }

    /**
     * Delete the file from storage.
     */
    public function deleteFile(): bool
    {
        if (Storage::disk('public')->exists($this->path)) {
            return Storage::disk('public')->delete($this->path);
        }

        return false;
    }

    /**
     * Override delete to also remove the file.
     */
    public function delete(): ?bool
    {
        $this->deleteFile();

        return parent::delete();
    }

    /**
     * Force delete with file removal.
     */
    public function forceDelete(): ?bool
    {
        $this->deleteFile();

        return parent::forceDelete();
    }
}
