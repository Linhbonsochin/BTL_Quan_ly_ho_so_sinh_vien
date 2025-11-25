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
<html lang="vi">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Đăng nhập - Hệ thống</title>
    <meta name="robots" content="noindex,nofollow">
    <link rel="stylesheet" href="/BTL_17-09/assets/dist/style.css">
    <style>
        body {
            background: #f6f7fb;
        }

        .container-login {
            width: 900px;
            max-width: 98vw;
            min-height: 520px;
            margin: 60px auto;
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.15);
            display: flex;
            overflow: hidden;
            position: relative;
        }

        .panel {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 48px 32px;
            transition: all 0.6s cubic-bezier(0.68, -0.55, 0.27, 1.55);
        }

        .panel.left {
            background: #fff;
        }

        .panel.right {
            background: linear-gradient(135deg, #f18d9e 0%, #fff94c 100%);
            color: #fff;
            position: relative;
        }

        .panel.right .icon {
            font-size: 64px;
            margin-bottom: 16px;
        }

        .panel.right .desc {
            font-size: 16px;
            margin-bottom: 32px;
            opacity: 0.9;
        }

        .panel.right .switch-btn {
            margin-top: 16px;
            background: none;
            border: 2px solid #fff;
            color: #fff;
            border-radius: 30px;
            padding: 10px 32px;
            font-size: 18px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: background 0.2s, color 0.2s;
        }

        .panel.right .switch-btn:hover {
            background: #fff;
            color: #a51f21ff;
        }

        .panel.left .login-title {
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 24px;
            color: #222;
        }

        .panel.left form {
            width: 100%;
            max-width: 320px;
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .panel.left input {
            padding: 12px 16px;
            border-radius: 6px;
            border: 1.5px solid #ddd;
            font-size: 16px;
            outline: none;
            transition: border 0.2s;
        }

        .panel.left input:focus {
            border: 1.5px solid #167c8eff;
        }

        .panel.left .role-label {
            margin: 0 0 8px 0;
            font-weight: 600;
            color: #650d10ff;
            text-align: left;
            font-size: 15px;
        }

        .panel.left button[type="submit"] {
            background: linear-gradient(135deg, #c2e59c 0%, #64b3f4 100%);
            color: #fff;
            border: none;
            border-radius: 8px;
            padding: 18px 0;
            font-size: 16.5px;
            font-weight: 800;
            cursor: pointer;
            margin-bottom: 12px;
            transition: background 0.2s, transform 0.15s;
            box-shadow: 0 4px 16px 0 rgba(255, 81, 47, 0.08);
        }

        .panel.left button[type="submit"]:hover {
            background: linear-gradient(135deg, #f09819 0%, #ff512f 100%);
            transform: scale(1.04);
        }

        .panel.left .loginwith {
            text-align: center;
            color: #888;
            margin: 12px 0 0 0;
            font-size: 15px;
        }

        .panel.left .socials {
            display: flex;
            justify-content: center;
            gap: 16px;
            margin-top: 8px;
        }

        .panel.left .socials a {
            color: #444;
            font-size: 22px;
            transition: color 0.2s;
        }

        .panel.left .socials a:hover {
            color: #a83f0eff;
        }

        .panel.left .error-msg {
            color: #870d0dff;
            font-weight: 600;
            margin-top: 8px;
        }

        .panel.left .success-msg {
            color: #1b5e20;
            font-weight: 600;
            margin-top: 8px;
        }

        /* Flip effect (chỉ chuyển panel, không làm mờ form) */
        .container-login.flipped .panel.left {
            transform: translateX(100%) scale(0.98);
            /* opacity: 0.2; */
            /* pointer-events: none; */
        }

        .container-login.flipped .panel.right {
            transform: translateX(-100%);
        }

        .container-login .panel.left,
        .container-login .panel.right {
            transition: transform 0.7s cubic-bezier(0.68, -0.55, 0.27, 1.55);
        }

        @media (max-width: 900px) {
            .container-login {
                flex-direction: column;
                min-height: 700px;
                width: 98vw;
            }

            .panel {
                min-height: 320px;
            }
        }
    </style>
</head>

<body>
    <div class="container-login" id="loginContainer">
        <div class="panel left">
            <div class="login-title">Đăng nhập</div>
            <form action="/BTL_17-09/handle/login_process.php" method="post" id="loginForm">
                <input name="username" placeholder="Tài khoản" type="text" required>
                <input id="password-field" name="password" placeholder="Mật khẩu" type="password" required>
                <input type="hidden" name="role" id="roleInput" value="student">
                <div class="role-label" id="roleLabel">Đăng nhập: Sinh viên</div>
                <?php if (isset($_SESSION['error'])): ?>
                    <div class="error-msg">
                        <?php echo $_SESSION['error'];
                        unset($_SESSION['error']); ?>
                    </div>
                <?php endif; ?>
                <?php if (isset($_SESSION['success'])): ?>
                    <div class="success-msg">
                        <?php echo $_SESSION['success'];
                        unset($_SESSION['success']); ?>
                    </div>
                <?php endif; ?>
                <button type="submit" name="login">Đăng nhập</button>
            </form>
            <div class="loginwith">Or Connect with</div>
            <div class="socials">
                <a href="#" title="Facebook"><svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z" />
                    </svg></a>
                <a href="#" title="Github"><svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 2C6.48 2 2 6.48 2 12c0 4.42 2.87 8.17 6.84 9.49.5.09.66-.22.66-.48 0-.24-.01-.87-.01-1.7-2.78.6-3.37-1.34-3.37-1.34-.45-1.15-1.1-1.46-1.1-1.46-.9-.62.07-.6.07-.6 1 .07 1.53 1.03 1.53 1.03.89 1.52 2.34 1.08 2.91.83.09-.65.35-1.08.63-1.33-2.22-.25-4.56-1.11-4.56-4.95 0-1.09.39-1.98 1.03-2.68-.1-.25-.45-1.27.1-2.65 0 0 .84-.27 2.75 1.02A9.56 9.56 0 0 1 12 6.8c.85.004 1.71.12 2.51.35 1.91-1.29 2.75-1.02 2.75-1.02.55 1.38.2 2.4.1 2.65.64.7 1.03 1.59 1.03 2.68 0 3.85-2.34 4.7-4.57 4.95.36.31.68.92.68 1.85 0 1.33-.01 2.4-.01 2.73 0 .27.16.58.67.48A10.01 10.01 0 0 0 22 12c0-5.52-4.48-10-10-10z" />
                    </svg></a>
            </div>
        </div>
        <div class="panel right" id="panelRight">
            <div class="icon" id="panelIcon">
                <svg width="64" height="64" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <circle cx="12" cy="8" r="4" />
                    <path d="M6 20v-2a4 4 0 0 1 4-4h0a4 4 0 0 1 4 4v2" />
                </svg>
            </div>
            <div class="panel-title" id="panelTitle"
                style="font-size:28px;font-weight:700;margin-bottom:8px;">Sinh viên</div>
            <div class="desc" id="panelDesc">Đăng nhập bằng tài khoản sinh viên</div>
            <button class="switch-btn" id="switchBtn">
                QUẢN TRỊ HỆ THỐNG
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" stroke="currentColor"
                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10" />
                    <polyline points="12 16 16 12 12 8" />
                    <line x1="8" y1="12" x2="16" y2="12" />
                </svg>
            </button>
        </div>
    </div>
    <script>
        // Flip logic
        const loginContainer = document.getElementById('loginContainer');
        const switchBtn = document.getElementById('switchBtn');
        const roleInput = document.getElementById('roleInput');
        const roleLabel = document.getElementById('roleLabel');
        const panelTitle = document.getElementById('panelTitle');
        const panelDesc = document.getElementById('panelDesc');
        const panelIcon = document.getElementById('panelIcon');
        let isAdmin = false;
        switchBtn.addEventListener('click', function () {
            isAdmin = !isAdmin;
            if (isAdmin) {
                loginContainer.classList.add('flipped');
                roleInput.value = 'admin';
                roleLabel.textContent = 'Đăng nhập: Quản trị hệ thống';
                panelTitle.textContent = 'Quản trị hệ thống';
                panelDesc.textContent = 'Đăng nhập bằng tài khoản quản trị';
                panelIcon.innerHTML = '<svg width="64" height="64" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="8.5" cy="7" r="4"/></svg>';
                switchBtn.innerHTML = 'SINH VIÊN <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 8 8 12 12 16"/><line x1="16" y1="12" x2="8" y2="12"/></svg>';
            } else {
                loginContainer.classList.remove('flipped');
                roleInput.value = 'student';
                roleLabel.textContent = 'Đăng nhập: Sinh viên';
                panelTitle.textContent = 'Sinh viên';
                panelDesc.textContent = 'Đăng nhập bằng tài khoản sinh viên';
                panelIcon.innerHTML = '<svg width="64" height="64" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="8" r="4"/><path d="M6 20v-2a4 4 0 0 1 4-4h0a4 4 0 0 1 4 4v2"/></svg>';
                switchBtn.innerHTML = 'QUẢN TRỊ HỆ THỐNG <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 16 16 12 12 8"/><line x1="8" y1="12" x2="16" y2="12"/></svg>';
            }
        });
    </script>
</body>

</html>