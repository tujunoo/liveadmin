/*
*<form>
*    <input type="text" data-validate-required="true" data-validate-error="error text" data-validate-rule="number" />
*    <input type="text" data-validate-required="false" data-validate-rule="email" />
*</form>
*
*新增规则：
*$.validate.addRule('number3',/\d{3}/);
*新增之后，就可以在<input type="text" data-validate-rule="number3"/>得到应用了。
*$('form').miniValidate();
*
*/
(function($){
	var empty_tip = 'Cannot be empty';//decodeURI('No be');
	var pwd_equally	= decodeURI('%E4%B8%A4%E6%AC%A1%E8%BE%93%E5%85%A5%E5%AF%86%E7%A0%81%E4%B8%8D%E4%B8%80%E8%87%B4');
	
	$.fn.miniValidate = function(){		
		return this.each(function(){
			var o = $(this);
			var tag = this.tagName.toLowerCase();
			if(tag !='form'){
				return;
			}		
			//var elems = o.find(':text,:password,:file,textarea,:radio,:checkbox,select');
			$.validate.init(o);			
			o.bind('submit',function(){
				return $.validate.check(o);
			});
		});
	};

	$.validate = {
		'init':function(formObj){
			var elems = formObj.find(':text,:password,:file,textarea,:radio,:checkbox,select');			
			if(elems.length == 0){return true;}
			elems.each(function(){
				$(this).focus($.validate.cleartip);
			});			
		},
		'check': function(formObj){
			var elems = formObj.find(':text,:password,:file,textarea,:radio,:checkbox,select');			
			var validated = true;
			if(elems.length == 0){return true;}		
			elems.each(function(){
				var t = $(this);
				var type = t.attr('type');				
				/*check text,textarea*/
				if((type && (type == 'text'||type == 'password')) || t[0].tagName.toLowerCase() == 'textarea'){
					if(t.attr('data-validate-required') == 'true'){					
						if($.trim(t.val()) == ''){
							validated = false;						
							$.validate.showtip(t,t.attr('data-validate-error') || empty_tip);
							return false;
						}
					}
					if(t.attr('data-validate-rule')){
						var v = $.trim(t.val());
						if(v == '' && (t.attr('data-validate-required') == 'false' || !t.attr('data-validate-required'))){
							return;
						}
						var rule_name = t.attr('data-validate-rule');
						if(rule_name in $.validate.rules){
							if(!$.validate.rules[rule_name].test(v)){
								validated = false;
								$.validate.showtip(t,t.attr('data-validate-ruler-error'));
								return false;
							}
						}
					}
					/*密码一致性验证*/
					if(t.attr('data-validate-same')){
						var pwd2 = $(t.attr('data-validate-same'));
						var v1 = t.val();
						var v2 = pwd2.val();						
						if(v1 !='' && v2 != '' && v1 != v2){
							validated = false;
							$.validate.showtip(pwd2,pwd_equally);
							return false;
						}
					}
				}
				/*check radio,checkbox*/
				if(type && (type=='radio' || type=='checkbox')){
					if(t.attr('data-validate-required') == 'true'){
						var len = $('[name="'+t.attr('name')+'"]:checked',formObj).length;
						if(len == 0){
							validated = false;						
							$.validate.showtip(t,t.attr('data-validate-error') || empty_tip);
							return false;
						}
					}
				}
				// check select
				if(t[0].tagName.toLowerCase() == 'select'){
					if(t.attr('data-validate-required') == 'true'){
						var selected = t.find('option:first').is(':selected');
						if(selected){
							validated = false;						
							$.validate.showtip(t,t.attr('data-validate-error') || empty_tip);
							return false;
						}
					}
				}

				
			});
			if(validated){$.validate.cleartip();}
			return validated;
		},
		'showtip':function(o,str){
			var tip = $('#mini-tip-con');
			if(tip.length == 0){
				$('body').append('<div id="mini-tip"><div id="mini-tip-con" class="tip-error"></div><span id="mini-tip-arrow"></span><span id="mini-tip-close" onclick="jQuery(\'#mini-tip\').fadeOut();"></span></div>');
				tip = $('#mini-tip-con');
			}
			
			var winH = $(window).height();
			var scrollT = $(document).scrollTop();
			var t = o.offset().top + (o.outerHeight() > 0 ? o.outerHeight()+6 : 28);
			var l = o.offset().left;
			var tipbox = $('#mini-tip');
			tip.html(str)
			tipbox.css({top:t,left:l}).show();			
			if(t<scrollT || t>scrollT+winH-60){
				$('html,body').animate({scrollTop:t-32});
			}						
		},
		'cleartip':function(){
			$('#mini-tip').fadeOut();
		},
		'rules':{
			'number':/\d+/,
			'email':/^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/
		},
		'addRule':function(name,r){
			if(typeof name=='string' && r.constructor === RegExp){
				$.validate.rules[name] = r;
			}
		}
	}
})(jQuery);
