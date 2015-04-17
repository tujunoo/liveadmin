(function($)
{
    $.fn.blink = function(options)
    {
       var defaults = { delay:500 };
       var options = $.extend(defaults, options);
 
        return this.each(function()
        {
            var obj = $(this);
            if (obj.attr("timerid") > 0) return;
                                
            function startglow()
            {
                if ($(obj).css("color") == "rgb(255, 0, 0)")
                {
                    $(obj).animate({color: '#06C'}, Math.round(options.delay/2));
                }
                else
                {
                    $(obj).animate({color: '#ff0000'}, Math.round(options.delay/2));
                }
            }
                                
            var timerid = setInterval(startglow, options.delay);
                                
            obj.attr("timerid", timerid);
                                
            //$(this).mouseover(function() { $(obj).animate({color: '#fff'}, Math.round(options.delay/2)); clearInterval(timerid) });
            //$(this).mouseout(function() { timerid = setInterval(startglow, options.delay); });
        });
    }
}(jQuery));