<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Поиск по комментариям</title>
    <link rel="stylesheet" href="css/style.css">
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
                        <blockquote>
                            <?php
                            $safeSearchTerm = htmlspecialchars($searchTerm);
                            $pattern = '/(' . preg_quote($safeSearchTerm, '/') . ')/i';
                            $commentBody = htmlspecialchars($result['comment_body']);
                            $highlighted_comment = preg_replace($pattern, '<mark>$1</mark>', $commentBody);
                            
                            echo $highlighted_comment;
                            ?>
                        </blockquote>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="no-results">Ничего не найдено.</p>
            <?php endif; ?>
        </div>
    <?php endif; ?>

</body>
</html>