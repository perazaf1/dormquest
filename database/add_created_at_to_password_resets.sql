-- Script pour ajouter la colonne created_at à la table password_resets
-- Ce script vérifie d'abord si la colonne existe déjà

-- Ajouter la colonne created_at si elle n'existe pas
ALTER TABLE password_resets
ADD COLUMN IF NOT EXISTS created_at DATETIME DEFAULT CURRENT_TIMESTAMP AFTER expires_at;

-- Note: Si vous utilisez une version de MySQL qui ne supporte pas "IF NOT EXISTS",
-- utilisez cette alternative (commentez la ligne ci-dessus et décommentez celle-ci):
-- ALTER TABLE password_resets ADD COLUMN created_at DATETIME DEFAULT CURRENT_TIMESTAMP AFTER expires_at;
