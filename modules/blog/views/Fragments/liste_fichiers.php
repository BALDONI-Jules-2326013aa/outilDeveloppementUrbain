<?php
use blog\models\FileModel;

$pdo = new PDO('pgsql:host=postgresql-siti.alwaysdata.net;dbname=siti_db', 'siti', 'motdepassesitia1');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$model = new FileModel($pdo);
// Supposons que $fileModel soit une instance de FileModel passée à la vue
$files = $model->getFiles();

usort($files, function($a, $b) {
    return $a['folder_id'] <=> $b['folder_id'];
});

$filesByFolder = [];
foreach ($files as $file) {
    $filesByFolder[$file['folder_id']][] = $file;
}

foreach ($filesByFolder as $folderId => $files): 
    $folderName = $model->getFolderName($folderId)['name'];
?>
    <details>
        <summary><?php echo htmlspecialchars($folderName); ?></summary>
        <ul>
            <?php foreach ($files as $file): ?>
                <li>
                    <span><?php echo htmlspecialchars($file['name']); ?> (User ID: <?php echo htmlspecialchars($file['utilisateur_id']); ?>)</span>
                    <form action="/telechargerFichier" method="post" style="display:inline;">
                        <input type="hidden" name="file_id" value="<?php echo $file['id']; ?>">
                        <button type="submit">Télécharger</button>
                    </form>
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