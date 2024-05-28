document.addEventListener("DOMContentLoaded", async function () {
    get_gallery_html("new"); //load the table first time
    search_bar()
    filter_gallery()
    document.getElementById("add_gallery").addEventListener("click", async () => {
        add_gallery();
    })
});

//filter gallery based on filter
function filter_gallery(){
    let select = document.getElementById("filterSelect")

    select.addEventListener("input",function(){
        get_gallery_html(select.value);
    })
}
function search_bar() {
    const blogSearchInput = document.getElementById("blogSearch");
    const blogListContainer = document.getElementById("blogList");

    blogSearchInput.addEventListener("input", function () {
        const searchValue = blogSearchInput.value.toLowerCase();
        const blogs = document.querySelectorAll(".manage_blog");
        let hasResults = false;

        blogs.forEach((blog) => {
            const blogTitle = blog.querySelector("p").textContent.toLowerCase();
            
            // Check if the search value is present in the gallery name
            const galleryName = blogTitle.includes(searchValue);

            if (blogTitle.includes(searchValue)) {
                blog.style.display = "flex";
                hasResults = true;
            } else {
                blog.style.display = "none";
            }
        });

        const messageElement = document.createElement("div");
        messageElement.classList.add("text-center", "w-100", "no_message", "mt-3");
        messageElement.innerHTML = hasResults ? "" : "<h1>Nenalezeny žádné výsledky</h1>";

        const existingMessage = blogListContainer.querySelector(".no_message");
        if (existingMessage) {
            blogListContainer.removeChild(existingMessage);
        }

        blogListContainer.appendChild(messageElement);
    });
}


async function add_photo(gallery_id) {
    try {
        
        const inputElement = document.createElement('input');
        inputElement.type = 'file';
        inputElement.accept = 'image/*';
        inputElement.multiple = true;

        inputElement.click();

        inputElement.addEventListener('change', async (event) => {
            const files = event.target.files;

            if (files.length === 0) {
                console.log('No images selected.');
                return;
            }

            const formData = new FormData();
            formData.append('galleryId', gallery_id);

            for (const file of files) {
                formData.append('photos[]', file);
            }
            const response = await fetch("../../controllers/adminControlers/process_galleries.php?action=add_photos_to_gallery", {
                method: "POST",
                body: formData,
            });

            const result = await response.json();
 

            
            if (result.error) {
                console.log(result.erorr);
                showModal(returnHTML("Nepodařilo se přidat fotku: " + result.error))
           
            } else {
                closeModal();
                get_gallery_html("new");
            }
        });
    } catch (error) {
        console.error('Error:', error);
    }
}

async function add_gallery(){
    let infocard = `
    <div class="ecard" >
        <div class="ecard-body" style="height:auto;">
            <div class="row flex-column flex-lg-row d-flex" >
                    <div class="col-12  d-flex flex-column mb-3 mb-lg-0">
                        <label for="blogTitle">Jméno</label>
                        <input  value="" type="text" id="FirstName" name="name" class="" required>
                      </div>
                    <div class="col-12 mt-3">
                      <label for="galleryImages">Vyberte obrázky</label>
                      <input type="file" id="galleryImages" name="galleryImages" class="" multiple required>
                  </div>
                </div>
                <div style="margin:0px 15px;">
                <div class="d-flex justify-content-end">
                <div class="d-flex flex-row w-50">
                <button  id="close_it" class="w-50 mt-3 mr-2 btn btn-success">Zavřít</button>
                <button id="save_gallery" class="w-50 mt-3 btn btn-success">Uložit</button>
                </div>
                </div>
                
                </div>

        </div>
    </div>
    `

    showModal(infocard);

    document.querySelector("#close_it").addEventListener("click",()=>{closeModal()})
    document.querySelector("#save_gallery").addEventListener("click", async () => {
        try {
            const name = document.querySelector("#FirstName").value;
           
    
            const formData = new FormData();
            formData.append("name", name); 
            
          
            const galleryPhotosInput = document.querySelector("#galleryImages");
            if (galleryPhotosInput.files.length > 0) {
                for (let file of galleryPhotosInput.files) {
                    formData.append("galleryPhotos[]", file);
                }
            }
    
            const response = await fetch("../../controllers/adminControlers/process_galleries.php?action=add_gallery", {
                method: "POST",
                body: formData,
            });

            let datas = await response.json()
            if(datas.error){
                closeModal();
                showModal(returnHTML("Nepodařilo se přidat galerii: " + datas.error))
                console.log(datas.error)
            }else{
                closeModal();
                get_gallery_html("new");
                 }
        } catch (error) {
            console.log(error);
        }
    });
}

//get gallery html
async function get_gallery_html(filter){
    try {
        const data = {
            filter: filter
        };

        const response = await fetch("../../controllers/adminControlers/process_galleries.php?action=get_galleries", {
            method: "POST",
            body: JSON.stringify(data),
            headers: {
                "Content-Type": "application/json",
            },
        });


        let gallery = [0];
        let gallery_HTML = "";
        if (response.ok) {
         gallery = await response.json();
            if (gallery.length == 0) {
                gallery_HTML = `<div class="text-center w-100"><h2>Galerie nenalezeny</h2></div>`
            }else{
            gallery.forEach((blog) => {
                let disabled = "no-visibility";
                if(blog.active){
                    disabled = "";
                }
                gallery_HTML += `
                    <div class="manage_blog row">
                        <div class="col col-lg-8 col-md-8 col-12"><p>${blog.name}</p></div>
                       
                        <div class="col col-lg-4 col-md-4 col-12 mt-md-0 mt-2 text-center">
                            <div class="table-icons d-flex flex-row justify-content-center ">
                                <button class="edit-icon mr-1"><i class="fas fa-edit"></i></button>
                                <button class="photo-icon mr-1"><i class="fas fa-images"></i></button>
                                <button class="delete-icon mr-1"><i class="fas fa-trash"></i></button>
                                <button  class="visibility-icon mr-1 ${disabled}"><i class="fas fa-eye"></i></button>
                            </div>
                        </div>
                    </div>
                `;
            });

            const manageBlogs = document.querySelector(".manage_blogs");
            manageBlogs.innerHTML = gallery_HTML;

            
            const deletes = document.querySelectorAll(".delete-icon");

            deletes.forEach((button,index) =>{
                button.addEventListener("click",()=>{
                    const id = gallery[index].id; 
                    remove_gallery(id)
                })
            })

            const visibilityes = document.querySelectorAll(".visibility-icon");

            visibilityes.forEach((button,index) =>{
                button.addEventListener("click",()=>{
                    const id = gallery[index].id; 
                    gallery_visibility(id); 
                })
            })

            const edits = document.querySelectorAll(".edit-icon");

            edits.forEach((button,index) =>{
                button.addEventListener("click",()=>{
                    const id = gallery[index].id; 
                    edit_gallery_name(id); 
                })
            })
            const photosg = document.querySelectorAll(".photo-icon");

            photosg.forEach((button,index) =>{
                button.addEventListener("click",()=>{
                    const id = gallery[index].id; 
                    edit_gallery(id); 
                })
            })

            }
        } else {
            gallery_HTML = `<div class="text-center w-100"><h1>Data nenalezena</h1></div>`
        }
        
        
    } catch (error) {
        console.log("Error: " + error);
    }
}
//remove gallery
    async function remove_gallery(id) {
        
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
                Opravdu chcete odstranit gallerii?
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
        galleryId:id
        };
        const response = await fetch("../../controllers/adminControlers/process_galleries.php?action=remove_gallery", {
        method: "POST",
        body: JSON.stringify(data),
        headers: {
        "Content-Type": "application/json",
        },
        });
        closeModal()
        get_gallery_html("new");
        let message = await response.json()
        
        if(message.error){
            showModal(returnHTML("Nepodařilo se odstranit galerii " + message.error))
            console.log(message.error)
        }else{

            get_gallery_html("new");
        }
    })
    } catch (error) {
        console.log(error);
    }
    
}
//edit gallery
async function edit_gallery_name(id) {
    const data = {
        galleryId:id,
        filter:"id"
    };

    const response = await fetch("../../controllers/adminControlers/process_galleries.php?action=get_galleries", {
        method: "POST",
        body: JSON.stringify(data),
        headers: {
            "Content-Type": "application/json",
        },
    });
    
    if (response.ok) {
        let gallery = await response.json();

        let infocard = `
        <div class="ecard"  id="write_blog">
            <div class="ecard-body" style="height:auto;">
                
                    <div class="col-12 col-lg-12 d-flex flex-column mb-3 mb-lg-0">
                        <label for="blogTitle">Jméno</label>
                        <input  value="${gallery[0].name}" type="text" id="edit_name" name="blogTitle" class="" required>
                    </div>
                    <div class="d-flex justify-content-center">
                    <div class="d-flex flex-row w-50">
                    <button  id="close_it" class="w-50 mt-3 mr-2 btn btn-success">Zavřít</button>
                    <button id="save_gallery" class="w-50 mt-3 btn btn-success">Uložit</button>
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

        document.querySelector("#save_gallery").addEventListener("click", async () => {
            try {
                const gallery_name = document.querySelector("#edit_name").value;
                const data = {
                    galleryId:id,
                    name:gallery_name
                };
                console.log(data);
                const response = await fetch("../../controllers/adminControlers/process_galleries.php?action=edit_gallery", {
                    method: "POST",
                    body: JSON.stringify(data),
                });
                let res = await response.text()
                if(res.error){
                    showModal(returnHTML("Nepodařilo se upravit galerii: " + res.error))
                    console.log(res.error)
                }else{
                    closeModal()
                    get_gallery_html("new");
                }
            } catch (error) {
                console.log(error);
            }
        });
               
    } else {
        showModal(returnHTML("Nepodařilo se načíst galerii"))
        console.log(await response.json())
    }
}



async function gallery_visibility(id){
   try {
        const data = {
            galleryId:id
        };

        const response = await fetch("../../controllers/adminControlers/process_galleries.php?action=activate_gallery", {
            method: "POST",
            body: JSON.stringify(data),
            headers: {
                "Content-Type": "application/json",
            },
        });
        let res = await response.text()
        if(res.error){
            console.log(res.error)
            showModal(returnHTML("Nepodařilo se změnit viditelnost galerie: " + res.error))
        }else{
            get_gallery_html("new");
        }
   } catch (error) {
        console.log(error);
        showModal(returnHTML("Nepodařilo se změnit viditelnost galerie"))
   }
}

async function edit_gallery(id){

    
    const gallery_id = {
        galleryId:id
    };

    const images = await fetch("../../controllers/adminControlers/process_galleries.php?action=get_gallery_photos", {
    method: "POST",
    body: JSON.stringify(gallery_id),
    headers: {
        "Content-Type": "application/json",
    },
    });

    let gallery_images = await images.json()
    let image_html = "";

    if(gallery_images[0] !== null){
        image_html += `<div class="ecard"><div class="ecard-body"><div class="image-container d-flex justify-content-center">`
        image_html += `
                <div class="image_card">
                <label for="image"></label>
                <div class="image add_one d-flex flex-row align-items-center justify-content-center ">
                
                <button class="img-add-button"><i class="fas fa-plus"></i></button>
                
                </div>
                </div>
                `
        if(gallery_images.length > 0){
            for (let index = 0; index < gallery_images.length; index++) {
                image_html += `
                <div class="image_card ">
                <label for="image">Fotka č.${index}</label>
                <div class="image ">
                <img data-enlargable src="${gallery_images[index]["url"]}" alt="Image">
                <button class="img-delete-button"><i class="fas fa-trash"></i></button>
                
                </div>
                </div>`
            }
            
        }
        image_html += "</div></div></div>"
    }
   

    showModal(image_html)

    const addphoto = document.querySelectorAll(".img-add-button")[0];
    
    addphoto.addEventListener("click",()=>{
        add_photo(id);
    })

    const deletes = document.querySelectorAll(".img-delete-button");

    deletes.forEach((button,index) =>{
        button.addEventListener("click",()=>{
            const id = gallery_images[index]["id"]; 
            delete_photo(id); 
        })
    })

}


async function delete_photo(id){
    try {
        const data = {
            photoId:id
        };
    
        const images = await fetch("../../controllers/adminControlers/process_galleries.php?action=remove_photo", {
        method: "POST",
        body: JSON.stringify(data),
        headers: {
            "Content-Type": "application/json",
        },
        });

        let response = await images.text()
      
        if(images.error){
            showModal(returnHTML("Nepodařilo se odstranit fotku: " + images.error ))
            console.log(images.error)
        
        }else{
            closeModal()
            get_gallery_html("new");
        }
    } catch (error) {
        console.log(error)
        showModal(returnHTML("Nepodařilo se odstranit fotku"))
    }
}

function formatDateToCzech(dateString) {
    const options = {
        year: "numeric",
        month: "long",
        day: "numeric",
        hour: "numeric",
        minute: "numeric",
        second: "numeric",
    };
    return new Date(dateString).toLocaleDateString("cs-CZ", options);
}

function limitText(text, maxLength) {
    if (text.length > maxLength) {
        return text.slice(0, maxLength) + '...';
    }
    return text;
}

//for create a html for message
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


function add_fullscreen(){
    $('img[data-enlargable]').addClass('img-enlargable').click(function () {
        var src = $(this).attr('src');
        var fullscreenDiv = $('<div>').css({
        background:'RGBA(0,0,0,.5)',
          width: '100%',
          height: '100%',
          position: 'fixed',
          zIndex: '10000',
          top: '0',
          left: '0',
          cursor: 'zoom-out',
          display: 'flex',
          alignItems: 'flex-start', 
          justifyContent: 'center', 
          paddingBottom: '50px',
          paddingTop: '50px' 
        }).addClass('fullscreen-wrapper');
    
        var imageDiv = $('<div>').css({
          background: ' url(' + src + ') no-repeat center',
          backgroundSize: 'contain',
          width: '100%',
          height: '100%',
          borderRadius:'0px'
        });
    
        fullscreenDiv.click(function () {
          $(this).remove();
          $('body').removeClass('fullscreen-enabled');
        });
    
        fullscreenDiv.append(imageDiv);
        fullscreenDiv.appendTo('body');
    
        $('body').addClass('fullscreen-enabled');
      });
}