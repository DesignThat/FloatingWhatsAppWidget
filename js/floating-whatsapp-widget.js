jQuery(document).ready(function($) {
    // Color Picker Integration
    $('.floating-whatsapp-widget-color-picker').wpColorPicker({
        change: function(event, ui) {
            $('#widget-preview').css('background-color', ui.color.toString());
        }
    })})