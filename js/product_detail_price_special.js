datepickerInit('.overridedatepicker');

Array.prototype.indexOf = function(val) {
	for (var i = 0; i < this.length; i++) {
		if (this[i] == val) return i;
	}
	return -1;
};
Array.prototype.remove = function(val) {
	var index = this.indexOf(val);
	if (index > -1) {
		this.splice(index, 1);
	}
};
$(function(){
    $('.date-time').datetimepicker({
        dateFormat:'yy-mm-dd',
        showSecond: true,
        timeFormat: 'hh:mm:ss',
        stepHour: 1,
        stepMinute: 10,
        stepSecond: 10
    });
	$('.add-exclude-date').each(function(){
		var btn = $(this);
		var input = btn.parent().find('.exclude-date-input');
		var wrap = btn.parent().find('.exclude-date-wrap');
		var v = btn.parent().find('.exclude-value');
		var v_arr = (v.val() == '') ? [] : v.val().split(',');
		btn.click(function(){
			var val = input.val();
			if(val == ''){
				return false;
			}
			if($.inArray(val,v_arr) > -1){
				alert('Date already exists.');
				return false;
			}
			wrap.append('<span><em>' + val + '</em><a href="javascript:;">Delete</a></span>');
			v_arr.push(val);
			wrap.find('a:last').click(function(){
				v_arr.remove(val);
				$(this).parent().remove();
				v.val(v_arr.join(','));
				if(v_arr.length == 0){
					wrap.hide();
				}
			});
			wrap.show('fast');
			v.val(v_arr.join(','));
		});

		//init remove button event
		wrap.find('a').each(function(){
			var me = $(this);
			var val = me.parent().find('em').text();
			me.click(function(){
				v_arr.remove(val);
				me.parent().remove();
				v.val(v_arr.join(','));
				if(v_arr.length == 0){
					wrap.hide();
				}
			});
		});
	});

	//special type change
	$('.special-type-select').each(function(){
		var me = $(this);
		var parent_row = me.closest('tr');
		var id = parent_row.attr('data-special-id');
		var logic_row = parent_row.closest('tbody').find('tr[data-special-id='+id+']');
		var percentage = parent_row.find('.special-type-percentage');
		var prices = logic_row.find('.special-type-price');
        var seckill_input = $('.seckill-type')
		var checkSelectValue = function(o){
			var v = o.val();
			if(v == '3'){
				percentage.removeAttr('disabled');
				prices.attr('disabled','disabled');
			}else{
				percentage.attr('disabled','disabled');
				prices.removeAttr('disabled');
			}
            if(v == '4') {
               seckill_input.removeAttr('disabled');
            } else {
                seckill_input.attr('disabled','disabled');
            }
		};
		me.change(function(){
			checkSelectValue(me);
		});
		checkSelectValue(me);
	});
});