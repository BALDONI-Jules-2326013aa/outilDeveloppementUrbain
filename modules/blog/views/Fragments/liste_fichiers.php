<?php
$files = $this->getFiles();

foreach ($files as $file): ?>
    <li>
        <?php echo htmlspecialchars($file['name']); ?>
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
