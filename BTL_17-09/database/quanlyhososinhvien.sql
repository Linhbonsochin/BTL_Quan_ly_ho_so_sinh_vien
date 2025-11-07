-- --------------------------------------------------------
-- HỆ THỐNG QUẢN LÝ HỒ SƠ SINH VIÊN
-- Mô tả: Database quản lý thông tin sinh viên, điểm số, khoa, lớp và người dùng
-- --------------------------------------------------------
-- Xóa database nếu tồn tại và tạo mới
DROP DATABASE IF EXISTS `quanlyhososinhvien`;
CREATE DATABASE IF NOT EXISTS `quanlyhososinhvien` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `quanlyhososinhvien`;

-- --------------------------------------------------------
-- Cấu trúc bảng `users`
-- --------------------------------------------------------
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','student') NOT NULL DEFAULT 'student',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dữ liệu mẫu cho bảng `users`
INSERT INTO `users` (`username`, `password`, `role`) VALUES
('admin', 'admin', 'admin'),
('student01', '123456', 'student'),
('student02', '123456', 'student'),
('student03', '123456', 'student'),
('student04', '123456', 'student'),
('student05', '123456', 'student'),
('student06', '123456', 'student'),
('student07', '123456', 'student'),
('student08', '123456', 'student'),
('student09', '123456', 'student'),
('student10', '123456', 'student'),
('student11', '123456', 'student'),
('student12', '123456', 'student'),
('student13', '123456', 'student'),
('student14', '123456', 'student'),
('student15', '123456', 'student');

-- --------------------------------------------------------
-- Cấu trúc bảng `departments`
-- --------------------------------------------------------
CREATE TABLE `departments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `department_code` varchar(20) NOT NULL,
  `department_name` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `department_code` (`department_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dữ liệu mẫu cho bảng `departments`
INSERT INTO `departments` (`department_code`, `department_name`) VALUES
('CNTT', 'Công nghệ thông tin'),
('KHMT', 'Khoa học máy tính'),
('KT', 'Kế toán'),
('NNH', 'Ngôn ngữ hàn'),
('NNT', 'Ngôn ngữ trung'),
('QTKD', 'Quản trị kinh doanh'),
('TMDT', 'Thương mại điện tử'),
('TT', 'Truyền thông');

-- --------------------------------------------------------
-- Cấu trúc bảng `classes`
-- --------------------------------------------------------
CREATE TABLE `classes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `class_code` varchar(20) NOT NULL,
  `class_name` varchar(100) NOT NULL,
  `department_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `class_code` (`class_code`),
  KEY `department_id` (`department_id`),
  CONSTRAINT `classes_ibfk_1` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dữ liệu mẫu cho bảng `classes`
INSERT INTO `classes` (`class_code`, `class_name`, `department_id`) VALUES
('CNTT1', 'Công nghệ thông tin 1', 1),
('CNTT2', 'Công nghệ thông tin 2', 1),
('CNTT3', 'Công nghệ thông tin 3', 1),
('KHMT1', 'Khoa học máy tính 1', 2),
('KHMT2', 'Khoa học máy tính 2', 2),
('KT1', 'Kế toán 1', 3),
('KT2', 'Kế toán 2', 3),
('NNH1', 'Ngôn ngữ Hàn 1', 4),
('NNH2', 'Ngôn ngữ Hàn 2', 4),
('NNT1', 'Ngôn ngữ Trung 1', 5),
('NNT2', 'Ngôn ngữ Trung 2', 5),
('QTKD1', 'Quản trị kinh doanh 1', 6),
('QTKD2', 'Quản trị kinh doanh 2', 6),
('TMDT1', 'Thương mại điện tử 1', 7),
('TT1', 'Truyền thông 1', 8);

-- --------------------------------------------------------
-- Cấu trúc bảng `students`
-- --------------------------------------------------------
CREATE TABLE `students` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `student_code` varchar(20) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `birth_date` date DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `gender` enum('Nam','Nữ') DEFAULT NULL,
  `address` text DEFAULT NULL,
  `class_id` int(11) DEFAULT NULL,
  `department_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `student_code` (`student_code`),
  KEY `class_id` (`class_id`),
  KEY `department_id` (`department_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `students_ibfk_1` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `students_ibfk_2` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `students_ibfk_3` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dữ liệu mẫu cho bảng `students`
INSERT INTO `students` (`student_code`, `full_name`, `birth_date`, `phone`, `email`, `gender`, `address`, `class_id`, `department_id`, `user_id`) VALUES
('SV001', 'Nguyễn Văn An', '2000-01-01', '0901234567', 'an.nguyen@example.com', 'Nam', 'Hà Nội', 1, 1, 2),
('SV002', 'Trần Thị Bình', '2000-02-02', '0912345678', 'binh.tran@example.com', 'Nữ', 'TP HCM', 1, 1, 3),
('SV003', 'Lê Văn Cường', '2000-03-03', '0923456789', 'cuong.le@example.com', 'Nam', 'Đà Nẵng', 2, 1, 4),
('SV004', 'Phạm Thị Diễm', '2000-04-04', '0934567890', 'diem.pham@example.com', 'Nữ', 'Hải Phòng', 2, 1, 5),
('SV005', 'Hoàng Văn Em', '2000-05-05', '0945678901', 'em.hoang@example.com', 'Nam', 'Cần Thơ', 3, 1, 6),
('SV006', 'Ngô Thị Fa', '2000-06-06', '0956789012', 'fa.ngo@example.com', 'Nữ', 'Huế', 4, 2, 7),
('SV007', 'Đỗ Văn Giang', '2000-07-07', '0967890123', 'giang.do@example.com', 'Nam', 'Nha Trang', 4, 2, 8),
('SV008', 'Lý Thị Hương', '2000-08-08', '0978901234', 'huong.ly@example.com', 'Nữ', 'Đà Lạt', 5, 2, 9),
('SV009', 'Bùi Văn Inh', '2000-09-09', '0989012345', 'inh.bui@example.com', 'Nam', 'Vũng Tàu', 6, 3, 10),
('SV010', 'Mai Thị Kim', '2000-10-10', '0990123456', 'kim.mai@example.com', 'Nữ', 'Biên Hòa', 6, 3, 11),
('SV011', 'Trịnh Văn Lâm', '2000-11-11', '0901234567', 'lam.trinh@example.com', 'Nam', 'Hà Nội', 8, 4, 12),
('SV012', 'Vũ Thị Minh', '2000-12-12', '0912345678', 'minh.vu@example.com', 'Nữ', 'TP HCM', 10, 5, 13),
('SV013', 'Đinh Văn Nam', '2001-01-01', '0923456789', 'nam.dinh@example.com', 'Nam', 'Đà Nẵng', 12, 6, 14),
('SV014', 'Dương Thị Oanh', '2001-02-02', '0934567890', 'oanh.duong@example.com', 'Nữ', 'Hải Phòng', 14, 7, 15),
('SV015', 'Lương Văn Phú', '2001-03-03', '0945678901', 'phu.luong@example.com', 'Nam', 'Cần Thơ', 15, 8, 16);

-- --------------------------------------------------------
-- Cấu trúc bảng `subjects`
-- --------------------------------------------------------
CREATE TABLE `subjects` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `subject_code` varchar(20) NOT NULL,
  `subject_name` varchar(100) NOT NULL,
  `credits` int(11) DEFAULT NULL,
  `department_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `subject_code` (`subject_code`),
  KEY `department_id` (`department_id`),
  CONSTRAINT `subjects_ibfk_1` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dữ liệu mẫu cho bảng `subjects`
INSERT INTO `subjects` (`subject_code`, `subject_name`, `credits`, `department_id`) VALUES
('LTCB', 'Lập trình cơ bản', 3, 1),
('CSDL', 'Cơ sở dữ liệu', 4, 1),
('MMT', 'Mạng máy tính', 3, 1),
('KTPM', 'Kiểm thử phần mềm', 3, 1),
('JAVA', 'Lập trình Java', 4, 1),
('TTNT', 'Trí tuệ nhân tạo', 4, 2),
('KHDL', 'Khoa học dữ liệu', 4, 2),
('ML', 'Học máy', 3, 2),
('KTVM', 'Kế toán vốn mặt', 3, 3),
('KTTC', 'Kế toán tài chính', 4, 3),
('KTQL', 'Kế toán quản lý', 3, 3),
('NNHCB', 'Tiếng Hàn cơ bản', 4, 4),
('VHHN', 'Văn hóa Hàn Quốc', 3, 4),
('NNTCB', 'Tiếng Trung cơ bản', 4, 5),
('VHTQ', 'Văn hóa Trung Quốc', 3, 5),
('QTKD1', 'Quản trị kinh doanh 1', 3, 6),
('QTKD2', 'Quản trị kinh doanh 2', 3, 6),
('TMDT1', 'Marketing số', 4, 7),
('TMDT2', 'Thương mại điện tử', 4, 7),
('TT01', 'Truyền thông đại chúng', 3, 8);

-- --------------------------------------------------------
-- Cấu trúc bảng `grades`
-- --------------------------------------------------------
CREATE TABLE `grades` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `student_id` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `grade` float NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `student_subject` (`student_id`,`subject_id`),
  KEY `subject_id` (`subject_id`),
  CONSTRAINT `grades_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `grades_ibfk_2` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `check_grade` CHECK (`grade` >= 0 AND `grade` <= 10)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dữ liệu mẫu cho bảng `grades`
INSERT INTO `grades` (`student_id`, `subject_id`, `grade`) VALUES
(1, 1, 8.5),
(1, 2, 7.5),
(1, 3, 8.0),
(1, 4, 7.8),
(1, 5, 8.2),
(2, 1, 9.0),
(2, 2, 8.0),
(2, 3, 8.5),
(2, 4, 8.2),
(2, 5, 8.8),
(3, 1, 7.0),
(3, 2, 7.5),
(3, 3, 7.8),
(3, 4, 8.0),
(3, 5, 7.2),
(4, 1, 8.3),
(4, 2, 8.5),
(4, 3, 8.7),
(5, 1, 7.8),
(5, 2, 8.2),
(6, 6, 8.5),
(6, 7, 8.0),
(7, 6, 7.5),
(7, 7, 7.8),
(8, 6, 8.2),
(8, 7, 8.5),
(9, 9, 8.0),
(9, 10, 8.2),
(10, 9, 7.8),
(10, 10, 8.5),
(11, 11, 8.3),
(11, 12, 8.5),
(12, 13, 7.8),
(12, 14, 8.0),
(13, 15, 8.5),
(13, 16, 8.2),
(14, 17, 8.8),
(14, 18, 8.5),
(15, 19, 9.0),
(15, 20, 8.7);

COMMIT;
