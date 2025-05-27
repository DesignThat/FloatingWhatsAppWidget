(function($){
    $(function(){
        $('#fww_use_link').on('change', function(){
            if($(this).val() === 'phone'){
                $('.phone-input').show();
                $('.link-input').hide();
            } else {
                $('.phone-input').hide();
                $('.link-input').show();
            }
        }).trigger('change');
        $('.fww-color-field').wpColorPicker();
    });
})(jQuery);
