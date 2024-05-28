document.addEventListener("DOMContentLoaded", async function () {
    get_items_html(""); // Load the user table first time
     search_bar();
     filter_items();
});

//for modal html
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
//filter users by rolee
function filter_items() {
    let select = document.getElementById("filterSelect");

    select.addEventListener("input", function () {
        get_items_html( select.value);
    });
}
//search users by name or email
function search_bar() {
    const userSearchInput = document.getElementById("blogSearch");
    const userListContainer = document.getElementById("blogList");

    userSearchInput.addEventListener("input", function () {
        const searchValue = userSearchInput.value.toLowerCase();
        const users = document.querySelectorAll(".manage_blog"); 
        let hasResults = false;

        users.forEach((user) => {
            const userFullName = user.querySelector(".col-lg-3").textContent.toLowerCase(); 
            const userEmail = user.querySelector(".col-lg-3").textContent.toLowerCase(); 

            if (userFullName.includes(searchValue) || userEmail.includes(searchValue)) {
                user.style.display = "flex";
                hasResults = true;
            } else {
                user.style.display = "none";
            }
        });

        const messageElement = document.createElement("div");
        messageElement.classList.add("text-center", "w-100", "no_message", "mt-3");
        messageElement.innerHTML = hasResults ? "" : "<h1>Nenalezeny žádné výsledky</h1>";

        const existingMessage = userListContainer.querySelector(".no_message");
        if (existingMessage) {
            userListContainer.removeChild(existingMessage);
        }

        userListContainer.appendChild(messageElement);
    });
}


async function get_items_html( filter) {
    try {
        const data = {
            request: true,
        };

        const response = await fetch("../../controllers/adminControlers/process_users.php?action=get_users", {
            method: "POST",
            body: JSON.stringify(data),
            headers: {
                "Content-Type": "application/json",
            },
        });

        let items = [0];
        let items_HTML = "";
        if (response.ok) {
            items = await response.json();
            
            
            items.forEach((item) => {

                let disabled = "no-visibility";
                if(item.blocked == 0){
                    disabled = "";
                }
                //if filter show only some
                if(filter != ""){
                    if(item.SecurityLevel_id == filter){
                            items_HTML += `
                        <div class="manage_blog row  ">
                            <div  class="col col-lg-3 col-md-3 col-12 d-flex flex-row align-items-center ">
                                <p class="mr-2 ">${item.FirstName} </p>
                                <p>${item.LastName}</p>
                            </div>
                            <div class="col col-lg-3 col-md-3 col-12 d-flex flex-row align-items-center ">
                                <p class="mr-2 ">${item.Email} </p>
                            </div>
                            <div class="col col-lg-1 col-md-1 col-6 text-center justify-content-end justify-content-md-center d-flex flex-row align-items-center">
                                <i class="mr-1 fa-solid fa-thumbs-up"></i>${item.likeCount}
                            </div>
                            <div class="col col-lg-1 col-md-1 col-6 text-center d-flex flex-row align-items-center">
                                <i class="mr-1 fa-regular fa-comment-dots"></i>${item.commentCount}
                            </div>
                            <div class="col col-lg-4 col-md-4 col-12 mt-md-0 mt-2 text-center">
                                <div class="table-icons d-flex flex-row justify-content-center">
                                    <button class="edit-icon mr-1" "><i class="fas fa-edit"></i></button>
                                    <button class="delete-icon mr-1""><i class="fas fa-trash"></i></button>
                                    <button class="photo-icon mr-1 ${disabled}" "><i class="fa-solid fa-ban"></i></button>
                                </div>
                            </div>
                        </div>
                    `;
                    }
                }else{
                    items_HTML += `
                    <div class="manage_blog row  ">
                        <div  class="col col-lg-3 col-md-3 col-12 d-flex flex-row align-items-center ">
                            <p class="mr-2 ">${item.FirstName} </p>
                            <p>${item.LastName}</p>
                        </div>
                        <div class="col col-lg-3 col-md-3 col-12 d-flex flex-row align-items-center ">
                            <p class="mr-2 ">${item.Email} </p>
                        </div>
                        <div class="col col-lg-1 col-md-1 col-6 text-center justify-content-end justify-content-md-center d-flex flex-row align-items-center">
                            <i class="mr-1 fa-solid fa-thumbs-up"></i>${item.likeCount}
                        </div>
                        <div class="col col-lg-1 col-md-1 col-6 text-center d-flex flex-row align-items-center">
                            <i class="mr-1 fa-regular fa-comment-dots"></i>${item.commentCount}
                        </div>
                        <div class="col col-lg-4 col-md-4 col-12 mt-md-0 mt-2 text-center">
                            <div class="table-icons d-flex flex-row justify-content-center">
                                <button class="edit-icon mr-1" "><i class="fas fa-edit"></i></button>
                                <button class="delete-icon mr-1""><i class="fas fa-trash"></i></button>
                                <button class="photo-icon mr-1 ${disabled}" "><i class="fa-solid fa-ban"></i></button>
                            </div>
                        </div>
                    </div>
                `;
                }
            });

            const manageItems = document.querySelector(".manage_blogs");
            manageItems.innerHTML = items_HTML;

            const deletes = document.querySelectorAll(".delete-icon");

            deletes.forEach((button,index) =>{
                button.addEventListener("click",()=>{
                    const id = items[index].id; 
                    delete_user(id); 
                })
            })
            const edits = document.querySelectorAll(".edit-icon");

            edits.forEach((button,index) =>{
                button.addEventListener("click",()=>{
                    const id = items[index].id; 
                    edit_user(id); 
                })
            })
            const blocked = document.querySelectorAll(".photo-icon");

            blocked.forEach((button,index) =>{
                button.addEventListener("click",()=>{
                    const id = items[index].id; 
                    block_user(id); 
                })
            })
        } else {
            items_HTML = `<div class="text-center w-100"><h1>Data nenalezena</h1></div>`;
        }
    } catch (error) {
        console.log("Error: " + error);
    }
}
//Delete user
async function delete_user(userId) {
    try {
                
            let request_html=`
            <div class="message_box">
            <div class="d-flex flex-row justify-content-between">
                    <i onclick="closeModal()" id="modalClose" class="modal-close fa-solid fa-xmark"></i>
                    <div class="ml-2 error-title d-flex flex-row">
                    <h2>Potvrzení<i class="ml-2 warning_icon fa-solid fa-circle-info"></i></h2>
                    </div>
                    </div>
                    <div class="container">
                    <h4 id="modal_message" class="">
                    Smazání uživatele vymaže spolu s ním i všechny jeho komentáře,blogy a lajky.
                    </h4>
            </div>
            <div class="d-flex justify-content-end mt-3 ">
            <button type="button" id="delete_affirmation" class="mr-3 w-25 btn btn-danger bg-danger">Odstranit</button>
                    
            </div>
            </div>
            `
            showModal(request_html)

            document.querySelector("#delete_affirmation").addEventListener("click",async()=>{
            const data = {
            userId:userId
            };
            const response = await fetch("../../controllers/adminControlers/process_users.php?action=delete_user", {
                method: "POST",
                body: JSON.stringify(data),
                headers: {
                "Content-Type": "application/json",
            },
            });
            responses = await response.json()
            if (responses.message) {
                console.log(responses.message);
                closeModal()
                location.reload()
            } else {
                console.error("Error deleting user:", responses.error);
            }
        })
    } catch (error) {
        console.error('Error:', error);
    }
}
//edit user
async function edit_user(id) {
    const data = {
        userId:id
    };

    const response = await fetch("../../controllers/adminControlers/process_users.php?action=get_user", {
        method: "POST",
        body: JSON.stringify(data),
        headers: {
            "Content-Type": "application/json",
        },
    });
    
    if (response.ok) {
        let user = await response.json();
            let select
        if(user.SecurityLevel_id == 0){
             select = '<option value="0" selected>Admin</option><option value="1">Blogger</option><option value="2">Uživatel</option>'
        }else if(user.SecurityLevel_id == 1){
             select = '<option value="1" selected>Blogger</option><option value="2">Uživatel</option><option value="0">Admin</option>'
        }else if(user.SecurityLevel_id == 2){
             select = '<option value="2" selected>Uživatel</option><option value="0">Admin</option><option value="1">Blogger</option>'
        }

        let infocard = `
        <div class="ecard" >
            <div class="ecard-body" style="height:auto;">
                <div class="row flex-column flex-lg-row d-flex" >
                        <div class="col-12 col-lg-6 d-flex flex-column mb-3 mb-lg-0">
                            <label for="blogTitle">Křestní Jméno</label>
                            <input  value="${user.FirstName}" type="text" id="FirstName" name="FirstName" class="" required>
                              </div>
                        <div class="col-12 col-lg-6 d-flex flex-column mb-3 mb-lg-0">
                            <label for="blogTitle">Příjmení</label>
                            <input  value="${user.LastName}" type="text" id="LastName" name="LastName" class="" required>
                        </div>
                        <div class="col-12 col-lg-9 d-flex flex-column mb-3 mb-lg-0">
                            <label for="blogTitle">E-mail</label>
                            <input  value="${user.Email}" type="text" id="Email" name="Email" class="" required>
                        </div>
                        <div class="col-12 col-lg-3 d-flex flex-column mb-3 mb-lg-0">
                        <label for="SecurityLevel_id">Role</label>
                            <select id="SecurityLevel_id" name="SecurityLevel_id" class="">
                                ${select}
                            </select>  
                        </div>
                       
                    </div>
                    
                    <div style="margin:0px 15px;">
                    
                    <div class="d-flex justify-content-end">
                    <div class="d-flex flex-row w-50">
                    <button  id="close_it" class="w-50 mt-3 mr-2 btn btn-success">Zavřít</button>
                    <button id="save_blog" class="w-50 mt-3 btn btn-success">Uložit</button>
                    </div>
                    </div>
                    
                    </div>

            </div>
        </div>
        `

        showModal(infocard);


        document.querySelector("#close_it").addEventListener("click",()=>{
            closeModal()
        })

        document.querySelector("#save_blog").addEventListener("click", async () => {
            try {
                const FirstName= document.querySelector("#FirstName").value;
                const LastName = document.querySelector("#LastName").value;
                const Email = document.querySelector("#Email").value;
                const SecurityLevel_id = document.querySelector("#SecurityLevel_id").value;
                
        
                const data = {
                    FirstName: FirstName,
                    LastName: LastName,
                    Email: Email,
                    SecurityLevel_id: SecurityLevel_id,
                    id:id
                };
                const response = await fetch("../../controllers/adminControlers/process_users.php?action=update_user", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                    },
                    body: JSON.stringify(data),
                });
                location.reload()
            } catch (error) {
                console.log(error);
            }
        });
               
    } else {
        showModal(returnHTML("Nepodařilo se načíst data" . response.error));
    }
}


async function block_user(userId) {
    
    try {
        const response = await fetch('../../controllers/adminControlers/process_users.php?action=block_user', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                userId: userId,
            }),
        });
        const data = await response.json();
        if (data.message) {
            console.log(data.message);
            location.reload()
        } else {
            console.error('Error blocking/unblocking user:', data.error);
            let string = "Nepodařilo se změnit stav uživatele" + data.error;
            let text = returnHTML(string)
            showModal(text);
        }
        
    } catch (error) {
        console.error('Error:', error);
    }
}