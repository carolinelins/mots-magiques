<?php
$stopwordsJson = file_get_contents('stopwords-fr.json');
$stopwordsArray = json_decode($stopwordsJson, true);
$word_count = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $word_count = [];
    if (isset($_FILES['textFile']) && $_FILES['textFile']['error'] === UPLOAD_ERR_OK) {
        $fileContent = file_get_contents($_FILES['textFile']['tmp_name']);
        $text = $fileContent;
    } else {
        $text = $_POST['text'] ?? '';
    }

    if ($text) {
        $text = strtolower($text);
        $text = preg_replace("/\b\w+['’]+/u", '', $text);
        $text = preg_replace("/[^\w\s'À-ž]+/u", '', $text);
        $words = preg_split('/\s+/', $text);

        foreach ($words as $word) {
            if (!in_array($word, $stopwordsArray) && strlen($word) > 2) {
                $word_count[$word] = ($word_count[$word] ?? 0) + 1;
            }
        }
        arsort($word_count);
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
        <textarea id="textInput" name="text" placeholder="Collez votre texte ici" rows="10"
            cols="50"></textarea><br><br>
        <span><b>ou télécharger un fichier texte :</b></span>
        <input type="file" name="textFile" accept=".txt"><br><br>
        <button type="submit">Générer</button><br><br>


    </form>

    <div id="wordCloud">
        <?php
        if (!empty($word_count)) {
            $maxFreq = max($word_count);

            $wordsShuffled = array_keys($word_count);
            shuffle($wordsShuffled);

            foreach ($wordsShuffled as $word) {
                $red = rand(0, 255);
                $green = rand(0, 255);
                $blue = rand(0, 255);

                $count = $word_count[$word];

                $hue = ($count / $maxFreq) * 360;

                echo '<span class="word" style="color: rgb(' . $red . ', ' . $green . ', ' . $blue . '); font-size: ' . (18 + ($count * 5)) . 'px;">' . htmlspecialchars($word) . '</span>';
            }
        } else {
            echo '<p style="text-align: center; font-size: 20px; color: #888;">Votre nuage de mots s\'affichera ici</p>';
        }
        ?>
    </div>

</body>

</html>