function darkMode() {
  var wasDarkMode = (localStorage.getItem('darkMode') === 'true');
  localStorage.setItem('darkMode', !wasDarkMode);
  document.body.classList.toggle('dark-mode', !wasDarkMode);
}

function darkModeLoad() {
  document.body.classList.toggle('dark-mode', localStorage.getItem('darkMode') === 'true');
}
