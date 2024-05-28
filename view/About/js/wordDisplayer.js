//sentence displayer
const sentenceArray = [
    "Společně objevujeme svět bez hranic."
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

        setTimeout(displayNextWord, 200); 
    }

    window.onload = displayNextWord;
