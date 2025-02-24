<?php
class User {
    /**
     * Find a user by email.
     */
    public static function findByEmail($pdo, $email) {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email LIMIT 1");
        $stmt->execute(['email' => $email]);
        return $stmt->fetch();
    }

    /**
     * Find a user by their Google ID.
     */
    public static function findByGoogleId($pdo, $googleId) {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE google_id = :google_id LIMIT 1");
        $stmt->execute(['google_id' => $googleId]);
        return $stmt->fetch();
    }

    /**
     * Create a new user using Google OAuth data.
     *
     * @param PDO $pdo
     * @param object $googleUser  // Expected to have properties: email, name, picture
     * @return array|false  The newly created user record.
     */
    public static function createFromGoogle($pdo, $googleUser) {
        // Sanitize incoming Google user data
        $email = filter_var($googleUser->email, FILTER_SANITIZE_EMAIL);
        $google_id = filter_var($googleUser->id, FILTER_SANITIZE_STRING);
        $alias = filter_var($googleUser->name, FILTER_SANITIZE_STRING);
        $profile_image = filter_var($googleUser->picture, FILTER_SANITIZE_URL);

        // Insert a new user record into the database.
        // Note: Password is not required since we use Google for authentication.
        $stmt = $pdo->prepare("
            INSERT INTO users (email, alias, profile_image, google_id, role, created_at)
            VALUES (:email, :alias, :profile_image, 'user', NOW())
        ");
        $stmt->execute([
            'email'         => $email,
            'alias'         => $alias,
            'google_id'     => $google_id,
            'profile_image' => $profile_image
        ]);

        // Retrieve and return the newly created user.
        return self::findByEmail($pdo, $email);
    }

    /**
     * Create a new user.
     */
    public static function create($pdo, $email, $password) {
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (email, password, created_at) VALUES (:email, :password, NOW())");
        return $stmt->execute([
            'email'    => $email,
            'password' => $passwordHash
        ]);
    }
}
?>
