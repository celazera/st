<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter une demande d'offre</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 20px;
        }
        form {
            max-width: 600px;
            background: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin: 0 auto;
        }
        label {
            display: block;
            margin-bottom: 10px;
        }
        input[type="text"],
        textarea,
        select {
            width: calc(100% - 22px);
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 16px;
        }
        textarea {
            height: 150px;
        }
        button[type="submit"] {
            background-color: #032B44;
            color: #ffffff;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 18px;
        }
        button[type="submit"]:hover {
            background-color: #021f30;
        }
    </style>
</head>
<body>
    <form action="upload.php" method="post" enctype="multipart/form-data">
        <h2>Ajouter une demande d'offre</h2>
        <label for="titre">Titre :</label>
        <input type="text" id="titre" name="titre" required>
        
        <label for="description">Description :</label>
        <textarea id="description" name="description" required></textarea>
        
        <label for="pdfFile">Sélectionner un fichier PDF :</label>
        <input type="file" id="pdfFile" name="pdfFile">
        
        <button type="submit" name="submit">Uploader</button>
    </form>
</body>
</html>
