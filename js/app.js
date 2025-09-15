// #region ***  DOM references                           ***********
// #endregion

// #region ***  Callback-Visualisation - show___         ***********

const showYear = () => {
  const year = new Date().getFullYear();
  document.querySelector(".js-year").innerHTML = year;
}

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

// #endregion

// #region ***  Init / DOMContentLoaded                  ***********

const init = () => {
    console.log('DOM loaded');
    listenToNav();
    showYear();
}

document.addEventListener('DOMContentLoaded',init);
// #endregion