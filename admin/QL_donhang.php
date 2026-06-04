<?php
// ...existing code...
include("../config/connect.php");

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 🔒 Chỉ cho phép admin
if (!isset($_SESSION['vaitro']) || $_SESSION['vaitro'] !== 'admin') {
    echo '<div class="alert alert-danger text-center mt-5">
            Bạn không có quyền truy cập trang này!
          </div>';
    exit();
}

/*
  ✅ Xử lý hành động cập nhật trạng thái / hủy đơn
  Flow trạng thái: Chờ xác nhận -> Đã xác nhận -> Đang giao hàng -> Hoàn thành
*/
if (isset($_GET['update_status']) && isset($_GET['id'])) {
    $id = $conn->real_escape_string($_GET['id']);

    $rs = $conn->query("SELECT TINHTRANG FROM donhang WHERE ID_DH = '$id' LIMIT 1");
    if ($rs && $rs->num_rows) {
        $r = $rs->fetch_assoc();
        $current = $r['TINHTRANG'];

        if ($current === 'Chờ xác nhận') {
            $next = 'Đã xác nhận';
        } elseif ($current === 'Đã xác nhận') {
            $next = 'Đang giao hàng';
        } elseif ($current === 'Đang giao hàng') {
            $next = 'Hoàn thành';
        } else {
            $next = $current;
        }

        $conn->query("UPDATE donhang SET TINHTRANG = '$next' WHERE ID_DH = '$id'");
    }

    header("Location: admin.php?page=QL_donhang");
    exit();
}

if (isset($_GET['cancel_order']) && isset($_GET['id'])) {
    $id = $conn->real_escape_string($_GET['id']);
    // Chỉ được hủy khi chưa hoàn thành/hủy
    $conn->query("UPDATE donhang SET TINHTRANG = 'Đã hủy' 
                  WHERE ID_DH = '$id' 
                  AND TINHTRANG NOT IN ('Hoàn thành', 'Đã hủy')");
    header("Location: admin.php?page=QL_donhang");
    exit();
}

// ✅ Lấy danh sách đơn hàng + thông tin người dùng
$sql = "SELECT dh.*, u.TK 
        FROM donhang dh 
        JOIN user u ON dh.ID_USER = u.ID_USER 
        ORDER BY dh.NGAYDAT DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý đơn hàng</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container py-5">
    <h1 class="text-center text-primary mb-4">📦 Quản lý đơn hàng</h1>

    <table class="table table-bordered table-striped align-middle text-center">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Người đặt</th>
                <th>Tên người nhận</th>
                <th>SĐT</th>
                <th>Địa chỉ</th>
                <th>Thành tiền</th>
                <th>Ngày đặt</th>
                <th>Tình trạng</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row['ID_DH']) ?></td>
                <td><?= htmlspecialchars($row['TK']) ?></td>
                <td><?= htmlspecialchars($row['TENNGUOINHAN']) ?></td>
                <td><?= htmlspecialchars($row['SDT']) ?></td>
                <td><?= htmlspecialchars($row['DIACHI']) ?></td>
                <td><?= number_format($row['THANHTIEN'], 0, ',', '.') ?>₫</td>
                <td><?= htmlspecialchars($row['NGAYDAT']) ?></td>
                <td>
                    <?php 
                        $tt = $row['TINHTRANG'];
                        if ($tt == 'Chờ xác nhận') {
                            echo '<a href="admin.php?page=QL_donhang&update_status=1&id='.$row['ID_DH'].'" class="btn btn-warning btn-sm">Chờ xác nhận</a>';
                        } elseif ($tt == 'Đã xác nhận') {
                            echo '<a href="admin.php?page=QL_donhang&update_status=1&id='.$row['ID_DH'].'" class="btn btn-primary btn-sm">Đã xác nhận</a>';
                        } elseif ($tt == 'Đang giao hàng') {
                            echo '<a href="admin.php?page=QL_donhang&update_status=1&id='.$row['ID_DH'].'" class="btn btn-info btn-sm">Đang giao hàng</a>';
                        } elseif ($tt == 'Hoàn thành') {
                            echo '<span class="badge bg-success">Hoàn thành</span>';
                        } elseif ($tt == 'Đã hủy') {
                            echo '<span class="badge bg-danger">Đã hủy</span>';
                        } else {
                            echo htmlspecialchars($tt);
                        }
                    ?>
                </td>
                <td>
                    <?php if ($tt == 'Chờ xác nhận' || $tt == 'Đã xác nhận' || $tt == 'Đang giao hàng'): ?>
                        <a href="admin.php?page=QL_donhang&cancel_order=1&id=<?= urlencode($row['ID_DH']) ?>"
                           class="btn btn-danger btn-sm"
                           onclick="return confirm('Bạn có chắc muốn hủy đơn hàng này không?');">
                           Hủy
                        </a>
                    <?php else: ?>
                        <span class="text-muted">-</span>
                    <?php endif; ?>

                    <!-- Nút In hóa đơn: chỉ hiển thị khi trạng thái = 'Đã xác nhận' trở lên -->
                    <?php if (in_array($tt, ['Đã xác nhận','Đang giao hàng','Hoàn thành'])): ?>
                        <a href="./print_invoice.php?id=<?= urlencode($row['ID_DH']) ?>" target="_blank" class="btn btn-primary btn-sm ms-1">
                            In hóa đơn
                        </a>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
</body>
</html>
