<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AttachmentNamingService
{
    /**
     * Normalize the purpose string into a safe filename component.
     */
    public static function normalizePurpose(string $purpose): string
    {
        $normalized = preg_replace('/[^a-zA-Z0-9\s_-]/', '', $purpose);
        $normalized = preg_replace('/[\s_-]+/', '_', trim((string) $normalized));

        return Str::studly($normalized) ?: 'Document';
    }

    /**
     * Generate a safe, collision-resistant filename following {ZANID}_{PURPOSE}.{EXTENSION}.
     */
    public static function generateFileName(
        string $zanId,
        string $purpose,
        string $extension,
        string $disk = 'public',
        string $directory = 'attachments'
    ): string {
        $cleanZanId = preg_replace('/[^0-9]/', '', $zanId) ?: 'APPLICANT';
        $cleanPurpose = static::normalizePurpose($purpose);
        $cleanExtension = strtolower(ltrim($extension, '.'));

        $baseName = "{$cleanZanId}_{$cleanPurpose}";
        $fileName = "{$baseName}.{$cleanExtension}";

        $counter = 1;
        while (Storage::disk($disk)->exists("{$directory}/{$fileName}")) {
            $fileName = "{$baseName}_{$counter}.{$cleanExtension}";
            $counter++;
        }

        return $fileName;
    }

    /**
     * Derive type: Document or Picture based on MIME type.
     */
    public static function deriveType(string $mimeType): string
    {
        return str_starts_with($mimeType, 'image/') ? 'Picture' : 'Document';
    }

    /**
     * Store the uploaded file using the global naming pattern and return file details.
     *
     * @return array{
     *     stored_path: string,
     *     file_name: string,
     *     original_name: string,
     *     mime_type: string,
     *     file_size: int,
     *     type: string
     * }
     */
    public static function storeAttachment(
        UploadedFile $file,
        string $zanId,
        string $purpose,
        string $disk = 'public',
        string $directory = 'attachments'
    ): array {
        $extension = $file->getClientOriginalExtension() ?: $file->extension() ?: 'bin';
        $fileName = static::generateFileName($zanId, $purpose, $extension, $disk, $directory);
        $storedPath = $file->storeAs($directory, $fileName, $disk);

        $mimeType = $file->getClientMimeType() ?: $file->getMimeType() ?: 'application/octet-stream';
        $fileSize = $file->getSize() ?: 0;
        $type = static::deriveType($mimeType);

        return [
            'stored_path' => $storedPath,
            'file_name' => $fileName,
            'original_name' => $file->getClientOriginalName(),
            'mime_type' => $mimeType,
            'file_size' => $fileSize,
            'type' => $type,
        ];
    }
}
