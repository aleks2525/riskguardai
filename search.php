<?php
// Настройки API DaData
$token = "b4e13a8932275d42bb582f61cd1dfd49be9258cd";
$dadata_url = "https://suggestions.dadata.ru/suggestions/api/4_1/rs/suggest/party";

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
    $html = "<table class='table'><thead><tr><th>Название</th><th>ИНН</th><th>КПП</th><th>Адрес</th><th>Статус</th></tr></thead><tbody>";

    if (empty($companies['suggestions'])) {
        $html .= "<tr><td colspan='5'>Компании не найдены</td></tr>";
    } else {
        foreach ($companies['suggestions'] as $company) {
            $html .= "<tr>";
            $html .= "<td>" . htmlspecialchars($company['value']) . "</td>";
            $html .= "<td>" . htmlspecialchars($company['data']['inn']) . "</td>";
            $html .= "<td>" . htmlspecialchars($company['data']['kpp']) . "</td>";
            $html .= "<td>" . htmlspecialchars($company['data']['address']['value'] ?? '') . "</td>";
            $html .= "<td>" . htmlspecialchars($company['data']['state']['status'] ?? '') . "</td>";
            $html .= "</tr>";
        }
    }

    $html .= "</tbody></table>";
    echo $html;
}
?>
