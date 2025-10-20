<?php

// Script pour ajouter les colonnes budget et sector à la table users
// À exécuter directement dans la base de données ou via php artisan tinker

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "Ajout des colonnes budget et sector à la table users...\n";

try {
    // Vérifier si les colonnes existent déjà
    if (!Schema::hasColumn('users', 'budget')) {
        DB::statement('ALTER TABLE users ADD COLUMN budget DECIMAL(10,2) NULL AFTER role COMMENT "Budget annuel du sponsor en euros"');
        echo "✓ Colonne 'budget' ajoutée avec succès\n";
    } else {
        echo "⚠ Colonne 'budget' existe déjà\n";
    }
    
    if (!Schema::hasColumn('users', 'sector')) {
        DB::statement('ALTER TABLE users ADD COLUMN sector VARCHAR(50) NULL AFTER budget COMMENT "Secteur d\'activité du sponsor"');
        echo "✓ Colonne 'sector' ajoutée avec succès\n";
    } else {
        echo "⚠ Colonne 'sector' existe déjà\n";
    }
    
    echo "\n🎉 Migration terminée avec succès !\n";
    echo "Vous pouvez maintenant utiliser le système de recommandations avec les budgets des sponsors.\n";
    
} catch (Exception $e) {
    echo "❌ Erreur lors de l'ajout des colonnes: " . $e->getMessage() . "\n";
    echo "Veuillez exécuter manuellement les requêtes SQL suivantes:\n";
    echo "ALTER TABLE users ADD COLUMN budget DECIMAL(10,2) NULL AFTER role;\n";
    echo "ALTER TABLE users ADD COLUMN sector VARCHAR(50) NULL AFTER budget;\n";
}
