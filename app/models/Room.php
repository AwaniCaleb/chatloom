<?php
class File {
    /**
     * Save file information to the database.
     */
    public static function save($pdo, $data) {
        $stmt = $pdo->prepare("INSERT INTO files (user_id, filename, original_name, file_type, size, uploaded_at) VALUES (:user_id, :filename, :original_name, :file_type, :size, :uploaded_at)");
        return $stmt->execute($data);
    }

    /**
     * Retrieve all uploaded files (for display on the home page).
     */
    public static function getAll($pdo) {
        $stmt = $pdo->query("SELECT * FROM files ORDER BY uploaded_at DESC");
        return $stmt->fetchAll();
    }
}
?>
