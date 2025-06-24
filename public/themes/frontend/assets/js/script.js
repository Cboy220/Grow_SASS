(function() {
    'use strict';
    jQuery(document).ready(function() {
        $('.heading_menu').meanmenu({
            meanMenuContainer: '.heading_mobile_thum',
            meanScreenWidth: '767',
            meanRevealPosition: 'right'
        });
    });
})();