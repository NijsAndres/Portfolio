const arrProjects = [
  {
    title: "Sinksen Kortrijk 2026",
    description: "A website that guides people through the Sinksen activities in an Amazigh theme.",
    imageUrl: "../assets/projects/sinksen-amazigh.png",
    projectUrl: "https://www.andresnijs.be/atelier2/",
    tags: ["html", "scss","js", "php"],
  },
  {
    title: "Banksy Tribute",
    description: "A tribute website to the famous street artist Banksy.",
    imageUrl: "../assets/projects/banksy.png",
    projectUrl: "https://www.andresnijs.be/banksy",
    tags: ["html", "scss"],
  },
  {
    title: "TUI Travel Redesign",
    description: "A redesign of the TUI Travel website focusing on user experience and modern design principles.",
    imageUrl: "../assets/projects/TUI-redesign.png",
    projectUrl: "https://andresnijs.be/atelier1/",
    tags: ["html", "scss", "js"],
  },
  {
    title: "UrbanGear Productpage Design",
    description: "A product page design for UrbanGear, a fictional outdoor gear brand.",
    imageUrl: "../assets/projects/urbangear-product.png",
    projectUrl: "https://www.figma.com/design/TVjQeiyLXWO8Nngji5C49o/LABO-08-Opdracht?node-id=2001-2&t=kRhPiKOopsDWyB1G-1",
    tags: ["figma", "UI"],
  },
];

// #region ***  DOM references                           ***********
// #endregion

// #region ***  Callback-Visualisation - show___         ***********

const showYear = () => {
  const year = new Date().getFullYear();
  document.querySelector(".js-year").innerHTML = year;
};

const showProjects = (filter) => {
  const HTMLProjects = document.querySelector(".js-projects");
  let output = "";
  const filteredProjects = arrProjects.filter((project) => project.tags.includes(filter) || filter === "all");
  for (let project of filteredProjects) {
    output += `<div class="col-lg-6">
                            <a href="${project.projectUrl}" target="_blank" class="c-projects__card">
                                <div class="c-projects__cardimgcontainer">
                                    <img class="c-projects__cardimg" src="${project.imageUrl}" alt="Mockup of ${project.title}">
                                    <span class="c-projects__badge">${project.tags.join(" / ")}</span>
                                </div>
                                <div class="c-projects__cardcontent">
                                    <h4 class="c-projects__cardtitle">${project.title}</h4>
                                    <p class="c-projects__cardtext">${project.description}</p>
                                </div>
                            </a>
                        </div>`;
  }
  HTMLProjects.innerHTML = output;
};

const updateActiveFilter = (selectedFilter) => {
  const filterButtons = document.querySelectorAll(".js-filter");
  for (let filter of filterButtons) {
    if (filter.dataset.filter === selectedFilter) {
      filter.classList.add("c-projects__filter--active");
    } else {
      filter.classList.remove("c-projects__filter--active");
    }
  }
};

// #endregion

// #region ***  Callback-No Visualisation - callback___  ***********
// #endregion

// #region ***  Data Access - get___                     ***********
// #endregion

// #region ***  Event Listeners - listenTo___            ***********

const listenToNav = () => {
  document.querySelector(".js-toggle").addEventListener("click", () => {
    document.querySelector(".js-nav").classList.toggle("c-nav--opened");
  });
};

const listenToFilters = () => {
  const filterButtons = document.querySelectorAll(".js-filter");
  for (let filter of filterButtons) {
    filter.addEventListener("click", (e) => {
      e.preventDefault();
      const selectedFilter = e.target.dataset.filter;
      console.log(selectedFilter);
      showProjects(selectedFilter);
      updateActiveFilter(selectedFilter);
    });
  }
};

// #endregion

// #region ***  Init / DOMContentLoaded                  ***********

const init = () => {
  console.log("DOM loaded");
  listenToNav();
  showYear();
  showProjects("all");
  listenToFilters();
};

document.addEventListener("DOMContentLoaded", init);
// #endregion
