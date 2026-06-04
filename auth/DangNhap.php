<?php
session_start();
include '../config/connect.php'; // kết nối CSDL

// Xử lý đăng nhập
if (isset($_POST['tk']) && isset($_POST['mk'])) {
    $tk = $_POST['tk'];
    $mk = $_POST['mk'];

    // Truy vấn kiểm tra tài khoản
    $sql = "SELECT * FROM user WHERE TK = '$tk' AND MK = '$mk'";
    $result = mysqli_query($conn, $sql);
    $user = mysqli_fetch_assoc($result);

    if ($user) {
        // ✅ Gán đầy đủ thông tin vào session
        $_SESSION['user_id'] = $user['ID_USER']; // Dùng cho các trang như thanh toán
        $_SESSION['tk'] = $user['TK'];           // Dùng để hiển thị tên đăng nhập
        $_SESSION['vaitro'] = $user['VAITRO'];   // Dùng để phân quyền

        // ✅ Điều hướng theo vai trò
        if ($user['VAITRO'] == 'admin') {
        header("Location: ../admin/admin.php");
        exit();
    } else {
        header("Location: ../index.php");
        exit();
}
    } else {
        echo "<script>alert('Sai thông tin đăng nhập!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập</title>
    <link rel="stylesheet" href="../assets/css/dangnhap.css">  
    <link rel="stylesheet" href="../assets/icon/css/font-awesome.css">  
</head>
<body>
    <?php include("../includes/banner.php") ?>
    <div class="content_dn">
        <div class="formdn">
            <p class="tddn">Đăng nhập</p>
            <p class="nddn">Vui lòng nhập đầy đủ thông tin để đăng nhập</p>
            <form method="POST" action="" class="frm_dn">
                <label for="tk" class="lbldn">Tên đăng nhập</label>
                <input type="text" class="txt_mail" name="tk" placeholder="Tên đăng nhập" required>

                <label for="mk" class="lblmk">Mật khẩu</label>
                <input type="password" class="txt_mk" name="mk" placeholder="Mật khẩu" required>

                <button type="submit" class="btndn">Đăng nhập</button>
            </form>

            <p class="nd_linkdk">
                Bạn chưa có tài khoản
                <a href="DangKy.php" style="color: red; text-decoration: underline;">Đăng ký tại đây.</a>
            </p>
            <p class="nd_linkmk">
                Bạn quên mật khẩu
                <a href="#" style="color: red; text-decoration: underline;">Lấy lại tại đây</a>
            </p>
            <div class="dn_khac">hoặc đăng nhập bằng</div>
            <div class="dn_social">
                <a href="#" class="btn_fb">
                    <i class="fa fa-facebook" aria-hidden="true"></i>
                    Facebook
                </a>
                <a href="#" class="btn_gg">
                    <i class="fa fa-google" aria-hidden="true"></i>
                    Google
                </a>
            </div>
        </div>
    </div>
    <?php include("../includes/footer.php") ?>
</body>
</html>