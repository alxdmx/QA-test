<?php
require_once __DIR__ . '/vendor/autoload.php'; // Подключаем автозагрузку Composer

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__); // Загружаем .env
$dotenv->load();

function sendRequest($url, $data, $username, $password) {
    $headers = ["Content-Type: application/json"];
    $auth = base64_encode("$username:$password");

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array_merge(
        $headers,
        ["Authorization: Basic $auth"]
    ));
    curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);

    $response = curl_exec($curl);
    $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);

    return [$httpCode, $response];
}

$data = [
    "name" => "Алексей",
    "patronymic" => "Владиславович",
    "surname" => "Даманин-Даманин",
    "phone" => "+7 921 71" . rand(100000, 999999),
    "email" => "testregisterphp@maildrop.cc",
    "address" => "г. Пенза, ул. Ленина, д. 1",
    "refovod_code" => "334",
    "password" => "TestPass817!"
];

echo "Запрос:\n" . json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n\n";

list($code, $response) = sendRequest(
    "https://go.dev-01.ru/api/v1/account/register",
    $data,
    $_ENV['BASIC_USER'],
    $_ENV['BASIC_PASS']
);

echo "HTTP-код: $code\n";
echo "Ответ сервера:\n$response\n";
echo $code === 201 ? "\n✅ Регистрация успешна\n" : "\n❌ Ошибка при регистрации\n";

