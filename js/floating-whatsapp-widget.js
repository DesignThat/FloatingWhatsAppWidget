jQuery(document).ready(function($) {
    // Color Picker Integration
    $('.floating-whatsapp-widget-color-picker').wpColorPicker({
        change: function(event, ui) {
            // Update the widget preview style directly
            $('#widget-preview').css('background-color', ui.color.toString());
        }
    });

});
