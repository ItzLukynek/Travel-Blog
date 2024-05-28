const paths = document.querySelectorAll('.ag-canvas_svg path');
const delay = 15; 

function showPathsSmoothly() {
  paths.forEach((path, index) => {
    setTimeout(() => {
      path.classList.add('show');
    }, index * delay);
  });
}
//for aninmation
function startAnimationIfVisible() {
  const mapElement = document.querySelector('.ag-canvas');
  const mapTopOffset = mapElement.getBoundingClientRect().top;
  const windowHeight = window.innerHeight;

  if (mapTopOffset < windowHeight - 150) {
    showPathsSmoothly();
    window.removeEventListener('scroll', startAnimationIfVisible);
  }
}

window.addEventListener('scroll', startAnimationIfVisible);

document.addEventListener('DOMContentLoaded',async ()=>{
  set_active_countries();
})
//for activating active countries
async function set_active_countries(){
    try {
      
      const response = await fetch("controllers/adminControlers/process_blogs.php?action=get_active_destination", {
        method: "GET",
        headers: {
            "Content-Type": "application/json",
        },
      });

        if (response.ok) {
          const destinations = await response.json();

          for (let i = 0; i < destinations.length; i++) {
              const id = "#" + destinations[i].id;
              const country = document.querySelector(id);

              if (country) {
                  country.classList.add("active_country");

                  
              } else {
                  console.log("Elemenet nenalezen" + id);
              }
          }
        } else {
            console.log("Nepodařilo se získat destinace" + response.status);
        }


    } catch (error) {
      console.log("Error: " + error)
    }
}
