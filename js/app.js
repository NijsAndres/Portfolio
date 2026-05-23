const arrProjects = [
  {
    id: "sinksen-kortrijk-2026",
    type: "school",
    title: "Sinksen Kortrijk 2026",
    description: "A website that guides people through the Sinksen activities in an Amazigh theme.",
    imageUrl: "../assets/projects/sinksen-amazigh.webp",
    projectUrl: "https://www.andresnijs.be/atelier2/",
    tags: ["html", "scss","js", "php"],
  },
  {
    id: "banksy-tribute",
    type: "concept",
    title: "Banksy Tribute",
    description: "A tribute website to the famous street artist Banksy.",
    imageUrl: "../assets/projects/banksy.webp",
    projectUrl: "https://www.andresnijs.be/banksy",
    tags: ["html", "scss"],
  },
  {
    id: "tui-travel-redesign",
    type: "school",
    title: "TUI Travel Redesign",
    description: "A redesign of the TUI Travel website focusing on user experience and modern design principles.",
    imageUrl: "../assets/projects/TUI-redesign.webp",
    projectUrl: "https://andresnijs.be/atelier1/",
    tags: ["html", "scss", "js"],
  },
  {
    id: "urbangear-productpage",
    type: "concept",
    title: "UrbanGear Productpage Design",
    description: "A product page design for UrbanGear, a fictional outdoor gear brand.",
    imageUrl: "../assets/projects/urbangear-product.webp",
    projectUrl: "https://www.figma.com/design/TVjQeiyLXWO8Nngji5C49o/LABO-08-Opdracht?node-id=2001-2&t=kRhPiKOopsDWyB1G-1",
    tags: ["figma", "UI"],
  },
];

const TYPE_LABELS = { school: "School", concept: "Concept", internship: "Internship" };

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
                            <button type="button" class="c-projects__card js-project-card" data-project-id="${project.id}" aria-haspopup="dialog" aria-controls="project-modal">
                                <div class="c-projects__cardimgcontainer">
                                    <img class="c-projects__cardimg" src="${project.imageUrl}" alt="Mockup of ${project.title}" width="1920" height="1079" loading="lazy">
                                    <span class="c-projects__badge">${project.tags.join(" / ")}</span>
                                </div>
                                <div class="c-projects__cardcontent">
                                    <p class="c-projects__cardtitle">${project.title}</p>
                                    <p class="c-projects__cardtext">${project.description}</p>
                                </div>
                            </button>
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

let lastTrigger = null;

const openProjectModal = (id) => {
  const project = arrProjects.find((p) => p.id === id);
  const dialog = document.querySelector("#project-modal");
  if (!project || !dialog) return;

  dialog.querySelector(".js-modal-title").textContent = project.title;

  const typeEl = dialog.querySelector(".js-modal-type");
  typeEl.textContent = TYPE_LABELS[project.type];
  typeEl.className = `c-modal__typebadge c-modal__typebadge--${project.type} js-modal-type`;

  dialog.querySelector(".js-modal-tags").innerHTML = project.tags
    .map((t) => `<span class="c-modal__tag">${t}</span>`)
    .join("");

  dialog.querySelector(".js-modal-visit").href = project.projectUrl;

  const body = dialog.querySelector(".js-modal-body");
  body.innerHTML = "";
  const tpl = document.querySelector(`#project-${id}`);
  if (tpl && "content" in tpl) body.appendChild(tpl.content.cloneNode(true));

  document.body.classList.add("u-modal-open");
  dialog.showModal();
  body.scrollTop = 0;
};

const closeProjectModal = () => {
  const dialog = document.querySelector("#project-modal");
  if (!dialog || !dialog.open) return;
  dialog.close();
};

const listenToProjects = () => {
  const grid = document.querySelector(".js-projects");
  const dialog = document.querySelector("#project-modal");
  if (!grid || !dialog) return;

  grid.addEventListener("click", (e) => {
    const card = e.target.closest(".js-project-card");
    if (!card) return;
    lastTrigger = card;
    openProjectModal(card.dataset.projectId);
  });

  dialog.querySelector(".js-modal-close").addEventListener("click", closeProjectModal);

  dialog.addEventListener("click", (e) => {
    if (e.target === dialog) closeProjectModal();
  });

  dialog.addEventListener("close", () => {
    document.body.classList.remove("u-modal-open");
    if (lastTrigger) {
      lastTrigger.focus();
      lastTrigger = null;
    }
  });
};

// #endregion

// #region ***  Init / DOMContentLoaded                  ***********

const init = () => {
  console.log("DOM loaded");
  listenToNav();
  showYear();
  showProjects("all");
  listenToFilters();
  listenToProjects();
};

document.addEventListener("DOMContentLoaded", init);
// #endregion
