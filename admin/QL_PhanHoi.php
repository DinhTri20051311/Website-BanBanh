<?php
include("../config/connect.php");
if (session_status() == PHP_SESSION_NONE) session_start();

// ✅ Kiểm tra quyền admin
if (!isset($_SESSION['vaitro']) || $_SESSION['vaitro'] !== 'admin') {
    header("Location: ../pages/dangnhap.php");
    exit();
}

// ✅ ADMIN GỬI PHẢN HỒI TRẢ LỜI USER
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['reply_id'])) {
    $reply_id = intval($_POST['reply_id']); // ép kiểu an toàn
    $traloi = trim($_POST['traloi']);

    if (!empty($traloi)) {
        // ❌ Không có cột NGAYPHANHOI, nên bỏ ra
        $sql = "UPDATE phanhoi SET TRALOI = ?, TRANGTHAI='Đã phản hồi' WHERE ID_PHANHOI = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $traloi, $reply_id);
        $stmt->execute();
        echo "<script>alert('Đã gửi phản hồi cho khách hàng!');window.location='".$_SERVER['PHP_SELF']."';</script>";
        exit();
    } else {
        echo "<script>alert('Vui lòng nhập nội dung phản hồi.');</script>";
    }
}

// ✅ Lấy toàn bộ phản hồi từ user
$sql = "SELECT p.*, u.TK, u.EMAIL
        FROM phanhoi p
        LEFT JOIN user u ON p.ID_USER = u.ID_USER
        ORDER BY p.NGAYGUI DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý phản hồi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
    <h2 class="mb-4 text-center">📩 Quản lý phản hồi khách hàng</h2>

    <table class="table table-bordered table-hover align-middle bg-white">
        <thead class="table-dark text-center">
            <tr>
                <th>ID</th>
                <th>Người gửi</th>
                <th>Email</th>
                <th>Nội dung</th>
                <th>Phản hồi</th>
                <th>Ngày gửi</th>
                <th>Trạng thái</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
        <?php if($result && $result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['ID_PHANHOI'] ?></td>
                    <td><?= htmlspecialchars($row['TK'] ?? 'Ẩn danh') ?></td>
                    <td><?= htmlspecialchars($row['EMAIL'] ?? '') ?></td>
                    <td><?= nl2br(htmlspecialchars($row['NOIDUNG'])) ?></td>
                    <td><?= $row['TRALOI'] ? nl2br(htmlspecialchars($row['TRALOI'])) : '<i>Chưa trả lời</i>' ?></td>
                    <td><?= $row['NGAYGUI'] ?></td>
                    <td class="text-center"><?= htmlspecialchars($row['TRANGTHAI']) ?></td>
                    <td class="text-center">
                        <form method="POST" style="display:inline-block;">
                            <input type="hidden" name="reply_id" value="<?= $row['ID_PHANHOI'] ?>">
                            <textarea name="traloi" class="form-control mb-2" rows="2" placeholder="Nhập phản hồi..."></textarea>
                            <button type="submit" class="btn btn-primary btn-sm">Gửi phản hồi</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="8" class="text-center">Chưa có phản hồi nào.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>
</body>
</html>
