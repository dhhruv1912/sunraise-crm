$(function () {
    $.get("https://ipinfo.io", function(response) {
        location_obj = {
            city    :response.city,
            country :response.country,
            ip      :response.ip,
            loc     :response.loc,
            org     :response.org,
            postal  :response.postal,
            region  :response.region,
        }
        document.cookie = 'location='+JSON.stringify(location_obj)+'; expires=' + new Date(new Date().getTime() + 1 * 24 * 60 * 60 * 1000).toUTCString(); // expires after 7 days
    }, "json");
    var userAgent = navigator.userAgent;
    deviceInfo = {};
    if (userAgent.match(/Windows/i)) {
        deviceInfo["os"] = "Windows";
    } else if (userAgent.match(/Macintosh/i)) {
        deviceInfo["os"] = "macOS";
    } else if (userAgent.match(/Android/i)) {
        deviceInfo["os"] = "Android";
    } else if (userAgent.match(/iPhone|iPad|iPod/i)) {
        deviceInfo["os"] = "iOS";
    } else {
        deviceInfo["os"] = "Unknown";
    }
    if (userAgent.match(/Mobi/i) || userAgent.match(/Android/i) || userAgent.match(/iPhone|iPad|iPod/i)) {
        deviceInfo["Device"] = "Mobile";
    } else {
        deviceInfo["Device"] = "Desktop";
    }
    document.cookie = 'device='+JSON.stringify(deviceInfo)+'; expires=' + new Date(new Date().getTime() + 1 * 24 * 60 * 60 * 1000).toUTCString(); // expires after 7 days
});
function isCookieSet(cookieName) {
    var cookies = document.cookie.split(';').map(cookie => cookie.trim());
    for (var i = 0; i < cookies.length; i++) {
        if (cookies[i].startsWith(cookieName + '=')) {
            return true;
        }
    }
    return false;
}
