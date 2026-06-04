<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

// ✅ Kết nối CSDL (dùng __DIR__ để lên 1 cấp)
include __DIR__ . "/../config/connect.php";

// ✅ Khi bấm MUA NGAY
if (isset($_POST['add_to_cart'])) {
    $id = $_POST['id_sp'];
    $name = $_POST['tensanpham'];
    $price = $_POST['gia'];
    $image = $_POST['hinhanh'];
    $quantity = 1;

    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    if (isset($_SESSION['cart'][$id])) {
        $_SESSION['cart'][$id]['quantity'] += 1;
    } else {
        $_SESSION['cart'][$id] = [
            'name' => $name,
            'price' => $price,
            'image' => $image,
            'quantity' => $quantity
        ];
    }

    // Chuyển về giỏ hàng (cùng thư mục pages)
    header("Location: giohang.php");
    exit();
}

// ✅ Lấy danh sách sản phẩm
$sql = "SELECT * FROM sanpham";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sản phẩm - Danisa Cake</title>
    
    <link rel="stylesheet" href="/Websitebanbanh/assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="/Websitebanbanh/assets/css/style.css">
    <link rel="stylesheet" href="/Websitebanbanh/assets/icon/css/font-awesome.css">
</head>
<body>

<?php include __DIR__ . "/../includes/banner.php"; ?>

<div class="container mt-5">
    <h2 class="text-center mb-4 text-warning fw-bold">Danh sách sản phẩm</h2>
    <div class="row">
        <?php while ($row = mysqli_fetch_assoc($result)) { ?>
        <div class="col-md-3 mb-4" id="sp_<?php echo htmlspecialchars($row['ID_SP']); ?>">
            <div class="card h-100 shadow-sm">
                <img src="../assets/image/banh/<?php echo htmlspecialchars($row['HINHANH']); ?>"
                     class="card-img-top"
                     alt="<?php echo htmlspecialchars($row['TENSANPHAM']); ?>"
                     style="height: 220px; object-fit: cover;">

                <div class="card-body text-center">
                    <h5 class="card-title fw-semibold"><?php echo htmlspecialchars($row['TENSANPHAM']); ?></h5>
                    <p class="text-danger fw-bold fs-5">
                        <?php echo number_format($row['GIA'], 0, ',', '.'); ?> ₫
                    </p>

                    <form method="POST">
                        <input type="hidden" name="id_sp" value="<?php echo $row['ID_SP']; ?>">
                        <input type="hidden" name="tensanpham" value="<?php echo htmlspecialchars($row['TENSANPHAM']); ?>">
                        <input type="hidden" name="gia" value="<?php echo $row['GIA']; ?>">
                        <input type="hidden" name="hinhanh" value="<?php echo htmlspecialchars($row['HINHANH']); ?>">
                        <button type="submit" name="add_to_cart" class="btn btn-warning w-100">
                            🛒 Mua ngay
                        </button>
                    </form>
                </div>
            </div>
        </div>
        <?php } ?>
    </div>
</div>

<?php include __DIR__ . "/../includes/footer.php"; ?>
</body>
</html>