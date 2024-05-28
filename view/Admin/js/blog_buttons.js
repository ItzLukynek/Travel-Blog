         
        
    document.addEventListener("DOMContentLoaded", function() {
        //for saving concept
        document.getElementById("save_concept").addEventListener("click", async () => {
        try {
            const title = document.getElementById("blogTitle").value;
            const text = document.getElementById("blogText").value;
            const selectCountry = document.getElementById("selectCountry").value;
            let blog_id = document.getElementById("blog_id").value

            if(!blog_id){
                blog_id = "";
            }

            const data = {
                blogTitle: title,
                blogText: text,
                selectCountry: selectCountry,
                blog_id:blog_id
            };
            
            console.log(data)
            
            const response = await fetch("../../controllers/adminControlers/process_blogs.php?action=save_concept", {
                method: 'POST',
                body: JSON.stringify(data),
                headers: {
                    'Content-Type': 'application/json'
                }
            });
            location.reload()

        } catch (error) {
            console.error("Error:", error);
        }
    });

    
    //if u click somewhere else in textarea it will give the cursor back to the start
    const textarea = document.getElementById("blogText");

        let clicked = false;
        textarea.addEventListener("click", function() {

        if(!clicked){
            this.selectionStart = 0;
            this.selectionEnd = 0;
            clicked = true;
        }
    });


    });
    //for correct
    function br2n(inputString) {
        return inputString.replace('<br>', '\n');
    }

    //showing concept modal
    document.getElementById("show_concept").addEventListener("click", async () => {
       
        try {
            const response = await fetch(`../../controllers/adminControlers/process_blogs.php?action=get_concept`, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json'
                }
            });
    
            if (response.ok) {
                try {
                    const responseData = await response.json();
                    let modalContentHTML = '<div class="w-100 bg-light rounded p-3  d-flex flex-column align-items-center justify-content-end"><h1 class="mb-5" style="color:black">Koncepty</h1><div class="row w-100">'; // Initialize an empty string to hold the modal content
    
                    responseData.forEach(concept => {
                        modalContentHTML += `
                            <div class="concept-item col col-md-6 col-12" data-concept-id="${concept.id}">
                                
                                    <div class="d-flex flex-row w-100 justify-content-between">
                                        <h3>${concept.desc.length > 15 ? concept.desc.substr(0, 15) + '...' : concept.desc}</h3>
                                        <h4>${concept.created_at}</h4>
                                    </div>
                                    <p>${concept.text.length > 35 ? concept.text.substr(0, 35) + '...' : concept.text}</p>
                               
                            </div>
                        
                        `;
                    });

                    modalContentHTML += '</div></div>'
                    
                    showModal(modalContentHTML)
    
                    //concepts
                    const conceptItems = document.querySelectorAll(".concept-item");
                    conceptItems.forEach(conceptItem => {
                        conceptItem.addEventListener("click", (event) => {
                            const conceptId = event.currentTarget.getAttribute("data-concept-id");
                            const selectedConcept = responseData.find(concept => concept.id === parseInt(conceptId));
    
                            if (selectedConcept) {
                                document.getElementById("blogTitle").value = selectedConcept.desc;
                                document.getElementById("blogText").value = br2n(selectedConcept.text);
                                document.getElementById("blog_id").value = selectedConcept.id;
    
                                const selectCountry = document.getElementById("selectCountry");
    
                                for (let i = 0; i < selectCountry.options.length; i++) {
                                    if (selectCountry.options[i].value === selectedConcept.Destination_id) {
                                        selectCountry.options[i].selected = true;
                                    } else {
                                        selectCountry.options[i].selected = false;
                                    }
                                }
                                closeModal();
                            }
                        });
                    });
                } catch (error) {
                    console.log(error);
                }
            } else {
                console.error("Error fetching data:", response.status);
            }
        } catch (error) {
            console.error("Error fetching data:", error);
        }
    });
    



