<?php
// generate-annonces.php - Script de génération de 50 annonces réalistes

define('ACCESS_ALLOWED', true);
require_once 'config/config.php';

// Connexion à la base de données
try {
    $pdo = new PDO(
        'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET,
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );
} catch (PDOException $e) {
    die('Erreur de connexion à la base de données : ' . $e->getMessage());
}



// Fonction pour générer un mot de passe hashé
function generatePassword() {
    return password_hash('password123', PASSWORD_DEFAULT);
}

// Données réalistes pour la génération
$prenoms = ['Marie', 'Pierre', 'Sophie', 'Jean', 'Camille', 'Thomas', 'Julie', 'Lucas', 'Emma', 'Antoine', 'Léa', 'Nicolas', 'Clara', 'Alexandre', 'Laura'];
$noms = ['Martin', 'Bernard', 'Dubois', 'Thomas', 'Robert', 'Richard', 'Petit', 'Durand', 'Leroy', 'Moreau', 'Simon', 'Laurent', 'Lefebvre', 'Michel', 'Garcia'];

$villes = [
    ['nom' => 'Paris', 'cp' => '75001', 'cp_range' => ['75001', '75020']],
    ['nom' => 'Lyon', 'cp' => '69001', 'cp_range' => ['69001', '69009']],
    ['nom' => 'Marseille', 'cp' => '13001', 'cp_range' => ['13001', '13016']],
    ['nom' => 'Toulouse', 'cp' => '31000', 'cp_range' => ['31000', '31500']],
    ['nom' => 'Nice', 'cp' => '06000', 'cp_range' => ['06000', '06300']],
    ['nom' => 'Nantes', 'cp' => '44000', 'cp_range' => ['44000', '44300']],
    ['nom' => 'Strasbourg', 'cp' => '67000', 'cp_range' => ['67000', '67200']],
    ['nom' => 'Montpellier', 'cp' => '34000', 'cp_range' => ['34000', '34090']],
    ['nom' => 'Bordeaux', 'cp' => '33000', 'cp_range' => ['33000', '33800']],
    ['nom' => 'Lille', 'cp' => '59000', 'cp_range' => ['59000', '59800']],
    ['nom' => 'Rennes', 'cp' => '35000', 'cp_range' => ['35000', '35700']],
    ['nom' => 'Reims', 'cp' => '51100', 'cp_range' => ['51100', '51100']],
];

$rues = ['Rue de la République', 'Avenue Jean Jaurès', 'Boulevard Victor Hugo', 'Rue Pasteur', 'Avenue de la Gare', 'Rue du Commerce', 'Place de la Mairie', 'Rue Voltaire', 'Avenue des Écoles', 'Rue Saint-Michel'];

$typesLogement = ['studio', 'colocation', 'residence_etudiante', 'chambre_habitant'];
$typesLoueur = ['particulier', 'agence', 'organisme', 'crous'];
$empreintes = ['A', 'B', 'C', 'D', 'E', 'F', 'G'];

// Templates de descriptions par type de logement
$descriptionsTemplates = [
    'studio' => [
        "Charmant studio lumineux idéal pour étudiant. Entièrement meublé avec coin cuisine équipé, salle d'eau moderne. Proche des transports et commerces. Quartier calme et sécurisé.",
        "Studio fonctionnel dans résidence récente. Parfait pour un étudiant recherchant l'indépendance. Cuisine américaine, rangements optimisés. À deux pas des universités.",
        "Beau studio refait à neuf, très bien agencé. Exposition lumineuse, parquet au sol. Idéal première location étudiante. Libre immédiatement.",
        "Studio cosy en centre-ville, tout équipé. Proche de toutes commodités et transports en commun. Parfait pour vos études supérieures."
    ],
    'colocation' => [
        "Superbe colocation dans appartement spacieux. Chambre meublée dans coloc conviviale avec 3 autres étudiants. Espaces communs agréables, cuisine équipée, salon chaleureux.",
        "Rejoins notre colocation sympa ! Grande maison avec jardin, 5 chambres. Ambiance studieuse et conviviale. Idéal pour faire de belles rencontres étudiantes.",
        "Chambre disponible en colocation moderne. Appartement récent, tout confort. Colocataires sérieux et respectueux. Charges comprises.",
        "Belle colocation étudiante dans quartier dynamique. Chambre meublée avec bureau. Cuisine et salon partagés. Fiber optique, parking vélo."
    ],
    'residence_etudiante' => [
        "Logement en résidence étudiante sécurisée. Studio meublé avec kitchenette, salle de bain privée. Services inclus : laverie, salle de sport, wifi. Gardien sur place.",
        "Résidence moderne réservée aux étudiants. Studio tout équipé dans environnement sécurisé. Nombreux services : local vélo, salle commune, wifi haut débit.",
        "Studio neuf en résidence étudiante récente. Tout confort avec accès aux espaces communs. Idéalement situé près du campus universitaire.",
        "Logement étudiant meublé en résidence. Cadre de vie agréable avec espaces de coworking. Éligible APL. Caution solidaire acceptée."
    ],
    'chambre_habitant' => [
        "Chambre chez l'habitant dans maison calme. Accès cuisine et salon. Ambiance familiale et bienveillante. Parfait pour étudiant sérieux.",
        "Loue chambre meublée dans pavillon avec jardin. Partage des espaces communs. Environnement studieux et respectueux. Proche transports.",
        "Belle chambre chez particulier, tout confort. Maison tranquille, idéale pour étudier. Possibilité petits plats faits maison (en option).",
        "Chambre indépendante chez l'habitant. Entrée privée, calme assuré. Petit-déjeuner inclus. Ambiance chaleureuse et accueillante."
    ]
];

echo "<h1>🚀 Génération de 50 annonces pour DormQuest</h1>";
echo "<pre>";

try {
    $pdo->beginTransaction();
    
    // Étape 1 : Créer 10 loueurs
    echo "\n📝 ÉTAPE 1 : Création de 10 loueurs\n";
    echo str_repeat("=", 50) . "\n";
    
    $loueursIds = [];
    for ($i = 0; $i < 10; $i++) {
        $prenom = $prenoms[array_rand($prenoms)];
        $nom = $noms[array_rand($noms)];
        $email = strtolower($prenom . '.' . $nom . $i) . '@example.com';
        $typeLoueur = $typesLoueur[array_rand($typesLoueur)];
        
        $stmt = $pdo->prepare("INSERT INTO utilisateurs (prenom, nom, email, motDePasse, role, typeLoueur, telephone, dateInscription) VALUES (?, ?, ?, ?, 'loueur', ?, ?, NOW())");
        $stmt->execute([
            $prenom,
            $nom,
            $email,
            generatePassword(),
            $typeLoueur,
            '06' . rand(10000000, 99999999)
        ]);
        
        $loueursIds[] = $pdo->lastInsertId();
        echo "✅ Loueur créé : $prenom $nom ($typeLoueur) - ID: " . $pdo->lastInsertId() . "\n";
    }
    
    // Étape 2 : Créer 50 annonces
    echo "\n🏠 ÉTAPE 2 : Création de 50 annonces\n";
    echo str_repeat("=", 50) . "\n";
    
    for ($i = 0; $i < 50; $i++) {
        // Sélection aléatoire
        $ville = $villes[array_rand($villes)];
        $typeLogement = $typesLogement[array_rand($typesLogement)];
        $idLoueur = $loueursIds[array_rand($loueursIds)];
        
        // Génération du code postal dans la plage de la ville
        $cpStart = (int)$ville['cp_range'][0];
        $cpEnd = (int)$ville['cp_range'][1];
        $codePostal = rand($cpStart, $cpEnd);
        
        // Génération du titre
        $titres = [
            'studio' => "Studio étudiant {$ville['nom']}",
            'colocation' => "Colocation sympa {$ville['nom']}",
            'residence_etudiante' => "Studio résidence étudiante {$ville['nom']}",
            'chambre_habitant' => "Chambre chez l'habitant {$ville['nom']}"
        ];
        $titre = $titres[$typeLogement];
        
        // Description aléatoire
        $description = $descriptionsTemplates[$typeLogement][array_rand($descriptionsTemplates[$typeLogement])];
        
        // Adresse
        $numeroRue = rand(1, 150);
        $rue = $rues[array_rand($rues)];
        $adresse = "$numeroRue $rue";
        
        // Prix selon le type et la ville
        $prixBase = [
            'Paris' => ['studio' => [600, 900], 'colocation' => [400, 650], 'residence_etudiante' => [550, 850], 'chambre_habitant' => [350, 550]],
            'Lyon' => ['studio' => [450, 700], 'colocation' => [350, 550], 'residence_etudiante' => [450, 650], 'chambre_habitant' => [300, 450]],
            'default' => ['studio' => [400, 600], 'colocation' => [300, 500], 'residence_etudiante' => [400, 600], 'chambre_habitant' => [250, 400]]
        ];
        
        $prixRange = $prixBase[$ville['nom']] ?? $prixBase['default'];
        $prix = rand($prixRange[$typeLogement][0], $prixRange[$typeLogement][1]);
        
        // Superficie selon type
        $superficieRanges = [
            'studio' => [18, 30],
            'colocation' => [80, 120],
            'residence_etudiante' => [18, 25],
            'chambre_habitant' => [12, 20]
        ];
        $superficie = rand($superficieRanges[$typeLogement][0], $superficieRanges[$typeLogement][1]);
        
        // Nombre de pièces
        $nombrePieces = ($typeLogement === 'studio' || $typeLogement === 'chambre_habitant') ? 1 : rand(3, 5);
        
        // Date de disponibilité (entre maintenant et 3 mois)
        $jours = rand(0, 90);
        $dateDisponibilite = date('Y-m-d', strtotime("+$jours days"));
        
        // Empreinte énergie (favoriser les bonnes notes)
        $empreinte = $empreintes[array_rand($empreintes)];
        
        // Email du loueur
        $stmtEmail = $pdo->prepare("SELECT email FROM utilisateurs WHERE id = ?");
        $stmtEmail->execute([$idLoueur]);
        $contactEmail = $stmtEmail->fetchColumn();
        
        // Insertion de l'annonce
        $stmt = $pdo->prepare("
            INSERT INTO annonces 
            (idLoueur, titre, description, adresse, ville, codePostal, typeLogement, prixMensuel, superficie, nombrePieces, colocationPossible, empreinteEnergie, dateDisponibilite, contactEmail, contactTelephone, dateCreation, statut) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), 'active')
        ");
        
        $stmt->execute([
            $idLoueur,
            $titre,
            $description,
            $adresse,
            $ville['nom'],
            $codePostal,
            $typeLogement,
            $prix,
            $superficie,
            $nombrePieces,
            ($typeLogement === 'colocation' ? 1 : 0),
            $empreinte,
            $dateDisponibilite,
            $contactEmail,
            '06' . rand(10000000, 99999999)
        ]);
        
        $annonceId = $pdo->lastInsertId();
        
        echo "✅ Annonce " . ($i + 1) . " : $titre - {$prix}€/mois - {$ville['nom']} (ID: $annonceId)\n";
        
        // Étape 3 : Ajouter 3-5 photos par annonce (Unsplash)
        $nbPhotos = rand(3, 5);
        $keywords = [
            'studio' => 'studio-apartment',
            'colocation' => 'shared-apartment',
            'residence_etudiante' => 'student-residence',
            'chambre_habitant' => 'bedroom'
        ];
        $keyword = $keywords[$typeLogement];
        
        for ($j = 0; $j < $nbPhotos; $j++) {
            // URL Unsplash avec dimensions fixes et seed pour cohérence
            $seed = $annonceId + $j;
            $photoUrl = "https://source.unsplash.com/800x600/?{$keyword}&sig={$seed}";
            
            $stmtPhoto = $pdo->prepare("INSERT INTO photos_annonces (idAnnonce, cheminPhoto, ordre, dateAjout) VALUES (?, ?, ?, NOW())");
            $stmtPhoto->execute([$annonceId, $photoUrl, $j]);
        }
        
        // Étape 4 : Ajouter critères de logement
        $stmtCriteres = $pdo->prepare("
            INSERT INTO criteres_logement 
            (idAnnonce, accesPMR, eligibleAPL, statutBoursier, animauxAcceptes, parkingDisponible, meuble) 
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        
        $stmtCriteres->execute([
            $annonceId,
            rand(0, 1), // accesPMR
            rand(0, 1), // eligibleAPL
            rand(0, 1), // statutBoursier
            rand(0, 1), // animauxAcceptes
            rand(0, 1), // parkingDisponible
            1           // meuble (toujours meublé pour étudiants)
        ]);
    }
    
    $pdo->commit();
    
    echo "\n" . str_repeat("=", 50) . "\n";
    echo "✅ SUCCÈS : 50 annonces créées avec succès !\n";
    echo "✅ 10 loueurs créés\n";
    echo "✅ Photos Unsplash intégrées (3-5 par annonce)\n";
    echo "✅ Critères de logement configurés\n\n";
    
    // Statistiques
    echo "📊 STATISTIQUES :\n";
    echo str_repeat("-", 50) . "\n";
    
    $stats = $pdo->query("SELECT typeLogement, COUNT(*) as nb FROM annonces GROUP BY typeLogement")->fetchAll();
    foreach ($stats as $stat) {
        echo "   - {$stat['typeLogement']} : {$stat['nb']} annonces\n";
    }
    
    $statsVilles = $pdo->query("SELECT ville, COUNT(*) as nb FROM annonces GROUP BY ville ORDER BY nb DESC LIMIT 5")->fetchAll();
    echo "\n🏙️  Top 5 des villes :\n";
    foreach ($statsVilles as $stat) {
        echo "   - {$stat['ville']} : {$stat['nb']} annonces\n";
    }
    
    $prixMoyen = $pdo->query("SELECT AVG(prixMensuel) as moyenne FROM annonces")->fetchColumn();
    echo "\n💰 Prix moyen : " . round($prixMoyen, 2) . "€/mois\n";
    
    echo "\n🎉 Vous pouvez maintenant utiliser votre plateforme DormQuest !\n";
    echo "🔑 Mot de passe des loueurs : password123\n\n";
    
} catch (Exception $e) {
    $pdo->rollBack();
    echo "\n❌ ERREUR : " . $e->getMessage() . "\n";
}

echo "</pre>";
?>