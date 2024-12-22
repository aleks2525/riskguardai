<?php
// Подключение к базе данных
$servername = "localhost"; // Укажите ваш сервер базы данных
$username = "047054032_b"; // Ваше имя пользователя для базы данных
$password = "yruz3t[-qk8A"; // Ваш пароль для базы данных$conn = new mysqli($servername, $username, $password, $dbname);
$dbname = "j993990_banki"; // Имя вашей базы данных


$conn = new mysqli($servername, $username, $password, $dbname);

// Проверка соединения
if ($conn->connect_error) {
    die("Ошибка подключения: " . $conn->connect_error);
}

// Запрос для выбора случайного ИНН
$sql = "SELECT inn FROM inn_table ORDER BY RAND() LIMIT 1";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Вывод случайного ИНН
    $row = $result->fetch_assoc();
    echo json_encode(['inn' => $row['inn']]);
} else {
    echo json_encode(['inn' => '']);
}

$conn->close();
?>
