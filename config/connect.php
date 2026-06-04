<?php
if (!isset($_SESSION)) {
    session_start();
}

// Cấu hình kết nối CSDL, dùng env variables khi deploy lên cloud
$dbHost = getenv('DB_HOST') ?: '127.0.0.1';
$dbUser = getenv('DB_USER') ?: 'root';
$dbPass = getenv('DB_PASS') ?: '';
$dbName = getenv('DB_NAME') ?: 'banhang';
$dbPort = getenv('DB_PORT') ?: 3306;
$cloudSqlConnectionName = getenv('CLOUD_SQL_CONNECTION_NAME');

if (!empty($cloudSqlConnectionName)) {
    // Khi dùng Cloud SQL trên App Engine
    $conn = mysqli_connect(null, $dbUser, $dbPass, $dbName, null, '/cloudsql/' . $cloudSqlConnectionName);
} else {
    $conn = mysqli_connect($dbHost, $dbUser, $dbPass, $dbName, $dbPort);
}

if (!$conn) {
    $error = "❌ Lỗi kết nối CSDL:\n";
    $error .= "- Lỗi: " . mysqli_connect_error() . "\n";
    $error .= "- Server: $dbHost:$dbPort\n";
    $error .= "- User: $dbUser\n";
    $error .= "- Database: $dbName\n\n";
    $error .= "💡 Giải pháp:\n";
    $error .= "1. Kiểm tra MySQL/Cloud SQL đang chạy\n";
    $error .= "2. Kiểm tra biến môi trường DB_HOST, DB_USER, DB_PASS, DB_NAME\n";
    $error .= "3. Nếu dùng XAMPP, mở MySQL trong XAMPP Control Panel\n";
    die("<pre>$error</pre>");
}

mysqli_set_charset($conn, 'utf8mb4');

// Định nghĩa đường dẫn gốc tự động theo host đang chạy
$scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$base_url = $scheme . '://' . $_SERVER['HTTP_HOST'] . '/';
define('BASE_URL', $base_url);

// ==================== THÊM DÒNG NÀY ĐỂ SỬA LỖI LOGO VÀ MENU ====================
?>