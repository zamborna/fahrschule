let allQuestions = [];
let selectedQuestions = [];
let currentIndex = 0;
let onlyCorrect = false;

const questionNumberEl = document.getElementById("question-number");
const questionTextEl = document.getElementById("question-text");
const answersEl = document.getElementById("answers");
const questionImageEl = document.getElementById("question-image");
const progressTextEl = document.getElementById("progress-text");

const prevBtn = document.getElementById("prev");
const nextBtn = document.getElementById("next");
const toggleBtn = document.getElementById("toggle-correct");

async function loadData() {
  const [questionsRes, selectedRes] = await Promise.all([
    fetch("questionstr.json"),
    fetch("selected-questions.json")
  ]);

  const questionsData = await questionsRes.json();
  const selectedIds = (await selectedRes.json()).map(String);

  allQuestions = questionsData.filter(q => selectedIds.includes(q.question_number));
  renderQuestion();
}

function renderQuestion() {
  const q = allQuestions[currentIndex];
  if (!q) return;

  const imagePath = q.image ? q.image.replace(/\\/g, "/") : "images/default.png";
  questionImageEl.src = imagePath;
  questionImageEl.onerror = () => {
    questionImageEl.src = "images/default.png";
  };

  questionNumberEl.textContent = `Frage ${q.question_number}`;
  questionTextEl.textContent = q.question;

  const options = onlyCorrect ? q.options.filter(o => o.correct) : q.options;

  answersEl.innerHTML = "";
  options.forEach(opt => {
    const div = document.createElement("div");
    div.className = `answer${opt.correct ? " correct" : ""}`;
    div.textContent = opt.text;
    answersEl.appendChild(div);
  });

  const category = q.categories?.[0] || "Unbekannte Kategorie";
  progressTextEl.textContent = `${currentIndex + 1} / ${allQuestions.length} Fragen, Kategorie: ${category}`;

  prevBtn.disabled = currentIndex === 0;
  nextBtn.disabled = currentIndex === allQuestions.length - 1;

  toggleBtn.textContent = onlyCorrect ? "Alle Antworten anzeigen" : "Nur richtige Antworten anzeigen";
}

// Event Listeners
prevBtn.addEventListener("click", () => {
  if (currentIndex > 0) {
    currentIndex--;
    renderQuestion();
  }
});

nextBtn.addEventListener("click", () => {
  if (currentIndex < allQuestions.length - 1) {
    currentIndex++;
    renderQuestion();
  }
});

toggleBtn.addEventListener("click", () => {
  onlyCorrect = !onlyCorrect;
  renderQuestion();
});

// Start
loadData();
