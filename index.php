<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>
<?php
session_start();
include("config/connect.php");  // Đã sửa: bỏ ./ vì file ở root

// ✅ Xử lý thêm sản phẩm vào giỏ
if (isset($_GET['add'])) {
    $id = $_GET['add'];
    $sql = "SELECT * FROM sanpham WHERE ID_SP = '$id'";
    $result = mysqli_query($conn, $sql);
    $sp = mysqli_fetch_assoc($result);

    if ($sp) {
        if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];

        if (isset($_SESSION['cart'][$id])) {
            $_SESSION['cart'][$id]['quantity'] += 1;
        } else {
            $_SESSION['cart'][$id] = [
                'name' => $sp['TENSANPHAM'],
                'price' => $sp['GIA'],
                'quantity' => 1,
                'image' => $sp['HINHANH']
            ];
        }
    }

       header("Location: pages/giohang.php");  // Đã sửa: giohang.php nằm trong thư mục pages/
    exit();
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Danisa Cake - Bánh kem tươi, bánh ngọt & giao hàng TP.HCM</title>
  <meta name="description" content="Danisa Cake - bánh kem tươi, cupcake, bánh sinh nhật chất lượng, giao hàng nhanh tại TP.HCM. Đặt bánh ngon, đẹp mắt và phục vụ tận tâm.">
  <meta name="keywords" content="bánh kem, cupcake, bánh sinh nhật, Danisa Cake, bánh ngọt, giao hàng TP.HCM, đặt bánh online">
  <meta name="author" content="Danisa Cake">
  <meta name="robots" content="index, follow">
  <link rel="canonical" href="https://danisacake.net.vn/">
  <meta property="og:site_name" content="Danisa Cake">
  <meta property="og:title" content="Danisa Cake - Bánh kem tươi, bánh ngọt & giao hàng TP.HCM">
  <meta property="og:description" content="Danisa Cake mang đến bánh kem và bánh ngọt chất lượng, đa dạng phong cách, giao hàng tận nơi TP.HCM.">
  <meta property="og:type" content="website">
  <meta property="og:url" content="https://danisacake.net.vn/">
  <meta property="og:image" content="https://danisacake.net.vn/assets/image/logo/danisha-logo.png">
  <meta property="og:image:alt" content="Logo Danisa Cake">
  <meta name="twitter:card" content="summary_large_image">
  <meta name="twitter:title" content="Danisa Cake - Bánh kem tươi, bánh ngọt & giao hàng TP.HCM">
  <meta name="twitter:description" content="Đặt bánh kem tươi và bánh ngọt tại Danisa Cake. Giao hàng nhanh, chất lượng, thiết kế đẹp mắt.">
  <meta name="twitter:image" content="https://danisacake.net.vn/assets/image/logo/danisha-logo.png">
  <meta name="theme-color" content="#d2691e">

  <!-- Font Awesome -->
  <link rel="stylesheet" href="assets/icon/css/font-awesome.css">  <!-- Đã sửa: bỏ ../ -->
  
  <!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">  
  <style>
    .carousel-item img {
      height: 400px;
      object-fit: cover;
    }
    .card {
      border-radius: 12px;
      transition: transform 0.2s ease-in-out;
    }
    .card:hover {
      transform: translateY(-5px);
      box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }
  </style>
</head>
<body>
  <?php include("includes/banner.php"); ?>  <!-- Đã sửa: đúng đường dẫn từ root -->

  <!-- Banner -->
  <div id="bannerCarousel" class="carousel slide" data-bs-ride="carousel">
      <div class="carousel-inner">
          <div class="carousel-item active">
              <img src="assets/image/banh/banh1.jpg" class="d-block w-100" alt="Bánh kem">  <!-- Đã sửa -->
              <div class="carousel-caption d-none d-md-block">
                  <h5>Bánh Kem Tươi Ngon</h5>
                  <p>Thơm lừng, mềm mịn, ngọt ngào từng lớp bánh.</p>
              </div>
          </div>
          <div class="carousel-item">
              <img src="assets/image/banh/banh2.png" class="d-block w-100" alt="Cupcake">  <!-- Đã sửa -->
              <div class="carousel-caption d-none d-md-block">
                  <h5>Cupcake Đáng Yêu</h5>
                  <p>Trang trí bắt mắt, hương vị tuyệt vời cho mọi lứa tuổi.</p>
              </div>
          </div>
          <div class="carousel-item">
              <img src="assets/image/banh/banh3.png" class="d-block w-100" alt="Bánh mì">  <!-- Đã sửa -->
              <div class="carousel-caption d-none d-md-block">
                  <h5>Bánh Kem Dâu</h5>
                  <p>Ngon ngọt, thơm mùi dâu, thích hợp cho bữa sáng.</p>
              </div>
          </div>
      </div>
      <button class="carousel-control-prev" type="button" data-bs-target="#bannerCarousel" data-bs-slide="prev">
          <span class="carousel-control-prev-icon"></span>
      </button>
      <button class="carousel-control-next" type="button" data-bs-target="#bannerCarousel" data-bs-slide="next">
          <span class="carousel-control-next-icon"></span>
      </button>
  </div>

  <!-- Ưu điểm nổi bật -->
  <div class="container mt-5">
    <div class="row text-center">
      <div class="col-md-4 mb-4">
        <div class="p-4 border rounded shadow-sm bg-light">
          <h4 class="text-warning fw-bold">Duy Nhất</h4>
          <p class="mt-2">Tại Việt Nam, chúng tôi mang đến trải nghiệm bánh ngọt độc đáo, không nơi nào có.</p>
        </div>
      </div>
      <div class="col-md-4 mb-4">
        <div class="p-4 border rounded shadow-sm bg-light">
          <h4 class="text-warning fw-bold">Tận Tâm</h4>
          <p class="mt-2">Phục vụ khách hàng bằng cả trái tim, từng chiếc bánh là một lời tri ân.</p>
        </div>
      </div>
      <div class="col-md-4 mb-4">
        <div class="p-4 border rounded shadow-sm bg-light">
          <h4 class="text-warning fw-bold">Cam Kết</h4>
          <p class="mt-2">Chất lượng sản phẩm luôn được đặt lên hàng đầu, đảm bảo an toàn và ngon miệng.</p>
        </div>
      </div>
    </div>
  </div>

  <!-- Sản phẩm nổi bật -->
  <div class="container mt-5">
      <h2 class="text-center mb-4 text-warning fw-bold">🔥 Bánh Nổi Bật</h2>
      <div class="row">
          <?php
          $sql_hot = "SELECT * FROM sanpham WHERE HOT_SP = 1 ORDER BY ID_SP DESC LIMIT 8";
          $result_hot = $conn->query($sql_hot);

          if ($result_hot && $result_hot->num_rows > 0) {
              while ($sp = $result_hot->fetch_assoc()) {
                  echo '
                  <div class="col-md-3 mb-4">
                      <div class="card h-100 shadow-sm">
                          <img src="assets/image/banh/' . htmlspecialchars($sp['HINHANH']) . '" 
                               class="card-img-top" alt="' . htmlspecialchars($sp['TENSANPHAM']) . '" 
                               style="height:220px;object-fit:cover;">
                          <div class="card-body text-center">
                              <h5 class="card-title text-dark fw-semibold">' . htmlspecialchars($sp['TENSANPHAM']) . '</h5>
                              <p class="text-success fw-bold mb-3">' . number_format($sp['GIA'], 0, ',', '.') . '₫</p>
                              <a href="?add=' . $sp['ID_SP'] . '" class="btn btn-outline-primary w-100">🛒 Mua ngay</a>
                          </div>
                      </div>
                  </div>';
              }
          } else {
              echo '
              <div class="col-12 text-center">
                  <div class="alert alert-info">Hiện chưa có sản phẩm nổi bật nào.</div>
              </div>';
          }
          ?>
      </div>
      <div class="text-center mt-3">
          <a href="./pages/SanPham.php" class="btn btn-warning btn-lg">Xem Thêm</a>  <!-- Đã sửa: SanPham.php nằm ở root -->
      </div>
  </div>

  <!-- Tin Tức (đang comment) -->
  <!-- Nếu sau này bật lại, nhớ sửa các src ảnh thành "assets/image/..." và href thành file thực tế -->

  <?php include("includes/footer.php"); ?>  <!-- Đã sửa -->
</body>
</html>
