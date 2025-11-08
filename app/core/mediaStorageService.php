<?php

use Cloudinary\Api\Upload\UploadApi;
use Cloudinary\Configuration\Configuration;

interface MediaStorageService{
    public function uploadMedia($filePath, $locationFolder);
    public function deleteMedia($publicId);
}

class CloudinaryMediaStorageService implements MediaStorageService {
    private $uploadApi;

    public function __construct() {
        $config = new Configuration($_ENV['CLOUDINARY_URL']);
        $this->uploadApi = new UploadApi($config);
    }

    public function uploadMedia($filePath, $locationFolder) {
        try {
            $response = $this->uploadApi->upload($filePath, [
                'folder' => $locationFolder,
                'resource_type' => 'auto',
            ]);
            return $response['secure_url'] ?? null;
        } catch (Exception $e) {
            error_log("Cloudinary upload error: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            return null;
        }
    }

    public function deleteMedia($publicId) {
        try {
            $response = $this->uploadApi->destroy($publicId, [
                'resource_type' => 'auto',
            ]);
            return $response['result'] === 'ok';
        } catch (Exception $e) {
            error_log("Cloudinary delete error: " . $e->getMessage());
            return false;
        }
    }
}

