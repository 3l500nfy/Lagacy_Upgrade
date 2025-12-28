$(function() {
    $(".chzn-select").chosen();
    $(".chzn-select-deselect").chosen({
        allow_single_deselect:true
    });
     
    
    $('#logo-image').on("mouseenter", function(){
    
          $(this).addClass("animated swing");
    });
    
    $('#logo-image').on("mouseleave", function(){
        $(this).removeClass("animated swing").delay(800);
    });
    /*
    $.datepicker.regional['en-GB'] = {
        closeText: 'Done',
        prevText: 'Prev',
        nextText: 'Next',
        currentText: 'Today',
        monthNames: ['January', 'February', 'March', 'April', 'May', 'June',
        'July', 'August', 'September', 'October', 'November', 'December'],
        monthNamesShort: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun',
        'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
        dayNames: ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'],
        dayNamesShort: ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
        dayNamesMin: ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'],
        weekHeader: 'Wk',
        dateFormat: 'dd/mm/yy',
        firstDay: 1,
        isRTL: false,
        showMonthAfterYear: false,
        yearSuffix: ''
    };
    
    $.datepicker.setDefaults($.datepicker.regional['en-GB']);
    */
    Date.format = 'dd/mm/yy';
/*
    $(".date").datepicker({
        changeMonth: true,
        changeYear: true,
        currentText: 'Today',
        duration: 'fast',
        showStatus: true,
        maxDate: new Date(2030, 1 - 1, 1),
        minDate: new Date(1940, 1 - 1, 1),
        showAnim: 'drop',
        showButtonPanel: false,
        yearRange: '1900:2030',
        dateFormat: 'dd/mm/yy',
        altFormat: "dd/mm/yy"
    });
    */
});