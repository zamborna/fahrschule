<?php
// Load questions from JSON file
$questionsFile = 'questions.json';
$selectedQuestionsFile = 'selected-questions.json';
$questions = json_decode(file_get_contents($questionsFile), true);
$selectedQuestions = file_exists($selectedQuestionsFile) ? json_decode(file_get_contents($selectedQuestionsFile), true) : [];

// Get category from request
$category = $_GET['category'] ?? 'all';
$showCorrectOnly = isset($_GET['show_correct']);

// Filter questions by category
$filteredQuestions = array_filter($questions, function ($q) use ($category, $selectedQuestions) {
    return $category === 'all' || (isset($q['categories'][0]) && $q['categories'][0] === $category) || ($category === 'slct' && in_array($q['question_number'], $selectedQuestions));
});

// Convert filtered questions to array
$filteredQuestions = array_values($filteredQuestions);

// Get current question index
$index = isset($_GET['index']) ? (int)$_GET['index'] : 0;
if ($index < 0) $index = 0;
if ($index >= count($filteredQuestions)) $index = count($filteredQuestions) - 1;

$question = $filteredQuestions[$index] ?? null;

if ($question) {
    echo json_encode([
        'question_number' => $question['question_number'],
        'question' => $question['question'],
        'image' => str_replace('\\', '/', $question['image']),
        'options' => array_map(function ($option) use ($showCorrectOnly) {
            return $showCorrectOnly && !$option['correct'] ? null : $option;
        }, $question['options'])
    ]);
} else {
    echo json_encode(['error' => 'No questions available.']);
}
?>
