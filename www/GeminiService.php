<?php
class GeminiService {
    private $apiKey;
    private $apiUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-pro-latest:generateContent';

    public function __construct($apiKey) {
        $this->apiKey = $apiKey;
    }

    public function analyzeImage($imagePath, $mimeType) {
        $imageData = base64_encode(file_get_contents($imagePath));

        $prompt = <<<EOT
この花の画像から、以下の情報をJSON形式で抽出・生成してください。
- flower_name: 最も可能性の高い花の名前
- impression: この写真全体から受ける詩的な印象 (50字程度)
- titles: Instagram投稿にぴったりなおすすめタイトル3案
- happy_sentence: この花を見て幸せな気持ちになるような短い一文
- hashtags: 関連する人気のハッシュタグ5つ (例: #花のある暮らし)
- flower_language: この花の花言葉

制約:
- 回答は必ずJSON形式で出力してください。
- 各項目は日本語で回答してください。
EOT;

        $data = [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $prompt],
                        [
                            'inline_data' => [
                                'mime_type' => $mimeType,
                                'data' => $imageData
                            ]
                        ]
                    ]
                ]
            ],
            'generationConfig' => [
                'response_mime_type' => 'application/json',
            ]
        ];

        $ch = curl_init($this->apiUrl . '?key=' . $this->apiKey);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200) {
            error_log("Gemini API Error: HTTP {$httpCode} " . $response);
            return null;
        }
        
        $result = json_decode($response, true);
        $jsonResponse = $result['candidates'][0]['content']['parts'][0]['text'] ?? null;
        
        if ($jsonResponse) {
            return json_decode($jsonResponse, true);
        }

        return null;
    }
}
?>