<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Memuat...</title>
    <style>
        body {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            background: linear-gradient(135deg, #fce7f3, #fbcfe8);
            font-family: 'Segoe UI', Roboto, sans-serif;
            margin: 0;
        }
        .loading-box {
            background: #fff;
            padding: 40px 60px;
            border-radius: 20px;
            text-align: center;
            box-shadow: 0 20px 40px rgba(236, 72, 153, 0.15);
        }
        .loading-box h2 {
            color: #831843;
            font-size: 1.2rem;
            font-weight: 800;
            margin-bottom: 16px;
        }
        .spinner {
            width: 40px;
            height: 40px;
            border: 4px solid #fbcfe8;
            border-top-color: #ec4899;
            border-radius: 50%;
            margin: 0 auto;
            animation: spin 0.8s linear infinite;
        }
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
    </style>
    <script>
        // ==========================================
        // KUNCI UTAMA: window.location.replace()
        // Mengganti URL history saat ini dengan target
        // Sehingga user TIDAK bisa back ke halaman login
        // ==========================================
        (function() {
            var target = @json($target);
            
            // Replace history — gantikan halaman login-success ini dengan dashboard
            window.location.replace(target);
        })();
    </script>
</head>
<body>
    <div class="loading-box">
        <h2>✅ Login Berhasil</h2>
        <div class="spinner"></div>
        <p style="color: #9ca3af; font-size: 0.85rem; margin-top: 16px;">
            Mengarahkan ke dashboard...
        </p>
    </div>
</body>
</html>