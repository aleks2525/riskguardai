<?php
// Настройки API DaData
$token = "b4e13a8932275d42bb582f61cd1dfd49be9258cd";
$dadata_url = "http://suggestions.dadata.ru/suggestions/api/4_1/rs/findById/party";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = file_get_contents("php://input");
    $data = json_decode($input, true);
    $query = isset($data['query']) ? trim($data['query']) : '';

    if (empty($query)) {
        echo json_encode(array("error" => "Введите поисковый запрос"));
        exit;
    }

    // Формируем запрос к API
    $options = array(
        'http' => array(
            'method'  => 'POST',
            'header'  => array(
                'Content-Type: application/json',
                'Accept: application/json',
                'Authorization: Token ' . $token,
            ),
            'content' => json_encode(array(
                'query' => $query,
                'count' => 5
            ))
        )
    );

    // Выполняем запрос
    $context = stream_context_create($options);
    $result = file_get_contents($dadata_url, false, $context);

    if ($result === FALSE) {
        echo json_encode(array("error" => "Ошибка при выполнении запроса к API"));
        exit;
    }

    // Обрабатываем результат
    $companies = json_decode($result, true);
    $html = "";

    if (empty($companies['suggestions'])) {
        $html .= "<div>Компании не найдены</div>";
    } else {
        foreach ($companies['suggestions'] as $company) {
            $html .= "<div class='company-info' style='margin-bottom: 20px; border: 1px solid #ddd; padding: 10px; border-radius: 5px;'>";
            
            $html .= "<h4>" . htmlspecialchars($company['value']) . "</h4>";
            
            $html .= "<div class='row'>";
            $html .= "<div class='col-md-6'>";
            $html .= "<p><strong>ИНН:</strong> " . htmlspecialchars($company['data']['inn']) . "</p>";
            $html .= "<p><strong>КПП:</strong> " . htmlspecialchars($company['data']['kpp']) . "</p>";
            $html .= "<p><strong>Адрес:</strong> " . htmlspecialchars($company['data']['address']['value'] ?? '') . "</p>";
            $html .= "</div>";
            $html .= "<div class='col-md-6'>";
            $html .= "<p><strong>Статус:</strong> " . htmlspecialchars($company['data']['state']['status'] ?? '') . "</p>";
            $html .= "<p><strong>ОГРН:</strong> " . htmlspecialchars($company['data']['ogrn'] ?? '') . "</p>";
            $html .= "<p><strong>Дата выдачи ОГРН:</strong> " . htmlspecialchars($company['data']['ogrn_date'] ?? '') . "</p>";
            $html .= "</div>";
            $html .= "</div>";

            $html .= "<div class='row'>";
            $html .= "<div class='col-md-12'>";
            $html .= "<p><strong>Код ОКВЭД:</strong> " . htmlspecialchars($company['data']['okved'] ?? '') . "</p>";
            $html .= "<p><strong>Версия ОКВЭД:</strong> " . htmlspecialchars($company['data']['okved_type'] ?? '') . "</p>";
            $html .= "</div>";
            $html .= "</div>";

            $html .= "</div>";
        }
    }

    echo $html;
}
?>