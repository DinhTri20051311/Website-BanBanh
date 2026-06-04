document.addEventListener('DOMContentLoaded', () => {
    const chatButton = document.getElementById('chatbot-button');
    const chatWindow = document.getElementById('chatbot-window');
    const closeBtn = document.querySelector('.close-btn');
    const chatBody = document.getElementById('chat-messages');
    const userInput = document.getElementById('user-input');
    const sendBtn = document.getElementById('send-button');

    // ⚠️ Kiểm tra lại đường dẫn này xem đã đúng tên thư mục web của bạn chưa
    const API = '/WEBSITEBANBANH/api/chatbot_api.php';

    function displayMessage(text, sender) {
        if (!chatBody) return;
        const div = document.createElement('div');
        div.className = 'chat-message ' + (sender === 'user' ? 'user-message' : 'bot-message');
        
        // 🔥 CẬP NHẬT QUAN TRỌNG: 
        // Dùng innerHTML để hiển thị được ảnh và thẻ div từ PHP trả về
        div.innerHTML = text; 
        
        chatBody.appendChild(div);
        chatBody.scrollTop = chatBody.scrollHeight;

        // 🔥 GỌI HÀM MỚI: Xử lý liên kết sản phẩm SAU KHI tin nhắn được hiển thị
        if (sender === 'bot') {
            attachProductLinkListeners(div);
        }
    }

    // 🔥 HÀM MỚI: Xử lý sự kiện click cho liên kết sản phẩm (để cuộn trang nội bộ)
    function attachProductLinkListeners(messageElement) {
        const productLinks = messageElement.querySelectorAll('.chat-product-link');
        productLinks.forEach(link => {
            link.addEventListener('click', (e) => {
                
                const url = new URL(link.href);
                // Lấy ID neo (hash) và bỏ dấu # (ví dụ: sp_B001)
                const targetId = url.hash.substring(1); 

                // Chỉ xử lý cuộn nếu đang ở trang SanPham.php VÀ có ID neo
                if (window.location.pathname.toLowerCase().includes('sanpham.php') && targetId) {
                    
                    // Ngăn chặn hành vi chuyển trang mặc định của thẻ <a>
                    e.preventDefault(); 
                    
                    const targetElement = document.getElementById(targetId);

                    if (targetElement) {
                        // 1. Cuộn đến vị trí sản phẩm trên trang
                        targetElement.scrollIntoView({
                            behavior: 'smooth', // Cuộn mượt
                            block: 'start' // Đặt sản phẩm ở đầu viewport
                        });
                        
                        // 2. (Tùy chọn) Highlight sản phẩm vừa cuộn đến
                        // Thêm hiệu ứng Box Shadow để dễ nhận biết
                        targetElement.style.transition = 'box-shadow 0.3s ease-out';
                        targetElement.style.boxShadow = '0 0 0 4px #ffc107'; 
                        setTimeout(() => {
                            targetElement.style.boxShadow = '';
                        }, 2000); // Xóa hiệu ứng sau 2 giây

                        // 3. Đóng Chatbot để người dùng tập trung xem sản phẩm
                        if (chatWindow) {
                            chatWindow.style.display = 'none';
                        }
                    } else {
                        // Nếu không tìm thấy ID neo (dữ liệu CSDL có thể không đồng bộ), 
                        // cho phép chuyển trang bình thường (fallback)
                        window.location.href = link.href;
                    }
                }
                // Nếu không phải trang sanpham.php, liên kết vẫn hoạt động mặc định (chuyển trang)
            });
        });
    }


    function setLoading(on) {
        if (!sendBtn) return;
        if (on) { 
            sendBtn.disabled = true; 
            sendBtn.textContent = '...'; 
        } else { 
            sendBtn.disabled = false; 
            sendBtn.textContent = 'Gửi'; 
        }
    }

    function send() {
        if (!userInput) return;
        const q = userInput.value.trim();
        if (!q) return;
        
        // Hiển thị câu hỏi của người dùng
        displayMessage(q, 'user');
        
        userInput.value = '';
        setLoading(true);

        const body = 'query=' + encodeURIComponent(q);
        fetch(API, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: body
        })
        .then(r => {
            if (!r.ok) throw new Error('HTTP ' + r.status);
            return r.json();
        })
        .then(data => {
            setLoading(false);
            // Hiển thị phản hồi từ server (Text hoặc HTML sản phẩm)
            displayMessage(data.response || data.error || 'Không có phản hồi', 'bot');
        })
        .catch(err => {
            setLoading(false);
            displayMessage('Lỗi: ' + (err.message || 'Kết nối thất bại'), 'bot');
        });
    }

    // --- CÁC SỰ KIỆN CLICK VÀ ENTER ---

    if (chatButton) {
        chatButton.addEventListener('click', () => {
            if (!chatWindow) return;
            chatWindow.style.display = chatWindow.style.display === 'flex' ? 'none' : 'flex';
            if (chatWindow.style.display === 'flex' && chatBody && chatBody.children.length === 0) {
                displayMessage('Xin chào! Tôi là Trợ Lý Ảo của Danisa Cake 🧁. Bạn cần tìm bánh gì hay muốn hỏi giá không?', 'bot');
            }
        });
    }

    if (closeBtn) {
        closeBtn.addEventListener('click', () => {
            if (chatWindow) chatWindow.style.display = 'none';
        });
    }

    if (sendBtn) {
        sendBtn.addEventListener('click', send);
    }

    if (userInput) {
        userInput.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') send();
        });
    }
});