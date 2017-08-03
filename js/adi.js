jQuery(document).ready(function($) {
    $('#googleOAuth').click(function(e) {
        e.preventDefault();
        PopupCenter(this.href, 'Login With Google | Aditya Hajare', '800', '600');
    });
    $('#facebookOAuth').click(function(e) {
        e.preventDefault();
        PopupCenter(this.href, 'Login With Facebook | Aditya Hajare', '800', '600');
    });
    $('#twitterOAuth').click(function(e) {
        e.preventDefault();
        PopupCenter(this.href, 'Login With Twitter | Aditya Hajare', '800', '600');
    });
    $('#linkedinOAuth').click(function(e) {
        e.preventDefault();
        PopupCenter(this.href, 'Login With Linkedin | Aditya Hajare', '800', '600');
    });
    $('#microsoftOAuth').click(function(e) {
        e.preventDefault();
        PopupCenter(this.href, 'Login With Microsoft | Aditya Hajare', '800', '600');
    });
    $('#yahooOAuth').click(function(e) {
        e.preventDefault();
        PopupCenter(this.href, 'Login With Yahoo | Aditya Hajare', '800', '600');
    });
});

function PopupCenter(url, title, w, h) {
    // Fixes dual-screen position Most browsers      Firefox
    var dualScreenLeft = window.screenLeft != undefined ? window.screenLeft : screen.left;
    var dualScreenTop = window.screenTop != undefined ? window.screenTop : screen.top;

    var width = window.innerWidth ? window.innerWidth : document.documentElement.clientWidth ? document.documentElement.clientWidth : screen.width;
    var height = window.innerHeight ? window.innerHeight : document.documentElement.clientHeight ? document.documentElement.clientHeight : screen.height;

    var left = ((width / 2) - (w / 2)) + dualScreenLeft;
    var top = ((height / 2) - (h / 2)) + dualScreenTop;
    var newWindow = window.open(url, title, 'toolbar=yes, location=no, directories=no, status=no, menubar=yes, scrollbars=yes, resizable=yes, copyhistory=no, width=' + w + ', height=' + h + ', top=' + top + ', left=' + left);

    // Puts focus on the newWindow
    if (window.focus) {
        newWindow.focus();
    }
}