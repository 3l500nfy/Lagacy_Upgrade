

$(document).on("keydown", ".numeric-input", function(event){ 
    ////alert($(this).val());
    //alert(event.keyCode);
    //190 & 110 for period
    //alert($(this).attr("data-type"));
    var datatype= (($(this).attr("data-type")==null || $(this).attr("data-type")== undefined)?("numeric"):($(this).attr("data-type")));
    keycode=event.keyCode;
    var validkeys;
    if(datatype=="numeric")
        validkeys=[8,9,16, 17, 35, 36, 37, 38, 39, 40, 46, 48, 49, 50, 51, 52, 53, 54, 55, 56, 57,96,97,98,99,100,101,102,103,104,105];
    else
    if(datatype=='float')
        validkeys=[8,9,16, 17, 35, 36, 37, 38, 39, 40, 46, 48, 49, 50, 51, 52, 53, 54, 55, 56, 57,96,97,98,99,100,101,102,103,104,105, 190, 110];

    if($.inArray(keycode, validkeys)==-1)
        return false;
    return true;
});

$(document).on("copy paste", '.numeric-input', function (e) {
    e.preventDefault();
});    


$(document).on('change keyup',".numeric-input", function(event){
    var max=$(this).attr('data-max');
    var min=$(this).attr('data-min');
    var old=$(this).attr('data-old');
    var datatype= (($(this).attr("data-type")==null || $(this).attr("data-type")== undefined)?("numeric"):($(this).attr("data-type")));

    if(old==null || old == undefined)
    {
        $(this).attr('data-old', "");
    }
    
    //check pattern
    
    var pattern;
    if(datatype=='numeric')
        pattern= /^[0-9]{0,}$/i;
    if(datatype=='float')
        pattern= /^[0-9]{0,}(\.){0,1}[0-9]{0,}$/i;
             
    if(!pattern.test($.trim($(this).val())))
    {
        $(this).val($(this).attr("data-old"));
        return false;
    }
    
    //check maximum
    if(max !=null || max!=undefined)
    {
        // alert($(this).val());
        if(($(this).val()-0)>(max-0))
            $(this).val($(this).attr('data-old'));
                
    }
    
    //check minimum    
    if(min !=null || min!=undefined)
    {
        // alert($(this).val());
        if(($(this).val()-0)<(min-0)&&($(this).val()-0)!="")
            $(this).val($(this).attr('data-old'));
                
    }

    $(this).attr('data-old', $(this).val());
    return true;
    
});