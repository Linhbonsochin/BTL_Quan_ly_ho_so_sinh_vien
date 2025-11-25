
// =============================

// Chức năng: Xử lý chuyển đổi giao diện đăng nhập giữa Quản trị hệ thống và Sinh viên
// =============================

const registerButton = document.getElementById('register') // Nút chuyển sang giao diện quản trị
const loginButton = document.getElementById('login') // Nút chuyển sang giao diện sinh viên
const container = document.getElementById('container') // Khối chứa giao diện đăng nhập

// Hàm chuyển đổi vai trò đăng nhập (admin/sinh viên)
function switchRole(role) {
	// Tìm select[name="role"] hoặc input ẩn name="role" trong trang
	var sel = document.querySelector('select[name="role"]');
	if (sel) {
		sel.value = role;
	} else {
		var hid = document.querySelector('input[name="role"]');
		if (hid) hid.value = role;
	}
	// Tự động focus vào ô nhập tên đăng nhập
	var user = document.querySelector('input[name="username"]');
	if (user) user.focus();
	// Hiển thị nhãn vai trò nếu có
	var lbl = document.getElementById('roleLabel');
	if (lbl) {
		lbl.textContent = (role === 'admin') ? 'Đăng nhập: Quản trị hệ thống' : 'Đăng nhập: Sinh viên';
	}
}

// Xử lý khi nhấn nút chuyển sang giao diện quản trị
if (registerButton) {
	registerButton.addEventListener('click', function(){
		container.className = 'close'; // Hiển thị form đăng nhập
		switchRole('admin'); // Chuyển sang vai trò admin
	});
}

// Xử lý khi nhấn nút chuyển sang giao diện sinh viên
if (loginButton) {
	loginButton.addEventListener('click', function(){
		container.className = 'close'; // Hiển thị form đăng nhập
		switchRole('student'); // Chuyển sang vai trò sinh viên
	});
}
