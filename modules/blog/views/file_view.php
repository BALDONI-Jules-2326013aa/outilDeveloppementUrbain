<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Gestion des fichiers</title>
</head>
<body>
    <h1>Gestion des fichiers GeoJSON</h1>
    <div class="file-upload">
        <form action="/fichier" method="post" enctype="multipart/form-data">
            <label for="file">Choisir un fichier GeoJSON:</label>
            <input type="file" name="file" id="file" accept=".geojson">
            <button type="submit">Uploader</button>
        </form>
    </div>
    <div class="file-list">
        <h2>Fichiers disponibles</h2>
        <ul>
            <?php foreach ($files as $file): ?>
                <li><?php echo htmlspecialchars($file['name']); ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
</body>
</html>