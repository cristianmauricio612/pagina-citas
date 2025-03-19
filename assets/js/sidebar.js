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

            if (isMobile) {
                $content.css({
                    'margin-left': '0',
                    'position': 'absolute'
                });
                $('body').css('overflow', 'auto'); // Habilitar scroll
            } else {
                $content.css({
                    'margin-left': '0',
                    'position': 'relative'
                });
            }
        } else {
            // Expandir sidebar
            $sidebar.css('width', '250px');
            $footer.css('margin-left', '250px');
            $navbar.css({
                left: '250px',
                width: 'calc(100% - 250px)'
            });

            if (isMobile) {
                $content.css({
                    'margin-left': '250px',
                    'position': 'absolute'
                });
                $('body').css('overflow', 'hidden'); // Bloquear scroll
                window.scrollTo(0, 0); // Llevar la pantalla al top
            } else {
                $content.css({
                    'margin-left': '250px',
                    'position': 'relative'
                });
            }
        }
    });

    // Ajustar dinámicamente al redimensionar
    $(window).resize(function () {
        const isMobile = window.innerWidth <= 768;

        if (isMobile) {
            $sidebar.css('left', '0');
            $content.css({
                'margin-left': '0',
                'position': 'absolute'
            });
            $('body').css('overflow', 'auto'); // Habilitar scroll
        } else {
            $sidebar.css('left', '0');
            $content.css({
                'margin-left': '250px',
                'position': 'relative'
            });
            $('body').css('overflow', 'auto'); // Scroll habilitado en desktop
        }
    });
});
