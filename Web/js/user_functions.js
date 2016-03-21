function news_isOnScreen(object) {
    var element = object.get(0);
    var bounds = element.getBoundingClientRect();
    return bounds.top < window.innerHeight && bounds.bottom > 0;
}

function removeFlash() {
    var $flash = $('#flash_message');
    if ($flash.length) $flash.remove();
}

function centerViewportToElem(elem, speed = 500) {
    var viewportHeight = $(window).height(),
        elHeight = elem.height(),
        elOffset = elem.offset();
    $('html, body').animate({scrollTop: (elOffset.top + (elHeight / 2) - (viewportHeight / 2) )}, speed);
}