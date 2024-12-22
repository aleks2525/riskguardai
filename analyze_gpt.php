<?php

header('Content-Type: application/json');

// Получаем данные
$input = json_decode(file_get_contents('php://input'), true);

// API ключ и URL
$api_key = 'sk-aitunnel-D2m10ioEmcaCGp5d8BNfomb7fwHbd4K3';
$api_url = 'https://api.aitunnel.ru/v1/chat/completions';

// Формируем промпт для нейросети
$prompt = "Исправь орфографические и грамматические ошибки, напиши резюме о возможности выдачи кредита, присвой скоринг балл, на что обратить внимание сотруднику банка.\n\n";
$prompt .= "Анализ LLAMA:\n" . $input['llama_analysis'] . "\n\n";
$prompt .= "Пожалуйста, предоставьте итоговое резюме и рекомендации.";

$data = [
    'model' => 'gpt-4o-mini',
    'max_tokens' => 1500,
    'messages' => [
        [
            'role' => 'user',
            'content' => $prompt
        ]
    ]
];

$ch = curl_init($api_url);

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Authorization: Bearer ' . $api_key
]);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

$response = curl_exec($ch);

if (curl_errno($ch)) {
    echo json_encode(['error' => curl_error($ch)]);
} else {
    $response_data = json_decode($response, true);
    
    if (isset($response_data['choices'][0]['message']['content'])) {
        $analysis = $response_data['choices'][0]['message']['content'];

        // Убираем символы # и *
        $cleaned_analysis = str_replace(['#', '*'], '', $analysis);

        echo json_encode([
            'success' => true,
            'analysis' => nl2br(htmlspecialchars($cleaned_analysis))
        ]);
    } else {
        echo json_encode(['error' => 'Не удалось получить ответ от нейросети']);
    }
}

curl_close($ch);

?>
