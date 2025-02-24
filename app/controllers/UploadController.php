<?php
require_once '../app/helpers/Utils.php';
require_once '../app/models/File.php';

class UploadController {
    /**
     * Display the file upload form.
     */
    public static function uploadForm() {
        if (!isset($_SESSION['user_id'])) {
            Utils::redirect(BASE_URL . 'index.php?page=login');
        }
        require_once '../app/views/upload.php';
    }

    /**
     * Process the uploaded file.
     */
    public static function processUpload($pdo) {
        if (!isset($_SESSION['user_id'])) {
            Utils::redirect(BASE_URL . 'index.php?page=login');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
            $userId = $_SESSION['user_id'];
            $uploadDir = '../public/uploads/';
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'video/mp4'];

            $file = $_FILES['file'];
            $fileType = mime_content_type($file['tmp_name']);

            if (!in_array($fileType, $allowedTypes)) {
                echo "Invalid file type.";
                exit;
            }

            if ($file['size'] > 10 * 1024 * 1024) { // 10MB limit
                echo "File size exceeds the limit.";
                exit;
            }

            $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $newFileName = uniqid() . '.' . $extension;
            $destination = $uploadDir . $newFileName;

            if (move_uploaded_file($file['tmp_name'], $destination)) {
                // Prepare file record data
                $fileRecord = [
                    'user_id'       => $userId,
                    'filename'      => $newFileName,
                    'original_name' => $file['name'],
                    'file_type'     => $fileType,
                    'size'          => $file['size'],
                    'uploaded_at'   => date('Y-m-d H:i:s'),
                ];
                if (File::save($pdo, $fileRecord)) {
                    Utils::redirect(BASE_URL . 'index.php?page=home');
                } else {
                    echo "Error saving file information.";
                }
            } else {
                echo "File upload failed.";
            }
        } else {
            Utils::redirect(BASE_URL . 'index.php?page=upload');
        }
    }
}
?>
