<?php
ini_set('display_errors', 'On');
error_reporting(E_ALL);

define('APP_ROOT', __DIR__);

class MyDB extends SQLite3
{
    public function __construct()
    {
        $this->open(APP_ROOT . '/data.db');
    }
}
