function CASetCookie(name, value) {
    var date = new Date();
    date.setTime(date.getTime() + 31536000000);
    var expires = "; expires=" + date.toGMTString();
    document.cookie = name+"="+value+expires+"; path=/";
}
function CAGetCookie(name) {
    var nameEQ = name + "=";
    var ca = document.cookie.split(';');
    for(var i=0; i < ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == ' ') c = c.substring(1, c.length);
        if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
    }
    return null;
}

window.onload = CACheck;

function CACheck() {
    if(CAGetCookie('CACookieName') != '1') {
        document.getElementById('CAContainer').setAttribute('style', 'display: block');
    }
}

function WHCloseCookiesWindow() {
    CASetCookie('CACookieName', '1');
    document.getElementById('CAContainer').setAttribute('style', 'display: hide');
}

function WHShowRulesWindow() {
    document.getElementById('CARules').setAttribute('style', 'display: block');
}

function WHCloseRulesWindow() {
    document.getElementById('CARules').setAttribute('style', 'display: hide');
}
