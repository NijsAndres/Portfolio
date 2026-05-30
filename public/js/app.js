const TYPE_LABELS = { school: "School", concept: "Concept", internship: "Internship" };

// #region ***  DOM references                           ***********
// #endregion

// #region ***  Callback-Visualisation - show___         ***********

// Cards are rendered server-side by Blade. Filtering just toggles their
// visibility based on the space-separated filter slugs in data-filters.
const showProjects = (filter) => {
  const cards = document.querySelectorAll(".js-project-card");
  for (let card of cards) {
    const slugs = (card.dataset.filters || "").split(" ").filter(Boolean);
    const visible = filter === "all" || slugs.includes(filter);
    // The card sits inside a .col-lg-6 wrapper; toggle that so the grid reflows.
    card.closest(".col-lg-6").hidden = !visible;
  }
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
      const selectedFilter = e.currentTarget.dataset.filter;
      showProjects(selectedFilter);
      updateActiveFilter(selectedFilter);
    });
  }
};

let lastTrigger = null;

const openProjectModal = (card) => {
  const dialog = document.querySelector("#project-modal");
  if (!card || !dialog) return;

  const { projectId, type, title, url, tags } = card.dataset;

  dialog.querySelector(".js-modal-title").textContent = title;

  const typeEl = dialog.querySelector(".js-modal-type");
  typeEl.textContent = TYPE_LABELS[type] || type;
  typeEl.className = `c-modal__typebadge c-modal__typebadge--${type} js-modal-type`;

  dialog.querySelector(".js-modal-tags").innerHTML = (tags || "")
    .split(",")
    .filter(Boolean)
    .map((t) => `<span class="c-modal__tag">${t}</span>`)
    .join("");

  const visit = dialog.querySelector(".js-modal-visit");
  if (url) {
    visit.href = url;
    visit.hidden = false;
  } else {
    visit.removeAttribute("href");
    visit.hidden = true;
  }

  const body = dialog.querySelector(".js-modal-body");
  body.innerHTML = "";
  const tpl = document.querySelector(`#project-${projectId}`);
  if (tpl && "content" in tpl) body.appendChild(tpl.content.cloneNode(true));

  document.documentElement.classList.add("u-modal-open");
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
    openProjectModal(card);
  });

  dialog.querySelector(".js-modal-close").addEventListener("click", closeProjectModal);

  dialog.addEventListener("click", (e) => {
    if (e.target === dialog) closeProjectModal();
  });

  dialog.addEventListener("close", () => {
    document.documentElement.classList.remove("u-modal-open");
    if (lastTrigger) {
      lastTrigger.focus();
      lastTrigger = null;
    }
  });
};

// #endregion

// #region ***  Init / DOMContentLoaded                  ***********

const init = () => {
  listenToNav();
  showProjects("all");
  listenToFilters();
  listenToProjects();
};

document.addEventListener("DOMContentLoaded", init);
// #endregion
