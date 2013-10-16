$(document).ready(function() {
    
    $('a[data-target="_blank"]').attr('target', '_blank');
    
    $('#topSearch .icon-search').click(function () {
        $('#topSearch').submit();
    });
    $('#footerSearch .icon-search').click(function () {
        $('#footerSearch').submit();
    });
});