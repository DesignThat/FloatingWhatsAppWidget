jQuery(document).ready(function($) {
    // Color Picker Integration
    $('.floating-whatsapp-widget-color-picker').wpColorPicker({
        change: function(event, ui) {
            $('#widget-preview').css('background-color', ui.color.toString());
        }
    });

    // Get settings values
    var animationEnabled = '<?php echo esc_attr( get_option( 'floating_whatsapp_widget_animation' ) ); ?>';
    var scrollBehavior = '<?php echo esc_attr( get_option( 'floating_whatsapp_widget_scroll_behavior' ) ); ?>';
    var widgetEnabled = '<?php echo esc_attr( get_option( 'floating_whatsapp_widget_enabled' ) ); ?>';

    // Add animation class if enabled
    if (animationEnabled === 'true') {
        $('#floating-whatsapp-widget').addClass('animated');
    }

    // Hide on scroll functionality
    if (scrollBehavior === 'hide_on_scroll') {
        $(window).scroll(function() {
            if ($(this).scrollTop() > 100) { // Adjust the scroll distance
                $('#floating-whatsapp-widget').addClass('hidden-on-scroll');
            } else {
                $('#floating-whatsapp-widget').removeClass('hidden-on-scroll');
            }
        });
    }

    // Enable/disable widget based on setting
    if (widgetEnabled === 'false') {
        $('#floating-whatsapp-widget').hide();
    }
});
