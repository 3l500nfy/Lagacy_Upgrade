//Start jquery validation
var validator = $("#createcredentialform").validate({
    errorClass: "help-inline",
    errorElement: "span",
    rules: {
        credentialname: "required"
    },
    messages: {
        credentialname: "Credential name is required"
    },
    highlight:function(element, errorClass, validClass)
    {
        $(element).parents('.control-group').addClass('error');
    },
    unhighlight: function(element, errorClass, validClass)
    {
        $(element).parents('.control-group').removeClass('error');
        $(element).parents('.control-group').addClass('success');
    },
    submitHandler: function() {}
});