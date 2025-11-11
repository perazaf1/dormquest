<?php
session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Simuler la récupération des favoris (à adapter selon votre base de données)
$favoris = [
    ['id' => 1, 'name' => 'Dorm A', 'price' => '250€/mois', 'image' => 'dorm-a.jpg'],
    ['id' => 2, 'name' => 'Dorm B', 'price' => '300€/mois', 'image' => 'dorm-b.jpg'],
];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes Favoris - DormQuest</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f5f5;
        }

        .favoris {
            max-width: 1000px;
            margin: 40px auto;
            padding: 20px;
        }

        .favoris__header {
            margin-bottom: 30px;
        }

        .favoris__title {
            font-size: 32px;
            color: #333;
            margin-bottom: 10px;
        }

        .favoris__subtitle {
            font-size: 14px;
            color: #666;
        }

        .favoris__list {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 20px;
        }

        .favoris__item {
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .favoris__item:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 16px rgba(0,0,0,0.15);
        }

        .favoris__image {
            width: 100%;
            height: 200px;
            background: #e0e0e0;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #999;
        }

        .favoris__content {
            padding: 16px;
        }

        .favoris__name {
            font-size: 18px;
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
        }

        .favoris__price {
            font-size: 16px;
            color: #007bff;
            font-weight: 500;
            margin-bottom: 12px;
        }

        .favoris__actions {
            display: flex;
            gap: 8px;
        }

        .favoris__btn {
            flex: 1;
            padding: 8px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
            transition: background-color 0.3s ease;
        }

        .favoris__btn--view {
            background-color: #007bff;
            color: white;
        }

        .favoris__btn--view:hover {
            background-color: #0056b3;
        }

        .favoris__btn--remove {
            background-color: #f0f0f0;
            color: #d9534f;
        }

        .favoris__btn--remove:hover {
            background-color: #e8e8e8;
        }

        .favoris__empty {
            text-align: center;
            padding: 60px 20px;
            color: #999;
        }

        .favoris__empty-icon {
            font-size: 64px;
            margin-bottom: 16px;
        }

        .favoris__empty-text {
            font-size: 18px;
            margin-bottom: 20px;
        }

        .favoris__empty-link {
            display: inline-block;
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            transition: background-color 0.3s ease;
        }

        .favoris__empty-link:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="favoris">
        <div class="favoris__header">
            <h1 class="favoris__title">Mes Favoris</h1>
            <p class="favoris__subtitle">Vous avez <?php echo count($favoris); ?> logement(s) en favoris</p>
        </div>

        <?php if (empty($favoris)): ?>
            <div class="favoris__empty">
                <div class="favoris__empty-icon">♡</div>
                <p class="favoris__empty-text">Aucun favori pour le moment</p>
                <a href="search.php" class="favoris__empty-link">Parcourir les logements</a>
            </div>
        <?php else: ?>
            <div class="favoris__list">
                <?php foreach ($favoris as $item): ?>
                    <div class="favoris__item" data-id="<?php echo $item['id']; ?>">
                        <div class="favoris__image"><?php echo $item['image']; ?></div>
                        <div class="favoris__content">
                            <h2 class="favoris__name"><?php echo htmlspecialchars($item['name']); ?></h2>
                            <p class="favoris__price"><?php echo htmlspecialchars($item['price']); ?></p>
                            <div class="favoris__actions">
                                <button class="favoris__btn favoris__btn--view" onclick="viewDorm(<?php echo $item['id']; ?>)">Voir</button>
                                <button class="favoris__btn favoris__btn--remove" onclick="removeFavorite(<?php echo $item['id']; ?>)">Supprimer</button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <script>
        function removeFavorite(id) {
            if (confirm('Supprimer ce favori ?')) {
                const item = document.querySelector(`[data-id="${id}"]`);
                item.style.opacity = '0';
                item.style.transform = 'scale(0.95)';
                setTimeout(() => {
                    item.remove();
                    location.reload();
                }, 300);
            }
        }

        function viewDorm(id) {
            window.location.href = `annonce.php?id=${id}`;
        }
    </script>
</body>
</html>