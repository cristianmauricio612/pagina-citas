$(document).ready(function () {
    const $sidebar = $('#sidebar');
    const $footer = $('#footer-container');
    const $navbar = $('#navbar');
    const $content = $('.content');

    // Loader
    setTimeout(() => {
        $('#loader').fadeOut();
    }, 1000);

    // Sidebar toggle
    $('#toggleSidebar').click(function () {
        const isMobile = window.innerWidth <= 768;

        if ($sidebar.width() > 0) {
            // Contraer sidebar
            $sidebar.css('width', '0');
            $footer.css('margin-left', '0');
            $navbar.css({
                left: '0',
                width: '100%'
            });
            $content.css({
                'margin-left': '0',
            });
        } else {
            // Expandir sidebar
            $sidebar.css('width', '250px');
            if (!isMobile) {
                $footer.css('margin-left', '250px');
                $navbar.css({
                    left: '250px',
                    width: 'calc(100% - 250px)'
                });
                $content.css({
                    'margin-left': '250px',
                });
            } else {
                $footer.css('margin-left', '0');
                $navbar.css({
                    left: '250px',
                    width: 'calc(100% - 250px)'
                });
                $content.css({
                    'margin-left': '0',
                });
            }
        }
    });

    // Ajustar dinámicamente al redimensionar
    $(window).resize(function () {
        if (window.innerWidth <= 1200) {
            $sidebar.css('width', '0');
            $footer.css('margin-left', '0');
            $navbar.css({
                left: '0',
                width: '100%'
            });
            $content.css({
                'margin-left': '0',
            });
        } 
    });
});
