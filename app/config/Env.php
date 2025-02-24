<?php
namespace App\Config;

use Dotenv\Dotenv;

class Env
{
    /**
     * @var array The array that holds all environment variables.
     */
    private $vars = [];

    /**
     * Constructor.
     *
     * Loads the .env file from the specified directory (if it exists) and populates the configuration.
     *
     * @param string $envPath The directory path where the .env file is located. Defaults to __DIR__.
     */
    public function __construct(string $envPath = __DIR__)
    {
        // Check if a .env file exists in the given path
        $envFile = $envPath . '/.env';
        if (file_exists($envFile)) {
            // Create a Dotenv instance and load the variables.
            // Using safeLoad() prevents exceptions if the file is missing or unreadable.
            $dotenv = Dotenv::createImmutable($envPath);
            $dotenv->safeLoad();
        }
        
        // Capture all environment variables into the internal array.
        // This assumes that variables are loaded into $_ENV; adjust if necessary.
        $this->vars = $_ENV;
    }

    /**
     * Retrieve a configuration value.
     *
     * @param string $key The configuration key.
     * @param mixed $default The default value to return if the key is not found.
     * @return mixed
     */
    public function get(string $key, $default = null)
    {
        return array_key_exists($key, $this->vars) ? $this->vars[$key] : $default;
    }

    /**
     * Set or override a configuration value.
     *
     * This method updates the internal array and also sets the value
     * in the environment (via putenv, $_ENV, and $_SERVER).
     *
     * @param string $key The configuration key.
     * @param mixed $value The value to set.
     */
    public function set(string $key, $value): void
    {
        $this->vars[$key] = $value;
        putenv("$key=$value");
        $_ENV[$key] = $value;
        $_SERVER[$key] = $value;
    }

    /**
     * Get all configuration values.
     *
     * @return array
     */
    public function all(): array
    {
        return $this->vars;
    }
}
