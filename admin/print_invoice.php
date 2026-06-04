<?php
// ====== Khởi động phiên ======
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ====== Kết nối CSDL ======
include __DIR__ . '/../config/connect.php';

// ====== Nhận ID đơn hàng ======
$id = isset($_GET['id']) ? trim($_GET['id']) : '';
if ($id === '') {
    echo 'Thiếu ID đơn hàng';
    exit;
}

// ====== Lấy thông tin đơn hàng ======
$stmt = $conn->prepare("SELECT * FROM donhang WHERE ID_DH = ? LIMIT 1");
$stmt->bind_param('s', $id);
$stmt->execute();
$res = $stmt->get_result();
$order = $res->fetch_assoc();
$stmt->close();

if (!$order) {
    echo 'Không tìm thấy đơn hàng';
    exit;
}





// ====== Helper định dạng tiền ======
function fm($n) {
    return number_format((float)$n, 0, ',', '.') . '₫';
}
?>
<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <title>Hóa đơn - <?= htmlspecialchars($order['ID_DH']) ?></title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <style>
        body{font-family:Arial, sans-serif;color:#222;margin:20px}
        .inv{max-width:900px;margin:0 auto;border:1px solid #ddd;padding:20px;border-radius:6px}
        .header{display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:12px}
        .brand{font-weight:700;color:#0d6efd;font-size:20px}
        table{width:100%;border-collapse:collapse;margin-top:10px}
        th,td{padding:8px;border-bottom:1px solid #eee;text-align:left;vertical-align:top}
        .right{text-align:right}
        .total{font-size:18px;color:#d63384;font-weight:700}
        .no-print{margin-top:14px;text-align:center}
        @media print{.no-print{display:none}}
        .section-title{font-weight:700;margin-top:14px}
        .kv {width:35%;color:#555}
        button{padding:6px 14px;margin:4px;border:none;border-radius:4px;background:#0d6efd;color:#fff;cursor:pointer}
        button:hover{background:#0b5ed7}
    </style>
</head>
<body>
<div class="inv" id="invoice">
    <div class="header">
        <div>
            <div class="brand">Website Bán Hàng</div>
            <div class="small">Hóa đơn bán hàng</div>
        </div>
        <div class="small">
            Mã đơn: <strong><?= htmlspecialchars($order['ID_DH']) ?></strong><br>
            Ngày đặt: <?= htmlspecialchars($order['NGAYDAT'] ?? $order['NGAY_DAT'] ?? '') ?>
        </div>
    </div>

    <div class="section-title">Thông tin đơn hàng</div>
    <table>
        <?php foreach ($order as $k => $v): ?>
            <tr>
                <td class="kv"><?= htmlspecialchars($k) ?></td>
                <td><?= nl2br(htmlspecialchars((string)$v)) ?></td>
            </tr>
        <?php endforeach; ?>
    </table>

   

    <table style="margin-top:12px">
        <tr>
            <td class="right" style="width:70%">Tổng (Thanh toán):</td>
            <td class="right total"><?= fm($order['THANHTIEN'] ?? $order['TONGTIEN'] ?? $order['TONG_TIEN'] ?? 0) ?></td>
        </tr>
    </table>

    <div style="margin-top:12px;font-size:12px;color:#666">
        Lưu ý: Hóa đơn này được tạo tự động từ hệ thống quản trị.
    </div>

    <div class="no-print">
        <button onclick="window.print()">🖨 In hóa đơn</button>
        <button onclick="window.close()">✖ Đóng</button>
    </div>
</div>
</body>
</html>
