<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'document_type',
        'original_name',
        'file_path',
        'file_size',
        'mime_type',
        'crop_data'
    ];

    protected $casts = [
        'crop_data' => 'array'
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get the document type options
     */
    public static function getDocumentTypes(): array
    {
        return [
            'photo' => 'Photo',
            'aadhar' => 'Aadhar Card',
            'qualification_certificate' => 'Qualification Certificate'
        ];
    }

    /**
     * Get allowed file extensions for each document type
     */
    public static function getAllowedExtensions(string $documentType): array
    {
        return match($documentType) {
            'photo' => ['jpg', 'jpeg', 'png'],
            'aadhar', 'qualification_certificate' => ['pdf', 'jpg', 'jpeg', 'png'],
            default => []
        };
    }

    /**
     * Check if file type is allowed for document type
     */
    public static function isAllowedFileType(string $documentType, string $mimeType): bool
    {
        $allowedExtensions = self::getAllowedExtensions($documentType);
        $fileExtension = strtolower(pathinfo($mimeType, PATHINFO_EXTENSION));
        
        return in_array($fileExtension, $allowedExtensions);
    }
}