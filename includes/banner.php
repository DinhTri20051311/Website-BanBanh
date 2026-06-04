<?php
if (!isset($_SESSION)) {
    session_start();
}

// XỬ LÝ ĐĂNG XUẤT
if (isset($_POST['logout'])) {
    session_destroy();
    header("Location: /WEBSITEBANBANH/index.php");
    exit();
}
?>
<!-- Bootstrap CSS -->

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<nav class="navbar navbar-expand-lg navbar-light bg-warning py-3">
    <div class="container">
        <!-- Logo -->
        <a class="navbar-brand d-flex align-items-center fw-bold text-dark" href="/WEBSITEBANBANH/index.php">
            <img src="/WEBSITEBANBANH/assets/image/logo/danisha-logo.png" 
                 alt="Danisa Cake" 
                 class="me-2" 
                 style="height: 48px;">
            <span class="fs-4">Danisa Cake</span>
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav mx-auto">
                <li class="nav-item"><a class="nav-link fw-semibold" href="/WEBSITEBANBANH/index.php">Trang Chủ</a></li>
                <li class="nav-item"><a class="nav-link fw-semibold" href="/WEBSITEBANBANH/pages/SanPham.php">Sản Phẩm</a></li>
                <li class="nav-item"><a class="nav-link fw-semibold" href="/WEBSITEBANBANH/pages/GioiThieu.php">Giới Thiệu</a></li>
                <li class="nav-item"><a class="nav-link fw-semibold" href="/WEBSITEBANBANH/pages/LienHe.php">Liên Hệ</a></li>
                <li class="nav-item"><a class="nav-link fw-semibold" href="/WEBSITEBANBANH/pages/CamNang.php">Cẩm Nang</a></li>
            </ul>

            <div class="d-flex align-items-center gap-4">
                <p class="mb-0 fw-semibold text-dark d-none d-lg-block">
                    Hotline: <span class="text-danger">1800 6750</span>
                </p>

                <!-- Tài khoản -->
                <?php if (isset($_SESSION['tk'])): ?>
                    <div class="dropdown">
                        <button class="btn btn-outline-dark dropdown-toggle d-flex align-items-center gap-1" type="button" data-bs-toggle="dropdown">
                            <i class="fa fa-user"></i> <?= htmlspecialchars($_SESSION['tk']) ?>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <form method="POST" class="px-3 py-1">
                                    <button type="submit" name="logout" class="btn btn-link text-danger p-0">Đăng xuất</button>
                                </form>
                            </li>
                        </ul>
                    </div>
                <?php else: ?>
                    <div class="dropdown">
                        <button class="btn btn-outline-dark dropdown-toggle d-flex align-items-center gap-1" type="button" data-bs-toggle="dropdown">
                            <i class="fa fa-user"></i> Tài khoản
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="/WEBSITEBANBANH/auth/DangNhap.php">Đăng nhập</a></li>
                            <li><a class="dropdown-item" href="/WEBSITEBANBANH/auth/DangKy.php">Đăng ký</a></li>
                        </ul>
                    </div>
                <?php endif; ?>

                <a href="#" class="text-dark fs-5"><i class="fa fa-search"></i></a>
                <a href="/WEBSITEBANBANH/pages/giohang.php" class="text-dark fs-5 position-relative">
                    <i class="fa fa-shopping-cart"></i>
                </a>
            </div>
        </div>
    </div>
</nav>