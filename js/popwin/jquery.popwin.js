 /*jquery plugin : popwin*/
 (function($) {
     $.popwin = null;
     var speed = 400;
     var isie = $.browser.msie;
     var ie6 = isie && ($.browser.version == "6.0") && !$.support.style;
     var lowIE = isie && (parseInt($.browser.version) < 9);
     var w, h, bg, wrap, conObj, conhtml, box, sel_for_ie6, hideCover, args, animate, mode;
     $.popwin = {
         'show': function(opt) {
             if ($.popwin.visible) {
                 $.popwin.close();
             }
             var o = args = opt || {};
             var u = o.url || '';
             var con = o.content || '';
             var aj = o.ajax;
             var hideClose = o.hideClose || 'no';
             var hideScroll = o.hideScroll || 'no';
             var header = o.header || '';
             var closeBtn;
             if (args.showBefore) {
                 args.showBefore();
             }
             mode = o.mode || 'cut';
             hideCover = o.hideCover || 'no';
             animate = o.animate || 'no';
             conObj = $(con).length > 0 ? $(con) : false;
             w = o.width || (conObj ? conObj.width() : 500);
             h = o.height || (conObj ? conObj.height() : 400);
             if ($('#popwin').length == 0) {
                 $('body').append('<div id="popwin"><table id="popwin-table" cellspacing="0" cellpadding="0" border="0"><tr><td class="pop-tl"></td><td class="pop-m"></td><td class="pop-tr"></td></tr><tr><td class="pop-m"></td><td><div id="popwin-con-outter"><div id="popwin-con"></div></div></td><td class="pop-m"></td></tr><tr><td class="pop-bl"></td><td class="pop-m"></td><td class="pop-br"></td></tr></table></div>');
                 if (hideCover == 'no') {
                     $('body').append('<div id="popwin-bg"></div>');
                 }
             }
             bg = $('#popwin-bg') || null;

             wrap = $('#popwin');
             box = $('#popwin-con');
             boxOutter = $('#popwin-con-outter');
             if ($('#popwin-close').length == 0) {
                 boxOutter.before('<div id="popwin-close"></div>');
                 $('#popwin-close').click(function() {
                     $.popwin.close();
                 });
             }
             // add title bar
             if (header != '') {
                 if ($('#popwin-header').length == 0)
                     boxOutter.before('<div id="popwin-header"><div id="popwin-header-inner">' + header + '</div></div>');
                 else
                     $('#popwin-header-inner').text(header);


                 if ($('#popwin-mini-rest').length == 0)
                     boxOutter.before('<div id="popwin-mini-rest" class="popwin-minimize"></div>');

                 $('#popwin-mini-rest').unbind('click').click(function() {
                     if ($(this).hasClass('popwin-minimize')) {
                         $(this).removeClass('popwin-minimize').addClass('popwin-restore');
                         $('#popwin-con-outter').hide();
                     } else {
                         $(this).removeClass('popwin-restore').addClass('popwin-minimize');
                         $('#popwin-con-outter').show();
                     }
                 });

                 // make draggable
                 $('#popwin').draggable({
                     handle: '#popwin-header-inner'
                 });
                 // adjust the popwin con height
                 h = h - 81;
             } else {
                 // remove heder
                 $('#popwin-header').remove();
             }

             closeBtn = $('#popwin-close');
             hideClose == 'yes' ? closeBtn.css('visibility', 'hidden') : closeBtn.css('visibility', 'visible');
             if (conObj) {
                 if (mode == 'normal') { /*如果是非剪切模式*/
                     wrap = conObj;
                     box = null;
                 } else {
                     conhtml = conObj.contents();
                     conhtml.appendTo(box);
                 }
             }

             if (mode == 'cut') {
                 boxOutter.css({
                     width: w,
                     height: h
                 });
                 box.css({
                     width: w,
                     height: h
                 });
                 //wrap.css({width:w+12,height:h+12});
                 if (header){
                     wrap_h = h + 55;
                 }else if (h == 'auto'){
                     wrap_h = h
                 }else{
                     wrap_h = h + 12;
                 }
                 wrap.css({
                     width: w + 12,
                     height: wrap_h
                 });
             } else {
                 //wrap.css({width:w,height:h});
                 if (header){
                     wrap_h = h + 55;
                 }else if (h == 'auto'){
                     wrap_h = h
                 }else{
                     wrap_h = h + 12;
                 }
                 wrap.css({
                     width: w + 12,
                     height: wrap_h
                 });
             }

             if (hideScroll == 'yes') {
                 $('html,body').css('overflow', 'hidden');
             }
             if (u) {
                 box.addClass('popwin-loading').html('<iframe id="popwin-iframe" frameborder="0" width="' + w + '" height="' + h + '" src=""></iframe>');
                 $('#popwin-iframe').attr('src', u).load(function() {
                     box.removeClass('popwin-loading');
                 });
             }
             if (aj) {
                 box.addClass('popwin-loading');
                 // remove content before loading
                 box.html('');
                 $.ajax({
                     url: aj.url,
                     data: aj.data || {},
                     dataType: aj.dataType || 'text',
                     success: function(data) {
                         if (aj.success) {
                             aj.success(data);
                         } else {
                             box.html(data);
                             $.popwin.fixPosition();
                         }
                         box.removeClass('popwin-loading');
                     }
                 });
             }

             if (ie6) {
                 sel_for_ie6 = $('select:visible');
                 sel_for_ie6.css('visibility', 'hidden');
             }

             /*quick function*/
             var qt = args.quicktype;
             if (qt) {
                 var isJump = false,
                     isFunction = false,
                     isCallback = false;
                 var qArg = args.quickarg;
                 var quickHTML = '<div class="popwin-quick-box">';
                 quickHTML += '<table border="0" cellpadding="0" cellspacing="0" width="320"><tr><td width="70"><span class="popwin-icon popwin-' + qt + '"></span></td>';
                 if (typeof qArg[0] == 'string') {
                     quickHTML += '<td valign="middle"><span class="popwin-quick-text">' + qArg[0] + '</span></td></tr></table>';
                 }
                 quickHTML += '<p class="popwin-btn-wrap">'
                 if (/^(error|info|warn|success)$/.test(qt)) {
                     quickHTML += '<a class="popwin-btn" href="javascript:void(0);" id="popwin-ok-btn">' + (qArg[1] || '\u786E\u5B9A') + '</a>';
                     if (qArg[2] && $.isFunction(qArg[2])) {
                         isCallback = true;
                     }
                 }
                 if (qt == 'ask') {
                     quickHTML += '<a class="popwin-btn" href="javascript:void(0);" id="popwin-ask-ok">' + (qArg[2] || '\u786E\u5B9A') + '</a>';
                     quickHTML += '<a style="margin-left:10px;" class="popwin-btn" href="javascript:void(0);" onclick="jQuery.popwin.close();">' + (qArg[3] || '\u53D6\u6D88') + '</a>';
                     if ($.isFunction(qArg[1])) {
                         isFunction = true;
                     } else if (typeof qArg[1] == 'string') {
                         isJump = true;
                     }
                 }
                 quickHTML += '</p></div>';
                 box.html(quickHTML);

                 $('#popwin-ok-btn').click(function() {
                     $.popwin.close();
                     if (isCallback) { /*error,info,warn,success四种情况如果设置了回调函数（第三个参数）*/
                         qArg[2]();
                     }
                 });

                 if (isJump) {
                     $('#popwin-ask-ok').click(function(event) {
                         event.stopPropagation();
                         $.popwin.close();
                         location.href = qArg[1];
                         return false;
                     });
                 }
                 if (isFunction) {
                     $('#popwin-ask-ok').click(function(event) {
                         $.popwin.close();
                         qArg[1]();
                         event.stopPropagation()
                         return false;
                     });
                 }
             }


             h = wrap.height();
             $.popwin.setposition();
             $.popwin.created = true;
             $.popwin.visible = true;
         },
         'close': function(closeArg) {
             if (hideCover == 'no') {
                 if (animate == 'yes') {
                     lowIE ? bg.hide() : bg.fadeOut(speed);
                 } else {
                     bg.hide();
                 }
             }
             if (mode == 'cut') {
                 box.removeClass('popwin-loading')
             };
             if (animate == 'yes') {
                 lowIE ? wrap.hide() : wrap.animate({
                     top: '-=10',
                     opacity: 'hide'
                 }, speed);
             } else {
                 wrap.hide();
             }
             if (args.hideScroll == 'yes') {
                 $('html,body').css('overflow', '');
             }
             $.popwin.visible = false;
             if (ie6) {
                 sel_for_ie6.css('visibility', 'visible');
             }
             if (conObj && mode == 'cut') {
                 conhtml.appendTo(conObj);                 
             }

             if (args.closeAfter) {
                 args.closeAfter(closeArg);
             }
             box.empty();
         },
         'setposition': function() {
             var docW = $('body').outerWidth();
             var docH = $(document).height();
             var screenWidth = $(window).width(),
                 screenHeight = $(window).height();
             var scrolltop = $(window).scrollTop();
             var objLeft = (screenWidth - w) / 2;
             var boxTop = (screenHeight - h) / 2;
             if (ie6) {
                 boxTop += scrolltop;
             }
             if (hideCover == 'no') {
                 bg.css({
                     width: docW,
                     height: docH
                 });
                 if (animate == 'yes') {
                     lowIE ? bg.show() : bg.fadeIn(speed);
                 } else {
                     bg.show();
                 }
             }
             if ($.popwin.visible) {
                 wrap.css({
                     left: objLeft,
                     top: boxTop
                 });
             } else {
                 wrap.css({
                     left: objLeft,
                     top: boxTop
                 });
                 if (animate == 'yes') {
                     lowIE ? wrap.show() : wrap.css({
                         top: boxTop - 20
                     }).animate({
                         top: boxTop,
                         opacity: 'show'
                     }, speed);
                 } else {
                     wrap.show();
                 }
             }
             if (args.showAfter) {
                 args.showAfter();
             }
         },
         'fixPosition': function() {
             w = wrap.width();
             h = wrap.height();
             $.popwin.setposition();
         },
         'created': false,
         'visible': false,
         'quick': function(str, args) {
             $.popwin.show({
                 width: 380,
                 height: 'auto',
                 hideClose: 'yes',
                 quicktype: str,
                 quickarg: args
             });
         },
         'error': function() {
             if (arguments.length > 0) {
                 $.popwin.quick('error', arguments);
             }
         },
         'info': function() {
             if (arguments.length > 0) {
                 $.popwin.quick('info', arguments);
             }
         },
         'warn': function() {
             if (arguments.length > 0) {
                 $.popwin.quick('warn', arguments);
             }
         },
         'ask': function() {
             if (arguments.length > 0) {
                 $.popwin.quick('ask', arguments);
             }
         },
         'success': function() {
             if (arguments.length > 0) {
                 $.popwin.quick('success', arguments);
             }
         }
     };
     $(window).resize(function() {
         if ($.popwin.created && $.popwin.visible) {
             $.popwin.setposition();
         }
     });
     if (ie6) {
         var timer_for_ie6;
         $(window).scroll(function() {
             if ($.popwin.created && $.popwin.visible) {
                 clearTimeout(timer_for_ie6);
                 timer_for_ie6 = setTimeout(function() {
                     $.popwin.setposition();
                 }, 200);
             }
         });
     }
     $(document).keydown(function(event) {
         if ($.popwin.visible && event.keyCode == 27) {
             $.popwin.close();
         }
     });
 })(jQuery);
