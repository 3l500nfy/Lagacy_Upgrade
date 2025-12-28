/*
 * jQuery Growl Calc
 * Version 2.0.0
 * Last Updated 2014-02-08
 * @requires jQuery v1.11.0 or later (untested on previous version)
 *
 * Examples at: http://projects.zoulcreations.com/jquery/jquery-calc/
 * Copyright (c) 2008-2014 David Higgins
 * 
 */

jQuery(function($) {
  var digits = $('#calculator .digits .digit');
  var solve = $('#calculator .digits .digit#solve');
  var clear = $('#calculator .clr');
  var toclear = false;
  var ans = null;
  var calculator = { left: false, right: false, result: 0, operator: '+' };
  function calculate() {
	  if(calculator.operator == 'sqr'){
		  var result = Math.pow(calculator.left-0, 2); 
	  }else
	  if(calculator.operator == 'sqrt'){
		  var result = Math.sqrt(calculator.left-0); 
	  }else
	  if(calculator.operator == 'cube'){
		  var result = Math.pow(calculator.left-0, 3); 
	  }else
	  if(calculator.operator == 'cbrt'){
		  var result = Math.pow(calculator.left-0, 1/3); 
	  }else{
          console.log(calculator.left + calculator.operator + calculator.right);
	  var result = eval(calculator.left + calculator.operator + calculator.right);
	  }
	  if(!isFinite(result) || isNaN(result)){
		  result = "MATH ERROR!";
		  ans = null;
	  }else{
	  result = result.toPrecision(15);
	  if(result.lastIndexOf('.') != -1){
		  while(result.lastIndexOf('0') == result.length-1){
			  result = result.substring(0, result.length-1);
		  }
	  }
	  if(result.lastIndexOf('.') == result.length-1){
		  result = result.substring(0, result.length-1);
	  }
	  }
    calculator.result = result;
    $('#calculator .result').text(calculator.result);
	ans = calculator.result+"";
    calculator.left = false;
    calculator.right = false;
    leftBuffer = (calculator.result==null)?(''):(calculator.result+"");
    rightBuffer = '';
	toclear = true;
  }
  
  function isDigit(key) {
    var digits = "0123456789";
    var digit = false;
    if(digits.indexOf(key) != -1)
      digit = true;
    return digit;
  }
  var leftBuffer = '';
  var rightBuffer = '';

  clear.click(function() {
    leftBuffer = '';
    rightBuffer = '';
    calculator = { left: false, right: false, result: 0, operator: '+' };
    $('#calculator .result').text(calculator.result);
	toclear = false;
  });

  digits.click(function() {
	  if(calculator.result == "MATH ERROR!")
	  {
		  return;
	  }
	
    var key = $(this).text();
	var dis = $(this);
    console.log(leftBuffer, rightBuffer);
	if(key == '+/-'){
		if(rightBuffer != ''){
			if(rightBuffer.substring(0,1) != '-'){
				rightBuffer = '-'+rightBuffer;
			}else if(rightBuffer.substring(0,1) == '-'){
				rightBuffer = rightBuffer.substr(1);
			}
			$('#calculator .result').text(rightBuffer);
				if(calculator.result){
				if(calculator.result.substring(0,1) != '-'){
					calculator.result = '-'+calculator.result;
				}else if(calculator.result.substring(0,1) == '-'){
				calculator.result = calculator.result.substr(1);
				}	
			}

			
			
		}else
		if(leftBuffer !=''){
			if(leftBuffer.substring(0,1) != '-'){
				leftBuffer = '-'+leftBuffer;
			}else if(leftBuffer.substring(0,1) == '-'){
				leftBuffer = leftBuffer.substr(1);
			}			
			$('#calculator .result').text(leftBuffer);
			if(ans != null){ ans = leftBuffer; }
			
			if(calculator.result){
				if(calculator.result.substring(0,1) != '-'){
					calculator.result = '-'+calculator.result;
				}else if(calculator.result.substring(0,1) == '-'){
				calculator.result = calculator.result.substr(1);
				}	
			}
		}
				
		return;
	}
	
    if(isDigit(key)) {
		ans = null;
		if(toclear){
			ll= calculator.left;
			clear.trigger("click");
			calculator.left = ll;
			leftBuffer = (ll==false)?(''):(ll);			
			toclear = false;
		}

      if(calculator.left) {
        rightBuffer += key.toString();
        $('#calculator .result').text(rightBuffer);
      } else {
		  if(ans != null){
			  calculator.left = ans;
			  leftBuffer = ans;
			  rightBuffer += key.toString();
			  $('#calculator .result').text(rightBuffer);
			  ans = null;
		  }else{
			leftBuffer += key.toString();
			$('#calculator .result').text(leftBuffer);
		  }
      }
    } else if(key != '=' && key != '.') { //Clicked Operators +, -, x, /...
		if(leftBuffer == ''){ return; }
		if(toclear){
			ll= calculator.left;
			rr = calculator.result;
			clear.trigger("click");
			calculator.left = ll;
			leftBuffer = (ll==false)?(''):(ll);	
			calculator.result = rr;
			$('#calculator .result').text(rr);
			toclear = false;
		}

	   var op = dis.attr('data-op');
	   if(op && op != 'mult' && op != 'div'){		   
		   if(op == 'sqr'){
			   if(rightBuffer != ''){
				   rightBuffer = Math.pow(rightBuffer-0, 2);
				   rightBuffer = rightBuffer.toString();
			   }else if (leftBuffer !=''){
				   calculator.operator = 'sqr';
				   calculator.left = leftBuffer;
			   }else if($('#calculator .result').text() != '' && $('#calculator .result').text() != 0){
				   calculator.operator = 'sqr';	
					ans = null;
				   calculator.left = $.trim($('#calculator .result').text());
				   leftBuffer = $.trim($('#calculator .result').text());
			   }
				solve.trigger("click");
				//ans =null;
		   }else
		   if(op == 'cube'){
			   if(rightBuffer != ''){
				   rightBuffer = Math.pow(rightBuffer-0, 3);
				   rightBuffer = rightBuffer.toString();
			   }else if (leftBuffer !=''){
				   calculator.operator = 'cube';
				   calculator.left = leftBuffer;
			   }else if($('#calculator .result').text() != '' && $('#calculator .result').text() != 0){
				   calculator.operator = 'cube';	
					ans = null;
				   calculator.left = $.trim($('#calculator .result').text());
				   leftBuffer = $.trim($('#calculator .result').text());
			   }
				solve.trigger("click");
		   }else
		   if(op == 'sqrt'){
			   if(rightBuffer != ''){
				   rightBuffer = Math.sqrt(rightBuffer-0);
				   rightBuffer = rightBuffer.toString();
			   }else if (leftBuffer !=''){
				   calculator.operator = 'sqrt';
				   calculator.left = leftBuffer;
			   }else if($('#calculator .result').text() != '' && $('#calculator .result').text() != 0){
				   calculator.operator = 'sqrt';	
					ans = null;
				   calculator.left = $.trim($('#calculator .result').text());
				   leftBuffer = $.trim($('#calculator .result').text());
			   }
				solve.trigger("click");
		   }else
		   if(op == 'cbrt'){
			   if(rightBuffer != ''){
				   rightBuffer = Math.pow(rightBuffer-0, (1/3));
				   rightBuffer = rightBuffer.toString();
			   }else if (leftBuffer !=''){
				   calculator.operator = 'cbrt';
				   calculator.left = leftBuffer;
			   }else if($('#calculator .result').text() != '' && $('#calculator .result').text() != 0){
				   calculator.operator = 'cbrt';	
					ans = null;
				   calculator.left = $.trim($('#calculator .result').text());
				   leftBuffer = $.trim($('#calculator .result').text());
			   }
				solve.trigger("click");
		   }
		   return;
	   }
      
      switch(op) {
        case 'div': key = '/'; break;
        case 'mult': key = '*'; break;
      }
     
	  if(ans == null){              
		calculator.left = leftBuffer;
	  }else{
		  calculator.left = ans;
		  leftBuffer= ans;
		  ans = null;
	  }
    	if(leftBuffer!='' && rightBuffer!=''){
			solve.trigger('click');
			leftBuffer = ans;
			calculator.left= ans;
			ans=null;
			toclear = false;
		}
		calculator.operator = key;
	} else if(key == '.') {
		ans = null;
		if(toclear){
			ll= calculator.left;
			clear.trigger("click");
			calculator.left = ll;
			leftBuffer = (ll==false)?(''):(ll);			
			toclear = false;
		}
	  
      if(calculator.left) {
		if(rightBuffer.indexOf('.') != -1){
		  return;
		}
        rightBuffer = (rightBuffer == false)?('.'): (rightBuffer+'.');
        $('#calculator .result').text(rightBuffer);
      } else {
		if(leftBuffer.indexOf('.') != -1){
		  return;
		}
        leftBuffer =(leftBuffer == false)?('.'): (leftBuffer+'.');
        $('#calculator .result').text(leftBuffer);
      }
    } else { //Click on '='
		if(ans!= null || (rightBuffer == '' && calculator.operator != 'sqrt' && calculator.operator != 'sqr' && calculator.operator != 'cube' && calculator.operator != 'cbrt' )){
			return;
		}
      calculator.right = rightBuffer;
		console.log(calculator.left);
      if(calculator.left.substring(calculator.left.length-1, 1) == '.')
        calculator.left += '0';
	console.log(calculator.right);
      if(calculator.right.substring(calculator.right.length-1, 1) == '.')
        calculator.right += '0';
      calculate();
    }
  });
  
	$(document).on("mousedown mouseup","#calculator .digits .digit, #calculator .digits .clr, #calculator .digits .close", function(event){
		if(event.type == "mousedown"){
			$(this).addClass("mousedown");
		}else{
			$(this).removeClass("mousedown");
		}
	});
});
