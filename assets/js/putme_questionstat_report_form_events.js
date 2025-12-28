
$(document).on("click", ".quests", function(event){
    $(".qchosen").removeClass("qchosen");
    $(this).addClass("qchosen");
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

$(document).on('click','#apply-filter, .quests',function(event){
    $("#report-data").html("<i>Loading...</i>");
    $.ajax({
        type:'POST',
        url:'filter_question_stat.php',
        data:{qid:$(".qchosen").attr('id'), tid:$("#testid").val(), tsubj:$("#sbjid").val()}
    }).done(function(msg){
        $("#report-data").html(msg);
    });
});

$(document).on('change','#sbjid',function(event){
    $("#fw-quest").html("Loading...");
    $("#report-data").html('');
    $.ajax({
        type:'POST',
        url:'get_questions.php',
        data:{sbjid:$(this).val(), tid:$("#testid").val()}
    }).done(function(msg){
        //alert(msg);
        $("#fw-quest").html(msg);
    });
});
