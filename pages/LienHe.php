<?php
if (session_status() === PHP_SESSION_NONE) session_start();

// ✅ Sửa đường dẫn kết nối và include
include("../config/connect.php");
include("../includes/banner.php");

// ====== XỬ LÝ GỬI PHẢN HỒI ======
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['lh-ndlh'])) {
    $hoten   = trim($_POST['lh-hoten'] ?? '');
    $email   = trim($_POST['lh-email'] ?? '');
    $noidung = trim($_POST['lh-ndlh'] ?? '');
    $id_user = $_SESSION['user_id'] ?? null;

    if ($id_user && !empty($noidung)) {
        $sql = "INSERT INTO phanhoi (ID_USER, NOIDUNG, NGAYGUI, TRANGTHAI) 
                VALUES (?, ?, NOW(), 'Chưa phản hồi')";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $id_user, $noidung);
        if ($stmt->execute()) {
            echo "<script>alert('Phản hồi đã được gửi thành công!'); window.location.href='LienHe.php';</script>";
            exit;
        } else {
            echo "<script>alert('Lỗi khi gửi phản hồi: " . addslashes($stmt->error) . "');</script>";
        }
    } else {
        echo "<script>alert('Vui lòng đăng nhập và nhập nội dung phản hồi.');</script>";
    }
}

// ====== XỬ LÝ XÓA PHẢN HỒI ======
if (isset($_GET['delete'])) {
    $id_delete = intval($_GET['delete']);
    $id_user = $_SESSION['user_id'] ?? null;
    if ($id_user) {
        $sql = "DELETE FROM phanhoi WHERE ID_PHANHOI = ? AND ID_USER = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("is", $id_delete, $id_user);
        $stmt->execute();
        echo "<script>alert('Đã xóa phản hồi thành công!'); window.location.href='LienHe.php';</script>";
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Liên hệ - Danisa</title>

    <!-- ✅ Sửa lại đường dẫn CSS & Icon -->
    <link rel="stylesheet" href="../assets/css/lienhe.css">
    <link rel="stylesheet" href="../assets/icon/css/font-awesome.css">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="content container py-4">
    <h1 class="mb-4">Liên hệ</h1>

    <div class="lh-nd mb-5 d-flex justify-content-around flex-wrap">
        <div class="lh-khung text-center">
            <i class="fa fa-map-marker fa-2x"></i>
            <h3>Địa chỉ</h3>
            <p>Tầng 6, tòa nhà Ladeco, 266 Đội Cấn, Liễu Giai, Hà Nội</p>
        </div>
        <div class="lh-khung text-center">
            <i class="fa fa-phone fa-2x"></i>
            <h3>Số điện thoại</h3>
            <p>1800 6750</p>
        </div>
        <div class="lh-khung text-center">
            <i class="fa fa-envelope fa-2x"></i>
            <h3>Email</h3>
            <p>hello@exier@gmail.com</p>
        </div>
    </div>

    <h2>Gửi yêu cầu của bạn</h2>
    <form class="lh-form mb-5" method="POST" action="">
        <div class="lh-khungfrm">
            <div class="lh-frm">
                <label>Họ và tên</label>
                <input type="text" name="lh-hoten" placeholder="Nhập họ và tên" required>
            </div>
            <div class="lh-frm">
                <label>Email</label>
                <input type="email" name="lh-email" placeholder="Nhập email" required>
            </div>
        </div>
        <label>Nội dung</label>
        <textarea name="lh-ndlh" rows="6" placeholder="Nhập nội dung yêu cầu" required></textarea>
        <div class="lh-nut">
            <button type="submit" class="btn btn-primary mt-3">Gửi tin nhắn</button>
        </div>
    </form>

    <?php
    if (!empty($_SESSION['user_id'])) {
        $id_user = $_SESSION['user_id'];
        $sql_list = "SELECT * FROM phanhoi WHERE ID_USER = ? ORDER BY NGAYGUI DESC";
        $stmt_list = $conn->prepare($sql_list);
        $stmt_list->bind_param("s", $id_user);
        $stmt_list->execute();
        $result = $stmt_list->get_result();

        echo "<h2>Lịch sử phản hồi của bạn</h2>";

        if ($result && $result->num_rows > 0) {
            echo "<table class='table table-bordered table-striped align-middle mt-3'>
                    <thead class='table-dark text-center'>
                        <tr>
                            <th>Ngày gửi</th>
                            <th>Trạng thái</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>";

            while ($row = $result->fetch_assoc()) {
                $id = $row['ID_PHANHOI'];
                echo "<tr class='text-center'>
                        <td>{$row['NGAYGUI']}</td>
                        <td>{$row['TRANGTHAI']}</td>
                        <td>
                            <button class='btn btn-info btn-sm' data-bs-toggle='modal' data-bs-target='#modal$id'>Chi tiết</button>
                            <a href='LienHe.php?delete=$id' class='btn btn-danger btn-sm' onclick='return confirm(\"Bạn có chắc muốn xóa phản hồi này không?\")'>Xóa</a>
                        </td>
                      </tr>";

                // Modal hiển thị chi tiết phản hồi
                echo "
                <div class='modal fade' id='modal$id' tabindex='-1'>
                  <div class='modal-dialog modal-dialog-centered'>
                    <div class='modal-content'>
                      <div class='modal-header'>
                        <h5 class='modal-title'>Chi tiết phản hồi</h5>
                        <button type='button' class='btn-close' data-bs-dismiss='modal'></button>
                      </div>
                      <div class='modal-body'>
                        <div class='mb-3'>
                          <strong>Bạn:</strong>
                          <div class='border rounded p-2 bg-light mt-1'>" . nl2br(htmlspecialchars($row['NOIDUNG'])) . "</div>
                        </div>";

                if (!empty($row['TRALOI'])) {
                    echo "<div class='mt-3'>
                            <strong>Phản hồi từ Admin:</strong>
                            <div class='border rounded p-2 bg-light mt-1'>" . nl2br(htmlspecialchars($row['TRALOI'])) . "</div>
                          </div>";
                } else {
                    echo "<p class='text-muted'><i>Chưa có phản hồi từ Admin.</i></p>";
                }

                echo "</div>
                      <div class='modal-footer'>
                        <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Đóng</button>
                      </div>
                    </div>
                  </div>
                </div>";
            }

            echo "</tbody></table>";
        } else {
            echo "<p class='mt-3 text-muted'>Bạn chưa gửi phản hồi nào.</p>";
        }
    } else {
        echo "<p class='mt-3 text-muted'>Vui lòng đăng nhập để gửi và xem phản hồi.</p>";
    }
    ?>
</div>

<!-- ✅ Sửa lại đường dẫn footer -->
<?php include("../includes/footer.php"); ?>
</body>
</html>
