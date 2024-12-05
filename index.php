<?php
// Récupére la liste des mots vides
$stopwordsJson = file_get_contents('stopwords-fr.json');
$stopwordsArray = json_decode($stopwordsJson, true);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $word_count = []; // Initialise un tableau vide pour le comptage de mots
    if (isset($_FILES['textFile']) && $_FILES['textFile']['error'] === UPLOAD_ERR_OK) {
        // Si un fichier a été envoyé avec succès
        $fileContent = file_get_contents($_FILES['textFile']['tmp_name']); // Lire le contenu du fichier temporaire
        $text = $fileContent; // Enregistre le contenu du fichier dans $text
    } else {
        // Si aucun fichier n'a été envoyé, utiliser le texte de la zone de texte
        $text = $_POST['textInput'] ?? '';
    }

    if ($text) {
        // Nettoyage des mots
        $text = strtolower($text);
        $text = preg_replace("/\b\w+['’]+/u", '', $text);
        $text = preg_replace("/[^\w\s'À-ž]+/u", '', $text);
        $words = preg_split('/\s+/', $text);

        // Parcourir chaque mot extrait du texte
        foreach ($words as $word) {
            // Vérifier si le mot n'est pas une stopword et a plus de 2 caractères
            if (!in_array($word, $stopwordsArray) && strlen($word) > 2) {
                // Incrémenter le compteur de mots ou l'initialiser à 1 s'il n'existe pas encore
                $word_count[$word] = ($word_count[$word] ?? 0) + 1;
            }
        }

        // Trier le tableau des mots par ordre décroissant de fréquence
        arsort($word_count);
        // Conserver seulement les 30 mots les plus fréquents
        $word_count = array_slice($word_count, 0, 30);
    }
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>MotsMagiques</title>
    <style>
        h1 {
            font-size: 36px;
            font-weight: 700;
            color: #E36F92;
            text-align: center;
            text-shadow: 2px 2px 5px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        body {
            font-family: Verdana, sans-serif;
            text-align: center;
            margin: 20px;
            background-color: #f5f5f5;
        }

        textarea {
            width: 80%;
            max-width: 800px;
            font-size: 18px;
            font-family: Verdana, sans-serif;
            border-radius: 10px;
            resize: vertical;
        }

        button {
            background-color: #E36F92;
            color: white;
            font-family: Verdana, sans-serif;
            font-size: 20px;
            padding: 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #D2587C;
        }

        #wordCloud {
            background-color: #ffffff;
            width: 800px;
            height: auto;
            margin: 20px auto;
            border: 1px solid #ccc;
            overflow-wrap: break-word;
            word-break: break-word;
            min-height: 200px;
            margin: 20px auto;
            border: 2px dotted #ccc;
            border-radius: 10px;
        }

        .word {
            display: inline-block;
            margin: 5px;
            padding: 3px;
            font-weight: bold;
        }
    </style>
</head>

<body>

    <h1>MotsMagiques</h1>
    <h4>générateur de nuages de mots</h4>

    <form id="textForm" method="post" enctype="multipart/form-data">
        <textarea id="textInput" name="textInput" placeholder="Collez votre texte ici" rows="10"
            cols="50"></textarea><br><br>
        <span><b>ou télécharger un fichier texte :</b></span>
        <input type="file" name="textFile" accept=".txt"><br><br>
        <button type="submit">Générer</button><br><br>
    </form>

    <div id="wordCloud">
        <?php
        // Vérifier si le tableau des mots n'est pas vide
        if (!empty($word_count)) {
            // Obtenir les clés (mots) du tableau et les mélanger aléatoirement
            $wordsShuffled = array_keys($word_count);
            shuffle($wordsShuffled);

            // Parcourir les mots mélangés
            foreach ($wordsShuffled as $word) {
                // Générer une couleur aléatoire pour chaque mot
                $red = rand(0, 255);
                $green = rand(0, 255);
                $blue = rand(0, 255);

                // Récupérer le nombre d'occurrences du mot
                $count = $word_count[$word];

                // Afficher chaque mot avec une taille de police en fonction de sa fréquence et une couleur aléatoire
                echo '<span class="word" style="color: rgb(' . $red . ', ' . $green . ', ' . $blue . '); font-size: ' . (18 + ($count * 5)) . 'px;">' . htmlspecialchars($word) . '</span>';
            }
        } else {
            // Si le tableau des mots est vide, afficher un message
            echo '<p style="text-align: center; font-size: 20px; color: #888;">Votre nuage de mots s\'affichera ici</p>';
        }
        ?>
    </div>

</body>

</html>