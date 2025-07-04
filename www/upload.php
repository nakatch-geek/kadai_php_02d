<?php
// 安全な場所にあるconfig.phpを読み込む
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/GeminiService.php';

// アップロードディレクトリがなければ作成
if (!is_dir(UPLOAD_DIR)) {
    mkdir(UPLOAD_DIR, 0777, true);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['flower_image'])) {
    $file = $_FILES['flower_image'];

    if ($file['error'] !== UPLOAD_ERR_OK) {
        die("アップロードエラーが発生しました。");
    }

    $mimeType = mime_content_type($file['tmp_name']);
    if (strpos($mimeType, 'image/') !== 0) {
        die("画像ファイルではありません。");
    }

    $filename = uniqid() . '_' . basename($file['name']);
    $uploadPath = UPLOAD_DIR . $filename;

    if (!move_uploaded_file($file['tmp_name'], $uploadPath)) {
        die("ファイルの保存に失敗しました。");
    }

    $gemini = new GeminiService(GEMINI_API_KEY);
    $analysisResult = $gemini->analyzeImage($uploadPath, $mimeType);

    if ($analysisResult) {
        try {
            $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4", DB_USER, DB_PASS);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $stmt = $pdo->prepare(
                "INSERT INTO flowers (image_path, flower_name, impression, title1, title2, title3, happy_sentence, hashtags, flower_language) 
                 VALUES (:image_path, :flower_name, :impression, :title1, :title2, :title3, :happy_sentence, :hashtags, :flower_language)"
            );

            $stmt->execute([
                ':image_path' => $uploadPath,
                ':flower_name' => $analysisResult['flower_name'] ?? '不明',
                ':impression' => $analysisResult['impression'] ?? '',
                ':title1' => $analysisResult['titles'][0] ?? '',
                ':title2' => $analysisResult['titles'][1] ?? '',
                ':title3' => $analysisResult['titles'][2] ?? '',
                ':happy_sentence' => $analysisResult['happy_sentence'] ?? '',
                ':hashtags' => is_array($analysisResult['hashtags']) ? implode(' ', $analysisResult['hashtags']) : '',
                ':flower_language' => $analysisResult['flower_language'] ?? ''
            ]);

        } catch (PDOException $e) {
            die("データベース接続エラー: " . $e->getMessage());
        }
    } else {
        error_log("AI解析に失敗しました。画像パス: " . $uploadPath);
    }

    header('Location: index.php');
    exit;
}
?>