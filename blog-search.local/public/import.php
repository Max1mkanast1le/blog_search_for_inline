<?php

require_once 'database.php';

// Функция для скачивания данных из API
function fetchData(string $url): array
{
    $json = @file_get_contents($url);
    if ($json === false) {
        die("Ошибка: не удалось получить данные с $url\n");
    }
    $data = json_decode($json, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        die("Ошибка: не удалось декодировать JSON с $url\n");
    }
    return $data;
}

try {
    $pdo = getPDO();

    // 1. Загрузка и вставка записей (posts)
    $posts = fetchData(API_POSTS_URL);
    $stmt = $pdo->prepare("INSERT INTO posts (id, user_id, title, body) VALUES (?, ?, ?, ?)");
    
    $pdo->beginTransaction();
    foreach ($posts as $post) {
        $stmt->execute([$post['id'], $post['userId'], $post['title'], $post['body']]);
    }
    $pdo->commit();
    $postsCount = count($posts);

    $comments = fetchData(API_COMMENTS_URL);
    $stmt = $pdo->prepare("INSERT INTO comments (id, post_id, name, email, body) VALUES (?, ?, ?, ?, ?)");

    $pdo->beginTransaction();
    foreach ($comments as $comment) {
        $stmt->execute([$comment['id'], $comment['postId'], $comment['name'], $comment['email'], $comment['body']]);
    }
    $pdo->commit();
    $commentsCount = count($comments);

    echo "Загружено {$postsCount} записей и {$commentsCount} комментариев\n";

} catch (PDOException $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    die("ОШИБКА: Не удалось выполнить операцию. Детали: " . $e->getMessage() . "\n");
}