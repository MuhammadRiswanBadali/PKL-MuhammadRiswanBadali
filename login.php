<?php
include ('koneksi.php');
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE username='$username'";
    $query = mysqli_query($koneksi, $sql) or die("Error database: " . mysqli_error($koneksi));

    if (mysqli_num_rows($query) > 0) {
        $row = mysqli_fetch_assoc($query);

        if (password_verify($password, $row['password'])) {
            $_SESSION['loggedin'] = 1;
            $_SESSION['username'] = $row['username'];
            $_SESSION['nama']     = $row['nama_lengkap'];
            $_SESSION['role']     = $row['role'];
            $_SESSION['id_user']  = $row['id_user'];

            if ($row['role'] === 'admin') {
                header('Location: dashboard.php'); 
            } 
            else if ($row['role'] === 'petugas') {
                header('Location: dashboard_petugas.php');
            } 
            else {
                header('Location: logout.php');
            }
            exit;

        } else {
            $error = "Username atau password salah!";
        }
    } else {
        $error = "Akun tidak ditemukan!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistem DLH</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            overflow: hidden;
            background: linear-gradient(135deg, #1a2980 0%, #26d0ce 100%);
        }
        
        .fullscreen-container {
            display: flex;
            min-height: 100vh;
            width: 100vw;
        }
        
        .left-panel {
            flex: 1;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            padding: 60px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            color: white;
            position: relative;
            overflow: hidden;
        }
        
        .left-panel::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('https://images.unsplash.com/photo-1551288049-bebda4e38f71?ixlib=rb-4.0.3&auto=format&fit=crop&w=1470&q=80') center/cover;
            opacity: 0.2;
            z-index: -1;
        }
        
        /* .logo-area {
            margin-bottom: 40px;
        } */
        
        /* .logo {
            font-size: 1rem;
            color: white;
            margin-bottom: 10px;
        } */
        
        .system-title {
            font-size: 8rem;
            font-weight: 700;
            margin-bottom: 10px;
            background: linear-gradient(45deg, #fff, #b3e5fc);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        
        .features-list {
            list-style: none;
            margin-top: 40px;
        }
        
        .features-list li {
            margin-bottom: 20px;
            font-size: 1.1rem;
            display: flex;
            align-items: center;
        }
        
        .features-list i {
            margin-right: 15px;
            font-size: 1.3rem;
            color: #4fc3f7;
        }
        
        .right-panel {
            flex: 1;
            background: white;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px;
        }
        
        .login-card {
            width: 100%;
            max-width: 450px;
            animation: slideIn 0.8s ease-out;
        }
        
        .login-header {
            text-align: center;
            margin-bottom: 40px;
        }
        
        .login-header h2 {
            color: #333;
            font-weight: 600;
            margin-bottom: 15px;
            font-size: 4rem;
        }
        
        .login-header p {
            color: #666;
            font-size: 2rem;
        }
        
        .form-group {
            position: relative;
            margin-bottom: 25px;
        }
        
        .form-control {
            width: 100%;
            padding: 16px 20px 16px 50px;
            border: 2px solid #e1e5e9;
            border-radius: 10px;
            font-size: 16px;
            transition: all 0.3s;
            background: #f8f9fa;
        }
        
        .form-control:focus {
            border-color: #1a2980;
            box-shadow: 0 0 0 3px rgba(26, 41, 128, 0.1);
            background: white;
            outline: none;
        }
        
        .form-icon {
            position: absolute;
            left: 20px;
            top: 50%;
            transform: translateY(-50%);
            color: #7b8793;
            font-size: 18px;
        }
        
        .btn-login {
            width: 100%;
            padding: 16px;
            background: linear-gradient(135deg, #1a2980 0%, #26d0ce 100%);
            border: none;
            border-radius: 10px;
            color: white;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            margin-top: 10px;
        }
        
        .btn-login:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(26, 41, 128, 0.2);
        }
        
        .btn-login:active {
            transform: translateY(-1px);
        }
        
        .alert-danger {
            background: linear-gradient(135deg, #ff6b6b 0%, #ee5a52 100%);
            color: white;
            border: none;
            border-radius: 10px;
            padding: 15px 20px;
            margin-top: 20px;
            animation: shake 0.5s;
        }
        
        .alert-danger i {
            margin-right: 10px;
        }
        
        .footer-links {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
        }
        
        .footer-links a {
            color: #666;
            text-decoration: none;
            font-size: 14px;
            margin: 0 10px;
            transition: color 0.3s;
        }
        
        .footer-links a:hover {
            color: #1a2980;
        }
        
        /* Animations */
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateX(50px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
        
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }
        
        /* Responsive Design */
        @media (max-width: 1024px) {
            .fullscreen-container {
                flex-direction: column;
            }
            
            .left-panel {
                padding: 30px;
                min-height: 300px;
            }
            
            .system-title {
                font-size: 2rem;
            }
            
            .right-panel {
                padding: 30px;
                min-height: calc(100vh - 300px);
            }
        }
        
        @media (max-width: 768px) {
            .left-panel {
                padding: 20px;
                min-height: 250px;
            }
            
            .system-title {
                font-size: 1.8rem;
            }
            
            .features-list {
                display: none;
            }
            
            .right-panel {
                padding: 20px;
            }
            
            .login-card {
                max-width: 100%;
            }
        }
        
        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }
        
        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }
        
        ::-webkit-scrollbar-thumb {
            background: #1a2980;
            border-radius: 4px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: #26d0ce;
        }
    </style>
</head>
<body>
    <div class="fullscreen-container">
        <!-- Left Panel - Branding & Info -->
        <div class="left-panel">
            <div class="logo-area">
                <!-- <div class="logo">
                    <img src="img/logo_kalsel.png" alt="Logo DLH" class="logo-img">
                </div> -->
                <h1 class="system-title">SISTEM INFORMASI MANAJEMEN DATA PEMANTAUAN KUALITAS UDARA </h1>
            </div>
            
        </div>
        
        <!-- Right Panel - Login Form -->
        <div class="right-panel">
            <div class="login-card">
                <div class="login-header">
                    <h2>Masuk ke Sistem</h2>
                    <p>Silakan masukkan kredensial Anda</p>
                </div>
                
                <form method="post" action="" class="login-form">
                    <div class="form-group">
                        <i class="fas fa-user form-icon"></i>
                        <input type="text" 
                               name="username" 
                               class="form-control" 
                               placeholder="Username" 
                               autofocus 
                               required>
                    </div>
                    
                    <div class="form-group">
                        <i class="fas fa-lock form-icon"></i>
                        <input type="password" 
                               name="password" 
                               class="form-control" 
                               placeholder="Password" 
                               required>
                    </div>
                    
                    <button type="submit" 
                            name="btnLogin" 
                            class="btn-login">
                        <i class="fas fa-sign-in-alt"></i> Masuk ke Sistem
                    </button>
                    
                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger" role="alert">
                            <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                        </div>
                    <?php endif; ?>
                    
                    <div class="footer-links">
                        <a href="#" onclick="alert('Hubungi Administrator Sistem');">
                            <i class="fas fa-key"></i> Lupa Password?
                        </a>
                        <a href="#" onclick="alert('Hubungi Bagian IT DLH');">
                            <i class="fas fa-user-plus"></i> Daftar Akun Baru
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Animasi untuk form
        document.addEventListener('DOMContentLoaded', function() {
            // Focus effect untuk input fields
            const inputs = document.querySelectorAll('.form-control');
            inputs.forEach(input => {
                input.addEventListener('focus', function() {
                    this.parentElement.classList.add('focused');
                });
                
                input.addEventListener('blur', function() {
                    if (!this.value) {
                        this.parentElement.classList.remove('focused');
                    }
                });
            });
            
            // Animasi pada submit
            const form = document.querySelector('.login-form');
            form.addEventListener('submit', function(e) {
                const btn = this.querySelector('.btn-login');
                btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memproses...';
                btn.disabled = true;
            });
            
            // Background color change on load
            document.body.style.opacity = '0';
            setTimeout(() => {
                document.body.style.transition = 'opacity 0.5s';
                document.body.style.opacity = '1';
            }, 100);
            
            // Prevent form resubmission on refresh
            if (window.history.replaceState) {
                window.history.replaceState(null, null, window.location.href);
            }
        });
        
        // Keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            // Ctrl + Enter untuk submit
            if (e.ctrlKey && e.key === 'Enter') {
                document.querySelector('.btn-login').click();
            }
            
            // Escape untuk reset form
            if (e.key === 'Escape') {
                document.querySelector('.login-form').reset();
            }
        });
    </script>
</body>
</html>