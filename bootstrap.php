<?php
require_once 'vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createUnsafeImmutable(__DIR__); //createImmutable or createUnsafeImmutable
$dotenv->load();