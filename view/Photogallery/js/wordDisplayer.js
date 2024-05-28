// Fill the array with the sentences you want to display
const sentenceArray = [
    "Vstupte do našeho světa plného barev a emocí."
    ];

    const sentenceDisplayDiv = document.getElementById("sentenceDisplay");
    let sentenceIndex = 0;
    let wordIndex = 0;

    function displayNextWord() {
        if (sentenceIndex >= sentenceArray.length) return; // All sentences displayed

        const currentSentence = sentenceArray[sentenceIndex];
        const words = currentSentence.split(" ");
        const currentWord = words[wordIndex];

        sentenceDisplayDiv.innerHTML +=  currentWord + " " ;

        if (wordIndex < words.length - 1) {
            wordIndex++;
        } else {
            wordIndex = 0;
            sentenceIndex++;
        }

        setTimeout(displayNextWord, 200); // Adjust the delay here (in milliseconds)
    }

    document.addEventListener("DOMContentLoaded", displayNextWord())
    
