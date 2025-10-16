const searchInput = document.getElementById("searchInput");

searchInput.addEventListener("focus", () => {
  searchInput.setAttribute("placeholder", "");
});

searchInput.addEventListener("blur", () => {
  if (!searchInput.value) {
    searchInput.setAttribute("placeholder", "Rechercher un logement...");
  }
});


const footer = document.getElementById("mainFooter");

const footerObserver = new IntersectionObserver((entries) => {
  entries.forEach(entry => {
    if (entry.isIntersecting) footer.classList.add("footerVisible");
  });
});

footerObserver.observe(footer);


let lastScroll = 0;
const header = document.getElementById("mainHeader");

window.addEventListener("scroll", () => {
  const currentScroll = window.pageYOffset;

  if (currentScroll > lastScroll && currentScroll > 100) {
    header.style.transform = "translateY(-100%)";
  } else {
    header.style.transform = "translateY(0)";
  }

  lastScroll = currentScroll;
});
