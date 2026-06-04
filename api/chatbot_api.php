<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// ✅ 1. KẾT NỐI DATABASE
// Điều chỉnh đường dẫn này nếu file connect.php không nằm ở /../config/
include __DIR__ . "/../config/connect.php"; 

ob_clean();
header('Content-Type: application/json; charset=utf-8');

$logFile = __DIR__ . '/debug_gemini.txt';
file_put_contents($logFile, "===== NEW REQUEST =====\n", FILE_APPEND);

// 1. Check method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['error' => 'Request must be POST']);
    exit;
}

// 2. Check query
if (empty($_POST['query'])) {
    echo json_encode(['error' => 'Missing query']);
    exit;
}

$query = trim($_POST['query']);

// 3. GEMINI API KEY (THAY KHÓA THẬT CỦA BẠN VÀO ĐÂY)
// Khóa mẫu: AIzaSyDDUMbO7Q7maU2NR6QgIwEosNJhQQDItgs
$api_key = 'AIzaSyDDUMbO7Q7maU2NR6QgIwEosNJhQQDItgs'; // Dùng khóa mẫu hoặc khóa thật của bạn
file_put_contents($logFile, "API KEY PREFIX: " . substr($api_key, 0, 10) . "...\n", FILE_APPEND);


// --- CÁC HÀM HỖ TRỢ ---

// Hàm 4A: Gửi yêu cầu đến Gemini API với cấu hình JSON
function call_gemini($systemPrompt, $api_key, $logFile) {
    $data = [
        "contents" => [
            ["role" => "user", "parts" => [["text" => $systemPrompt]]]
        ],
        "generationConfig" => [ 
            "temperature" => 0.4, 
        ]
    ];

    $jsonPayload = json_encode($data, JSON_UNESCAPED_UNICODE);
    file_put_contents($logFile, "REQUEST JSON:\n$jsonPayload\n", FILE_APPEND);
    
    // CURL config
    $url = "https://generativelanguage.googleapis.com/v1/models/gemini-2.5-flash:generateContent?key=" . $api_key;
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $jsonPayload,
        CURLOPT_HTTPHEADER => ["Content-Type: application/json"],
        CURLOPT_TIMEOUT => 30
    ]);
    
    $response = curl_exec($ch);
    $curlError = curl_error($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    file_put_contents($logFile, "HTTP CODE: $httpCode\n", FILE_APPEND);
    file_put_contents($logFile, "CURL ERROR: $curlError\n", FILE_APPEND);
    file_put_contents($logFile, "RAW RESPONSE:\n$response\n\n", FILE_APPEND);

    $json = json_decode($response, true);
    $reply = $json['candidates'][0]['content']['parts'][0]['text'] ?? '{}';
    
    return [
        'http_code' => $httpCode,
        'response' => $json,
        'reply' => $reply
    ];
}

// 🔥 HÀM ĐÃ SỬA LỖI LOGIC RÀNG BUỘC THAM SỐ
function search_products($conn, $keyword, $max_price) {
    $sql = "SELECT ID_SP, TENSANPHAM, GIA, HINHANH FROM sanpham WHERE 1=1";
    $types = "";
    $params = [];

    // Logic tìm kiếm: Dùng TENSANPHAM 
    if (!empty($keyword)) {
        // Sử dụng TENSANPHAM
        $sql .= " AND (TENSANPHAM LIKE ?)"; 
        $types .= "s";
        $params[] = "%" . $keyword . "%";
        
        // Nếu bạn muốn tìm kiếm thêm cả trong mô tả (MO_TA):
        /*
        $sql .= " AND (TENSANPHAM LIKE ? OR MO_TA LIKE ?)"; 
        $types .= "ss";
        $params[] = "%" . $keyword . "%";
        $params[] = "%" . $keyword . "%";
        */
    }

    // Logic tìm giá
    if ($max_price > 0) {
        $sql .= " AND GIA <= ?";
        $types .= "i";
        $params[] = $max_price;
    }

    $sql .= " LIMIT 5"; 

    $stmt = mysqli_prepare($conn, $sql);
    if ($stmt === false) {
        return "Lỗi CSDL khi chuẩn bị truy vấn: " . mysqli_error($conn);
    }
    
    // Ràng buộc tham số chỉ khi có tham số
    if (!empty($params)) {
        // Tạo mảng tham chiếu để sử dụng với mysqli_stmt_bind_param
        $refs = [];
        foreach ($params as $key => $value) {
            $refs[$key] = &$params[$key];
        }
        array_unshift($refs, $types);
        
        // Gọi hàm bind_param
        if (!call_user_func_array('mysqli_stmt_bind_param', array_merge([$stmt], $refs))) {
             return "Lỗi CSDL khi bind tham số.";
        }
    }
    
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $html = "";
    if (mysqli_num_rows($result) > 0) {
        $html .= "<div>Dưới đây là các bánh mình tìm thấy nè:</div>";
        while ($row = mysqli_fetch_assoc($result)) {
            $product_id = htmlspecialchars($row['ID_SP'] ?? ''); 
            
            $linkUrl = "/WEBSITEBANBANH/pages/sanpham.php#sp_{$product_id}"; 

            $imgUrl = "/WEBSITEBANBANH/assets/image/banh/" . htmlspecialchars($row['HINHANH'] ?? '');
            $price = number_format($row['GIA'] ?? 0, 0, ',', '.');
            
            $html .= "
            <a href='$linkUrl' class='chat-product-link' data-product-id='{$product_id}'>
                <div class='chat-product-item'>
                    <img src='$imgUrl' class='chat-product-img' alt='anh'>
                    <div class='chat-product-info'>
                        <p class='chat-product-name'>{$row['TENSANPHAM']}</p>
                        <p class='chat-product-price'>{$price} đ</p>
                    </div>
                </div>
            </a>";
        }
        $html .= "<div>Bấm vào bánh để xem chi tiết!</div>"; 
    } else {
        $price_msg = $max_price > 0 ? " với mức giá tối đa " . number_format($max_price, 0, ',', '.') . " đ" : "";
        $keyword_msg = !empty($keyword) ? " có từ khóa '{$keyword}'" : "";
        $html = "Tiếc quá, mình không tìm thấy bánh nào phù hợp{$keyword_msg}{$price_msg} của bạn 😢. Bạn thử tìm kiếm với các từ khóa hoặc mức giá khác nhé!";
    }
    return $html;
}


// Helper: produce a useful fallback reply when model call fails or returns nothing
function generate_fallback($q, $httpCode) {
    $qLower = mb_strtolower($q, 'UTF-8');
    $prefix = '';
    if ($httpCode === 429) {
        $prefix = "Mình tạm thời chưa kết nối được với dịch vụ AI (quota/Billing). ";
    } else if ($httpCode === 400) {
        $prefix = "Lỗi cấu hình yêu cầu API (400). ";
    } else {
        $prefix = "Mình hiện chưa lấy được phản hồi từ AI. ";
    }

    // simple keyword-based canned replies (Dùng lại logic cũ)
    if (mb_stripos($qLower, 'giá') !== false || mb_stripos($qLower, 'bao nhiêu') !== false) {
        return $prefix . "Về giá: vui lòng cho biết tên sản phẩm hoặc kiểm tra trang chi tiết sản phẩm để xem giá chính xác.";
    }
    if (mb_stripos($qLower, 'mua') !== false || mb_stripos($qLower, 'đặt') !== false || mb_stripos($qLower, 'order') !== false) {
        return $prefix . "Để đặt hàng: bạn có thể bấm nút 'Mua ngay' trên trang sản phẩm hoặc gọi Hotline: 1800 6750.";
    }
    if (mb_stripos($qLower, 'giao') !== false || mb_stripos($qLower, 'ship') !== false) {
        return $prefix . "Chúng tôi có giao hàng tận nơi. Vui lòng cung cấp địa chỉ để mình kiểm tra phí và thời gian giao.";
    }
    if (mb_stripos($qLower, 'chi nhánh') !== false || mb_stripos($qLower, 'địa chỉ') !== false) {
        return $prefix . "Địa chỉ chính: 48 Cao Thắng, Phường 4, Quận 3, TP.HCM. Liên hệ Hotline để biết chi nhánh khác.";
    }

    // default helpful suggestions
    return $prefix . "Mình vẫn có thể giúp: hỏi về sản phẩm, cách đặt hàng, giờ mở cửa, hoặc 'tư vấn bánh sinh nhật'.";
}


// --- XỬ LÝ CHÍNH ---

// 4. Prompt mới yêu cầu JSON
$systemPrompt = "
Bạn là trợ lý ảo bán bánh của Danisa Cake. Nhiệm vụ của bạn là phân tích yêu cầu của khách hàng: '$query'.
Trả về kết quả DƯỚI DẠNG JSON (KHÔNG SỬ DỤNG markdown như ```json) với cấu trúc sau:
{
    \"intent\": \"search\" hoặc \"chat\",
    \"keyword\": \"tên bánh hoặc thành phần chính khách muốn tìm (nếu có, ví dụ: 'socola', 'kem dâu')\",
    \"max_price\": số_tiền_tối_đa (số nguyên, nếu khách nhắc đến giá, nếu không có thì để 0),
    \"reply\": \"Câu trả lời bình thường nếu là chat xã giao, hoặc tóm tắt yêu cầu tìm kiếm (ví dụ: 'Mình sẽ tìm các bánh có kem dâu cho bạn')\"
}
- Nếu khách hỏi về sản phẩm, giá, hoặc tìm kiếm, hãy đặt \"intent\": \"search\".
- Nếu khách chào hỏi, hỏi giờ làm việc, địa chỉ, hoặc yêu cầu tư vấn chung, hãy đặt \"intent\": \"chat\".
- Luôn đặt \"keyword\" và \"max_price\" là 0 nếu intent là \"chat\".
";


// 5 & 6. Gửi yêu cầu tới Gemini
$geminiResult = call_gemini($systemPrompt, $api_key, $logFile);

$httpCode = $geminiResult['http_code'];
$rawResponseText = $geminiResult['reply'];

$finalResponse = '';
$usedFallback = false;

// 7. Xử lý phản hồi
if ($httpCode === 200) {
    // 7.1. Clean up JSON response (Đôi khi AI trả về kèm markdown ```json...)
    $rawResponseText = str_replace('```json', '', $rawResponseText);
    $rawResponseText = str_replace('```', '', $rawResponseText);
    $parsedData = json_decode($rawResponseText, true);
    
    if (isset($parsedData['intent'])) {
        if ($parsedData['intent'] === 'search') {
            // A. Nếu là tìm kiếm -> Gọi Database
            $kw = $parsedData['keyword'] ?? '';
            $price = $parsedData['max_price'] ?? 0;
            file_put_contents($logFile, "INTENT: SEARCH (KW=$kw, PRICE=$price)\n", FILE_APPEND);
            
            // Chạy tìm kiếm trong DB và nhận HTML
            // Cần có kết nối CSDL, nhưng không thấy trong code bạn gửi, 
            // nên tôi giả định include __DIR__ . "/../config/connect.php"; 
            // ở đầu file đã tạo ra biến $conn.
            $finalResponse = search_products($conn, $kw, $price); 

        } else {
            // B. Nếu là chat thường -> Lấy câu trả lời của AI
            file_put_contents($logFile, "INTENT: CHAT\n", FILE_APPEND);
            $finalResponse = $parsedData['reply'] ?? "Xin lỗi, mình chưa hiểu ý bạn.";
        }
    } else {
        // AI trả về 200 nhưng không phải JSON chuẩn
        $finalResponse = generate_fallback($query, $httpCode);
        $usedFallback = true;
        file_put_contents($logFile, "USING FALLBACK (AI response not valid JSON)\n", FILE_APPEND);
    }
} else {
    // 8. Lỗi kết nối API -> Dùng Fallback cũ
    $finalResponse = generate_fallback($query, $httpCode);
    $usedFallback = true;
    file_put_contents($logFile, "USING FALLBACK (http_code=$httpCode)\n", FILE_APPEND);
}

// 9. Return structured JSON
echo json_encode([
    'http_code' => $httpCode,
    'response' => $finalResponse,
    'fallback' => $usedFallback
], JSON_UNESCAPED_UNICODE);

exit;