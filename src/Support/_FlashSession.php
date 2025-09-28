<?php
namespace App\Support;

class FlashSession
{
    public static function set(string $key, $message): void
    {
        $_SESSION['flash'][$key] = $message;
    }

    public static function get(string $key, $default = null)
    {
        if (! isset($_SESSION['flash'][$key])) {
            return $default;
        }

        $message = $_SESSION['flash'][$key];
        unset($_SESSION['flash'][$key]);
        return $message;
    }

    public static function all(): array
    {
        $messages = $_SESSION['flash'] ?? [];
        unset($_SESSION['flash']);
        return $messages;
    }
}
