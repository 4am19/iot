<?php
try {
    $db = new PDO('mysql:host=127.0.0.1', 'root', '');
    $db->exec('CREATE DATABASE IF NOT EXISTS iot_database');
    echo 'DB_OK';
} catch (Exception $e) {
    echo 'DB_ERROR: ' . $e->getMessage();
}
