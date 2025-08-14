<?php
// Подключаем функцию для работы с БД
require_once 'database.php';

// Инициализируем переменные, которые будут использоваться в шаблоне
$searchResults = [];
$searchTerm = '';
$error = '';

// Проверяем, был ли отправлен POST-запрос
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $searchTerm = trim($_POST['search'] ?? '');

    if (mb_strlen($searchTerm) < 3) {
        $error = "Поисковый запрос должен содержать минимум 3 символа.";
    } else {
        try {
            // Получаем подключение к БД
            $pdo = getPDO();

            // Запрос на поиск
            $sql = "SELECT p.title, c.body AS comment_body 
                    FROM posts p
                    JOIN comments c ON p.id = c.post_id
                    WHERE c.body LIKE ?";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute(["%{$searchTerm}%"]);

            $searchResults = $stmt->fetchAll();

        } catch (PDOException $e) {
            $error = "Ошибка при поиске: " . $e->getMessage();
        }
    }
}

require 'search_template.php';