document.addEventListener("DOMContentLoaded", function () {
    let currentIndex = 0;
    let category = "all";
    let showCorrectOnly = false;

    const questionText = document.getElementById("question-text");
    const questionImage = document.getElementById("question-image");
    const optionsContainer = document.getElementById("options-container");
    const categoryDropdown = document.getElementById("category-filter");
    const toggleCorrectBtn = document.getElementById("toggle-correct");
    const prevBtn = document.getElementById("prev");
    const nextBtn = document.getElementById("next");

function loadQuestion(index) {
    fetch(`quiz.php?index=${index}&category=${category}${showCorrectOnly ? "&show_correct=1" : ""}`)
        .then(response => response.json())
        .then(data => {
            console.log("Loaded question:", data); // Debugging line
            if (data.error) {
                questionText.innerText = "No questions available.";
                questionImage.src = "";
                optionsContainer.innerHTML = "";
                return;
            }

            questionText.innerText = data.question;
            questionImage.src = data.image ? data.image : "default.jpg"; // Fallback for missing image
            optionsContainer.innerHTML = "";

            data.options.forEach(option => {
                if (!option) return; // Skip null options
                let optionElement = document.createElement("div");
                optionElement.innerText = option.text;
                if (option.correct) {
                    optionElement.classList.add("correct");
                }
                optionsContainer.appendChild(optionElement);
            });
        })
        .catch(error => console.error("Error loading question:", error));
}

    categoryDropdown.addEventListener("change", function () {
        category = this.value;
        currentIndex = 0;
        loadQuestion(currentIndex);
    });

    toggleCorrectBtn.addEventListener("click", function () {
        showCorrectOnly = !showCorrectOnly;
        loadQuestion(currentIndex);
    });

    prevBtn.addEventListener("click", function () {
        if (currentIndex > 0) {
            currentIndex--;
            loadQuestion(currentIndex);
        }
    });

    nextBtn.addEventListener("click", function () {
        currentIndex++;
        loadQuestion(currentIndex);
    });

    loadQuestion(currentIndex);
});
