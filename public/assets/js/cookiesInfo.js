function allowCookies() {
  //var cookie = (localStorage.getItem('cookiesStatus') === 'true');
  localStorage.setItem('cookiesStatus', 'true');
  loadCookieStatus();
}

function denyCookies() {
  localStorage.setItem('denyCookies', 'true');
  localStorage.setItem('cookiesStatus', 'false');
  loadCookieStatus();
}

function loadCookieStatus() {
  var cookie = localStorage.getItem('cookiesStatus');
  var denyCookie = localStorage.getItem('denyCookies');
  if(cookie === 'true') {
    document.getElementById("cookies-policy-page").style.display = "none";
  }
  else  {
    if(denyCookie === 'true') {
      document.getElementById("cookies-policy-page").style.display = "none";
    }
  }
}

function checkedCookie() {
  var cookiesStatus = document.getElementById("cookieStatus");
  if(cookiesStatus.checked) {
    localStorage.setItem('cookiesStatus', 'true');
    loadCookieStatus();
  }
  else {
    localStorage.setItem('cookiesStatus', 'false');
    loadCookieStatus();
  }
}
