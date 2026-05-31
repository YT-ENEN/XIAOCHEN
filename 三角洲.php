<?php
// ==========================================
// 1. 設定區：填入你的 API Key 與「大資料夾」網址
// ==========================================
$api_key = "AIzaSyB0pjUimtoomamQ9BVPGwIB-36VNFlVlds";

// ▼ 把你的大資料夾 (小Chen三角洲現貨) 網址貼在這裡 ▼
$master_folder_url = "https://drive.google.com/drive/folders/13TT9OAKKGqmdSsLgZgBmUbMWaPVrQtbQ"; 

// ==========================================
// 自動萃取 Google Drive ID 的輔助函式
// ==========================================
function getDriveId($url) {
    if (preg_match('/[-\w]{25,}/', $url, $matches)) {
        return $matches[0]; 
    }
    return $url;
}
$master_folder_id = getDriveId($master_folder_url);

$context = stream_context_create([
    'http' => ['ignore_errors' => true]
]);

// ==========================================
// 2. 處理 AJAX 請求 (讀取封面圖 / 讀取資料夾全圖片)
// ==========================================
if (isset($_GET['action'])) {
    header('Content-Type: application/json');
    $folderId = getDriveId($_GET['folderId'] ?? '');
    
    if (empty($folderId)) {
        echo json_encode(['error' => 'No folder ID provided']);
        exit;
    }

    // 取得資料夾內的「所有圖片」(用於點擊查看詳情)
    if ($_GET['action'] === 'get_images') {
        // 加入 orderBy=name 確保圖片按照檔名順序排列
        $query = "'" . $folderId . "' in parents and mimeType contains 'image/' and trashed = false";
        $drive_api_url = "https://www.googleapis.com/drive/v3/files?q=" . urlencode($query) . "&orderBy=name&supportsAllDrives=true&includeItemsFromAllDrives=true&key=" . $api_key;
        
        $response = file_get_contents($drive_api_url, false, $context);
        $data = json_decode($response, true);
        
        if (isset($data['error'])) {
            echo json_encode(['debug_error' => $data['error']['message'], 'code' => $data['error']['code'], 'extracted_id' => $folderId]);
            exit;
        }
        
        $images = [];
        if (isset($data['files'])) {
            foreach ($data['files'] as $file) {
                $images[] = "https://drive.google.com/thumbnail?id=" . $file['id'] . "&sz=w1000";
            }
        }
        echo json_encode($images);
        exit; 
    }

    // 取得資料夾內的「第一張圖片」(用於非同步載入封面圖)
    if ($_GET['action'] === 'get_cover') {
        $query = "'" . $folderId . "' in parents and mimeType contains 'image/' and trashed = false";
        // pageSize=1 只抓第一張，減少伺服器負擔
        $drive_api_url = "https://www.googleapis.com/drive/v3/files?q=" . urlencode($query) . "&pageSize=1&orderBy=name&supportsAllDrives=true&includeItemsFromAllDrives=true&key=" . $api_key;
        
        $response = file_get_contents($drive_api_url, false, $context);
        $data = json_decode($response, true);
        
        if (isset($data['files']) && count($data['files']) > 0) {
            echo json_encode(['url' => "https://drive.google.com/thumbnail?id=" . $data['files'][0]['id'] . "&sz=w800"]);
        } else {
            echo json_encode(['url' => null]);
        }
        exit;
    }
}

// ==========================================
// 3. 正常網頁載入 (讀取大資料夾內的「所有子資料夾」)
// ==========================================
$folders = [];
$drive_api_error = "";

if (!empty($master_folder_id)) {
    // 搜尋大資料夾底下的所有「資料夾」，並依照名稱排序 (orderBy=name)，這樣 01, 02 才會照順序排
    $query = "'" . $master_folder_id . "' in parents and mimeType = 'application/vnd.google-apps.folder' and trashed = false";
    $folders_api_url = "https://www.googleapis.com/drive/v3/files?q=" . urlencode($query) . "&orderBy=name&supportsAllDrives=true&includeItemsFromAllDrives=true&key=" . $api_key;
    
    $response = file_get_contents($folders_api_url, false, $context);
    $data = json_decode($response, true);
    
    if (isset($data['error'])) {
        $drive_api_error = "錯誤代碼: " . $data['error']['code'] . "<br>詳細訊息: " . $data['error']['message'];
    } elseif (isset($data['files'])) {
        $folders = $data['files'];
    }
} else {
    $drive_api_error = "請在程式碼頂部填入有效的大資料夾網址。";
}

// 網站基本設定
$site_title = "XIAOCHEN | 三角洲帳號區";
$bg_html_file = "background.html"; 
$logo_image = "LOGO.png"; 

$social_links = [
    "LINE" => ["url" => "https://line.me/R/ti/p/@682gddht", "img" => "https://img.icons8.com/color/48/line-me.png"],
    "IG" => ["url" => "https://www.instagram.com/xiao_chen_1112?igsh=OXA5eXRsenVsZXN0&utm_source=qr", "img" => "https://img.icons8.com/fluency/48/instagram-new.png"],
    "FB" => ["url" => "https://www.facebook.com/chen.yuting.439481?locale=zh_TW", "img" => "https://img.icons8.com/fluency/48/facebook-new.png"],
    "TK" => ["url" => "https://www.tiktok.com/@chenyuting111", "img" => "https://img.icons8.com/fluency/48/tiktok.png"],
    "DC" => ["url" => "https://discord.gg/5ZGRXmfxwb", "img" => "https://img.icons8.com/fluency/48/discord-logo.png"],
    "Threads" => ["url" => "https://www.threads.com/@xiao_chen_1112?igshid=NTc4MTIwNjQ2YQ==", "img" => "https://img.icons8.com/ios-filled/48/ffffff/threads.png"]
];
?>
<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <link rel="icon" href="MARK.ico" type="image/x-icon">
    <title><?php echo $site_title; ?></title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700&family=Noto+Sans+TC:wght@300;500&display=swap');
        
        :root { --accent: #00f2ff; --glass: rgba(25, 25, 30, 0.7); }
        html { scroll-behavior: smooth; height: 100%; }
        
        .bg-iframe { position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; border: none; z-index: -100; pointer-events: none; }
        
        body { margin: 0; background: transparent; color: #fff; font-family: 'Noto Sans TC', sans-serif; overflow-x: hidden; display: flex; flex-direction: column; min-height: 100vh; }
        .content-wrapper { flex: 1; display: flex; flex-direction: column; }
        
        /* 導覽列 */
        nav { position: fixed; top: 0; width: 100%; padding: 10px 50px; box-sizing: border-box; background: rgba(5,5,5,0.6); backdrop-filter: blur(15px); display: flex; justify-content: space-between; align-items: center; z-index: 1000; border-bottom: 1px solid rgba(0,242,255,0.2); }
        .nav-logo img { width: 45px; height: 45px; border-radius: 10px; object-fit: cover; display: block; }
        .nav-links { display: flex; gap: 30px; align-items: center; }
        .nav-links a { color: #fff; text-decoration: none; font-size: 0.9rem; transition: 0.3s; letter-spacing: 2px; }
        .nav-links a:hover { color: var(--accent); }
        .nav-socials { display: flex; gap: 10px; align-items: center; background: rgba(0, 0, 0, 0.5); border: 1px solid rgba(0,242,255,0.2); padding: 5px 15px; border-radius: 50px; }
        .social-icon { width: 36px; height: 36px; border-radius: 50%; transition: 0.3s; filter: drop-shadow(0 0 2px rgba(255,255,255,0.2)); }
        .social-icon:hover { transform: scale(1.2); filter: drop-shadow(0 0 8px var(--accent)); }

        /* 標題區 */
        .page-header { margin-top: 120px; text-align: center; padding: 20px; }
        .page-header h1 { font-family: 'Orbitron', sans-serif; font-size: 3.5rem; letter-spacing: 8px; margin: 0; color: var(--accent); text-shadow: 0 0 15px rgba(0,242,255,0.3); }
        .page-header p { letter-spacing: 3px; color: #ccc; margin-top: 10px; }
        
        /* 網格系統：電腦版最多 3 個一排 */
        .account-grid { max-width: 1020px; margin: 30px auto 50px auto; padding: 0 20px; display: grid; grid-template-columns: repeat(3, 1fr); gap: 30px; width: 100%; box-sizing: border-box; }
        .acc-card { background: var(--glass); backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.1); border-radius: 15px; overflow: hidden; transition: 0.4s; display: flex; flex-direction: column; }
        .acc-card:hover { border-color: var(--accent); transform: translateY(-10px); box-shadow: 0 10px 30px rgba(0,242,255,0.15); }
        
        /* 封面圖比例控制 */
        .acc-cover { width: 100%; aspect-ratio: 300 / 415; object-fit: cover; border-bottom: 1px solid rgba(255,255,255,0.1); background: #111; display: flex; align-items: center; justify-content: center; }
        
        .acc-info { padding: 22px; display: flex; flex-direction: column; flex-grow: 1; justify-content: space-between; }
        .acc-title { font-size: 1.25rem; margin: 0 0 15px 0; font-weight: 500; text-align: center; letter-spacing: 1px; }
        
        .btn-view { display: block; text-align: center; padding: 12px; background: transparent; border: 1px solid var(--accent); color: var(--accent); border-radius: 5px; text-decoration: none; transition: 0.3s; cursor: pointer; font-family: 'Noto Sans TC', sans-serif; font-size: 1rem; }
        .btn-view:hover { background: var(--accent); color: #000; }

        /* Modal 彈出視窗 */
        #img-modal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.85); backdrop-filter: blur(10px); z-index: 2000; align-items: center; justify-content: center; flex-direction: column; }
        .modal-content { width: 90%; max-width: 800px; max-height: 80vh; overflow-y: auto; background: var(--glass); border: 1px solid var(--accent); border-radius: 15px; padding: 30px; text-align: center; }
        .modal-content::-webkit-scrollbar { width: 8px; }
        .modal-content::-webkit-scrollbar-thumb { background: var(--accent); border-radius: 4px; }
        
        .close-btn { position: absolute; top: 20px; right: 40px; font-size: 3rem; color: #fff; cursor: pointer; transition: 0.3s; }
        .close-btn:hover { color: var(--accent); }
        .modal-img { width: 100%; border-radius: 8px; margin-bottom: 15px; border: 1px solid rgba(255,255,255,0.1); }
        .modal-img[alt] { min-height: 100px; background: rgba(255,255,255,0.05); color: #888; display: flex; align-items: center; justify-content: center; }

        .loading-text { color: var(--accent); font-family: 'Orbitron', sans-serif; font-size: 1.5rem; letter-spacing: 2px; animation: blink 1.5s infinite; }
        @keyframes blink { 0%, 100% { opacity: 1; } 50% { opacity: 0.3; } }

        /* Footer */
        footer { padding: 60px 20px; border-top: 1px solid rgba(255,255,255,0.1); text-align: center; background: rgba(0,0,0,0.6); backdrop-filter: blur(10px); width: 100%; box-sizing: border-box; }
        .footer-socials { display: inline-flex; gap: 20px; align-items: center; justify-content: center; flex-wrap: wrap; background: rgba(10, 15, 20, 0.6); border: 1px solid rgba(0, 242, 255, 0.2); padding: 10px 25px; border-radius: 50px; margin-bottom: 25px; }

        /* RWD 手機版 */
        @media (max-width: 768px) {
            nav { padding: 12px 20px; flex-wrap: wrap; justify-content: space-between; }
            .nav-links { gap: 20px; order: 3; width: 100%; justify-content: center; margin-top: 12px; }
            .nav-links a { font-size: 0.85rem; letter-spacing: 1px; }
            .nav-socials { padding: 4px 10px; gap: 6px; flex-wrap: wrap; max-width: 70%; justify-content: flex-end; }
            .social-icon { width: 28px; height: 28px; }
            .page-header { margin-top: 90px; padding: 15px; }
            .page-header h1 { font-size: 2.2rem; letter-spacing: 4px; }
            .page-header p { font-size: 0.85rem; letter-spacing: 1px; }
            .close-btn { top: 10px; right: 20px; }
            .modal-content { padding: 15px; }
            
            /* 手機版強制 2 個一排 */
            .account-grid { grid-template-columns: repeat(2, 1fr) !important; gap: 12px !important; padding: 0 10px !important; margin-top: 20px; }
            .acc-info { padding: 12px !important; }
            .acc-title { font-size: 1rem !important; margin-bottom: 10px !important; }
            .btn-view { padding: 8px !important; font-size: 0.85rem !important; }
            footer { padding: 40px 15px; }
            .footer-socials { gap: 12px; padding: 8px 20px; }
            .footer-socials .social-icon { width: 34px; height: 34px; }
        }
    </style>
</head>
<body>

    <iframe src="<?php echo $bg_html_file; ?>" class="bg-iframe"></iframe>

    <div class="content-wrapper">
        <nav>
            <div class="nav-logo"><a href="index.php"><img src="<?php echo $logo_image; ?>" alt="LOGO"></a></div>
            <div class="nav-links">
                <a href="index.php">首頁返回</a>
                <a href="購買須知.php">購買須知</a>
                <a href="#accounts-grid">帳號一覽</a>
            </div>
            <div class="nav-socials">
                <?php foreach ($social_links as $name => $data): ?>
                    <a href="<?php echo $data['url']; ?>" target="_blank" title="<?php echo $name; ?>"><img src="<?php echo $data['img']; ?>" alt="<?php echo $name; ?>" class="social-icon"></a>
                <?php endforeach; ?>
            </div>
        </nav>

        <div class="page-header">
            <h1>DELTA FORCE ACCOUNTS</h1>
            <p>《三角洲行動》專屬資產展示特區</p>
        </div>

        <div id="accounts-grid" class="account-grid">
            <?php if (!empty($drive_api_error)): ?>
                <div style="grid-column: 1 / -1; background: rgba(255, 50, 50, 0.15); border: 1px solid #ff4d4d; border-radius: 15px; padding: 30px; color: #ff4d4d; font-family: monospace;">
                    <h3 style="margin-top: 0;">⚠️ Google Drive API 連線失敗 (讀取大資料夾)</h3>
                    <p style="font-size: 1.1rem; line-height: 1.5;"><?php echo $drive_api_error; ?></p>
                </div>
            <?php elseif (empty($folders)): ?>
                <div style="text-align: center; grid-column: 1 / -1;">
                    <p style="color: #aaa;">大資料夾中目前沒有任何子資料夾。</p>
                </div>
            <?php else: ?>
                <?php foreach ($folders as $folder): ?>
                    <?php 
                    $title = htmlspecialchars($folder['name']);
                    $folderId = htmlspecialchars($folder['id']);
                    // 預設的載入中圖片
                    $placeholder = 'https://via.placeholder.com/300x415/151515/00f2ff?text=Loading...'; 
                    ?>
                    <div class="acc-card">
                        <img src="<?php echo $placeholder; ?>" data-folder-id="<?php echo $folderId; ?>" alt="Cover" class="acc-cover lazy-cover">
                        <div class="acc-info">
                            <h2 class="acc-title"><?php echo $title; ?></h2>
                            <button class="btn-view" onclick="openModal('<?php echo $folderId; ?>')">查看更多詳圖</button>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div> 

    <div id="img-modal">
        <span class="close-btn" onclick="closeModal()">&times;</span>
        <div class="modal-content" id="modal-content"></div>
    </div>

    <footer>
        <div class="footer-socials">
            <?php foreach ($social_links as $name => $data): ?>
                <a href="<?php echo $data['url']; ?>" target="_blank" title="<?php echo $name; ?>"><img src="<?php echo $data['img']; ?>" alt="<?php echo $name; ?>" class="social-icon"></a>
            <?php endforeach; ?>
        </div>
        <p style="color:#777; font-size:0.8rem;">© 2026 ENENGAMES. ALL RIGHTS RESERVED.</p>
    </footer>

    <script>
        // === 1. 非同步載入各帳號封面圖 ===
        document.addEventListener('DOMContentLoaded', function() {
            const covers = document.querySelectorAll('.lazy-cover');
            covers.forEach(img => {
                const folderId = img.getAttribute('data-folder-id');
                // 向後端請求該資料夾的第一張圖片
                fetch(`?action=get_cover&folderId=${folderId}`)
                    .then(res => res.json())
                    .then(data => {
                        if (data && data.url) {
                            img.src = data.url;
                        } else {
                            img.src = 'https://via.placeholder.com/300x415/151515/888888?text=No+Image';
                        }
                    })
                    .catch(err => console.error("封面圖載入失敗", err));
            });
        });

        // === 2. 點擊查看更多細節 (抓取所有圖片) ===
        const modal = document.getElementById('img-modal');
        const modalContent = document.getElementById('modal-content');

        function openModal(folderId) {
            modal.style.display = "flex";
            modalContent.innerHTML = "<p class='loading-text'>ACCESSING DRIVE DATA...</p>";

            fetch(`?action=get_images&folderId=${folderId}`)
                .then(response => response.text()) 
                .then(text => {
                    try {
                        const data = JSON.parse(text);
                        modalContent.innerHTML = "";
                        
                        if (data.debug_error) {
                            modalContent.innerHTML = `
                                <div style="text-align: left; background: rgba(255, 50, 50, 0.15); border: 1px solid #ff4d4d; border-radius: 10px; padding: 20px; color: #ff4d4d; font-family: monospace;">
                                    <strong>⚠️ Google Drive API 錯誤 (${data.code})</strong><br>
                                    <p>Google 官方回覆：${data.debug_error}</p>
                                    <p style="color: #00f2ff; margin-top: 15px;">🔍 系統實際去搜尋的資料夾 ID：<br><strong>${data.extracted_id}</strong></p>
                                </div>
                            `;
                            return;
                        }

                        if(!data || data.length === 0) {
                            modalContent.innerHTML = "<p>資料夾內無圖片，或權限未開放。</p>";
                            return;
                        }
                        
                        data.forEach(imgUrl => {
                            modalContent.innerHTML += `<img src="${imgUrl}" class="modal-img" alt="[圖片載入中...]">`;
                        });
                        
                    } catch (e) {
                        modalContent.innerHTML = `
                            <div style="text-align: left; background: rgba(255, 50, 50, 0.15); border: 1px solid #ff4d4d; border-radius: 10px; padding: 20px; color: #ff4d4d; overflow-x: auto;">
                                <strong>⚠️ 伺服器回傳格式錯誤：</strong><br><br>
                                <textarea style="width:100%; height:200px; background:#111; color:#ff4d4d;">${text}</textarea>
                            </div>
                        `;
                    }
                })
                .catch(err => {
                    console.error(err);
                    modalContent.innerHTML = "<p style='color:red;'>網路連線中斷，請檢查伺服器狀態。</p>";
                });
        }

        function closeModal() {
            modal.style.display = "none";
            modalContent.innerHTML = "";
        }
    </script>
</body>
</html>