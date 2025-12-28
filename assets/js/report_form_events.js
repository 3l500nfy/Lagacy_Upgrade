

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

$('.filter-faculty-all').bind('click',function(event){
    if($(this).prop('checked')==true)
    {
        $('.filter-faculty').prop('checked', true);
        $('.filter-department-all').prop('checked',false).trigger('click');
    }
    else
    {
        $('.filter-faculty').prop('checked', false) ;
        $('.filter-department-all').prop('checked',true).trigger('click');
    }
//alert($(this).prop('checked'));
});

$('.filter-faculty').bind('click',function(event){
    $('.filter-faculty-all').prop('checked', false) ;
    var v=$(this).val();
    if($(this).prop('checked'))
        {
            $('.filter-department[data-facid='+v+']').prop('checked',false).trigger('click');
        }
        else
            {
               $('.filter-department[data-facid='+v+']').prop('checked',true).trigger('click'); 
            }
//alert($(this).prop('checked'));
});

//Event to handle department selection
$('.filter-department-all').bind('click',function(event){
    if($(this).prop('checked')==true)
    {
        $('.filter-department').prop('checked', true);
        $('.filter-programme-all').prop('checked',false).trigger('click');
        $('.filter-faculty').prop('checked',true);
    }
    else
    {
        $('.filter-department').prop('checked', false) ;
        $('.filter-programme-all').prop('checked',true).trigger('click');
        $('.filter-faculty, .filter-faculty-all').prop('checked',false);
    }
//alert($(this).prop('checked'));
});

$('.filter-department').bind('click',function(event){
    $('.filter-department-all').prop('checked', false) ;
    var v=$(this).val();
    if($(this).prop('checked'))
        {
            //alert(v);
            $('.filter-programme[data-deptid='+v+']').prop('checked',false).trigger('click');
            $('.filter-faculty[value='+v+']').prop('checked', true);
        }
        else
            {
               $('.filter-programme[data-deptid='+v+']').prop('checked',true).trigger('click'); 
            }
//alert($(this).prop('checked'));
});

//Event to handle programme selection
$('.filter-programme-all').bind('click',function(event){
    if($(this).prop('checked')==true)
    {
        $('.filter-programme').prop('checked', true);
        $('.filter-faculty, .filter-department').prop('checked',true);
    }
    else
    {
        $('.filter-programme').prop('checked', false) ;
        $('.filter-faculty, .filter-faculty-all, .filter-department-all, .filter-department').prop('checked',false);
        
    }
//alert($(this).prop('checked'));
});

$('.filter-programme').bind('click',function(event){
    $('.filter-programme-all').prop('checked', false) ;
    if($(this).prop('checked'))
        {
         $('.filter-faculty[value='+$('.filter-department[value='+v+']').prop('checked', true).attr('data-facid')+']').prop('checked', true);
        }
    
//alert($(this).prop('checked'));
});
