/*global loading style*/
$(function(){
   var body = $('body');
   var win = $(window);
    var doc = $(document);
  body.append('<div id="global-loading"></div>');
 var ld = $('#global-loading');
  var w = ld.width();
     var h = ld.height();
    var setLoading = function(){
            var winW = win.width();
         var winH = win.height();
                var scrollT = doc.scrollTop();
          var t = scrollT + (winH-h)/2;
           var l = (winW-w)/2;
             ld.css({left:l,top:t});
 };
      setLoading();
   win.scroll(setLoading).resize(setLoading);
      body.ajaxStart(function(){
              ld.show();              
        });
     body.ajaxStop(function(){
               ld.hide();              
        });
});

/*folding title*/
$(function(){
    $('div.folding').each(function(){
               var me = $(this);
               var tit = me.find('.sub-title');
                var con = me.next();
            if(!con.is('.sub-content')){return;}
            var animaing = false;
           tit.click(function(event){                      
                        if(animaing){return false;}
                     animaing = true;
                        if(con.is(':hidden')){
                          tit.removeClass('sub-title-open');
                              con.slideDown('fast',function(){
                                        animaing = false;
                               });
                     }else{
                          tit.addClass('sub-title-open');
                         con.slideUp('fast',function(){
                                  animaing = false;
                               });
                     }
                       event.stopPropagation()
         });
     });     
});

/*table script
 * zebra(odd row and even row different style)
 * row highlight
 */
$(function(){
 $('table').each(function(){
             var table = $(this);
            if(table.hasClass('zebra')){
                    table.find('>tbody>tr:has(td):even').addClass('trEven');
                }
               if(table.hasClass('row-hl')){
                   table.find('>tbody>tr:has(td)').each(function(){
                                var tr = $(this);
                               tr.hover(function(){
                                    tr.addClass('high-light');
                              },function(){
                                   tr.removeClass('high-light');
                           }).click(function(){
                                    table.find('tr.tr-clicked').removeClass('tr-clicked');
                                  tr.addClass('tr-clicked');
                              });
                     });
             }
       });
});




/*tab*/
$(function(){
  var hash_str = location.hash;
  var loc_id = '';
  if(hash_str != ''){
   loc_id = hash_str.replace('#','');
  }
  var cur_tab_select = null;
  var isHaveHash = false;

  
  $('.tab-box').each(function(){
    var box = $(this);    
    var tits = box.find('>.tab-tit h3:not(.outlink),>.tab-tit .tab-select');
    var cons = box.find('>.tab-con');
    if(tits.length == 0){return;}
    if(tits.length == 1){
      tits.eq(0).addClass('current');
      cons.eq(0).show();
      return;
    }
    var cur_active_index = 0;

    tits.each(function(i){
      var me = $(this);
      var cons_i = cons.eq(i);
      var isSelect = me.hasClass('tab-select');
      var isSelectLink = me.hasClass('tab-select-link');
      if(isSelect){
        //me.find('span').wrapInner('<em />');
        var ul = me.find('ul');          
        me.hover(function(){
          $(this).css('zIndex', '2');
          ul.show();
          cur_tab_select = ul;
        },function(){
          $(this).css('zIndex', '1');
          box.find('.tab-select ul').hide();
        });
        if(isSelectLink){return;}
        var select_cons = cons_i.find('.tab-select-con');
        var lis = ul.find('>li');
        lis.each(function(n){            
          var li = $(this);              
            li.click(function(event){
              tits.filter('.current').removeClass('current');
              me.addClass('current');
              cons.hide();
              cons_i.show();
              select_cons.hide();
              select_cons.eq(n).show();
              ul.hide();
              lis.filter('.cur').removeClass('cur');
              li.addClass('cur');
              if (li.attr('id')) {
                location.hash = li.attr('id');
                $.cookie('tab_control_cur_id', li.attr('id'));
              }
              event.stopPropagation();
            });
            
            li.hover(function(){
              li.addClass('hover');
            },function(){
              li.removeClass('hover');
            });

            if(li.attr('id') == loc_id){
              lis.eq(n).trigger('click');
              isHaveHash = true;
            }
            var cookie_str_id = $.cookie('tab_control_cur_id');
            if(li.attr('id') == cookie_str_id && !isHaveHash) {
              lis.eq(n).trigger('click');
              isHaveHash = true;
              return false;
            }

        });
        
      }else{
        me.click(function(){            
          tits.filter('.current').removeClass('current');
          me.addClass('current');
          cons.hide();
          cons_i.show();
          location.hash = me.attr('id');
          $.cookie('tab_control_cur_id',me.attr('id'))
        });
      }      
    });



    tits.each(function(m){
        var c = $(this);
        var cookie_str_id = $.cookie('tab_control_cur_id');
        if(c.attr('id') == loc_id){
          isHaveHash = true;
          tits.eq(m).trigger('click');
          return false;
        }else if(c.attr('id') == cookie_str_id){
          isHaveHash = true;
          tits.eq(m).trigger('click');
          return false;
        }
      });
   
  if(!isHaveHash){
    tits.eq(0).trigger('click');
  }
    
  });
});





/*datepicker init*/
function datepickerInit(obj,arg){
	var opt = {dateFormat:'yy-mm-dd',changeYear:true,changeMonth:true,yearRange:'1900:2050'}
	opt = $.extend(opt , arg||{});
	$(obj).datepicker(opt);
}

function datetimepickerInit(obj,arg){
	var opt = {
		changeYear:true,
		changeMonth:true,
		yearRange:'1900:2050',
		showSecond: true,
		dateFormat:'yy-mm-dd',
		timeFormat: 'hh:mm:ss'
	};
	opt = $.extend(opt , arg||{});
	$(obj).datetimepicker(opt);	
}

/*default calendar init*/
$(function(){
    if($.datepicker){
        datepickerInit('input.calendar');
    }       
});

/*forms layout script*/
$(function(){
 $('.frmWrap').each(function(){
          var me = $(this);
               var leftv = me.attr('leftwidth');
               var rightv = me.attr('rightwidth');
             if(leftv && parseInt(leftv) > 0){
                       me.find('>.flist>.flabel').width(leftv);
                }
               if(rightv && parseInt(rightv) > 0){
                     me.find('>.flist>.fcon').width(rightv);
         }
       });
});

/*form validate*/
$(function(){
    
        /*set tip texts*/
       var tiptext = {
         empty:'This can\'t be empty.',
          num:'This must be a number.',
           email:'Email format error.'
     };

      var showtip = function(o,str){
          var tip = $('#mini-tip-con');
           if(tip.length == 0){
                    $('body').append('<div id="mini-tip"><div id="mini-tip-con" class="tip-error"></div><span id="mini-tip-arrow"></span><span id="mini-tip-close" onclick="$(\'#mini-tip\').fadeOut();"></span></div>');
                   tip = $('#mini-tip-con');
               }               
                var t = o.offset().top + (o.outerHeight() > 0 ? o.outerHeight()+6 : 28);
                var l = o.offset().left;
                var tipbox = $('#mini-tip');
            tip.html(str)
           tipbox.css({top:t,left:l}).show();              
                tipbox;
         //$('html,body').animate({scrollTop:t-32});             
        };
      
        var cleartip = function(){
              $('#mini-tip').fadeOut();
       };
              
        
        var validate = function(o){             
                var elems = o.find(':text,textarea,:radio,:checkbox');          
                var validated = true;
           if(elems.length == 0){return true;}             
                elems.each(function(){
                  var t = $(this);
                        var type = t.attr('type');
                      /*check text,textarea*/
                 if((type && type == 'text') || t[0].tagName == 'TEXTAREA'){
                             if(t.attr('must') == 'yes'){                                    
                                        if($.trim(t.val()) == ''){
                                              validated = false;                                              
                                                showtip(t,tiptext.empty);                                       
                                                return false;
                                   }
                               }
                               if(t.attr('rule')){
                                     var v = $.trim(t.val());
                                        if(v == '' && (t.attr('must') == 'no' || !t.attr('must'))){
                                             return;
                                 }
                                       switch(t.attr('rule')){
                                         case 'number':
                                          if(isNaN(v)){
                                                   validated = false;
                                                      showtip(t,tiptext.num);                                         
                                                        return false;
                                           }
                                               break;                                          
                                                case 'email':
                                           if(!/^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/.test(v)){
                                                   validated = false;
                                                      showtip(t,tiptext.email);                                               
                                                        return false;
                                           }
                                               break;
                                  }
                               }
                       }
                       /*check radio,checkbox*/
                        if(type && (type=='radio' || type=='checkbox')){
                                if(t.attr('must') == 'yes'){
                                    var len = $('[name="'+t.attr('name')+'"]:checked',o).length;
                                      if(len == 0){
                                           validated = false;                                              
                                                showtip(t,'You must selected one of them.');                                    
                                                return false;   
                                        }
                               }
                       }
               });             
                
                if(validated){cleartip();}
              return validated;
       };
      $('[validate=yes]').each(function(){
            var frm = $(this);
              var isForm = (frm[0].tagName == 'FORM');
                if(isForm){
                     frm.submit(function(){
                          return validate(frm);
                   });
             }else{
                  frm.find('.validate-trigger').click(function(){                         
                                if(validate(frm)){                                      
                                        frm.attr('pass','yes');                                 
                                }
                       });
             }
       });
});



/*checkall plugin for checkbox*/
/*
example:
<input type="checkbox" data-checkall="OrderStatusHistory[complaints][]" />
*/
$(function(){
  $(document).on('click',function(event){
    var tar = $(event.target);
    if(tar.is(':checkbox') && tar.attr('data-checkall')){
      var checked = tar.is(':checked');
      var name_str = tar.attr('data-checkall');
      $('input[type=checkbox]').each(function(){
        var me = $(this);
        if(me.attr('name') == name_str){
          if(checked){me.attr('checked','checked')}else{me.removeAttr('checked')}
        }
      });
    }
  });
});