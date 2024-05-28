document.addEventListener("DOMContentLoaded", function () {
    //for settings
    handleCheckboxChange("turn_off_comments");
    handleCheckboxChange("aprove_comments");
    handleCheckboxChange("validate_comments");
    handleCheckboxChange("turn_off_likes");

    const saveButton = document.getElementById("save_allowed_words");
    saveButton.addEventListener("click", handleSaveButtonClick);
});

///html for modal
function returnHTML(text){
    let html = '<div class="message_box">\
                        <div class="d-flex flex-row justify-content-between">\
                                <i onclick="closeModal()" id="modalClose" class="modal-close fa-solid fa-xmark"></i>\
                                <div class="ml-2 error-title d-flex flex-row">\
                                <h2>Oznámení<i class="ml-2 warning_icon fa-solid fa-circle-info"></i></h2>\
                                </div>\
                                </div>\
                                <div class="container">\
                                <h4 id="modal_message" class="">'+text+'</h4></div></div>';
    return html;
}
//adjust setting based on checkbox activyt
function handleCheckboxChange(checkboxId) {
    const checkbox = document.getElementById(checkboxId);

    if (checkbox) {
        checkbox.addEventListener("change", async function () {
            const isChecked = checkbox.checked;

            await fetch('../../controllers/adminControlers/process_settings.php?action=set_setting', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    name: checkboxId, 
                    value: isChecked ? 1 : 0,
                }),
            })
            .then(response => response.json()) 
            .then(data => {
                if (data.message) {
                    console.log(checkboxId + ' changed successfully: ' + data.message);
                } else if (data.error) {
                    console.error('Error changing ' + checkboxId + ':', data.error);
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        });
    }
}
//save
function handleSaveButtonClick() {
    const allowedWordsTextarea = document.getElementById("allowed_words");
    const inputText = allowedWordsTextarea.value;

    let phrasesArray = inputText.split(",").map(phrase => phrase.trim());
    phrasesArray = phrasesArray.map(phrase => phrase.replace(" ",""))
    updateValidations(phrasesArray);
}
//update phrases for filtering
async function updateValidations(phrasesArray) {
    try {
        const response = await fetch('../../controllers/adminControlers/process_settings.php?action=update_validations', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                phrases: phrasesArray
            }),
        });

        const data = await response.json();

        

        if (data.message) {
            console.log('Update successful:', data.message);
            showModal(returnHTML(data.message));
        } else if (data.error) {
            showModal(returnHTML(data.error));
            console.error('Error updating validations:', data.error);
        }
    } catch (error) {
        console.error('Error:', error);
    }
}
