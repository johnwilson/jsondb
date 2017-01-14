<?php

// error_reporting(E_ALL);

// Helper functions
require_once __DIR__ . '/src/helpers.php';

// Setup configuration
IBT\JsonDB\Client::$config = include_once(__DIR__ . '/config/config.php');