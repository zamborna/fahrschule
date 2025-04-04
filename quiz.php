<?php
session_start();

// Load data
$questionsData = json_decode(file_get_contents('questions.json'), true);
$selectedIds = json_decode(file_get_contents('selected-questions.json'), true);

// Normalize selected IDs as strings for comparison
$selectedIds = array_map('strval', $selectedIds);

// Filter questions to only those in selected-questions.json
$selectedQuestions = array_values(array_filter($questionsData, function($q) use ($selectedIds) {
    return in_array($q['question_number'], $selectedIds);
}));

$totalQuestions = count($selectedQuestions);

// Get current index from query string
$currentIndex = isset($_GET['q']) ? intval($_GET['q']) : 0;
$currentIndex = max(0, min($currentIndex, $totalQuestions - 1));

// Toggle correct answer view
if (isset($_GET['toggle'])) {
    $_SESSION['only_correct'] = !isset($_SESSION['only_correct']) || !$_SESSION['only_correct'];
}
$onlyCorrect = isset($_SESSION['only_correct']) && $_SESSION['only_correct'];

// Get current question
$currentQuestion = $selectedQuestions[$currentIndex];

// Normalize image path
$imagePath = str_replace("\\", "/", $currentQuestion['image']);
if (!file_exists($imagePath)) {
    $imagePath = "images/default.png";
}

// Filter answers
$options = $currentQuestion['options'];
if ($onlyCorrect) {
    $options = array_filter($options, fn($opt) => $opt['correct']);
}

// Category (optional)
$category = isset($currentQuestion['categories'][0]) ? $currentQuestion['categories'][0] : "Unbekannte Kategorie";
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="style.css">
    <meta charset="UTF-8">
    <title>Quiz</title>
</head>
<body>
    <div class="progress-bar">
        <span id="progress-text"><?= ($currentIndex + 1) ?> / <?= $totalQuestions ?> Fragen, Kategorie: <?= htmlspecialchars($category) ?></span>
    </div>

    <div class="container">
        <div class="image-section">
            <img id="question-image" src="<?= htmlspecialchars($imagePath) ?>" alt="Frage Bild">
        </div>

        <div class="question-section">
            <h2 id="question-number">Frage <?= htmlspecialchars($currentQuestion['question_number']) ?></h2>
            <p id="question-text"><?= htmlspecialchars($currentQuestion['question']) ?></p>
        </div>
    </div>

    <div class="answers-section">
        <div id="answers">
            <?php foreach ($options as $opt): ?>
                <div class="answer<?= $opt['correct'] ? ' correct' : '' ?>">
                    <?= htmlspecialchars($opt['text']) ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="navigation">
        <?php if ($currentIndex > 0): ?>
            <a href="?q=<?= $currentIndex - 1 ?>"><button id="prev">Zurück</button></a>
        <?php endif; ?>
        <?php if ($currentIndex < $totalQuestions - 1): ?>
            <a href="?q=<?= $currentIndex + 1 ?>"><button id="next">Weiter</button></a>
        <?php endif; ?>
    </div>

    <div class="navigation">
        <a href="?q=<?= $currentIndex ?>&toggle=1">
            <button id="toggle-correct">
                <?= $onlyCorrect ? "Alle Antworten anzeigen" : "Nur richtige Antworten anzeigen" ?>
            </button>
        </a>
    </div>
</body>
</html>
