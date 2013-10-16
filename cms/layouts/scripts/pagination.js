$('.pages .gotoPage').bind('submit', function() {
    var gotoPage = parseInt($(this).find('input#gotoPage').val());

    if (isNaN(gotoPage)) {
        alert('Wartość musi być liczbą!');
        return false;
    }

    if (gotoPage == <?= $this->current; ?>) {
        alert('Aktualnie przebywasz na tej stronie...');
        return false;
    }

    var actUrl = "<?= $this->url($urlOptions = array('page'=>1)); ?>";
    if (!actUrl) {
        actUrl = document.location.href;
    }
    var newUrl = actUrl;

    var pagePattern = /page\/([0-9]+)?/i;


    if (actUrl.match(pagePattern)) {
        newUrl = actUrl.replace(pagePattern, 'page/'+gotoPage);
    } else {
        newUrl = actUrl.replace(/\/?$/, '/page/'+gotoPage);
    }

    document.location.href = newUrl;
});