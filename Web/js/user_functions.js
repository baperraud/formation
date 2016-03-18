function news_isOnScreen(object) {
    var element = object.get(0);
    var bounds = element.getBoundingClientRect();
    return bounds.top < window.innerHeight && bounds.bottom > 0;
}

function removeFlash() {
    var $flash = $('#flash_message');
    if ($flash.length) $flash.remove();
}