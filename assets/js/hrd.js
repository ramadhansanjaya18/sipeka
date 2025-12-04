$(document).ready(function() {
    $('.mobile-menu-toggle').click(function() {
        $('.sidebar').toggleClass('active');
    });

    $('.sidebar-overlay').click(function() {
        $('.sidebar').removeClass('active');
    });

    // --- Alert/Message Handling ---
    const $alerts = $('.message.animated');

    if ($alerts.length) {
        // Auto-hide after 3.5 seconds for better user experience
        const timeout = setTimeout(() => {
            $alerts.addClass('hide');
        }, 3000);

        // Remove from DOM after transition
        $alerts.on('transitionend', function(e) {
            // Ensure we are targeting the correct event
            if (e.originalEvent.propertyName === 'opacity' && $(this).hasClass('hide')) {
                $(this).remove();
                clearTimeout(timeout); // Clear timeout if closed manually
            }
        });

        // Handle manual close
        $alerts.find('.close-btn').on('click', function() {
            // When the close button is clicked, hide its parent message
            $(this).closest('.message').addClass('hide');
        });
    }
});
