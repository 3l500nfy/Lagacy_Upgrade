

//Event to handle subject selection
$('.filter-subject-all').bind('click',function(event){
    if($(this).prop('checked')==true)
    {
        $('.filter-subject').prop('checked', true) ;
    }
    else
    {
        $('.filter-subject').prop('checked', false) ;
    }
//alert($(this).prop('checked'));
});

$('.filter-subject').bind('click',function(event){
    $('.filter-subject-all').prop('checked', false) ;
//alert($(this).prop('checked'));
});


//Event to handle state selection
$('.filter-state-all').bind('click',function(event){
    if($(this).prop('checked')==true)
    {
        $('.filter-state').prop('checked', true) ;
    }
    else
    {
        $('.filter-state').prop('checked', false) ;
    }
//alert($(this).prop('checked'));
});

$('.filter-state').bind('click',function(event){
    $('.filter-state-all').prop('checked', false) ;
//alert($(this).prop('checked'));
});


//Event to handle state selection
$('.filter-field-all').bind('click',function(event){
    if($(this).prop('checked')==true)
    {
        $('.filter-field').prop('checked', true) ;
    }
    else
    {
        $('.filter-field').prop('checked', false) ;
    }
//alert($(this).prop('checked'));
});

$('.filter-field').bind('click',function(event){
    $('.filter-field-all').prop('checked', false) ;
//alert($(this).prop('checked'));
});

//event to handle Faculty selection

$(document).on('click', '#filter-faculty-all', function(event){
    if($(this).prop('checked')==true)
    {
        
        $("#fw-prog").html();
        $('.filter-faculty').prop('checked', true);
        //alert($("#filter-frm").serializeArray())
        $.ajax({
            type:'POST',
            async:false,
            url:'../../func/get_parameterized_departments.php',
            error:function(e){
                alert("error");
            },
            data:$("#filter-frm").serialize() 
        }).done(function(msg){//alert(msg);
            $("#fw-dept").html(msg);
        });
    //$('.filter-dept-all').prop('checked',false).trigger('click');
    }
    else
    {
        $('.filter-faculty').prop('checked', false) ;
        $('#fw-dept, #fw-prog').html("");
    }
//alert($(this).prop('checked'));
});

$(document).on('click', '.filter-faculty', function(event){
    $('.filter-faculty-all').prop('checked', false) ;
    var v=$(this).val();
    if($(this).prop('checked'))
    {
        $.ajax({
            type:'POST',
            async:false,
            url:'../../func/get_parameterized_departments.php',
            error:function(e){
                alert("error");
            },
            data:$("#filter-frm").serialize() 
        }).done(function(msg){//alert(msg);
            $("#fw-dept").html(msg);
        });
    }
    else
    {
        p=$('.filter-dept[data-facid='+v+']').val();
        $('.filter-dept[data-facid='+v+']').parent().remove(); 
        $('.filter-prog[data-deptid='+p+']').parent().remove();
        $('.filter-dept-all, .filter-prog-all').prop('checked',false); 
    }
//alert($(this).prop('checked'));
});

//Event to handle department selection
$(document).on('click','.filter-dept-all',function(event){
    if($(this).prop('checked')==true)
    {
        $('.filter-dept').prop('checked', true);
        
        //alert($("#filter-frm").serializeArray())
        $.ajax({
            type:'POST',
            async:false,
            url:'../../func/get_parameterized_programmes.php',
            error:function(e){
                alert("error");
            },
            data:$("#filter-frm").serialize() 
        }).done(function(msg){//alert(msg);
            $("#fw-prog").html(msg);
        });

    }
    else
    {
        $('.filter-dept').prop('checked', false) ;
        $('#fw-prog').html("");
    }
//alert($(this).prop('checked'));
});

$(document).on('click', '.filter-dept',function(event){
    $('.filter-dept-all').prop('checked', false) ;
    var v=$(this).val();
    if($(this).prop('checked'))
    {
        //alert($("#filter-frm").serializeArray())
        $.ajax({
            type:'POST',
            async:false,
            url:'../../func/get_parameterized_programmes.php',
            error:function(e){
                alert("error");
            },
            data:$("#filter-frm").serialize() 
        }).done(function(msg){//alert(msg);
            $("#fw-prog").html(msg);
        });
    }
    else
    {
        $('.filter-prog[data-deptid='+v+']').parent().remove(); 
        $('.filter-prog-all').prop('checked',false); 
    }
//alert($(this).prop('checked'));
});

//Event to handle programme selection
$(document).on('click', '.filter-prog-all', function(event){
    if($(this).prop('checked')==true)
    {
        $('.filter-prog').prop('checked', true);
    }
    else
    {
        $('.filter-prog').prop('checked', false) ;
    }
//alert($(this).prop('checked'));
});

$(document).on('click', '.filter-prog', function(event){
    $('.filter-prog-all').prop('checked', false) ;
//alert($(this).prop('checked'));
});

$(document).on('click','.filter-field', function(event){
    var v;
    if($(this).prop('checked')==true)
        {
            if($(this).val()=='all')
                {
                    $('.filter-field').prop('checked',true);
                    $('th, td').removeClass("filtered");
                }
                else
                    {
                         v=$(this).attr('data-tohide');
                        $('.'+v).removeClass("filtered");
                    }
        }
        else
            {
            if($(this).val()=='all')
                {
                    $('.filter-field').prop('checked',false);
                    $('.f3, .f4, .f5, .f6, .f7').addClass("filtered");
                }
                else
                    {
                         v=$(this).attr('data-tohide');
                        $('.'+v).addClass("filtered");
                    }
            }
    
});


$(document).on('click','#apply-filter',function(event){
    $.ajax({
        type:'POST',
        url:'filter_summary.php',
        data:$("#filter-frm").serialize()
    }).done(function(msg){ //alert(msg)
        $("#report-data").html(msg);
    });
});


$(document).on('click','#print-ctr',function(event){
    $.ajax({
        type:'POST',
        url:'filter_summary_print.php',
        data:$("#filter-frm").serialize()
    }).done(function(msg){
        var w=window.open();
        $(w.document.body).html(msg);
    });
});
