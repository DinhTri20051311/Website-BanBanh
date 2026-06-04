<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include '../config/connect.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json; charset=utf-8');

// ===== KIỂM TRA QUYỀN ADMIN =====
if (!isset($_SESSION['vaitro']) || $_SESSION['vaitro'] !== 'admin') {
  http_response_code(403);
  echo json_encode(['success' => false, 'message' => '❌ Không có quyền truy cập']);
  exit;
}

// ===== NHẬN DỮ LIỆU =====
$input = file_get_contents('php://input');
$data = json_decode($input, true);

// Validate JSON
if (!is_array($data)) {
  http_response_code(400);
  echo json_encode(['success' => false, 'message' => '❌ JSON không hợp lệ']);
  exit;
}

$action = trim($data['action'] ?? '');
$id = trim($data['id'] ?? '');

// Validate ID
if (empty($id)) {
  http_response_code(400);
  echo json_encode(['success' => false, 'message' => '❌ ID người dùng trống']);
  exit;
}

// Escape ID
$id = mysqli_real_escape_string($conn, $id);

// ===== XỬ LÝ CẬP NHẬT =====
if ($action === 'update') {
  $allowedFields = ['TK', 'MK', 'EMAIL', 'VAITRO'];
  $updates = [];

  foreach ($allowedFields as $field) {
    if (isset($data[$field]) && $data[$field] !== '') {
      $val = mysqli_real_escape_string($conn, trim($data[$field]));
      $updates[] = "$field = '$val'";
    }
  }

  if (empty($updates)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => '❌ Không có dữ liệu cập nhật']);
    exit;
  }

  $sql = "UPDATE user SET " . implode(', ', $updates) . " WHERE ID_USER = '$id'";
  
  if (mysqli_query($conn, $sql)) {
    echo json_encode(['success' => true, 'message' => '✅ Cập nhật thành công!']);
  } else {
    http_response_code(500);
    $error = mysqli_error($conn);
    file_put_contents(__DIR__ . '/error.log', date('Y-m-d H:i:s') . " UPDATE: $error\n", FILE_APPEND);
    echo json_encode(['success' => false, 'message' => '❌ Lỗi cơ sở dữ liệu: ' . $error]);
  }
  exit;
}

// ===== XỬ LÝ XÓA =====
if ($action === 'delete') {
  try {
    // Kiểm tra user tồn tại
    $checkSql = "SELECT ID_USER FROM user WHERE ID_USER = '$id' LIMIT 1";
    $checkResult = mysqli_query($conn, $checkSql);
    
    if (!$checkResult || mysqli_num_rows($checkResult) === 0) {
      http_response_code(404);
      echo json_encode(['success' => false, 'message' => '❌ Người dùng không tồn tại']);
      exit;
    }

    // 1️⃣ Xóa chi tiết đơn hàng (ctdh)
    $sql1 = "DELETE FROM ctdh 
             WHERE ID_DH IN (SELECT ID_DH FROM donhang WHERE ID_USER = '$id')";
    if (!mysqli_query($conn, $sql1)) {
      throw new Exception('Xóa chi tiết đơn hàng thất bại: ' . mysqli_error($conn));
    }

    // 2️⃣ Xóa đơn hàng (donhang)
    $sql2 = "DELETE FROM donhang WHERE ID_USER = '$id'";
    if (!mysqli_query($conn, $sql2)) {
      throw new Exception('Xóa đơn hàng thất bại: ' . mysqli_error($conn));
    }

    // 3️⃣ Xóa phản hồi (phanhoi)
    $sql3 = "DELETE FROM phanhoi WHERE ID_USER = '$id'";
    if (!mysqli_query($conn, $sql3)) {
      throw new Exception('Xóa phản hồi thất bại: ' . mysqli_error($conn));
    }

    // 4️⃣ Xóa người dùng (user)
    $sql4 = "DELETE FROM user WHERE ID_USER = '$id'";
    if (!mysqli_query($conn, $sql4)) {
      throw new Exception('Xóa người dùng thất bại: ' . mysqli_error($conn));
    }

    http_response_code(200);
    echo json_encode(['success' => true, 'message' => '🗑️ Xóa thành công!']);

  } catch (Exception $e) {
    http_response_code(500);
    $errorMsg = $e->getMessage();
    file_put_contents(__DIR__ . '/error.log', date('Y-m-d H:i:s') . " DELETE ($id): $errorMsg\n", FILE_APPEND);
    echo json_encode(['success' => false, 'message' => '❌ Lỗi: ' . $errorMsg]);
  }
  exit;
}

// ===== HÀNH ĐỘNG KHÔNG HỢP LỆ =====
http_response_code(400);
echo json_encode(['success' => false, 'message' => '❌ Hành động không hợp lệ']);
?>
