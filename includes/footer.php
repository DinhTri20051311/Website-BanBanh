<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$base_url = dirname($_SERVER['SCRIPT_NAME']);
$knownFolders = ['/pages', '/auth', '/includes', '/admin', '/modules'];
while (true) {
    $changed = false;
    foreach ($knownFolders as $folder) {
        if (str_contains($base_url, $folder)) {
            $base_url = substr($base_url, 0, strrpos($base_url, '/'));
            $changed = true;
            break;
        }
    }
    if (!$changed) {
        break;
    }
}

if ($base_url === '/' || $base_url === '\\') {
    $base_url = '';
}
if (substr($base_url, -1) === '/' && $base_url !== '') {
    $base_url = substr($base_url, 0, -1);
}
?>

<!-- ==================== CHATBOT DANISA CAKE ==================== -->
<div id="chatbot-container">
    <div id="chatbot-button" class="chatbot-button-main">
        <i class="fa fa-comments fa-2x"></i>
    </div>

    <div id="chatbot-window">
        <div class="chatbot-header">
            <span>Trợ Lý Ảo Danisa Cake 🧁</span>
            <span class="close-btn">&times;</span>
        </div>
        <div class="chatbot-body" id="chat-messages"></div>
        <div class="chatbot-footer">
            <input type="text" id="user-input" placeholder="Gõ câu hỏi của bạn..." autocomplete="off">
            <button id="send-button">Gửi</button>
        </div>
    </div>
</div>

<footer class="footer">
    <div class="container">
      <div class="row text-center text-md-start">
        <div class="col-md-3 mb-4">
          <img src="<?= $base_url ?>/assets/image/logo/danisha-logo.png" alt="Logo" class="logo2">
          <p>Địa chỉ: 48 Cao Thắng, Phường 4, Quận 3, TP.HCM</p>
          <p>Hotline: 1800 6750</p>
          <p>Email: hellocafein@gmail.com</p>
          <p>Hệ thống chi nhánh</p>
        </div>

        <div class="col-md-3 mb-4">
          <h5>Thông tin sản phẩm</h5>
          <p>Giới thiệu</p>
          <p>Tuyển dụng</p>
          <p>Hệ thống cửa hàng</p>
        </div>

        <div class="col-md-3 mb-4">
          <h5>Chính sách</h5>
          <p>Chính sách đổi trả</p>
          <p>Chính sách bảo hành</p>
          <p>Chính sách thanh toán</p>
          <p>Chính sách giao hàng</p>
        </div>

        <div class="col-md-3 mb-4">
          <h5>Dịch vụ</h5>
          <p>Hỗ trợ khách hàng</p>
          <p>Hướng dẫn đặt hàng</p>
        </div>
      </div>
    </div>
    <p class="banquyen">© 2026 Bản quyền thuộc về Công ty TNHH Thương Mại Dịch Vụ Danisha</p>
</footer>

<link rel="stylesheet" href="<?= $base_url ?>/assets/css/chatbot.css">
<script src="<?= $base_url ?>/assets/js/chatbot.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
