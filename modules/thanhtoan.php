<?php
session_start();
include("../config/connect.php");

// ✅ Kiểm tra đăng nhập
if (!isset($_SESSION['tk'])) {
    echo '<div class="container py-5 text-center">
            <div class="alert alert-danger">Bạn chưa đăng nhập. Vui lòng đăng nhập để thanh toán.</div>
            <a href="/auth/DangNhap.php" class="btn btn-primary">Đăng nhập ngay</a>
          </div>';
    exit();
}

$thongbao = "";

// ✅ Lấy ID_USER từ CSDL dựa theo tài khoản đang đăng nhập
$tk = $_SESSION['tk'];
$getUser = mysqli_query($conn, "SELECT ID_USER FROM user WHERE TK = '$tk' LIMIT 1");
$userData = mysqli_fetch_assoc($getUser);
$id_user = $userData ? $userData['ID_USER'] : null;

if (!$id_user) {
    echo '<div class="alert alert-danger text-center">Không tìm thấy thông tin người dùng. Vui lòng đăng nhập lại.</div>';
    session_destroy();
    exit();
}

// ✅ Xử lý khi người dùng bấm nút xác nhận
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ten = mysqli_real_escape_string($conn, $_POST['ten']);
    $sdt = mysqli_real_escape_string($conn, $_POST['sdt']);
    $diachi = mysqli_real_escape_string($conn, $_POST['diachi']);
    $ngaydat = date('Y-m-d H:i:s');
    $tongtien = 0;

    foreach ($_SESSION['cart'] as $sp) {
        $tongtien += $sp['price'] * $sp['quantity'];
    }

    // ✅ Thêm đơn hàng
    $sql = "INSERT INTO donhang (ID_USER, TENNGUOINHAN, SDT, DIACHI, THANHTIEN, NGAYDAT, TINHTRANG)
            VALUES ('$id_user', '$ten', '$sdt', '$diachi', '$tongtien', '$ngaydat', 'Chờ xác nhận')";
    $result = mysqli_query($conn, $sql);

    if ($result) {
        $id_dh = mysqli_insert_id($conn);

        // ✅ Ghi chi tiết sản phẩm
        foreach ($_SESSION['cart'] as $id_sp => $sp) {
            $soluong = $sp['quantity'];
            $dongia = $sp['price'];
            mysqli_query($conn, "INSERT INTO ctdh (ID_DH, ID_SP, SOLUONG, DONGIA)
                                 VALUES ('$id_dh', '$id_sp', '$soluong', '$dongia')");
        }

        unset($_SESSION['cart']);
        $thongbao = "🎉 Đặt hàng thành công! Cảm ơn bạn đã mua hàng tại Anh Hòa Bakery.";
    } else {
        $thongbao = "⚠️ Có lỗi xảy ra khi đặt hàng. Vui lòng thử lại sau!";
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Thanh toán đơn hàng</title>
    <!-- ✅ Bootstrap 5.3.2 -->
	<link href="/assets/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include("../includes/banner.php"); ?>

    <div class="container py-5">
        <h1 class="text-center text-success mb-4">🧾 Thanh toán đơn hàng</h1>

        <?php if ($thongbao): ?>
            <div class="d-flex justify-content-center align-items-center" style="min-height: 50vh;">
                <div class="alert alert-success text-center w-75">
                    <?= $thongbao ?>
                    <div class="mt-3">
                        <a href="/WEBSITEBANBANH/pages/SanPham.php" class="btn btn-primary">Tiếp tục mua sắm</a>
                    </div>
                </div>
            </div>
        <?php elseif (isset($_SESSION['cart']) && count($_SESSION['cart']) > 0): ?>
            <div class="mb-4">
                <h5>Sản phẩm đã chọn:</h5>
                <ul class="list-group">
                    <?php
                    $tongtien = 0;
                    foreach ($_SESSION['cart'] as $sp):
                        $thanhtien = $sp['price'] * $sp['quantity'];
                        $tongtien += $thanhtien;
                    ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <?= htmlspecialchars($sp['name']) ?> x <?= $sp['quantity'] ?>
                            <span><?= number_format($thanhtien, 0, ',', '.') ?>₫</span>
                        </li>
                    <?php endforeach; ?>
                </ul>
                <div class="text-end mt-2">
                    <strong>Tổng tiền: <?= number_format($tongtien, 0, ',', '.') ?>₫</strong>
                </div>
            </div>

            <form method="POST" class="border p-4 rounded bg-light shadow-sm">
                <div class="mb-3">
                    <label for="ten" class="form-label">Họ tên người nhận</label>
                    <input type="text" class="form-control" id="ten" name="ten" required>
                </div>
                <div class="mb-3">
                    <label for="sdt" class="form-label">Số điện thoại</label>
                    <input type="text" class="form-control" id="sdt" name="sdt" maxlength="11" required>
                </div>
                <div class="mb-3">
                    <label for="diachi" class="form-label">Địa chỉ nhận hàng</label>
                    <textarea class="form-control" id="diachi" name="diachi" rows="3" required></textarea>
                </div>
                <button type="submit" class="btn btn-success w-100">Xác nhận đặt hàng</button>
            </form>
        <?php else: ?>
            <div class="alert alert-warning text-center">
                Giỏ hàng của bạn đang trống 😢
            </div>
            <div class="text-center">
                <a href="/pages/SanPham.php" class="btn btn-outline-secondary">← Quay lại mua sắm</a>
            </div>
        <?php endif; ?>
    </div>

    <?php include("../includes/footer.php"); ?>
</body>
</html>
