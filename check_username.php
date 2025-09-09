<?php
$usersFile = __DIR__ . '/users.json';
$username = $_GET['username'] ?? '';

if (!$username) {
    echo json_encode(["exists" => false]);
    exit;
}

$users = file_exists($usersFile) ? json_decode(file_get_contents($usersFile), true) : [];

echo json_encode(["exists" => isset($users[$username])]);