<?php
// --- 設定區 ---
$site_title = "小Chen小舖|各種專業買賣";
$bg_html_file = "background.html"; // 你的流星雨背景檔

// 主 LOGO 圖片路徑
$logo_image = "LOGO.png"; 

// 服務項目
$services = [
    ["id" => "accounts", "title" => "三角洲帳號", "description" => "遊戲詳圖幫我直接點下方查看👇🏻", "link" => "三角洲.php"]
];

// ▼ 社群 ICON 設定區（完整最終排列順序：LINE, IG, FB, TK, DC, Threads） ▼
$social_links = [
    "LINE" => [
        "url" => "https://line.me/R/ti/p/@682gddht", 
        "img" => "https://img.icons8.com/color/48/line-me.png" // 正版 LINE 圖示
    ],
    "IG" => [
        "url" => "https://www.instagram.com/xiao_chen_1112?igsh=OXA5eXRsenVsZXN0&utm_source=qr", 
        "img" => "https://img.icons8.com/fluency/48/instagram-new.png"
    ],
    "FB" => [
        "url" => "https://www.facebook.com/chen.yuting.439481?locale=zh_TW", 
        "img" => "https://img.icons8.com/fluency/48/facebook-new.png"
    ],
    "TK" => [
        "url" => "https://www.tiktok.com/@chenyuting111", 
        "img" => "https://img.icons8.com/fluency/48/tiktok.png"
    ],
    "DC" => [
        "url" => "https://discord.gg/5ZGRXmfxwb", 
        "img" => "https://img.icons8.com/fluency/48/discord-logo.png"
    ],
    "Threads" => [
        "url" => "https://www.threads.com/@xiao_chen_1112?igshid=NTc4MTIwNjQ2YQ==", 
        "img" => "https://img.icons8.com/ios-filled/48/ffffff/threads.png" // 高質感純白 Threads 圖示
    ]
];
?>
<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <link rel="stylesheet" href="https://fonts.googleapis.com/earlyaccess/cwtexyen.css">
    <link rel="icon" href="MARK.ico" type="image/x-icon">
    <title><?php echo $site_title; ?></title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700&family=Noto+Sans+TC:wght@300;500&display=swap');
        
        :root { --accent: #00f2ff; --glass: rgba(25, 25, 30, 0.6); }
        html { scroll-behavior: smooth; height: 100%; }
        
        /* 🚀 【背景全螢幕核心設定】：使用 fixed 固定，寬高設定 vw/vh 並關閉邊框，保證手機滾動時背景絕對滿版 */
        .bg-iframe { 
            position: fixed; 
            top: 0; 
            left: 0; 
            width: 100vw; 
            height: 100vh; 
            border: none; 
            z-index: -100; 
            pointer-events: none; 
        }
        
        body { 
            margin: 0; 
            background: transparent; 
            color: #fff; 
            font-family: 'Noto Sans TC', sans-serif; 
            overflow-x: hidden; 
            min-height: 100vh;
        }
        
        /* === 導覽列 === */
        nav { 
            position: fixed; top: 0; width: 100%; padding: 10px 50px; box-sizing: border-box;
            background: rgba(5,5,5,0.6); backdrop-filter: blur(15px); 
            display: flex; justify-content: space-between; align-items: center; 
            z-index: 1000; border-bottom: 1px solid rgba(0,242,255,0.2); 
        }
        
        /* 導覽列 - 左側 LOGO */
        .nav-logo img { width: 45px; height: 45px; border-radius: 10px; object-fit: cover; display: block; }
        
        /* 導覽列 - 中間選單 */
        .nav-links { display: flex; gap: 30px; align-items: center; }
        .nav-links a { color: #fff; text-decoration: none; font-size: 0.9rem; transition: 0.3s; letter-spacing: 2px; }
        .nav-links a:hover { color: var(--accent); }

        /* 導覽列 - 右側 ICON (膠囊型底框設計) */
        .nav-socials {
            display: flex; gap: 10px; align-items: center;
            background: rgba(0, 0, 0, 0.5); border: 1px solid rgba(0,242,255,0.2);
            padding: 5px 15px; border-radius: 50px;
        }
        .social-icon { 
            width: 40px; height: 40px; border-radius: 50%; transition: 0.3s; 
            filter: drop-shadow(0 0 2px rgba(255,255,255,0.2));
        }
        .social-icon:hover { transform: scale(1.2); filter: drop-shadow(0 0 8px var(--accent)); }

        /* === 主視覺區域 === */
        .hero { height: 85vh; display: flex; flex-direction: column; justify-content: center; align-items: center; text-align: center; padding: 0 20px; position: relative; box-sizing: border-box; }
        .hero-logo { 
            width: 120px; height: 120px; border-radius: 20px; object-fit: cover; margin-bottom: 25px; 
            box-shadow: 0 0 25px rgba(0, 242, 255, 0.4); border: 2px solid rgba(0, 242, 255, 0.3);
            animation: float 4s ease-in-out infinite;
        }
        
        @keyframes float { 0% { transform: translateY(0px); } 50% { transform: translateY(-10px); } 100% { transform: translateY(0px); } }

        h1 { font-family: 'cwTeXYen', sans-serif; font-size: 5rem; letter-spacing: 10px; margin: 0; color: #fff; text-shadow: 0 0 20px rgba(0,242,255,0.3); }
        
        .scroll-down-btn {
            margin-top: 50px; padding: 12px 35px; color: var(--accent); text-decoration: none;
            border: 1px solid var(--accent); border-radius: 50px; font-family: 'Orbitron', sans-serif;
            font-size: 0.9rem; letter-spacing: 3px; transition: 0.3s; animation: bounce 2s infinite;
        }
        .scroll-down-btn:hover { background: var(--accent); color: #000; box-shadow: 0 0 20px var(--accent); }

        @keyframes bounce { 0%, 20%, 50%, 80%, 100% { transform: translateY(0); } 40% { transform: translateY(-15px); } 60% { transform: translateY(-7px); } }

        .about-section { padding: 100px 20px; text-align: center; background: linear-gradient(to bottom, transparent, rgba(5,5,10,0.8)); }
        .main-container { max-width: 900px; margin: 0 auto; padding: 50px 20px; box-sizing: border-box; }
        
        .service-card { background: var(--glass); backdrop-filter: blur(20px); border: 1px solid rgba(255,255,255,0.1); padding: 40px; border-radius: 20px; display: flex; justify-content: space-between; align-items: center; transition: 0.4s; box-sizing: border-box; }
        .service-card:hover { border-color: var(--accent); transform: translateX(10px); background: rgba(0,242,255,0.1); }
        .btn { padding: 12px 25px; border: 1px solid var(--accent); color: var(--accent); text-decoration: none; border-radius: 50px; transition: 0.3s; white-space: nowrap; }
        .btn:hover { background: var(--accent); color: #000; }

        /* === Footer 樣式 === */
        footer { padding: 60px 20px; border-top: 1px solid rgba(255,255,255,0.1); text-align: center; background: rgba(0,0,0,0.6); backdrop-filter: blur(10px); box-sizing: border-box; }
        .footer-socials {
            display: inline-flex; gap: 20px; align-items: center; justify-content: center; flex-wrap: wrap;
            background: rgba(10, 15, 20, 0.6); border: 1px solid rgba(0, 242, 255, 0.2);
            padding: 10px 25px; border-radius: 50px; margin-bottom: 25px;
        }
        .footer-socials .social-icon { width: 40px; height: 40px; } 

        /* =========================================
           🚀 響應式手機版本核心 UI 與全螢幕背景優化
           ========================================= */
        @media (max-width: 768px) {
            nav { padding: 12px 20px; flex-wrap: wrap; justify-content: space-between; }
            /* 中間文字按鈕選單移至最下方並維持水平置中 */
            .nav-links { gap: 20px; order: 3; width: 100%; justify-content: center; margin-top: 12px; }
            .nav-links a { font-size: 0.85rem; letter-spacing: 1px; }
            
            /* 右上角膠囊 ICON 自動縮小與適應，防止超出畫面 */
            .nav-socials { padding: 4px 10px; gap: 6px; flex-wrap: wrap; max-width: 70%; justify-content: flex-end; }
            .social-icon { width: 30px; height: 30px; }
            
            /* 主頁核心文字與 LOGO 縮放 */
            .hero { height: 80vh; padding-top: 80px; }
            .hero-logo { width: 95px; height: 95px; margin-bottom: 15px; } 
            h1 { font-size: 2.8rem; letter-spacing: 4px; line-height: 1.2; }
            .hero p { font-size: 0.85rem; letter-spacing: 1px; margin-top: 8px; }
            .scroll-down-btn { margin-top: 35px; padding: 10px 25px; font-size: 0.8rem; }
            
            /* 內容區塊結構優化 */
            .about-section { padding: 60px 15px; }
            .about-section h2 { font-size: 1.4rem; }
            .about-section p { font-size: 0.9rem; line-height: 1.6; }
            
            .main-container { padding: 20px 15px; }
            .service-card { flex-direction: column; text-align: center; padding: 25px 20px; }
            .service-card:hover { transform: translateY(-5px); } /* 手機上改為向上輕微浮動更直覺 */
            .service-card div { margin-bottom: 20px; }
            .service-card h2 { font-size: 1.2rem; }
            .service-card p { font-size: 0.88rem; line-height: 1.5; }
            
            .btn { display: block; width: 100%; box-sizing: border-box; padding: 12px; font-size: 0.95rem; }
            
            /* 底部 ICON 優化 */
            footer { padding: 40px 15px; }
            .footer-socials { gap: 12px; padding: 8px 20px; }
            .footer-socials .social-icon { width: 34px; height: 34px; }
        }
    </style>
</head>
<body>

    <iframe src="<?php echo $bg_html_file; ?>" class="bg-iframe"></iframe>

    <nav>
        <div class="nav-logo">
            <a href="#"><img src="<?php echo $logo_image; ?>" alt="XIAOCHEN Logo"></a>
        </div>
        
        <div class="nav-links">
            <a href="#about">工作室簡介</a>
            <a href="#services">服務項目</a>
            <a href="購買須知.php">購買須知</a>
        </div>
        
        <div class="nav-socials">
            <?php foreach ($social_links as $name => $data): ?>
                <a href="<?php echo $data['url']; ?>" target="_blank" title="<?php echo $name; ?>">
                    <img src="<?php echo $data['img']; ?>" alt="<?php echo $name; ?>" class="social-icon">
                </a>
            <?php endforeach; ?>
        </div>
    </nav>
    
    <br class="mobile-hide"><br class="mobile-hide">

    <div class="hero">
        <img src="<?php echo $logo_image; ?>" alt="Main Logo" class="hero-logo">
        <h1>XIAO CHEN</h1>
        <p style="letter-spacing: 5px; color: var(--accent);">買賣一時 服務一世</p>
        <a href="#about" class="scroll-down-btn">查看更多 ▼</a>
    </div>
<br><br><br><br>
    <section id="about" class="about-section">
        <div style="max-width: 700px; margin: 0 auto;">
            <h2 style="font-family: 'Orbitron', sans-serif; color: var(--accent);">工作室簡介</h2>
            <p style="color: #ccc; line-height: 1.8; font-size: 0.95rem;">我們是一隻很專業的團隊 各種項目都有服務 遊戲帳號目前分別有三角洲/傳說/荒野/絕地 我們還有分期一律不加價挺各位 每隻帳號皆有包售後服務 不怕帳號出問題沒人處理 請放心購買 我們會持續大量收購各種帳號 如果各位老闆也要需求 可以私訊官方賴@682gddht 一律比外面高價收購</p>
        </div>
    </section>

    <div id="services" class="main-container">
        <?php foreach ($services as $s): ?>
            <div class="service-card">
                <div>
                    <h2 style="margin:0 0 10px 0; font-size: 1.3rem;"><?php echo $s['title']; ?></h2>
                    <p style="color:#aaa; margin:0; font-size: 0.95rem; line-height: 1.6;"><?php echo $s['description']; ?></p>
                </div>
                <a href="<?php echo $s['link']; ?>" class="btn">查看詳情</a>
            </div>
        <?php endforeach; ?>
        <br>
    </div>

    <footer>
        <div class="footer-socials">
            <?php foreach ($social_links as $name => $data): ?>
                <a href="<?php echo $data['url']; ?>" target="_blank" title="<?php echo $name; ?>">
                    <img src="<?php echo $data['img']; ?>" alt="<?php echo $name; ?>" class="social-icon">
                </a>
            <?php endforeach; ?>
        </div>
        <p style="color:#777; font-size:0.8rem;">© 2026 ENENGAMES. ALL RIGHTS RESERVED.</p>
    </footer>

</body>
</html>