-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th12 15, 2025 lúc 10:43 AM
-- Phiên bản máy phục vụ: 10.4.32-MariaDB
-- Phiên bản PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `banhang`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `ctdh`
--

CREATE TABLE `ctdh` (
  `ID_CT` int(10) NOT NULL,
  `ID_DH` int(10) NOT NULL,
  `ID_SP` varchar(10) NOT NULL,
  `SOLUONG` int(11) NOT NULL,
  `DONGIA` decimal(18,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `ctdh`
--

INSERT INTO `ctdh` (`ID_CT`, `ID_DH`, `ID_SP`, `SOLUONG`, `DONGIA`) VALUES
(39, 30, 'SP003', 1, 4000.00),
(45, 35, 'SP002', 1, 4000.00),
(46, 36, 'SP003', 1, 4000.00),
(47, 37, 'SP003', 1, 4000.00),
(48, 38, 'SP003', 1, 4000.00),
(49, 39, 'SP004', 3, 123.00),
(50, 40, 'SP004', 1, 123.00),
(51, 42, 'SP004', 1, 123.00),
(52, 42, 'SP003', 1, 4000.00),
(53, 44, 'SP001', 1, 4000.00),
(54, 44, 'SP003', 1, 4000.00),
(55, 45, 'SP010', 1, 30000.00),
(56, 46, 'SP002', 1, 40000.00),
(57, 47, 'SP008', 1, 40000.00);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `donhang`
--

CREATE TABLE `donhang` (
  `ID_DH` int(10) NOT NULL,
  `ID_USER` varchar(10) NOT NULL,
  `TENNGUOINHAN` varchar(100) NOT NULL,
  `SDT` varchar(20) NOT NULL,
  `DIACHI` varchar(100) NOT NULL,
  `THANHTIEN` decimal(18,2) NOT NULL,
  `NGAYDAT` datetime NOT NULL,
  `TINHTRANG` varchar(50) NOT NULL DEFAULT 'Chờ xác nhận'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `donhang`
--

INSERT INTO `donhang` (`ID_DH`, `ID_USER`, `TENNGUOINHAN`, `SDT`, `DIACHI`, `THANHTIEN`, `NGAYDAT`, `TINHTRANG`) VALUES
(30, 'U005', 'shion2', '123', 'ádf', 4000.00, '2025-10-25 16:31:18', 'Hoàn thành'),
(35, 'U005', 'shion', '12345', 'dfasdf', 4000.00, '2025-10-25 17:36:48', 'Chờ xác nhận'),
(36, 'U005', 'trung', '12345', 'ádf', 4000.00, '2025-10-25 17:41:18', 'Chờ xác nhận'),
(37, 'U003', 'trung', '12345', 'ádf', 4000.00, '2025-10-25 18:50:09', 'Chờ xác nhận'),
(38, 'U010', 'trung', '12345', 'ádf', 4000.00, '2025-10-26 09:21:32', 'Hoàn thành'),
(39, 'U005', 'trung', '123', 'ádf', 369.00, '2025-10-26 11:54:55', 'Hoàn thành'),
(40, 'U005', 'shion', '12345', 'ádfasdga', 123.00, '2025-10-27 08:56:08', 'Chờ xác nhận'),
(41, 'U005', '', '', '', 0.00, '2025-10-27 08:56:11', 'Đã hủy'),
(42, 'U005', 'trung89', '12345', 'sdfasdfa', 4123.00, '2025-10-27 09:09:26', 'Đã xác nhận'),
(43, 'U005', '', '', '', 0.00, '2025-10-27 09:09:29', 'Đã hủy'),
(44, 'U005', 'trung', '12345', 'ăds', 8000.00, '2025-12-14 07:43:01', 'Chờ xác nhận'),
(45, 'U005', 'trung', 'ádf', 'ádgadhadf', 30000.00, '2025-12-15 08:39:04', 'Chờ xác nhận'),
(46, 'U005', 'trung', 'ádf', 'adsf', 40000.00, '2025-12-15 08:40:01', 'Chờ xác nhận'),
(47, 'U005', 'trung', 'ÁDF', 'ADF', 40000.00, '2025-12-15 08:40:46', 'Chờ xác nhận');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `phanhoi`
--

CREATE TABLE `phanhoi` (
  `ID_PHANHOI` int(11) NOT NULL,
  `ID_USER` varchar(10) NOT NULL,
  `NOIDUNG` text NOT NULL,
  `TRALOI` text DEFAULT NULL,
  `NGAYGUI` datetime DEFAULT current_timestamp(),
  `TRANGTHAI` enum('Chưa phản hồi','Đã phản hồi') DEFAULT 'Chưa phản hồi'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `phanhoi`
--

INSERT INTO `phanhoi` (`ID_PHANHOI`, `ID_USER`, `NOIDUNG`, `TRALOI`, `NGAYGUI`, `TRANGTHAI`) VALUES
(1, 'U005', 'ádf', NULL, '2025-10-26 00:46:20', 'Chưa phản hồi'),
(3, 'U010', 'ádfadgafa', 'ádf', '2025-10-26 15:24:42', 'Đã phản hồi');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `phanloai`
--

CREATE TABLE `phanloai` (
  `MA_LOAI` varchar(10) NOT NULL,
  `TENLOAI` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `phanloai`
--

INSERT INTO `phanloai` (`MA_LOAI`, `TENLOAI`) VALUES
('B', 'Bánh'),
('C', 'Cái'),
('H', 'Hộp'),
('L', 'Lít');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `sanpham`
--

CREATE TABLE `sanpham` (
  `ID_SP` varchar(10) NOT NULL,
  `TENSANPHAM` varchar(100) NOT NULL,
  `GIA` int(11) NOT NULL,
  `HINHANH` varchar(100) NOT NULL,
  `MA_LOAI` varchar(10) NOT NULL,
  `HOT_SP` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `sanpham`
--

INSERT INTO `sanpham` (`ID_SP`, `TENSANPHAM`, `GIA`, `HINHANH`, `MA_LOAI`, `HOT_SP`) VALUES
('SP001', 'Bánh kem matcha', 8000, 'banh1.jpg', 'B', 1),
('SP002', 'Bánh ngọt dâu tây', 40000, 'banh3.png', 'B', 1),
('SP003', 'Bánh kem sữa tươi', 40000, 'banh5.jpg', 'B', 0),
('SP004', 'Bánh cuộn trà xanh', 25000, 'images.jpg', 'B', 0),
('SP005', 'Bánh bông lan trứng muối', 50000, 'Bánh bông lan trứng muối.jpg', 'B', 1),
('SP006', 'Bánh Muffin', 200000, 'Bánh Muffin.jpg', 'B', 0),
('SP007', 'Bánh Cupcake', 10000, 'Bánh Cupcake.jpg', 'B', 0),
('SP008', 'Bánh Pancake', 40000, 'Bánh Pancake.jpg', 'B', 1),
('SP009', 'Bánh Cheesecake', 40000, 'Bánh Cheesecake.jpg', 'B', 0),
('SP010', 'Bánh Donut', 30000, 'Bánh Donut.jpg', 'H', 1),
('SP011', 'Bánh Gato', 400000, 'Bánh Gato.jpg', 'B', 0),
('SP012', 'Bánh mousse', 500000, 'Bánh mousse.webp', 'B', 0),
('SP013', 'Bánh rán Dorayaki', 10000, 'Bánh rán Dorayaki.jpg', 'B', 0),
('SP014', 'bánh xu kem', 10000, 'bánh su kem.jpg', 'H', 1),
('SP015', 'Bánh Tiramisu', 400000, 'Bánh Tiramisu.jpg', 'B', 1);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `user`
--

CREATE TABLE `user` (
  `ID_USER` varchar(10) NOT NULL,
  `TK` varchar(100) NOT NULL,
  `MK` varchar(100) NOT NULL,
  `EMAIL` varchar(255) NOT NULL,
  `VAITRO` enum('admin','user') NOT NULL DEFAULT 'user'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `user`
--

INSERT INTO `user` (`ID_USER`, `TK`, `MK`, `EMAIL`, `VAITRO`) VALUES
('U003', 'shion', '123456', 'trungduong99914@gmail.com', 'admin'),
('U005', 'trung', '123456', 'trung@gmail.com', 'user'),
('U007', 'trung', '123', 'lo@gmail.com', 'user'),
('U008', 'trung', '123', '123@gmail.com', 'user'),
('U009', 'trung', '321', '321@gmail.com', 'user'),
('U010', 'shion3', '123456', 'shion@gmail.com', 'user'),
('U011', 'trung23', '123', 'trungduong999df14@gmail.com', 'user');

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `ctdh`
--
ALTER TABLE `ctdh`
  ADD PRIMARY KEY (`ID_CT`),
  ADD KEY `ID_DH` (`ID_DH`,`ID_SP`),
  ADD KEY `ID_SP` (`ID_SP`);

--
-- Chỉ mục cho bảng `donhang`
--
ALTER TABLE `donhang`
  ADD PRIMARY KEY (`ID_DH`),
  ADD KEY `ID_USER` (`ID_USER`);

--
-- Chỉ mục cho bảng `phanhoi`
--
ALTER TABLE `phanhoi`
  ADD PRIMARY KEY (`ID_PHANHOI`),
  ADD KEY `ID_USER` (`ID_USER`);

--
-- Chỉ mục cho bảng `phanloai`
--
ALTER TABLE `phanloai`
  ADD PRIMARY KEY (`MA_LOAI`);

--
-- Chỉ mục cho bảng `sanpham`
--
ALTER TABLE `sanpham`
  ADD PRIMARY KEY (`ID_SP`),
  ADD KEY `MA_LOAI` (`MA_LOAI`);

--
-- Chỉ mục cho bảng `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`ID_USER`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `ctdh`
--
ALTER TABLE `ctdh`
  MODIFY `ID_CT` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=58;

--
-- AUTO_INCREMENT cho bảng `donhang`
--
ALTER TABLE `donhang`
  MODIFY `ID_DH` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;

--
-- AUTO_INCREMENT cho bảng `phanhoi`
--
ALTER TABLE `phanhoi`
  MODIFY `ID_PHANHOI` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `ctdh`
--
ALTER TABLE `ctdh`
  ADD CONSTRAINT `ctdh_ibfk_1` FOREIGN KEY (`ID_SP`) REFERENCES `sanpham` (`ID_SP`),
  ADD CONSTRAINT `ctdh_ibfk_2` FOREIGN KEY (`ID_DH`) REFERENCES `donhang` (`ID_DH`);

--
-- Các ràng buộc cho bảng `donhang`
--
ALTER TABLE `donhang`
  ADD CONSTRAINT `donhang_ibfk_1` FOREIGN KEY (`ID_USER`) REFERENCES `user` (`ID_USER`);

--
-- Các ràng buộc cho bảng `phanhoi`
--
ALTER TABLE `phanhoi`
  ADD CONSTRAINT `phanhoi_ibfk_1` FOREIGN KEY (`ID_USER`) REFERENCES `user` (`ID_USER`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `sanpham`
--
ALTER TABLE `sanpham`
  ADD CONSTRAINT `sanpham_ibfk_1` FOREIGN KEY (`MA_LOAI`) REFERENCES `phanloai` (`MA_LOAI`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
