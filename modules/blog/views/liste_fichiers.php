<?php
use blog\models\FileModel;

// Connexion à la base de données PostgreSQL
$pdo = new PDO('pgsql:host=postgresql-siti.alwaysdata.net;dbname=siti_db', 'siti', 'motdepassesitia1');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Création d'une instance du modèle de fichier
$model = new FileModel($pdo);
// Récupération de tous les fichiers
$files = $model->getFiles();

// Tri des fichiers par ID de dossier
usort($files, function($a, $b) {
    return $a['folder_id'] <=> $b['folder_id'];
});

// Regroupement des fichiers par dossier
$filesByFolder = [];
foreach ($files as $file) {
    $filesByFolder[$file['folder_id']][] = $file;
}

// Affichage des fichiers par dossier
foreach ($filesByFolder as $folderId => $files):
    // Récupération du nom du dossier
    $folderName = $model->getFolderName($folderId)['name'];
    ?>
    <details>
        <summary><?php echo htmlspecialchars($folderName); ?></summary>
        <ul>
            <?php foreach ($files as $file): ?>
                <li>
                    <span><?php echo htmlspecialchars($file['name']); ?> (User ID: <?php echo htmlspecialchars($file['utilisateur_id']); ?>)</span>
                    <!-- Formulaire pour télécharger le fichier -->
                    <form action="/telechargerFichier" method="post" style="display:inline;">
                        <input type="hidden" name="file_id" value="<?php echo $file['id']; ?>">
                        <button type="submit">Télécharger</button>
                    </form>
                    <!-- Formulaire pour supprimer le fichier -->
                    <form action="/supprimerFichier" method="post" style="display:inline;">
                        <input type="hidden" name="file_id" value="<?php echo $file['id']; ?>">
                        <input type="hidden" name="action" value="supprimer">
                        <button type="submit">Supprimer</button>
                    </form>
                </li>
            <?php endforeach; ?>
        </ul>
    </details>
<?php endforeach; ?>