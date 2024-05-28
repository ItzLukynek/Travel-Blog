//for sentece display
const sentenceArray = [
    "Zapomeňte na hranice, <br> objevujme svět společně!"
    ];

    const sentenceDisplayDiv = document.getElementById("sentenceDisplay");
    let sentenceIndex = 0;
    let wordIndex = 0;

    function displayNextWord() {
        if (sentenceIndex >= sentenceArray.length) return;

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

        setTimeout(displayNextWord, 150); 
    }

    document.addEventListener("DOMContentLoaded", function () {
        displayNextWord();
      });
