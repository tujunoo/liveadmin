datepickerInit('.overridedatepicker');

function showDiscountItem(type) {
    var amountDiscount = $('#amount-discount');
    var percentageDiscount = $('#percentage-discount');
    if(type=='amount'){
        amountDiscount.show();
        percentageDiscount.hide();
    }else{
        amountDiscount.hide();
        percentageDiscount.show();
    }
}

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
    $('.add-exclude-date-promotion').each(function(){
        var btn = $(this);
        var input = btn.parent().find('.exclude-date-input-promotion');
        var wrap = btn.parent().find('.exclude-date-wrap-promotion');
        var v = btn.parent().find('.exclude-value-promotion');
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
});
