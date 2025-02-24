<?php
require_once '../app/models/User.php';
require_once '../app/helpers/Utils.php';

// Load Composer's autoloader for Google Client
require_once '../vendor/autoload.php';

use Google\Service\Oauth2;
use App\Config\Env;

class AuthController
{
    /**
     * Show the login form.
     */
    public static function loginForm()
    {
        require_once '../app/views/login.php';
    }

    /**
     * Process login data.
     */
    public static function processLogin(PDO $pdo)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = Utils::sanitize($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';

            $user = User::findByEmail($pdo, $email);
            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                Utils::redirect(BASE_URL . 'index.php?page=home');
            } else {
                // You can add error handling or flash messages here.
                Utils::redirect(BASE_URL . 'index.php?page=login');
            }
        } else {
            Utils::redirect(BASE_URL . 'index.php?page=login');
        }
    }

    /**     * Redirects the user to Google's OAuth 2.0 server.
     */
    public static function signInWithGoogle(Env $env) {
        $client = new Google_Client();
        $client->setClientId($env->get('GOOGLE_CLIENT_ID'), null);
        $client->setClientSecret($env->get('GOOGLE_CLIENT_SECRET'), null);
        $client->setApplicationName($env->get('APPLICATION_NAME'), null);
        $client->setRedirectUri(BASE_URL . "index.php?page=google_callback");
        $client->setPrompt('consent');
        $client->addScope("email");
        $client->addScope("profile");
        
        $authUrl = $client->createAuthUrl();
        header("Location: " . $authUrl);
        exit;
    }

    /**
     * Handles the OAuth 2.0 server response.
     */
    public static function googleCallback(PDO $pdo, Env $env)
    {
        if (!isset($_GET['code'])) {
            Utils::redirect(BASE_URL . "index.php?page=login");
        }

        $client = new Google_Client();
        $client->setClientId($env->get('GOOGLE_CLIENT_ID'), null);
        $client->setClientSecret($env->get('GOOGLE_CLIENT_SECRET'), null);
        $client->setApplicationName($env->get('APPLICATION_NAME'), null);
        $client->setPrompt('consent');
        $client->setRedirectUri(BASE_URL . "index.php?page=google_callback");

        try {
            $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
            $client->setAccessToken($token['access_token']);
        } catch (Exception $e) {
            // Handle the exception, e.g., log the error, show an error message, etc.
            Utils::redirect(BASE_URL . "index.php?page=login&error=google_auth_failed");
            error_log($e->getMessage(), 0, "/logs/error.log");
            exit;
        }

        $oauth2 = new Oauth2($client);
        $googleUser = $oauth2->userinfo->get();

        // Process the Google user info
        // Example: Check if the user already exists using their email
        $user = User::findByEmail($pdo, $googleUser->email);
        if (!$user) {
            // Create a new user with data from Google
            $user = User::createFromGoogle($pdo, $googleUser);
        }

        // Set user session
        $_SESSION['user_id'] = $user['id'];
        Utils::redirect(BASE_URL . "index.php?page=home");
    }

    /**
     * Logout the user.
     */
    public static function logout()
    {
        session_destroy();
        Utils::redirect(BASE_URL . 'index.php?page=login');
    }
}
