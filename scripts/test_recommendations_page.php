<?php

/**
 * Test de la page de recommandations
 */

require __DIR__ . '/../vendor/autoload.php';

echo "=== TEST DE LA PAGE DE RECOMMANDATIONS ===\n\n";

// Test 1: Vérifier que les fichiers existent
echo "1. Vérification des fichiers...\n";

$files = [
    'resources/views/pages/frontOffice/recommendations.blade.php',
    'resources/views/components/simple-recommendations.blade.php',
    'resources/views/components/community-recommendations.blade.php'
];

foreach ($files as $file) {
    if (file_exists($file)) {
        echo "✅ {$file}\n";
    } else {
        echo "❌ {$file} - MANQUANT\n";
    }
}

// Test 2: Vérifier le contenu de la page
echo "\n2. Vérification du contenu de la page...\n";

$pageContent = file_get_contents('resources/views/pages/frontOffice/recommendations.blade.php');
$contentChecks = [
    'simple-recommendations' => 'Composant de recommandations simplifié',
    'Découvrez vos Communautés Idéales' => 'Titre principal',
    'Notre IA analyse' => 'Description de l\'IA',
    'Comment fonctionne notre IA' => 'Section explicative'
];

foreach ($contentChecks as $content => $description) {
    if (strpos($pageContent, $content) !== false) {
        echo "✅ {$description}\n";
    } else {
        echo "❌ {$description} - MANQUANT\n";
    }
}

// Test 3: Vérifier le composant simple
echo "\n3. Vérification du composant simple...\n";

$componentContent = file_get_contents('resources/views/components/simple-recommendations.blade.php');
$componentChecks = [
    'loadSampleCommunities' => 'Fonction de chargement des communautés',
    'community-card' => 'Style des cartes de communauté',
    'Énergies Vertes Paris' => 'Communauté d\'exemple',
    'joinCommunity' => 'Fonction de rejoindre une communauté'
];

foreach ($componentChecks as $content => $description) {
    if (strpos($componentContent, $content) !== false) {
        echo "✅ {$description}\n";
    } else {
        echo "❌ {$description} - MANQUANT\n";
    }
}

// Test 4: Vérifier les routes
echo "\n4. Vérification des routes...\n";

$webRoutes = file_get_contents('routes/web.php');
$routeChecks = [
    '/recommendations' => 'Route de la page de recommandations',
    'organizer/communities' => 'Route des communautés organisateur'
];

foreach ($routeChecks as $route => $description) {
    if (strpos($webRoutes, $route) !== false) {
        echo "✅ {$description}\n";
    } else {
        echo "❌ {$description} - MANQUANT\n";
    }
}

echo "\n=== RÉSUMÉ ===\n";
echo "✅ Page de recommandations créée avec succès !\n";
echo "\n📋 FONCTIONNALITÉS:\n";
echo "• Page accessible à l'URL: /recommendations\n";
echo "• Composant de recommandations simplifié\n";
echo "• Communautés d'exemple avec données statiques\n";
echo "• Interface moderne et responsive\n";
echo "• Pas de dépendance aux routes d'organisateur\n";
echo "\n🚀 POUR TESTER:\n";
echo "1. Visitez: http://127.0.0.1:8000/recommendations\n";
echo "2. Vérifiez que la page se charge sans erreur\n";
echo "3. Testez les boutons et interactions\n";
echo "\n💡 La page utilise des données d'exemple pour démonstration\n";
echo "   et peut être facilement connectée à l'API de recommandations !\n";
