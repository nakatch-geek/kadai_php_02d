<?php
// 安全な場所にあるconfig.phpを読み込む
require_once __DIR__ . '/../config.php';
$backgroundImage = '';
try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4", DB_USER, DB_PASS);
    $stmt = $pdo->query("SELECT image_path FROM flowers WHERE image_path IS NOT NULL AND image_path != '' ORDER BY RAND() LIMIT 1");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($result) {
        $backgroundImage = htmlspecialchars($result['image_path']);
    }
} catch (PDOException $e) {
    // DBエラー時は背景なし
    $backgroundImage = '';
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>新しい花を登録 - AI Flower Book</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="register-background" style="background-image: url('<?= $backgroundImage ?>');">
    
    <div class="register-container">
        <h2>新しい花を登録</h2>
        <p>あなたの見つけた花をAIが素敵な言葉で彩ります</p>
        <form action="upload.php" method="post" enctype="multipart/form-data">
            <input type="file" name="flower_image" accept="image/*" required>
            <button type="submit">AIにこの花について教えてもらう</button>
        </form>
        <div class="back-link">
            <a href="index.php">図鑑に戻る</a>
        </div>
    </div>

</body>
</html>