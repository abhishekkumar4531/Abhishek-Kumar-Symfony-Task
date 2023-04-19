/**
 * When user click on dark mode button it will first check is dark mode status
 * with the help of loac storage of browser.
 * Then update the dark mode status in local storage.
 * And also toggle the dark-mode css class
 *
 */
function darkMode() {
  var wasDarkMode = (localStorage.getItem('darkMode') === 'true');
  localStorage.setItem('darkMode', !wasDarkMode);
  document.body.classList.toggle('dark-mode', !wasDarkMode);
}

/**
 * It will be update the dark mode satatus on every page load.
 */
function darkModeLoad() {
  document.body.classList.toggle('dark-mode', localStorage.getItem('darkMode') === 'true');
}
