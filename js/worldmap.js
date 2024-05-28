
document.addEventListener("DOMContentLoaded", async function () {
    loadmap();
});

async function loadmap() {
    let fetchurl = "controllers/adminControlers/process_blogs.php?action=get_active_destination";
    const response = await fetch(fetchurl, {
      method: "GET",
      headers: {
        "Content-Type": "application/json",
      },
    });

    if (response.ok) {
      let countryNames = await response.json();
      const worldMap = document.getElementById('worldMap');
      const tooltip = document.getElementById('tooltip');

      worldMap.addEventListener('mouseover', showCountryName);
      worldMap.addEventListener('mousemove', updateTooltipPosition);
      worldMap.addEventListener('mouseout', hideTooltip);

      function showCountryName(event) {
        const path = event.target;
        if(path.id === "tooltip"){
            console.log("tooltip");
        }
        if(path.tagName === 'path' && path.classList.contains('active_country')) {
            path.addEventListener('click',()=>{
                window.location.href = "view/Blog/blog.php?destination=" + path.id;
            })
            let countryName = "";
            countryNames.forEach(country => {
              if (country.id == path.id) {
                countryName = country.CzechName;
              }
            });
    
            tooltip.innerHTML = countryName;
            tooltip.style.display = 'block';
        }
      }

      function updateTooltipPosition(event) {
        tooltip.style.left = event.pageX + 'px';
        tooltip.style.top = event.pageY + 'px';
      }

      function hideTooltip() {
        tooltip.style.display = 'none';
      }
    }
  }
