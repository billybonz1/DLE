$(document).ready(function(){
    $('#alex_expand_category_select').click(function(){
        //$('#category_select').css('height','700px').addClass;    
        $('#category_select').toggleClass('alex_tall_select');
        $(this).toggleClass('alex_active');
        if($(this).hasClass('alex_active') == true){$(this).html('Свернуть');}
        if($(this).hasClass('alex_active') == false){$(this).html('Развернуть');}
            
    });         
});
