<?php
session_start();
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $remember = isset($_POST['remember']) ? true : false;
    
    $stmt = $conn->prepare("SELECT nama_lengkap FROM admin_users WHERE username=? AND password=? LIMIT 1");
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows == 1) {
        $stmt->bind_result($nama_lengkap);
        $stmt->fetch();
        $_SESSION['admin_login'] = true;
        $_SESSION['admin_nama'] = $nama_lengkap;
        
        // Handle remember me functionality - save credentials to cookie
        if ($remember) {
            $expires = time() + (30 * 24 * 60 * 60); // 30 days
            setcookie('remember_username', $username, $expires, '/');
            setcookie('remember_password', $password, $expires, '/');
        } else {
            // Clear remember me cookies if not checked
            setcookie('remember_username', '', time() - 3600, '/');
            setcookie('remember_password', '', time() - 3600, '/');
        }
        
        header("Location: login.php?success=Login%20berhasil!%20Mengalihkan%20ke%20admin...&rediradmin=1");
        return;
    } else {
        header("Location: login.php?error=Username%20atau%20password%20salah!&redir=1");
        return;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Admin Jadwal Sholat</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .login-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            max-width: 900px;
            width: 100%;
            margin: 20px;
        }
        .login-left {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 60px 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        .login-left::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="stars" x="0" y="0" width="20" height="20" patternUnits="userSpaceOnUse"><circle cx="10" cy="10" r="1" fill="rgba(255,255,255,0.1)"/></pattern></defs><rect width="100" height="100" fill="url(%23stars)"/></svg>');
            animation: float 20s ease-in-out infinite;
            z-index: 1;
        }
        .login-left > * {
            position: relative;
            z-index: 2;
        }
        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(180deg); }
        }
        .mosque-icon {
            font-size: 4rem;
            margin-bottom: 1rem;
            opacity: 0.9;
        }
        .login-right {
            padding: 60px 40px;
        }
        .form-control {
            border: 2px solid #e9ecef;
            border-radius: 12px;
            padding: 12px 16px;
            font-size: 16px;
            transition: all 0.3s ease;
        }
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        .input-group-text {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
            border-radius: 12px 0 0 12px;
        }
        .input-group .form-control {
            border-left: none;
            border-radius: 0 12px 12px 0;
        }
        .btn-login {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 12px;
            padding: 12px 30px;
            font-weight: 600;
            font-size: 16px;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.3);
        }
        .btn-login:active {
            transform: translateY(0);
        }
        .alert {
            border: none;
            border-radius: 12px;
            padding: 15px 20px;
        }
        .alert-danger {
            background: linear-gradient(135deg, #ff6b6b 0%, #ee5a52 100%);
            color: white;
        }
        .alert-success {
            background: linear-gradient(135deg, #51cf66 0%, #40c057 100%);
            color: white;
        }
        .form-check-input:checked {
            background-color: #667eea;
            border-color: #667eea;
        }
        .islamic-quote {
            font-style: italic;
            opacity: 0.8;
            font-size: 0.9rem;
            margin-top: 1rem;
        }
        .loading-spinner {
            display: none;
        }
        .btn-login.loading .loading-spinner {
            display: inline-block;
        }
        .btn-login.loading .btn-text {
            display: none;
        }
        @media (max-width: 768px) {
            .login-left {
                padding: 40px 20px;
            }
            .login-right {
                padding: 40px 20px;
            }
            .mosque-icon {
                font-size: 3rem;
            }
        }
        .prayer-times {
            margin-top: 2rem;
            font-size: 0.85rem;
            opacity: 0.8;
        }
    </style>
</head>
<body>    
    <div class="login-container">
        <div class="row g-0 h-100">
            <!-- Left Side - Islamic Theme -->
            <div class="col-lg-6 login-left">
                <div class="mosque-icon">
                    <i class="bi bi-moon-stars-fill"></i>
                </div>
                <h2 class="mb-3">Admin Jadwal Sholat</h2>
                <p class="lead mb-4">Sistem Manajemen Jadwal Sholat & Berita Islami</p>
                <div class="islamic-quote">
                    "وَأَقِيمُوا الصَّلَاةَ وَآتُوا الزَّكَاةَ"<br>
                    <small>"Dan dirikanlah sholat, tunaikanlah zakat"</small>
                </div>
            </div>
            <!-- Right Side - Login Form -->
            <div class="col-lg-6 login-right">
                <div class="text-center mb-4">
                    <h3 class="fw-bold text-dark">Selamat Datang</h3>
                    <p class="text-muted">Silakan masuk ke panel admin</p>
                </div>
                <!-- Alert Messages -->
                <div id="alertContainer"></div>
                <form id="loginForm" method="post" action="login.php">
                    <div class="mb-3">
                        <label for="username" class="form-label fw-semibold">Username atau Email</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="bi bi-person-fill"></i>
                            </span>
                            <input type="text" class="form-control" id="username" name="username" 
                                   placeholder="Masukkan username atau email" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label fw-semibold">Password</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="bi bi-lock-fill"></i>
                            </span>
                            <input type="password" class="form-control" id="password" name="password" 
                                   placeholder="Masukkan password" required>
                            <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                    </div>
                    <div class="mb-3 d-flex justify-content-between align-items-center">
                        <div class="form-check">
                            <input class="form-check-input-lg" type="checkbox" id="rememberMe" name="remember">
                            <label class="form-check-label" for="rememberMe">
                                Ingat saya
                            </label>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-login text-white w-100 mb-3">
                        <span class="btn-text">
                            <i class="bi bi-box-arrow-in-right me-2"></i>Masuk ke Admin
                        </span>
                        <span class="loading-spinner">
                            <i class="bi bi-arrow-clockwise spin"></i> Memproses...
                        </span>
                    </button>
                    <div class="text-center">
                        <small class="text-muted">
                            <i class="bi bi-shield-check me-1"></i>
                            Sistem dilindungi dengan enkripsi SSL
                        </small>
                    </div>
                </form>
                <hr class="my-4">
                <div class="text-center">
                    <small class="text-muted">
                        Butuh bantuan? Hubungi 
                        <a href="mailto:admin@telkomuniversity.ac.id" class="forgot-password">
                            admin@telkomuniversity.ac.id
                        </a>
                    </small>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('togglePassword').addEventListener('click', function() {
            const passwordInput = document.getElementById('password');
            const icon = this.querySelector('i');
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.classList.remove('bi-eye');
                icon.classList.add('bi-eye-slash');
            } else {
                passwordInput.type = 'password';
                icon.classList.remove('bi-eye-slash');
                icon.classList.add('bi-eye');
            }
        });

        // Show alert function
        function showAlert(message, type = 'danger') {
            const alertContainer = document.getElementById('alertContainer');
            alertContainer.innerHTML = `
                <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                    <i class="bi bi-${type === 'success' ? 'check-circle' : 'exclamation-triangle'} me-2"></i>
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `;
            setTimeout(() => {
                const alert = alertContainer.querySelector('.alert');
                if (alert) new bootstrap.Alert(alert).close();
            }, 2000);
        }
        document.getElementById('loginForm').addEventListener('submit', function() {
            const loginBtn = document.querySelector('.btn-login');
            loginBtn.classList.add('loading');
            loginBtn.disabled = true;
        });
        window.addEventListener('DOMContentLoaded', function() {
            const params = new URLSearchParams(window.location.search);
            if (params.has('error')) {
                showAlert(decodeURIComponent(params.get('error')));
                if (params.has('redir')) {
                    setTimeout(() => window.location.href = 'login.php', 2000);
                }
            }
            if (params.has('success')) {
                showAlert(decodeURIComponent(params.get('success')), 'success');
                if (params.get('success').includes('logout')) {
                    setTimeout(() => window.location.href = 'login.php', 2000);
                }
                if (params.has('rediradmin')) {
                    setTimeout(() => window.location.href = 'admin.php', 2000);
                }
            }
            
            // Check if remember me credentials exist and fill the form
            if (document.cookie.includes('remember_username') && document.cookie.includes('remember_password')) {
                // Get username and password from cookies
                const cookies = document.cookie.split(';');
                let savedUsername = '';
                let savedPassword = '';
                
                cookies.forEach(cookie => {
                    const [name, value] = cookie.trim().split('=');
                    if (name === 'remember_username') {
                        savedUsername = decodeURIComponent(value);
                    }
                    if (name === 'remember_password') {
                        savedPassword = decodeURIComponent(value);
                    }
                });
                
                if (savedUsername && savedPassword) {
                    document.getElementById('username').value = savedUsername;
                    document.getElementById('password').value = savedPassword;
                    document.getElementById('rememberMe').checked = true;
                }
            }
        });
        const style = document.createElement('style');
        style.textContent = `
            .spin {
                animation: spin 1s linear infinite;
            }
            @keyframes spin {
                from { transform: rotate(0deg); }
                to { transform: rotate(360deg); }
            }
        `;
        document.head.appendChild(style);
        document.querySelectorAll('.form-control').forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.style.transform = 'scale(1.02)';
                this.parentElement.style.transition = 'transform 0.2s ease';
            });
            input.addEventListener('blur', function() {
                this.parentElement.style.transform = 'scale(1)';
            });
        });
    </script>
</body>
</html>