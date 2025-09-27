<?php
namespace App\Controllers;

class BaseController
{
    public function url($path)
    {
        return $_ENV['BASE_PATH'] ? $_ENV['BASE_PATH'] . '/' . $path : $path;
    }
}
