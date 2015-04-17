/* plugins by stephan
* 2014 3 10
*/
(function($){
	$.extend({
		"extendObj": function(datas, temp, prefix) {
			function replace(a, b, c) {
				return a.indexOf(b) > -1 ? a.split(b).join(null === c ? "" : c) : a;
			}
			function objectToHtml(data, item, itemIndex) {
				item = $.trim(item || temp);
				var testReg = /\{@test\s+([^@]+)\s+@\}/, inlayReg = /\{@getTemplate\s*=\s*"([^"]*)"\}/, foreachReg = /\{@foreach\s+items="([^"]+)"\s*\}(.*)(?=\{\/)\{\/@foreach\}/, ifReg = /\{@if\s*=\s*"([^"]+)"\s*\}(.*)(?=\{\/)\{\/@if\}/, elseReg = /(.*)\{@else\}(.*)/;
				for (var p in data) {
					var theval = data[p];
					if (theval && "object" == typeof theval && !theval.length) for (var objAttr in theval) item = replace(item, "{" + p + "." + objAttr + "}", theval[objAttr]); else (null == theval || "object" != typeof theval) && (item = replace(item, "{" + prefix + p + "}", theval));
				}
				for (; ifReg.test(item); ) {
					var test = ifReg.exec(item), check = function($index, $data) {
						return eval(test[1]);
					}(itemIndex, data), testResult = "";
					if (elseReg.test(test[2])) {
						var elseTest = elseReg.exec(test[2]);
						testResult = check ? elseTest[1] : elseTest[2];
					} else testResult = check ? test[2] : "";
					item = item.split(test[0]).join(testResult);
				}
				for (; inlayReg.test(item); ) {
					var test = inlayReg.exec(item);
					item = item.split(test[0]).join($("#" + test[1]).extendObj(data));
				}
				for (; foreachReg.test(item); ) {
					var test = foreachReg.exec(item), items = data[test[1]], itemTemp = test[2], itemsHtml = "";
					"object" == typeof items && items.length && (itemsHtml += $.extendObj(items, itemTemp, ".")), item = item.split(test[0]).join(itemsHtml);
				}
				for (; testReg.test(item); ) {
					var test = testReg.exec(item);
					item = item.split(test[0]).join(function($index, $data) {
						return eval(test[1]);
					}(itemIndex, data));
				}
				return item;
			}
			temp = $.trim(temp.replace(/(\s*)?(\n|\r)(\s*)?/g, "")), prefix = prefix || "";
			var html = "";
			if (temp.length) {
				if ("string" == typeof datas && datas.length) html += temp.split("{" + prefix + "value}").join(datas); else if ("object" == typeof datas && datas.length) if ("string" == typeof datas[0]) for (var i = 0; i < datas.length; i++) html += temp.split("{" + prefix + "value}").join(datas[i]); else for (var i = 0; i < datas.length; i++) html += objectToHtml(datas[i], null, i); else "object" == typeof datas && (html += objectToHtml(datas));
				return html;
			}
			return html;
		}
	})
})(jQuery);
/*Layout Function
 * Note:
 * All sizes in this function were calculated by jQuery
 * So,you do not modify the numbers in this code
 * If you want to do that,modify the CSS file
 */
$(function(){
        var top = $('#admin-top');
        var main = $('#admin-main');
        var side = $('#admin-side');
        var sideCon = $('#admin-side-con');
        var mainCon = $('#admin-main-con');
        var sideToggle = $('#admin-side-toggle');
        var topToggle = $('#admin-top-toggle');
        var side_width = side.width() || 200;
        var top_height = top.height() || 55;
        var top_toggle_height = topToggle.height() || 7;
        var side_toggle_width = sideToggle.width() || 10;
        var side_title_height = $('.admin-side-title').height() || 30;
        var foot_height = $('#admin-foot').height() || 30;
        var main_padding_left = parseInt(main.css('margin-left')) || 210;
        /*save sideCon and mainCon current height*/
        var sideCon_cur_height , mainCon_cur_height;

        var setLayout = function(){
                var winW = $(window).width();
                var winH = $(window).height();
                /*update sideCon_cur_height and mainCon_cur_height values*/
                if(top.is(':hidden')){
                        sideCon_cur_height = winH - top_toggle_height - foot_height - side_title_height - 1;
                }else{
                        sideCon_cur_height = winH - top_height - top_toggle_height - foot_height - side_title_height - 1;
                }
                mainCon_cur_height = sideCon_cur_height + side_title_height - 1;
                sideCon.height(sideCon_cur_height);
                mainCon.height(sideCon_cur_height + side_title_height - 1);
                sideToggle.height(sideCon_cur_height + side_title_height + 1);
        };
        /* click hide or show side */
        sideToggle.toggle(function(){
                main.css('margin-left','10px');
                sideToggle.css('left','0px');
                sideToggle.addClass('admin-side-hide');
                side.hide();
        },function(){
                main.css('margin-left',main_padding_left+'px');
                side.show();
                sideToggle.css('left',(main_padding_left-10)+'px');
                sideToggle.removeClass('admin-side-hide');
        });

        /* click hide or show top */
        topToggle.toggle(function(){
                /*change the mainCon height when topToggle clicked*/
                mainCon_cur_height += top_height;
                mainCon.css('height',mainCon_cur_height+'px');
                /*change the sideCon height when topToggle clicked*/
                sideCon_cur_height += top_height;
                sideCon.css('height',sideCon_cur_height+'px');

                topToggle.addClass('admin-top-hide');
                side.css('top',top_toggle_height+'px');
                sideToggle.css({top:top_toggle_height,height:mainCon_cur_height+2});
                top.hide();
        },function(){
                mainCon_cur_height -= top_height;
                mainCon.css('height',mainCon_cur_height+'px');
                sideCon_cur_height -= top_height;
                sideCon.css('height',sideCon_cur_height+'px');
                topToggle.removeClass('admin-top-hide');
                side.css('top',(top_height + top_toggle_height)+'px');
                sideToggle.css({top:top_toggle_height + top_height,height:mainCon_cur_height+2});
                top.show();
        });
        setLayout();
        $(window).resize(setLayout);
});


/*change sys*/
$(function(){
        var box = $('#ChangeSys');
        var tit = $('.changeSysTitle',box);
        var sub = $('#ChangeSysCon',box);
        box.hover(function(){
                tit.addClass('changeSysTitleHover');
                sub.show();
        },function(){
                tit.removeClass('changeSysTitleHover');
                sub.hide();
        });
        sub.find('li').hover(function(){
                $(this).addClass('hover');
        },function(){
                $(this).removeClass('hover');
        });
});


/*custom menu*/
$(function(){
        var menus = $('.custom-menu');
        menus.each(function(){
                var me = $(this);
                var tit = me.find('.custom-menu-title');
                var con = me.find('.custom-menu-list');
                me.hover(function(){
                        tit.addClass('custom-title-hover');
                        con.css('z-index','9').show();
                },function(){
                        tit.removeClass('custom-title-hover');
                        con.css('z-index','auto').hide();
                });
        });
});

/*admin-nav*/
$(function(){
        var subWrap = $('#admin-nav-sub-wrap');
        var posY = 30;
        var timer;
        var setSubPosition = function(p,sub){
                subWrap.html('<ul class="navsub">'+sub.html()+'</ul>');
                var subH = subWrap.outerHeight();
                var parentY = p.offset().top;
                var winH = $(window).height();
                if((parentY+subH) > winH){
                        subWrap.css('top','auto');
                        subWrap.css('bottom','0px');
                }else{
                        subWrap.css('bottom','auto');
                        subWrap.css('top',parentY+'px');
                }
        };
        var tits = $('#admin-nav .navtit');
        var curLink = null;
        tits.each(function(){
                var me = $(this);
                var meLink = me.find('>a');
                var sub = me.find('.navsub');
                me.hover(function(){
                        if(curLink){curLink.removeClass('hover');}
                        meLink.addClass('hover');
                        curLink = meLink;
                        if(sub.length > 0){
                                clearTimeout(timer);
                                subWrap.show();
                                setSubPosition(me,sub);
                        }
                },function(){
                        if(sub.length > 0){
                                clearTimeout(timer);
                                timer = setTimeout(function(){
                                        subWrap.hide();
                                        curLink.removeClass('hover');
                                },200);
                        }else{
                                curLink.removeClass('hover');
                        }
                }).click(function(){
                        tits.find('>a').filter('.current').removeClass('current');
                        meLink.addClass('current');
                });
        });

        subWrap.hover(function(){
                clearTimeout(timer);
        },function(){
                timer = setTimeout(function(){
                        subWrap.hide();
                        curLink.removeClass('hover');
                },200);
        });
});
//refresh current page with params
function jumpTo(url,param){
        if(typeof(param)!='undefined'){
                var s=url.indexOf("?");
                var lc=url.charAt(url.length-1);
                if(s==-1){
                        url+='?'+param;
                }else if(lc=='?'){
                        url+=param;
                }else{
                        url+='&'+param ;
                }
        }
        window.location.href = url ;
}

