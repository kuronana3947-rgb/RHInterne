## Commandes SQLite utiles pour tester les seeders

Ces commandes servent à remettre la base à jour quand tu modifies un seeder. Comme plusieurs seeders contiennent un garde-fou du type `if ($builder->countAllResults() > 0) return;`, il faut souvent vider la table ou repartir d’une base propre avant de relancer le seeder.

### 1. Recréer toute la base puis reseeder tout le projet

```bash
rm -f "/home/harena/Documents/ITU/S4/Système d’information/RHInterne/rh_interne.db"
php spark migrate
php spark db:seed DatabaseSeeder
```

**Détail :**
- Supprime le fichier SQLite actuel.
- Relance toutes les migrations pour recréer les tables.
- Relance `DatabaseSeeder`, qui appelle les seeders dans l’ordre.
- C’est la meilleure option si tu as modifié plusieurs seeders ou le schéma.

### 2. Vider la table `soldes` puis relancer `SoldeSeeder`

```bash
php -r '$db=new PDO("sqlite:/home/harena/Documents/ITU/S4/Système d’information/RHInterne/rh_interne.db");$db->exec("DELETE FROM soldes");echo "soldes vidée\n";'
php spark db:seed SoldeSeeder
```

**Détail :**
- Supprime toutes les lignes de la table `soldes`.
- Permet au seeder de réinsérer les soldes de test.
- À utiliser après une modification de `app/Database/Seeds/SoldeSeeder.php`.

### 3. Vider la table `employes` puis relancer `EmployeSeeder`

```bash
php -r '$db=new PDO("sqlite:/home/harena/Documents/ITU/S4/Système d’information/RHInterne/rh_interne.db");$db->exec("DELETE FROM employes");echo "employes vidée\n";'
php spark db:seed EmployeSeeder
```

**Détail :**
- Vide la table des employés avant reseeding.
- Utile si tu modifies les identifiants, rôles, emails ou mots de passe du seeder.
- Important si tu veux voir les changements immédiatement sans recréer toute la base.

### 4. Vider la table `departements` puis relancer `DepartementSeeder`

```bash
php -r '$db=new PDO("sqlite:/home/harena/Documents/ITU/S4/Système d’information/RHInterne/rh_interne.db");$db->exec("DELETE FROM departements");echo "departements vidée\n";'
php spark db:seed DepartementSeeder
```

**Détail :**
- Supprime les départements existants.
- Permet de recharger les départements de référence.
- À faire si tu modifies `app/Database/Seeds/DepartementSeeder.php`.

### 5. Vider la table `types_conge` puis relancer `TypeCongeSeeder`

```bash
php -r '$db=new PDO("sqlite:/home/harena/Documents/ITU/S4/Système d’information/RHInterne/rh_interne.db");$db->exec("DELETE FROM types_conge");echo "types_conge vidée\n";'
php spark db:seed TypeCongeSeeder
```

**Détail :**
- Supprime les types de congé existants.
- Recharge les types de congé utilisés par les soldes et les congés.

### 6. Vider la table `statuts` puis relancer `StatutSeeder`

```bash
php -r '$db=new PDO("sqlite:/home/harena/Documents/ITU/S4/Système d’information/RHInterne/rh_interne.db");$db->exec("DELETE FROM statuts");echo "statuts vidée\n";'
php spark db:seed StatutSeeder
```

**Détail :**
- Supprime les statuts de congé.
- Recharge les valeurs `en_attente`, `approuve`, `refuse` et `annule`.

### 7. Vérifier les données d’une table après seeding

```bash
php -r '$db=new PDO("sqlite:/home/harena/Documents/ITU/S4/Système d’information/RHInterne/rh_interne.db");$rows=$db->query("SELECT * FROM employes ORDER BY id")->fetchAll(PDO::FETCH_ASSOC);foreach($rows as $row){echo json_encode($row, JSON_UNESCAPED_UNICODE)."\n";}'
```

**Détail :**
- Affiche toutes les lignes de la table `employes`.
- Pratique pour confirmer que le seeder a bien été appliqué.
- Tu peux remplacer `employes` par `soldes`, `departements`, `types_conge` ou `statuts`.

### 8. Relancer uniquement les seeders sans toucher aux migrations

```bash
php spark db:seed DatabaseSeeder
```

**Détail :**
- Rejoue tous les seeders définis dans `DatabaseSeeder`.
- Utile si les tables existent déjà et que tu veux seulement remettre les données.
- Si rien ne change, c’est souvent parce que la table contient déjà des lignes et que le seeder sort immédiatement.

