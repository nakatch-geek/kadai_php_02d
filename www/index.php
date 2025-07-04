<?php
// 安全な場所にあるconfig.phpを読み込む
require_once __DIR__ . '/../config.php';
try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4", DB_USER, DB_PASS);
    $stmt = $pdo->query("SELECT * FROM flowers ORDER BY created_at DESC");
    $flowers = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("データベース接続エラー: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Flower Book 🌸</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>AI Flower Book 🌸</h1>
        <div class="header-actions">
            <a href="register.php" class="button-register">新しい花を登録する</a>
        </div>
    </header>

    <main>
        <section class="gallery">
            <div class="grid-container">
                <?php foreach ($flowers as $flower): ?>
                <div class="card">
                    <img src="<?= htmlspecialchars($flower['image_path']) ?>" alt="<?= htmlspecialchars($flower['flower_name']) ?>">
                    <div class="card-content">
                        <h3><?= htmlspecialchars($flower['flower_name']) ?></h3>
                        <p class="language">花言葉: <?= htmlspecialchars($flower['flower_language']) ?></p>
                        <p class="impression"><em>"<?= htmlspecialchars($flower['impression']) ?>"</em></p>
                        <p class="sentence">✨ <?= htmlspecialchars($flower['happy_sentence']) ?></p>
                        <div class="titles">
                            <strong>おすすめタイトル案:</strong>
                            <ul>
                                <li><?= htmlspecialchars($flower['title1']) ?></li>
                                <li><?= htmlspecialchars($flower['title2']) ?></li>
                                <li><?= htmlspecialchars($flower['title3']) ?></li>
                            </ul>
                        </div>
                        <p class="hashtags"><?= htmlspecialchars($flower['hashtags']) ?></p>
                        <p class="date"><?= date('Y/m/d H:i', strtotime($flower['created_at'])) ?></p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </section>
    </main>
</body>
</html>