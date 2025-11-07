<?php
// Bắt đầu phiên làm việc
session_start();

// Kiểm tra nếu người dùng đã đăng nhập (tồn tại user_id trong session)
if (isset($_SESSION['user_id'])) {
    // Nếu là admin thì chuyển đến trang quản lý điểm
    if ($_SESSION['role'] === 'admin') {
        // Redirect admin users to the new admin dashboard
        header('Location: /BTL_17-09/views/admin_dashboard.php');
    }
    // Nếu là sinh viên thì chuyển đến trang xem điểm
    else {
        header('Location: /BTL_17-09/views/home.php');
    }
    exit;
}
?>
<!DOCTYPE html>

?><html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Đăng nhập hệ thống</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="./css/login.css" rel="stylesheet">
    <meta name="robots" content="noindex,nofollow">
    <style>
        /* small overrides to ensure layout close to sample */
        .input-group-addon { width:44px; display:flex; align-items:center; justify-content:center; }
    </style>
</head>

<body class="mainLogin">
    <div class="formLogin">
        <div>
            <div>
                <a class="logo" style="margin-left: 40%">
                    <img style="height:auto; width:100px;" src="image/sutu.png" alt="logo">
                </a>
            </div>
            <h2 class="text-center">Đăng nhập tài khoản</h2>

            <div id="loginbox">
                <form action="/BTL_17-09/handle/login_process.php" method="post" id="loginform">
                    <div class="panel-body">
                        <div id="login-alert" class="alert alert-danger" style="display:none"></div>

                        <div style="margin-bottom: 8px;margin-top:22px" class="input-group">
                            <span class="input-group-text input-group-addon" style="color:#ff9c00;border:1px solid #d3d4d5;background :transparent"><i class="fa fa-home"></i></span>
                            <select class="form-select" id="roleSelect" name="role" style="font-weight:bold;border:1px solid #d3d4d5">
                                <option value="student">Sinh Viên</option>
                                <option value="admin">Quản Trị Hệ Thống</option>
                            </select>
                        </div>

                        <div style="margin-bottom: 8px;" class="input-group">
                            <span class="input-group-text input-group-addon" style="color:#1272b7;border:1px solid #d3d4d5;background :transparent">
                                <i class="fa fa-user"></i>
                            </span>
                            <input class="form-control" id="UserName" name="username" placeholder="Tài khoản" style=" border:1px solid #d3d4d5" type="text" value="">
                        </div>

                        <div style="margin-bottom: 8px;opacity: 0.9" class="input-group">
                            <span class="input-group-text input-group-addon" style=" color:#625c5c;border:1px solid #d3d4d5;background :transparent"><i class="fa fa-lock"></i></span>
                            <input id="password-field" class="form-control" name="password" placeholder="Mật khẩu" style=" border:1px solid #d3d4d5" type="password">
                            <span class="input-group-text input-group-addon" style="color: #625c5c; border: 1px solid #d3d4d5; background: transparent "><i id="pass-status" class="fa fa-eye" aria-hidden="true" onclick="viewPassword()"></i></span>
                        </div>

                       

                        <!-- Messages -->
                        <?php if (isset($_SESSION['error'])): ?>
                            <div class="alert alert-danger" role="alert">
                                <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                            </div>
                        <?php endif; ?>

                        <?php if (isset($_SESSION['success'])): ?>
                            <div class="alert alert-success" role="alert">
                                <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                            </div>
                        <?php endif; ?>

                        <div class="d-flex justify-content-center gap-3 mt-3">
                            <button type="submit" class="btn btn-info" name="login" style="opacity:0.9"><i class="fa fa-sign-in"></i> Đăng nhập</button>
                            <a id="btn-login" href="/BTL_17-09/views/home.php" class="btn btn-danger" style="opacity:0.8"><i class="fa fa-home"></i> Trang chủ</a>
                        </div>

                    </div>
                </form>
            </div>

        </div>

    </div>

   

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        // Thiết lập giá trị mặc định cho select role là 'student'
        $(function(){
            $('#roleSelect').val('student');
        });

        // Hàm xử lý hiển thị/ẩn mật khẩu khi click vào icon mắt
        function viewPassword() {
            var passwordInput = document.getElementById('password-field');
            var passStatus = document.getElementById('pass-status');

            // Nếu đang ẩn mật khẩu thì chuyển sang hiện
            if (passwordInput.type == 'password') {
                passwordInput.type = 'text';
                passStatus.className = 'fa fa-eye-slash';
            }
            // Nếu đang hiện mật khẩu thì chuyển sang ẩn
            else {
                passwordInput.type = 'password';
                passStatus.className = 'fa fa-eye';
            }
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>