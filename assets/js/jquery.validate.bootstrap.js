/**
 * jQuery Validate, with settings updated to use Bootstrap classes for showing errors.
 * Tested with jQuery Validate 1.9.0 and Bootstrap 2.0
 *
 * Usage: $("#myform").validate_bootstrap();
 *
 * @see http://docs.jquery.com/Plugins/Validation
 * @author Josh Justice <josh.justice@northpoint.org>
 * @version 1.0
 */
(function($) {
	$.fn.validate_bootstrap = function( options ) {
		var options = $.extend( options, {
			// change the highlight function to highlight the proper parent object
	        highlight: function (element, errorClass, validClas) { 
	                $(element).parents("div[class='control-group']").addClass("error"); 
	        }, 
	
			// change the unhilight function to unhighlight the proper parent object
	        unhighlight: function (element, errorClass, validClass) { 
	                $(element).parents(".error").removeClass("error"); 
	        }, 
	
			// change the error description label to a span
	        errorElement: 'span',
	
			// change the error description span class
			errorClass: 'help-inline',
		});
		this.validate(options);
	}
})(jQuery);
