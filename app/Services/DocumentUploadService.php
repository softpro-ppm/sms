<?php

namespace App\Services;

use App\Models\StudentDocument;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Support\Str;

class DocumentUploadService
{
    protected $imageManager;

    public function __construct()
    {
        $this->imageManager = new ImageManager(new Driver());
    }

    /**
     * Upload and process a student document
     */
    public function uploadDocument(UploadedFile $file, int $studentId, string $documentType, ?array $cropData = null): StudentDocument
    {
        // Validate file type
        $this->validateFileType($file, $documentType);

        // Create directory structure
        $directory = "student-documents/{$documentType}";
        $filename = uniqid() . '_' . time() . '.' . $file->getClientOriginalExtension();
        $filePath = $directory . '/' . $filename;

        // Store the file
        if ($documentType === 'photo' && $cropData) {
            $filePath = $this->processAndStoreImage($file, $directory, $filename, $cropData);
        } else {
            $filePath = $this->storeFile($file, $directory, $filename, $cropData);
        }

        // Create database record
        return StudentDocument::create([
            'student_id' => $studentId,
            'document_type' => $documentType,
            'original_name' => $file->getClientOriginalName(),
            'file_path' => $filePath,
        ]);
    }

    /**
     * Update an existing document
     */
    public function updateDocument(StudentDocument $document, UploadedFile $file, ?array $cropData = null): StudentDocument
    {
        // Validate file type
        $this->validateFileType($file, $document->document_type);

        // Delete old file
        if (Storage::disk('public')->exists($document->file_path)) {
            Storage::disk('public')->delete($document->file_path);
        }

        // Create new file path
        $directory = "student-documents/{$document->document_type}";
        $filename = uniqid() . '_' . time() . '.' . $file->getClientOriginalExtension();
        $filePath = $directory . '/' . $filename;

        // Store the file
        if ($document->document_type === 'photo' && $cropData) {
            $filePath = $this->processAndStoreImage($file, $directory, $filename, $cropData);
        } else {
            $filePath = $this->storeFile($file, $directory, $filename, $cropData);
        }

        // Update database record
        $document->update([
            'original_name' => $file->getClientOriginalName(),
            'file_path' => $filePath,
        ]);

        return $document;
    }

    /**
     * Validate file type based on document type
     */
    private function validateFileType(UploadedFile $file, string $documentType): void
    {
        $allowedTypes = [
            'photo' => ['image/jpeg', 'image/jpg', 'image/png'],
            'aadhar' => ['application/pdf', 'image/jpeg', 'image/jpg', 'image/png'],
            'qualification_certificate' => ['application/pdf', 'image/jpeg', 'image/jpg', 'image/png'],
        ];

        if (!isset($allowedTypes[$documentType])) {
            throw new \InvalidArgumentException("Invalid document type: {$documentType}");
        }

        if (!in_array($file->getMimeType(), $allowedTypes[$documentType])) {
            throw new \InvalidArgumentException("Invalid file type for {$documentType}. Allowed: " . implode(', ', $allowedTypes[$documentType]));
        }
    }

    /**
     * Store file without image processing
     */
    private function storeFile(UploadedFile $file, string $directory, string $filename, ?array $cropData = null): string
    {
        $filePath = $directory . '/' . $filename;
        Storage::disk('public')->putFileAs($directory, $file, $filename);
        return $filePath;
    }

    /**
     * Process and store image with cropping
     */
    private function processAndStoreImage(UploadedFile $file, string $directory, string $filename, array $cropData): string
    {
        $image = $this->imageManager->read($file);
        
        // Apply crop if crop data is provided
        if (isset($cropData['x'], $cropData['y'], $cropData['width'], $cropData['height'])) {
            $image->crop(
                (int)$cropData['width'],
                (int)$cropData['height'],
                (int)$cropData['x'],
                (int)$cropData['y']
            );
        }

        // Resize image to exact certificate photo size: 3.5 x 4.5 cm
        // At 300 DPI: 3.5 cm = 413 pixels, 4.5 cm = 531 pixels
        // At 150 DPI: 3.5 cm = 207 pixels, 4.5 cm = 266 pixels
        // Using 150 DPI for web display while maintaining aspect ratio
        $targetWidth = 207;  // 3.5 cm at 150 DPI
        $targetHeight = 266; // 4.5 cm at 150 DPI
        
        // Resize to exact dimensions (this will crop if aspect ratio differs)
        $image->resize($targetWidth, $targetHeight);

        // Encode and store
        $encoded = $image->encode();
        $filePath = $directory . '/' . $filename;
        Storage::disk('public')->put($filePath, $encoded);

        return $filePath;
    }

    /**
     * Get document URL
     */
    public function getDocumentUrl(string $filePath): string
    {
        return Storage::url($filePath);
    }

    /**
     * Delete document
     */
    public function deleteDocument(StudentDocument $document): bool
    {
        if (Storage::disk('public')->exists($document->file_path)) {
            Storage::disk('public')->delete($document->file_path);
        }

        return $document->delete();
    }
}
