<?php
require_once __DIR__ . '/config.php';

session_start();

function require_auth() {
  if (!isset($_SESSION['authed']) || $_SESSION['authed'] !== true) {
    header('Location: login.php');
    exit;
  }
}

function auth_json_guard() {
  if (!isset($_SESSION['authed']) || $_SESSION['authed'] !== true) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
  }
}
