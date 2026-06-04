a<?php
include '../config/connect.php'; // kết nối CSDL

// BƯỚC 1: CẬP NHẬT LOGIC PHP (Thêm kiểm tra 'mk_confirm')
if (isset($_POST['tk']) && isset($_POST['email']) && isset($_POST['mk']) && isset($_POST['mk_confirm'])) {
    // Gán giá trị từ form vào biến
    $tk = $_POST['tk'];
    $email = $_POST['email'];
    $mk = $_POST['mk'];
    $mk_confirm = $_POST['mk_confirm']; // Lấy mật khẩu xác nhận
    $vaitro = 'user';

    // KIỂM TRA MẬT KHẨU KHỚP NHAU
    if ($mk !== $mk_confirm) {
        // Nếu không khớp, thông báo lỗi và dừng xử lý
        echo "<script>alert('Lỗi: Mật khẩu xác nhận không khớp!');</script>";
    } else {    
        // KIỂM TRA EMAIL ĐÃ TỒN TẠI
        $sql_kiemtra = "SELECT * FROM user WHERE EMAIL = '$email'";
        $ketqua = mysqli_query($conn, $sql_kiemtra);

        if (mysqli_num_rows($ketqua) > 0) {
            echo "<script>alert('Lỗi: Email đã tồn tại!');</script>";
        } else {
            // Tạo mã ID_USER dạng U001, U002,...
            $sql_max = "SELECT MAX(ID_USER) AS max_id FROM user";
            $ketqua_max = mysqli_query($conn, $sql_max);
            $dong = mysqli_fetch_assoc($ketqua_max);

            if ($dong['max_id']) {
                $so = (int)substr($dong['max_id'], 1);
                $so_moi = $so + 1;
            } else {
                $so_moi = 1;
            }
            $id_user = "U" . str_pad($so_moi, 3, "0", STR_PAD_LEFT);
            
            // LƯU Ý: Bạn nên mã hóa mật khẩu ($mk) trước khi lưu vào CSDL bằng hàm như password_hash()
            
            // Câu lệnh chèn dữ liệu
            $sql_dangky = "INSERT INTO user (ID_USER, TK, MK, EMAIL, VAITRO)
                           VALUES ('$id_user', '$tk', '$mk', '$email', '$vaitro')";

            if (mysqli_query($conn, $sql_dangky)) {
                echo "<script>alert('Đăng ký thành công!'); window.location.href='DangNhap.php';</script>";
            } else {
                echo "<script>alert('Lỗi: " . mysqli_error($conn) . "');</script>";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký tài khoản</title>
    <link rel="stylesheet" href="../assets/css/dangky.css"> 
    <link rel="stylesheet" href="../assets/icon/css/font-awesome.css">
</head>
<body>
    <?php include("../includes/banner.php") ?>

    <div class="content_dk">
        <div class="formdk">
            <p class="tddk">THÔNG TIN ĐĂNG KÝ</p>
            <form class="frm_dk" method="POST" action="">
                <label class="lbldk">Tài khoản <span class="required">*</span></label>
                <input type="text" class="txt_dk" name="tk" placeholder="Tên đăng nhập" required>

                <label class="lbldk">Email <span class="required">*</span></label>
                <input type="email" class="txt_dk" name="email" placeholder="Email" required>

                <label class="lbldk">Mật khẩu <span class="required">*</span></label>
                <input type="password" class="txt_dk" name="mk" placeholder="Mật khẩu" required>

                <label class="lbldk">Xác nhận mật khẩu <span class="required">*</span></label>
                <input type="password" class="txt_dk" name="mk_confirm" placeholder="Xác nhận mật khẩu" required>

                <button type="submit" class="btndk">ĐĂNG KÝ</button>
            </form>
            
            <p class="dk_link">
                Bạn đã có tài khoản? Đăng nhập <a href="DangNhap.php">tại đây</a>
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