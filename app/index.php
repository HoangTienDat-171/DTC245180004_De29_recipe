<?php
declare(strict_types=1);

function h($value): string {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

try {
    $db = new PDO(
        'mysql:host=' . getenv('DB_HOST') .
        ';dbname=' . getenv('DB_NAME') .
        ';charset=utf8mb4',
        getenv('DB_USER'),
        getenv('DB_PASSWORD'),
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
} catch (PDOException $e) {
    http_response_code(503);
    exit('Khong the ket noi co so du lieu. Vui long thu lai.');
}

$categories = $db->query(
    'SELECT * FROM categories ORDER BY id'
)->fetchAll(PDO::FETCH_ASSOC);

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT) ?: 0;
$category = filter_input(INPUT_GET, 'category', FILTER_VALIDATE_INT) ?: 0;
$search = trim((string)($_GET['q'] ?? ''));

$recipe = null;
$recipes = [];

if ($id > 0) {
    $stmt = $db->prepare(
        'SELECT r.*, c.name AS category_name
         FROM recipes r
         JOIN categories c ON r.category_id = c.id
         WHERE r.id = :id'
    );
    $stmt->execute(['id' => $id]);
    $recipe = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$recipe) {
        http_response_code(404);
    }
} else {
    $sql = 'SELECT r.*, c.name AS category_name
            FROM recipes r
            JOIN categories c ON r.category_id = c.id
            WHERE 1=1';
    $params = [];

    if ($category > 0) {
        $sql .= ' AND r.category_id = :category';
        $params['category'] = $category;
    }

    if ($search !== '') {
        $sql .= ' AND r.name LIKE :search';
        $params['search'] = '%' . $search . '%';
    }

    $sql .= ' ORDER BY r.id';
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    $recipes = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Bếp Nhà Mình - Công thức nấu ăn</title>
<style>
* { box-sizing: border-box; }
body {
    margin: 0;
    background: #fffaf3;
    color: #343434;
    font-family: Arial, sans-serif;
}
header {
    background: #f97316;
    color: white;
    padding: 24px 8%;
}
header h1 { margin: 0 0 8px; }
header p { margin: 0; }
.container {
    max-width: 1100px;
    margin: 35px auto;
    padding: 0 20px;
}
.hero {
    background: #ffedd5;
    padding: 32px;
    border-radius: 18px;
    margin-bottom: 28px;
}
.hero h2 { margin-top: 0; }
form {
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
    margin-bottom: 28px;
}
input, select, button {
    padding: 13px;
    border-radius: 9px;
    border: 1px solid #e5e7eb;
    font-size: 15px;
}
input { flex: 1; min-width: 180px; }
button {
    background: #ea580c;
    color: white;
    border: 0;
    cursor: pointer;
}
.grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(230px, 1fr));
    gap: 22px;
}
.card, .detail {
    background: white;
    border-radius: 16px;
    padding: 24px;
    box-shadow: 0 5px 20px #0000000d;
}
.card:hover { box-shadow: 0 8px 24px #0000001a; }
.emoji { font-size: 70px; text-align: center; }
.card h3 { color: #c2410c; }
.meta { color: #777; font-size: 14px; }
a { color: #ea580c; text-decoration: none; font-weight: bold; }
.detail { max-width: 800px; margin: auto; }
.detail li { margin: 12px 0; line-height: 1.6; }
footer {
    text-align: center;
    color: #777;
    padding: 30px;
}
</style>
</head>
<body>

<header>
    <h1>🍳 Bếp Nhà Mình</h1>
    <p>Chia sẻ công thức nấu ăn ngon, dễ làm mỗi ngày</p>
</header>

<main class="container">
<?php if ($id > 0): ?>
    <div class="detail">
        <a href="/">← Quay lại trang chủ</a>

        <?php if ($recipe): ?>
            <div class="emoji"><?= h($recipe['emoji']) ?></div>
            <h1><?= h($recipe['name']) ?></h1>
            <p><?= h($recipe['description']) ?></p>
            <p class="meta">
                Danh mục: <?= h($recipe['category_name']) ?>
                | Thời gian: <?= (int)$recipe['cooking_time'] ?> phút
            </p>

            <h2>🥗 Nguyên liệu</h2>
            <ul>
            <?php foreach (explode(';', $recipe['ingredients']) as $item): ?>
                <li><?= h($item) ?></li>
            <?php endforeach; ?>
            </ul>

            <h2>👩‍🍳 Các bước thực hiện</h2>
            <ol>
            <?php foreach (explode(';', $recipe['instructions']) as $step): ?>
                <li><?= h($step) ?></li>
            <?php endforeach; ?>
            </ol>
        <?php else: ?>
            <h2>Không tìm thấy công thức</h2>
        <?php endif; ?>
    </div>
<?php else: ?>
    <section class="hero">
        <h2>Hôm nay ăn gì?</h2>
        <p>Khám phá những món ăn ngon cho gia đình bạn.</p>
    </section>

    <form method="GET">
        <input
            type="text"
            name="q"
            placeholder="Tìm món ăn..."
            value="<?= h($search) ?>"
        >
        <select name="category">
            <option value="0">Tất cả danh mục</option>
            <?php foreach ($categories as $c): ?>
                <option value="<?= (int)$c['id'] ?>"
                    <?= $category === (int)$c['id'] ? 'selected' : '' ?>>
                    <?= h($c['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <button type="submit">Tìm kiếm</button>
    </form>

    <h2>📖 Danh sách công thức</h2>
    <div class="grid">
    <?php foreach ($recipes as $r): ?>
        <article class="card">
            <div class="emoji"><?= h($r['emoji']) ?></div>
            <h3><?= h($r['name']) ?></h3>
            <p><?= h($r['description']) ?></p>
            <p class="meta">
                <?= h($r['category_name']) ?> |
                <?= (int)$r['cooking_time'] ?> phút
            </p>
            <a href="/?id=<?= (int)$r['id'] ?>">
                Xem công thức →
            </a>
        </article>
    <?php endforeach; ?>
    </div>

    <?php if (!$recipes): ?>
        <p>Không tìm thấy món ăn phù hợp.</p>
    <?php endif; ?>
<?php endif; ?>
</main>

<footer>
    © 2026 Bếp Nhà Mình - Website Công thức Nấu ăn
</footer>

</body>
</html>
