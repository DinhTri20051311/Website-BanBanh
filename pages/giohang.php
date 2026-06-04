<?php
session_start();
include("../config/connect.php");
include("../includes/banner.php");

// ✅ Kiểm tra đăng nhập
$logged_in = isset($_SESSION['tk']); // đổi lại vì hệ thống của bạn dùng $_SESSION['tk']
$user_tk = $logged_in ? $_SESSION['tk'] : null;

// ✅ Lấy ID_USER của người đăng nhập (nếu có)
$user_id = null;
if ($logged_in) {
    $result = $conn->query("SELECT ID_USER FROM user WHERE TK = '$user_tk' LIMIT 1");
    if ($result && $result->num_rows > 0) {
        $user_id = $result->fetch_assoc()['ID_USER'];
        $_SESSION['user_id'] = $user_id; // lưu lại để trang thanhtoan.php dùng
    }
}

// ✅ Xử lý xóa sản phẩm khỏi giỏ
if (isset($_GET['remove'])) {
    $remove_id = $_GET['remove'];
    unset($_SESSION['cart'][$remove_id]);
    header("Location: giohang.php");
    exit();
}

// ✅ Xử lý hủy / xóa đơn hàng
if (isset($_GET['cancel_order']) && $logged_in) {
    $cancel_id = $_GET['cancel_order'];
    $check = $conn->query("SELECT TINHTRANG FROM donhang WHERE ID_DH = '$cancel_id' AND ID_USER = '$user_id'");
    if ($check && $check->num_rows > 0) {
        $status = $check->fetch_assoc()['TINHTRANG'];
        if ($status === 'Chờ xác nhận') {
            $conn->query("UPDATE donhang SET TINHTRANG = 'Đã hủy' WHERE ID_DH = '$cancel_id' AND ID_USER = '$user_id'");
        } elseif ($status === 'Đã hủy') {
            $conn->query("DELETE FROM ctdh WHERE ID_DH = '$cancel_id'");
            $conn->query("DELETE FROM donhang WHERE ID_DH = '$cancel_id'");
        }
    }
    header("Location: giohang.php?view=orders");
    exit();
}

// ✅ Lấy danh sách đơn hàng của user
$orders = [];
if ($logged_in && $user_id) {
    $sql = "SELECT * FROM donhang WHERE ID_USER = '$user_id' ORDER BY NGAYDAT DESC";
    $orders = $conn->query($sql);
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Giỏ hàng</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .hidden { display: none; }
        .toggle-btn { min-width: 180px; }
        .table th { background-color: #343a40; color: #fff; }
        .card { border-radius: 16px; box-shadow: 0 4px 12px rgba(0,0,0,0.08); }
        img.product-img { border-radius: 12px; }
    </style>
</head>
<body>
<div class="container py-5">
    <div class="text-center mb-5">
        <h1 class="fw-bold text-primary">🛒 Giỏ hàng của bạn</h1>
        <p class="text-muted">Quản lý sản phẩm và đơn hàng của bạn dễ dàng hơn bao giờ hết</p>
    </div>

    <!-- Nút chuyển -->
    <div class="d-flex justify-content-center mb-4 gap-3">
        <?php if ($logged_in): ?>
            <button id="btnGioHang" class="btn btn-outline-primary toggle-btn">🛍️ Xem giỏ hàng</button>
            <button id="btnDonHang" class="btn btn-outline-success toggle-btn">📦 Đơn hàng của tôi</button>
        <?php endif; ?>
    </div>

    <!-- 🛍️ GIỎ HÀNG -->
    <div id="giohangSection" <?= isset($_GET['view']) && $_GET['view'] === 'orders' ? 'class="hidden"' : '' ?>>
        <div class="card p-4">
            <?php if (isset($_SESSION['cart']) && count($_SESSION['cart']) > 0): ?>
                <div class="table-responsive">
                    <table class="table align-middle text-center">
                        <thead>
                            <tr>
                                <th>Hình ảnh</th>
                                <th>Tên sản phẩm</th>
                                <th>Giá</th>
                                <th>Số lượng</th>
                                <th>Tổng tiền</th>
                                <th>Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $tongtien = 0;
                            foreach ($_SESSION['cart'] as $id => $sp):
                                $name = $sp['tensp'] ?? $sp['name'] ?? '';
                                $price = $sp['gia'] ?? $sp['price'] ?? 0;
                                $quantity = $sp['soluong'] ?? $sp['quantity'] ?? 1;
                                $image = $sp['hinhanh'] ?? $sp['image'] ?? '';
                                $thanhtien = $price * $quantity;
                                $tongtien += $thanhtien;
                            ?>
                            <tr>
                                <!-- ✅ Sửa đường dẫn ảnh đúng -->
                                <td>
                                    <img src="../assets/image/banh/<?= htmlspecialchars($image) ?>" 
                                         alt="<?= htmlspecialchars($name) ?>" 
                                         class="product-img" style="width: 70px;">
                                </td>
                                <td class="fw-semibold"><?= htmlspecialchars($name) ?></td>
                                <td><?= number_format($price, 0, ',', '.') ?>₫</td>
                                <td><?= (int)$quantity ?></td>
                                <td class="text-success fw-bold"><?= number_format($thanhtien, 0, ',', '.') ?>₫</td>
                                <td>
                                    <a href="giohang.php?remove=<?= urlencode($id) ?>" class="btn btn-sm btn-outline-danger">
                                        🗑 Xóa
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <div class="text-end mt-4">
                    <h4 class="fw-bold text-success">Tổng cộng: <?= number_format($tongtien, 0, ',', '.') ?>₫</h4>
                    <a href="../modules/thanhtoan.php" class="btn btn-primary mt-3 px-4 py-2">Thanh toán ngay</a>
                </div>
            <?php else: ?>
                <div class="alert alert-warning text-center py-4">
                    <strong>Giỏ hàng của bạn đang trống 😢</strong><br>
                    <a href="SanPham.php" class="btn btn-outline-primary mt-3">← Quay lại mua sắm</a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- 📦 ĐƠN HÀNG -->
    <div id="donhangSection" <?= isset($_GET['view']) && $_GET['view'] === 'orders' ? '' : 'class="hidden"' ?>>
        <div class="card p-4">
            <h3 class="text-success text-center mb-4 fw-bold">📦 Đơn hàng của tôi</h3>
            <?php if ($logged_in && $orders && $orders->num_rows > 0): ?>
                <div class="table-responsive">
                    <table class="table align-middle text-center">
                        <thead>
                            <tr>
                                <th>Mã đơn</th>
                                <th>Ngày đặt</th>
                                <th>Người nhận</th>
                                <th>SĐT</th>
                                <th>Địa chỉ</th>
                                <th>Thành tiền</th>
                                <th>Tình trạng</th>
                                <th>Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $orders->fetch_assoc()): 
                                $status = $row['TINHTRANG'];
                                $color = match($status) {
                                    'Chờ xác nhận' => 'warning',
                                    'Đang giao' => 'info',
                                    'Hoàn thành' => 'success',
                                    'Đã hủy' => 'danger',
                                    default => 'secondary',
                                };
                            ?>
                            <tr>
                                <td><?= $row['ID_DH'] ?></td>
                                <td><?= date('d/m/Y H:i', strtotime($row['NGAYDAT'])) ?></td>
                                <td><?= htmlspecialchars($row['TENNGUOINHAN']) ?></td>
                                <td><?= htmlspecialchars($row['SDT']) ?></td>
                                <td><?= htmlspecialchars($row['DIACHI']) ?></td>
                                <td class="fw-bold text-success"><?= number_format($row['THANHTIEN'], 0, ',', '.') ?>₫</td>
                                <td><span class="badge bg-<?= $color ?>"><?= htmlspecialchars($status) ?></span></td>
                                <td>
                                    <?php if (in_array($status, ['Chờ xác nhận', 'Đã hủy'])): ?>
                                        <a href="?cancel_order=<?= urlencode($row['ID_DH']) ?>" class="btn btn-sm btn-outline-danger"
                                           onclick="return confirm('Bạn có chắc muốn <?= $status === 'Đã hủy' ? 'xóa' : 'hủy' ?> đơn hàng này không?');">
                                           <?= $status === 'Đã hủy' ? 'Xóa đơn' : 'Hủy đơn' ?>
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted">Không thể hủy</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="alert alert-info text-center py-4">
                    <strong>Bạn chưa có đơn hàng nào.</strong>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include("../includes/footer.php"); ?>

<script>
const giohangSection = document.getElementById("giohangSection");
const donhangSection = document.getElementById("donhangSection");
const btnGioHang = document.getElementById("btnGioHang");
const btnDonHang = document.getElementById("btnDonHang");

if (btnGioHang && btnDonHang) {
    btnDonHang.addEventListener("click", () => {
        giohangSection.classList.add("hidden");
        donhangSection.classList.remove("hidden");
        btnDonHang.classList.add("active");
        btnGioHang.classList.remove("active");
        history.replaceState(null, "", "?view=orders");
    });
    btnGioHang.addEventListener("click", () => {
        donhangSection.classList.add("hidden");
        giohangSection.classList.remove("hidden");
        btnGioHang.classList.add("active");
        btnDonHang.classList.remove("active");
        history.replaceState(null, "", "giohang.php");
    });
}
</script>
</body>
</html>
