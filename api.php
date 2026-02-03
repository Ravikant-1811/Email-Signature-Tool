<?php
require_once __DIR__ . '/auth.php';
header('Content-Type: application/json');

auth_json_guard();

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

$dbPath = __DIR__ . '/storage/signatures.sqlite';
$uploadsDir = __DIR__ . '/uploads';

if (!is_dir($uploadsDir)) {
  mkdir($uploadsDir, 0775, true);
}

try {
  $db = new PDO('sqlite:' . $dbPath);
  $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  $db->exec("CREATE TABLE IF NOT EXISTS signatures (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    full_name TEXT,
    pronouns TEXT,
    role_line TEXT,
    dept_line TEXT,
    mobile TEXT,
    telephone TEXT,
    email TEXT,
    website TEXT,
    publications TEXT,
    linkedin TEXT,
    x TEXT,
    facebook TEXT,
    instagram TEXT,
    github TEXT,
    youtube TEXT,
    accent TEXT,
    theme TEXT,
    photo_path TEXT,
    banner_path TEXT,
    created_at TEXT
  )");
} catch (Exception $e) {
  http_response_code(500);
  echo json_encode(['error' => 'DB init failed', 'details' => $e->getMessage()]);
  exit;
}

function saveUpload($field, $uploadsDir) {
  if (!isset($_FILES[$field]) || $_FILES[$field]['error'] !== UPLOAD_ERR_OK) {
    return '';
  }
  $tmp = $_FILES[$field]['tmp_name'];
  $ext = pathinfo($_FILES[$field]['name'], PATHINFO_EXTENSION);
  $safeExt = preg_replace('/[^a-zA-Z0-9]/', '', $ext);
  $name = uniqid($field . '_', true) . ($safeExt ? '.' . $safeExt : '');
  $target = $uploadsDir . '/' . $name;
  if (!move_uploaded_file($tmp, $target)) {
    return '';
  }
  return 'uploads/' . $name;
}

function pickPath($uploadedPath, $fallback) {
  if ($uploadedPath) {
    return $uploadedPath;
  }
  return $fallback ?? '';
}

if ($action === 'list') {
  $rows = $db->query('SELECT id, full_name, email, created_at FROM signatures ORDER BY id DESC')->fetchAll(PDO::FETCH_ASSOC);
  echo json_encode(['items' => $rows]);
  exit;
}

if ($action === 'upload' && $method === 'POST') {
  $photoPath = saveUpload('photo', $uploadsDir);
  $bannerPath = saveUpload('banner', $uploadsDir);
  echo json_encode(['photo' => $photoPath, 'banner' => $bannerPath]);
  exit;
}

if ($action === 'get') {
  $id = intval($_GET['id'] ?? 0);
  $stmt = $db->prepare('SELECT * FROM signatures WHERE id = :id');
  $stmt->execute([':id' => $id]);
  $row = $stmt->fetch(PDO::FETCH_ASSOC);
  echo json_encode(['item' => $row]);
  exit;
}

if ($action === 'save' && $method === 'POST') {
  $photoPath = saveUpload('photo', $uploadsDir);
  $bannerPath = saveUpload('banner', $uploadsDir);
  $photoFinal = pickPath($photoPath, $_POST['photoUrl'] ?? '');
  $bannerFinal = pickPath($bannerPath, $_POST['bannerUrl'] ?? '');

  $payload = [
    'full_name' => $_POST['fullName'] ?? '',
    'pronouns' => $_POST['pronouns'] ?? '',
    'role_line' => $_POST['roleLine'] ?? '',
    'dept_line' => $_POST['deptLine'] ?? '',
    'mobile' => $_POST['mobile'] ?? '',
    'telephone' => $_POST['telephone'] ?? '',
    'email' => $_POST['email'] ?? '',
    'website' => $_POST['website'] ?? '',
    'publications' => $_POST['publications'] ?? '',
    'linkedin' => $_POST['linkedin'] ?? '',
    'x' => $_POST['x'] ?? '',
    'facebook' => $_POST['facebook'] ?? '',
    'instagram' => $_POST['instagram'] ?? '',
    'github' => $_POST['github'] ?? '',
    'youtube' => $_POST['youtube'] ?? '',
    'accent' => $_POST['accent'] ?? '',
    'theme' => $_POST['theme'] ?? '',
    'photo_path' => $photoFinal,
    'banner_path' => $bannerFinal,
    'created_at' => date('c')
  ];

  $stmt = $db->prepare("INSERT INTO signatures (
    full_name, pronouns, role_line, dept_line, mobile, telephone, email, website,
    publications, linkedin, x, facebook, instagram, github, youtube, accent, theme,
    photo_path, banner_path, created_at
  ) VALUES (
    :full_name, :pronouns, :role_line, :dept_line, :mobile, :telephone, :email, :website,
    :publications, :linkedin, :x, :facebook, :instagram, :github, :youtube, :accent, :theme,
    :photo_path, :banner_path, :created_at
  )");

  $stmt->execute($payload);
  echo json_encode(['success' => true, 'id' => $db->lastInsertId(), 'photo' => $photoFinal, 'banner' => $bannerFinal]);
  exit;
}

if ($action === 'update' && $method === 'POST') {
  $id = intval($_POST['id'] ?? 0);
  if ($id <= 0) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing id']);
    exit;
  }

  $photoPath = saveUpload('photo', $uploadsDir);
  $bannerPath = saveUpload('banner', $uploadsDir);
  $photoFinal = pickPath($photoPath, $_POST['photoUrl'] ?? '');
  $bannerFinal = pickPath($bannerPath, $_POST['bannerUrl'] ?? '');

  $payload = [
    'id' => $id,
    'full_name' => $_POST['fullName'] ?? '',
    'pronouns' => $_POST['pronouns'] ?? '',
    'role_line' => $_POST['roleLine'] ?? '',
    'dept_line' => $_POST['deptLine'] ?? '',
    'mobile' => $_POST['mobile'] ?? '',
    'telephone' => $_POST['telephone'] ?? '',
    'email' => $_POST['email'] ?? '',
    'website' => $_POST['website'] ?? '',
    'publications' => $_POST['publications'] ?? '',
    'linkedin' => $_POST['linkedin'] ?? '',
    'x' => $_POST['x'] ?? '',
    'facebook' => $_POST['facebook'] ?? '',
    'instagram' => $_POST['instagram'] ?? '',
    'github' => $_POST['github'] ?? '',
    'youtube' => $_POST['youtube'] ?? '',
    'accent' => $_POST['accent'] ?? '',
    'theme' => $_POST['theme'] ?? '',
    'photo_path' => $photoFinal,
    'banner_path' => $bannerFinal
  ];

  $stmt = $db->prepare(\"UPDATE signatures SET
    full_name = :full_name,
    pronouns = :pronouns,
    role_line = :role_line,
    dept_line = :dept_line,
    mobile = :mobile,
    telephone = :telephone,
    email = :email,
    website = :website,
    publications = :publications,
    linkedin = :linkedin,
    x = :x,
    facebook = :facebook,
    instagram = :instagram,
    github = :github,
    youtube = :youtube,
    accent = :accent,
    theme = :theme,
    photo_path = :photo_path,
    banner_path = :banner_path
    WHERE id = :id
  \");
  $stmt->execute($payload);
  echo json_encode(['success' => true, 'id' => $id, 'photo' => $photoFinal, 'banner' => $bannerFinal]);
  exit;
}

if ($action === 'delete' && $method === 'POST') {
  $id = intval($_POST['id'] ?? 0);
  if ($id <= 0) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing id']);
    exit;
  }
  $stmt = $db->prepare('DELETE FROM signatures WHERE id = :id');
  $stmt->execute([':id' => $id]);
  echo json_encode(['success' => true]);
  exit;
}

http_response_code(400);
echo json_encode(['error' => 'Invalid request']);
