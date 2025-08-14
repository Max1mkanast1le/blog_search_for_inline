<?php
require_once 'database.php';

$searchResults = [];
$searchTerm = '';
$error = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $searchTerm = trim($_POST['search'] ?? '');

    if (mb_strlen($searchTerm) < 3) {
        $error = "Поисковый запрос должен содержать минимум 3 символа.";
    } else {
        try {
            $pdo = getPDO();

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
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Поиск по комментариям</title>
    <style>
    /* Общие стили для страницы */
    body {
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        background-color: #f0f2f5;
        color: #1c1e21;
        max-width: 800px;
        margin: 2em auto;
        padding: 0 1em;
    }

    h1 {
        text-align: center;
        color: #333;
    }

    /* Контейнер для формы */
    .form-container {
        background: #ffffff;
        padding: 30px;
        border-radius: 56px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        margin-bottom: 30px;
    }
    
    /* Стилизация формы */
    form {
        display: flex;
        gap: 10px;
    }

    input[type="text"] {
        flex-grow: 1;
        padding: 15px 20px;
        font-size: 16px;
        color: #333;
        
        border: 2px solid #ccc;
        border-radius: 50px;
        
        background-color: #fff;
        outline: none;
        
        transition: all 0.3s ease-in-out;
    }

    input[type="text"]:focus {
        border-color: #ffc107;
        background-color: #fffbeb;
        box-shadow: 0 0 10px rgba(255, 193, 7, 0.5);
    }

    button {
        padding: 15px 30px;
        font-size: 16px;
        font-weight: bold;
        color: #fff;
        
        background-color: #007bff;
        border: none;
        border-radius: 50px;
        cursor: pointer;
        
        transition: all 0.3s ease;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
    }

    button:hover {
        background-color: #0056b3;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
    }
    
    button:active {
        transform: translateY(1px);
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
    }

    .error { color: red; margin-top: 10px; text-align: center; }
    .results-container { margin-top: 20px; }
    .result-item { border: 1px solid #ddd; background: #fff; padding: 15px 20px; margin-bottom: 15px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
    .result-item h3 { margin-top: 0; color: #0056b3; }
    .result-item blockquote { background: #f8f9fa; border-left: 4px solid #ffc107; padding: 10px 15px; margin-left: 0; font-style: italic; color: #555; }
    .no-results { color: #555; text-align: center; font-size: 18px; }
</style>
</head>
<body>

    <h1>Поиск записей по тексту комментария</h1>

    <div class="form-container">
        <form action="index.php" method="POST">
            <input type="text" name="search" placeholder="Введите текст для поиска (мин. 3 символа)" value="<?= htmlspecialchars($searchTerm) ?>" required minlength="3">
            <button type="submit">Найти</button>
        </form>
        <?php if ($error): ?>
            <p class="error"><?= $error ?></p>
        <?php endif; ?>
    </div>

    <?php if ($_SERVER["REQUEST_METHOD"] === "POST" && empty($error)): ?>
        <div class="results-container">
            <h2>Результаты поиска по запросу "<?= htmlspecialchars($searchTerm) ?>"</h2>
            <?php if (!empty($searchResults)): ?>
                <?php foreach ($searchResults as $result): ?>
                    <div class="result-item">
                        <h3><?= htmlspecialchars($result['title']) ?></h3>
                        <blockquote><?= htmlspecialchars($result['comment_body']) ?></blockquote>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="no-results">Ничего не найдено.</p>
            <?php endif; ?>
        </div>
    <?php endif; ?>

</body>
</html>