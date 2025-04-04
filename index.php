<?php
// Load questions from JSON
$questionsFile = 'questions.json';
$questions = [];
if (file_exists($questionsFile)) {
    $questions = json_decode(file_get_contents($questionsFile), true);
}

header('Content-Type: text/html; charset=UTF-8');
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Question Catalogue</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        /* Include the same styles here */
    </style>
</head>
<body>
    <div class="progress-bar">
        <span id="progress-text">0 / <?php echo count($questions); ?> Fragen, Kategorie</span>
    </div>
    
    <div class="category-filter">
        <label for="category-select">Kategorie:</label>
        <select id="category-select">
            <option value="all">Alle Kategorien</option>
            <?php
            $categories = [];
            foreach ($questions as $q) {
                foreach ($q['categories'] as $cat) {
                    $categories[$cat] = true;
                }
            }
            foreach (array_keys($categories) as $category) {
                echo "<option value='" . htmlspecialchars($category) . "'>" . htmlspecialchars($category) . "</option>";
            }
            ?>
        </select>
    </div>
    
    <div class="container">
        <div class="question-section">
            <h2 id="question-number">Frage 1</h2>
            <p id="question-text">Lade Frage...</p>
        </div>
        <div class="image-section">
            <img id="question-image" src="images/default.png" alt="Frage Bild">
        </div>
    </div>
    
    <div class="answers-section">
        <div id="answers"></div>
        <button id="toggle-correct">Nur richtige Antworten anzeigen</button>
    </div>
    
    <div class="navigation">
        <button id="prev">Zurück</button>
        <button id="next">Weiter</button>
    </div>
    
    <script>
        let questions = <?php echo json_encode($questions); ?>;
        let filteredQuestions = [...questions];
        let currentIndex = 0;
        let showOnlyCorrect = false;
        let selectedCategory = "all";
        
        function updateQuestion() {
            if (filteredQuestions.length === 0) return;
            
            const question = filteredQuestions[currentIndex];
            document.getElementById('progress-text').textContent = `${currentIndex + 1} / ${filteredQuestions.length} Fragen, ${question.categories.join(', ')}`;
            document.getElementById('question-number').textContent = `Frage ${question.question_number}`;
            document.getElementById('question-text').textContent = question.question;
            document.getElementById('question-image').src = question.image;
            
            const answersContainer = document.getElementById('answers');
            answersContainer.innerHTML = '';
            
            question.options.forEach(option => {
                if (!showOnlyCorrect || option.correct) {
                    const answerDiv = document.createElement('div');
                    answerDiv.textContent = option.text;
                    answerDiv.classList.add('answer');
                    if (option.correct) answerDiv.classList.add('correct');
                    answersContainer.appendChild(answerDiv);
                }
            });
        }
        
        document.getElementById('prev').addEventListener('click', () => {
            if (currentIndex > 0) {
                currentIndex--;
                updateQuestion();
            }
        });
        
        document.getElementById('next').addEventListener('click', () => {
            if (currentIndex < filteredQuestions.length - 1) {
                currentIndex++;
                updateQuestion();
            }
        });
        
        document.getElementById('toggle-correct').addEventListener('click', () => {
            showOnlyCorrect = !showOnlyCorrect;
            updateQuestion();
        });
        
        document.getElementById('category-select').addEventListener('change', (event) => {
            selectedCategory = event.target.value;
            filteredQuestions = selectedCategory === "all" ? questions : questions.filter(q => q.categories.includes(selectedCategory));
            currentIndex = 0;
            updateQuestion();
        });
        
        updateQuestion();
    </script>
</body>
</html>
