<?php

use Cloudinary\Api\Upload\UploadApi;
use Cloudinary\Configuration\Configuration;
use Cloudinary\Asset\Media;

interface MediaStorageService {
    public function uploadMedia($filePath, $locationFolder);
    public function uploadSignedMedia($filePath, $locationFolder);
    public function getMediaSignedURL($secretKey, $expireInSeconds);
    public function deleteMedia($publicId);
}

class CloudinaryMediaStorageService implements MediaStorageService {
    private $uploadApi;

    public function __construct() {
        $config = new Configuration(CLOUDINARY_URL);
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

    public function uploadSignedMedia($filePath, $locationFolder) {
        try {

            $response = $this->uploadApi->upload($filePath, [
                'folder'        => $locationFolder,
                'resource_type' => 'auto',
                'type'          => 'private',
            ]);

            return $response['public_id'] . '|' . $response['resource_type'];
        } catch (Exception $e) {
            error_log("Cloudinary signed upload error: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            return null;
        }
    }

    // This only works with this format public_id|resource_type as both are required.
    // If used uploadSignedMedia then it will already be in this format.
    public function getMediaSignedURL($secretKey, $expireInSeconds) {
        try {
            if (empty($secretKey) || strpos($secretKey, '|') === false) {
                throw new Exception("Invalid secret key format for signed media URL: " . $secretKey);
            }

            [$publicId, $resourceType] = explode('|', $secretKey, 2);

            $expiresAt = time() + (int) $expireInSeconds;

            return $this->uploadApi->privateDownloadUrl($publicId, '', [
                'resource_type' => $resourceType,
                'type'          => 'private',
                'expires_at'    => $expiresAt,
                'attachment'    => false,  // false = display inline, true = force download
            ]);

        } catch (Exception $e) {
            error_log("Cloudinary get authenticated media URL error: " . $e->getMessage());
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

