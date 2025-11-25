--
-- ========================================
-- Chức năng: Định nghĩa cấu trúc và dữ liệu mẫu cho hệ thống quản lý hồ sơ sinh viên
-- Các phần chính: users, departments, majors, classes, students, grades
-- ========================================
--
-- --------------------------------------------------------
-- HỆ THỐNG QUẢN LÝ HỒ SƠ SINH VIÊN
-- Mô tả: Database quản lý thông tin sinh viên, điểm số, khoa, lớp và người dùng
-- --------------------------------------------------------
-- Xóa database nếu tồn tại và tạo mới
DROP DATABASE IF EXISTS `quanlyhososinhvien`;
CREATE DATABASE IF NOT EXISTS `quanlyhososinhvien` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `quanlyhososinhvien`;

--
-- Phần 1: Cấu trúc bảng `users` (Tài khoản người dùng: admin, sinh viên)
--
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

--
-- Phần 2: Cấu trúc bảng `departments` (Khoa/Phòng ban)
--
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
('DUOC', 'KHOA DƯỢC HỌC'),
('DD', 'KHOA ĐIỀU DƯỠNG'),
('TCNH', 'TÀI CHÍNH – NGÂN HÀNG'),
('KT', 'KHOA KẾ TOÁN'),
('QTKD', 'KHOA QUẢN TRỊ KINH DOANH'),
('QTDL', 'KHOA QUẢN TRỊ DU LỊCH VÀ LỮ HÀNH'),
('LKT', 'KHOA LUẬT KINH TẾ'),
('CNTT', 'KHOA CÔNG NGHỆ THÔNG TIN'),
('QHCC', 'KHOA QUAN HỆ CÔNG CHÚNG – TRUYỀN THÔNG'),
('NNA', 'KHOA NGÔN NGỮ ANH'),
('NNTQ', 'KHOA NGÔN NGỮ TRUNG QUỐC'),
('NNN', 'KHOA NGÔN NGỮ NHẬT BẢN'),
('NNH', 'KHOA NGÔN NGỮ HÀN QUỐC');
;


-- --------------------------------------------------------
-- Cấu trúc bảng `majors` (ngành học)
-- --------------------------------------------------------
CREATE TABLE `majors` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `major_code` varchar(20) NOT NULL,
  `major_name` varchar(100) NOT NULL,
  `department_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `major_code` (`major_code`),
  KEY `department_id` (`department_id`),
  CONSTRAINT `majors_ibfk_1` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dữ liệu mẫu cho bảng `majors`
INSERT INTO `majors` (`major_code`, `major_name`, `department_id`) VALUES
('DUOC', 'Dược học', 1),
('DD', 'Điều dưỡng', 2),
('TCNH', 'Tài chính ngân hàng', 3),
('KTOAN', 'Kế toán', 4),
('QTKD', 'Quản trị kinh doanh', 5),
('QTDVDL', 'Quản trị dịch vụ du lịch và lữ hành', 6),
('LKT', 'Luật kinh tế', 7),
('LUAT', 'Luật', 7),
('CNTT', 'Công nghệ thông tin', 8),
('KHMT', 'Khoa học máy tính', 8),
('HTTT', 'Hệ thống thông tin', 8),
('TTDPT', 'Truyền thông đa phương tiện', 9),
('QHCC', 'Quan hệ công chúng', 9),
('NNA', 'Ngôn ngữ Anh', 10),
('NNTQ', 'Ngôn ngữ Trung Quốc', 11),
('NNN', 'Ngôn ngữ Nhật', 12),
('NNH', 'Ngôn ngữ Hàn Quốc', 13);

-- --------------------------------------------------------
-- Cấu trúc bảng `classes`
-- --------------------------------------------------------
CREATE TABLE `classes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `class_code` varchar(20) NOT NULL,
  `class_name` varchar(100) NOT NULL,
  `department_id` int(11) DEFAULT NULL,
  `major_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `class_code` (`class_code`),
  KEY `department_id` (`department_id`),
  KEY `major_id` (`major_id`),
  CONSTRAINT `classes_ibfk_1` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `classes_ibfk_2` FOREIGN KEY (`major_id`) REFERENCES `majors` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dữ liệu mẫu cho bảng `classes`
INSERT INTO `classes` (`class_code`, `class_name`, `department_id`, `major_id`) VALUES
('CNTT1', 'Công nghệ thông tin 1', 8, 9),
('CNTT2', 'Công nghệ thông tin 2', 8, 9),
('KHMT1', 'Khoa học máy tính 1', 8, 10),
('HTTT1', 'Hệ thống thông tin 1', 8, 11),
('KTOAN1', 'Kế toán 1', 4, 4),
('QTKD1', 'Quản trị kinh doanh 1', 5, 5),
('QTDL1', 'Quản trị du lịch 1', 6, 6),
('LUAT1', 'Luật 1', 7, 8),
('NNA1', 'Ngôn ngữ Anh 1', 10, 14),
('NNH1', 'Ngôn ngữ Hàn 1', 13, 17),
('TTDPT1', 'Truyền thông đa phương tiện 1', 9, 12),
('NNTQ1', 'Ngôn ngữ Trung Quốc 1', 11, 15),
('NNN1', 'Ngôn ngữ Nhật Bản 1', 12, 16);

-- --------------------------------------------------------
-- Cấu trúc bảng `students`
-- --------------------------------------------------------
CREATE TABLE `students` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `student_code` varchar(20) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `birth_date` date DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `phone_alt` varchar(50) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `student_email` varchar(255) DEFAULT NULL,
  `gender` enum('Nam','Nữ') DEFAULT NULL,
  `address` text DEFAULT NULL,
  `class_id` int(11) DEFAULT NULL,
  `department_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `nationality` varchar(100) DEFAULT NULL,
  `ethnicity` varchar(100) DEFAULT NULL,
  `religion` varchar(100) DEFAULT NULL,
  `cccd_number` varchar(50) DEFAULT NULL,
  `cccd_place` varchar(255) DEFAULT NULL,
  `cccd_issue_date` date DEFAULT NULL,
  `education_system` varchar(100) DEFAULT NULL,
  `major` varchar(255) DEFAULT NULL,
  `course_year` varchar(50) DEFAULT NULL,
  `admission_batch` varchar(50) DEFAULT NULL,
  `is_union_member` tinyint(1) DEFAULT 0,
  `is_party_member` tinyint(1) DEFAULT 0,
  `join_date` date DEFAULT NULL,
  `grade12_academic` varchar(100) DEFAULT NULL,
  `grade12_conduct` varchar(100) DEFAULT NULL,
  `permanent_province` varchar(150) DEFAULT NULL,
  `permanent_ward` varchar(150) DEFAULT NULL,
  `permanent_address` text DEFAULT NULL,
  `current_province` varchar(150) DEFAULT NULL,
  `current_ward` varchar(150) DEFAULT NULL,
  `current_address` text DEFAULT NULL,
  `birth_province` varchar(150) DEFAULT NULL,
  `birth_ward` varchar(150) DEFAULT NULL,
  `bank_name` varchar(255) DEFAULT NULL,
  `bank_account` varchar(100) DEFAULT NULL,
  `is_offcampus` tinyint(1) DEFAULT 0,
  `dorm_address` text DEFAULT NULL,
  `residence_term` varchar(100) DEFAULT NULL,
  `residence_year` varchar(50) DEFAULT NULL,
  `reporter_name` varchar(255) DEFAULT NULL,
  `reporter_phone` varchar(50) DEFAULT NULL,
  `contact_address` text DEFAULT NULL,
  `avatar_path` varchar(255) DEFAULT NULL,
  `cccd_front_path` varchar(255) DEFAULT NULL,
  `cccd_back_path` varchar(255) DEFAULT NULL,
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
INSERT INTO `students` (`student_code`, `full_name`, `birth_date`, `phone`, `email`, `gender`, `address`, `class_id`, `department_id`, `user_id`, `avatar_path`, `cccd_front_path`, `cccd_back_path`) VALUES
('SV001', 'Nguyễn Văn An', '2000-01-01', '0901234567', 'an.nguyen@example.com', 'Nam', 'Hà Nội', 1, 8, 2, NULL, NULL, NULL),
('SV002', 'Trần Thị Bình', '2000-02-02', '0912345678', 'binh.tran@example.com', 'Nữ', 'TP HCM', 1, 8, 3, NULL, NULL, NULL),
('SV003', 'Lê Văn Cường', '2000-03-03', '0923456789', 'cuong.le@example.com', 'Nam', 'Đà Nẵng', 2, 8, 4, NULL, NULL, NULL),
('SV004', 'Phạm Thị Diễm', '2000-04-04', '0934567890', 'diem.pham@example.com', 'Nữ', 'Hải Phòng', 2, 8, 5, NULL, NULL, NULL),
('SV005', 'Hoàng Văn Em', '2000-05-05', '0945678901', 'em.hoang@example.com', 'Nam', 'Cần Thơ', 3, 8, 6, NULL, NULL, NULL),
('SV006', 'Ngô Thị Fa', '2000-06-06', '0956789012', 'fa.ngo@example.com', 'Nữ', 'Huế', 4, 4, 7, NULL, NULL, NULL),
('SV007', 'Đỗ Văn Giang', '2000-07-07', '0967890123', 'giang.do@example.com', 'Nam', 'Nha Trang', 4, 4, 8, NULL, NULL, NULL),
('SV008', 'Lý Thị Hương', '2000-08-08', '0978901234', 'huong.ly@example.com', 'Nữ', 'Đà Lạt', 5, 4, 9, NULL, NULL, NULL),
('SV009', 'Bùi Văn Inh', '2000-09-09', '0989012345', 'inh.bui@example.com', 'Nam', 'Vũng Tàu', 6, 5, 10, NULL, NULL, NULL),
('SV010', 'Mai Thị Kim', '2000-10-10', '0990123456', 'kim.mai@example.com', 'Nữ', 'Biên Hòa', 6, 5, 11, NULL, NULL, NULL),
('SV011', 'Trịnh Văn Lâm', '2000-11-11', '0901234567', 'lam.trinh@example.com', 'Nam', 'Hà Nội', 8, 7, 12, NULL, NULL, NULL),
('SV012', 'Vũ Thị Minh', '2000-12-12', '0912345678', 'minh.vu@example.com', 'Nữ', 'TP HCM', 9, 10, 13, NULL, NULL, NULL),
('SV013', 'Đinh Văn Nam', '2001-01-01', '0923456789', 'nam.dinh@example.com', 'Nam', 'Đà Nẵng', 10, 10, 14, NULL, NULL, NULL),
('SV014', 'Dương Thị Oanh', '2001-02-02', '0934567890', 'oanh.duong@example.com', 'Nữ', 'Hải Phòng', 11, 9, 15, NULL, NULL, NULL),
('SV015', 'Lương Văn Phú', '2001-03-03', '0945678901', 'phu.luong@example.com', 'Nam', 'Cần Thơ', 7, 6, 16, NULL, NULL, NULL);


-- --------------------------------------------------------
-- Cấu trúc bảng `static_pages` (thông báo, văn bản, tin tức)
-- --------------------------------------------------------
CREATE TABLE `static_pages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` enum('notification','document','news') NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text DEFAULT NULL,
  `published` tinyint(1) NOT NULL DEFAULT 0,
  `image_path` varchar(255) DEFAULT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dữ liệu mẫu cho `static_pages`
INSERT INTO `static_pages` (`type`, `title`, `content`, `published`, `image_path`, `file_path`) VALUES
('notification', 'Thông báo lịch thi học kỳ 1', 'Lịch thi học kỳ 1 sẽ được công bố vào ngày 01/12. Sinh viên chú ý kiểm tra lịch cụ thể trên hệ thống.', 1, NULL, NULL),
('notification', 'Thông báo nghỉ lễ', 'Nhà trường thông báo lịch nghỉ Tết Dương lịch từ 01/01 đến 03/01.', 1, NULL, NULL),
('document', 'Mẫu đơn xin rút môn', 'File mẫu đơn xin rút môn kèm theo hướng dẫn.', 1, NULL, '/BTL_17-09/image/static_files/mau_don_rut_mon.pdf'),
('document', 'Quy định học bù', 'Quy định và biểu mẫu học bù dành cho sinh viên.', 1, NULL, '/BTL_17-09/image/static_files/quy_dinh_hoc_bu.pdf'),
('news', 'Tin hoạt động sinh viên tháng 11', 'Tháng 11 có nhiều hoạt động bổ ích cho sinh viên, mời xem chi tiết.', 1, NULL, NULL),
('news', 'Hội thảo công nghệ 2025', 'Mời sinh viên tham dự hội thảo về công nghệ mới.', 1, NULL, NULL);

-- --------------------------------------------------------
-- Cấu trúc bảng `subjects`
-- --------------------------------------------------------
CREATE TABLE `subjects` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `subject_code` varchar(20) NOT NULL,
  `subject_name` varchar(100) NOT NULL,
  `credits` int(11) DEFAULT NULL,
  `department_id` int(11) DEFAULT NULL,
  `major_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `subject_code` (`subject_code`),
  KEY `department_id` (`department_id`),
  KEY `major_id` (`major_id`),
  CONSTRAINT `subjects_ibfk_1` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `subjects_ibfk_2` FOREIGN KEY (`major_id`) REFERENCES `majors` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dữ liệu mẫu cho bảng `subjects`
INSERT INTO `subjects` (`subject_code`, `subject_name`, `credits`, `department_id`, `major_id`) VALUES
('LTCB', 'Lập trình cơ bản', 3, 8, 9),
('CSDL', 'Cơ sở dữ liệu', 4, 8, 9),
('MMT', 'Mạng máy tính', 3, 8, 9),
('KTPM', 'Kiểm thử phần mềm', 3, 8, 9),
('JAVA', 'Lập trình Java', 4, 8, 9),
('TTNT', 'Trí tuệ nhân tạo', 4, 8, 10),
('KHDL', 'Khoa học dữ liệu', 4, 8, 10),
('ML', 'Học máy', 3, 8, 10),
('KTVM', 'Kế toán vốn mặt', 3, 4, 4),
('KTTC', 'Kế toán tài chính', 4, 4, 4),
('KTQL', 'Kế toán quản lý', 3, 4, 4),
('NNHCB', 'Tiếng Hàn cơ bản', 4, 13, 17),
('VHHN', 'Văn hóa Hàn Quốc', 3, 13, 17),
('NNTCB', 'Tiếng Trung cơ bản', 4, 11, 15),
('VHTQ', 'Văn hóa Trung Quốc', 3, 11, 15),
('QTKD1', 'Quản trị kinh doanh 1', 3, 5, 5),
('QTKD2', 'Quản trị kinh doanh 2', 3, 5, 5),
('TTDPT', 'Truyền thông đa phương tiện', 3, 9, 12),
('QHCC', 'Quan hệ công chúng', 3, 9, 13);

-- --------------------------------------------------------
-- Cấu trúc bảng `grades`
-- --------------------------------------------------------
CREATE TABLE `grades` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `student_id` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `attendance_score` float DEFAULT NULL,
  `midterm_score` float DEFAULT NULL,
  `final_score` float DEFAULT NULL,
  `total_score` float DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `student_subject` (`student_id`,`subject_id`),
  KEY `subject_id` (`subject_id`),
  CONSTRAINT `grades_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `grades_ibfk_2` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dữ liệu mẫu cho bảng `grades`
INSERT INTO `grades` (`student_id`, `subject_id`, `attendance_score`, `midterm_score`, `final_score`, `total_score`) VALUES
(1, 1, 8.0, 8.5, 9.0, 8.5),
(1, 2, 7.5, 7.0, 8.0, 7.5),
(1, 3, 8.0, 8.0, 8.0, 8.0),
(1, 4, 7.8, 7.5, 8.0, 7.8),
(1, 5, 8.2, 8.0, 8.5, 8.2),
(2, 1, 9.0, 8.5, 9.5, 9.0),
(2, 2, 8.0, 8.0, 8.0, 8.0),
(3, 1, 7.0, 7.5, 7.5, 7.3),
(3, 2, 7.5, 7.0, 8.0, 7.5),
(3, 3, 7.8, 7.5, 8.0, 7.8),
(3, 4, 8.0, 8.0, 8.0, 8.0),
(3, 5, 7.2, 7.0, 7.5, 7.2),
(4, 1, 8.3, 8.0, 8.5, 8.3),
(4, 2, 8.5, 8.0, 9.0, 8.5),
(4, 3, 8.7, 8.5, 9.0, 8.7),
(5, 1, 7.8, 7.5, 8.0, 7.8),
(5, 2, 8.2, 8.0, 8.5, 8.2),
(6, 6, 8.5, 8.0, 9.0, 8.5),
(6, 7, 8.0, 7.5, 8.5, 8.0),
(7, 7, 7.8, 7.5, 8.0, 7.8),
(8, 6, 8.2, 8.0, 8.5, 8.2),
(8, 7, 8.5, 8.0, 9.0, 8.5),
(9, 9, 8.0, 7.5, 8.5, 8.0),
(9, 10, 8.2, 8.0, 8.5, 8.2),
(10, 9, 7.8, 7.5, 8.0, 7.8),
(10, 10, 8.5, 8.0, 9.0, 8.5),
(11, 11, 8.3, 8.0, 8.5, 8.3),
(11, 12, 8.5, 8.0, 9.0, 8.5),
(12, 13, 7.8, 7.5, 8.0, 7.8),
(12, 14, 8.0, 7.5, 8.5, 8.0),
(13, 15, 8.5, 8.0, 9.0, 8.5),
(13, 16, 8.2, 8.0, 8.5, 8.2),
(14, 17, 8.8, 8.5, 9.0, 8.8),
(14, 18, 8.5, 8.0, 9.0, 8.5),
(15, 19, 9.0, 8.5, 9.5, 9.0),
(15, 2, 8.7, 8.0, 9.0, 8.7);

COMMIT;
