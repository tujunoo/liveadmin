function SetFocus() {
        /*
  if (document.forms.length > 0) {
    var field = document.forms[0];
    for (i=0; i<field.length; i++) {
      if ( (field.elements[i].type != "image") &&
           (field.elements[i].type != "hidden") &&
           (field.elements[i].type != "reset") &&
           (field.elements[i].type != "submit") ) {

        document.forms[0].elements[i].focus();

        if ( (field.elements[i].type == "text") ||
             (field.elements[i].type == "password") )
          document.forms[0].elements[i].select();

        break;
      }
    }
  }
  */
}
function rowOverEffect(object)
{
  if (object.className == 'dataTableRow') object.className = 'dataTableRowOver';
}
function rowOutEffect(object)
{
  if (object.className == 'dataTableRowOver') object.className = 'dataTableRow';
}
function toggel_div(divid)
{
        if(eval("document.getElementById('" +  divid + "').style.display") == '')
            eval("document.getElementById('" +  divid + "').style.display = 'none'");
        else
            eval("document.getElementById('" +  divid + "').style.display = ''");
}
function chkallfun()
{
        var i = 0 ;
        var j = 0;
        for ( i= 0 ; i < window.document.orderitemcheck.elements.length ; i ++)
        {
                if(window.document.orderitemcheck.elements[i].type == "checkbox" )
                {
                        if (window.document.orderitemcheck.chkall.checked == true)
                                window.document.orderitemcheck.elements[i].checked= true ;
                        else
                                window.document.orderitemcheck.elements[i].checked= false;
                }
        }

}
var XMLHttpRequestObject = createXMLHttpRequestObject();
function createXMLHttpRequestObject()
{
  var XMLHttpRequestObject = false;
  
  try
  {
    XMLHttpRequestObject = new XMLHttpRequest();
  }
  catch(e)
  {
    var aryXmlHttp = new Array(
                               "MSXML2.XMLHTTP",
                               "Microsoft.XMLHTTP",
                               "MSXML2.XMLHTTP.6.0",
                               "MSXML2.XMLHTTP.5.0",
                               "MSXML2.XMLHTTP.4.0",
                               "MSXML2.XMLHTTP.3.0"
                               );
    for (var i=0; i<aryXmlHttp.length && !XMLHttpRequestObject; i++)
    {
      try
      {
        XMLHttpRequestObject = new ActiveXObject(aryXmlHttp[i]);
      } 
      catch(e){document.write("createXMLHttpRequestObject: XMLHttpRequestObject Error");}
    }
  }
  
  if (!XMLHttpRequestObject)
  {
    alert("Error: failed to create the XMLHttpRequest object.");
  }
  else 
  {
    return XMLHttpRequestObject;
  }
}

var is_busy_ajax_call = false;
function sendFormData(idForm, dataSource, divID, ifLoading, refreshtrue, refresh_url, Js_code)
{
  var postData='';
  var strReplaceTemp;
  
  if(XMLHttpRequestObject && is_busy_ajax_call == false)
  {
    
	XMLHttpRequestObject.open("POST", dataSource, true);
    XMLHttpRequestObject.setRequestHeader("Method", "POST " + dataSource + " HTTP/1.1");
	XMLHttpRequestObject.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	XMLHttpRequestObject.setRequestHeader("Content-length", dataSource.length);
	XMLHttpRequestObject.setRequestHeader("Connection", "close");  
	is_busy_ajax_call = true;  
    XMLHttpRequestObject.onreadystatechange = function()
    {
      if (XMLHttpRequestObject.readyState == 4 &&
          XMLHttpRequestObject.status == 200)
      {
        try
        {
          var objDiv = document.getElementById(divID);
		  objDiv.innerHTML = '';
          objDiv.innerHTML = XMLHttpRequestObject.responseText;
		  if(Js_code!="" && typeof(Js_code)!="undefined"){
		  	eval(Js_code);
		  }
		  is_busy_ajax_call = false;		  
		  if(refreshtrue == 'refreshtrue'){
			Refresher(1, refresh_url);
		  }
        }
        catch(e){document.write("sendFormData: getElementById(divID) Error"+e);}
      }
      else
      {
        if(ifLoading)
        {
          try
          {
            var objDiv = document.getElementById(divID);
            objDiv.innerHTML += "<img src=/img/loading.gif />";
          }
          catch(e){document.write("sendFormData->ifLoading: getElementById(divID) Error");}
        }
      }
    }    
    for(i=0; i<document.getElementById(idForm).elements.length - 1; i++)
    {
		 document.getElementById(idForm).elements[i].name = document.getElementById(idForm).elements[i].name.replace(/\[\]/g, "");
		 document.getElementById(idForm).elements[i].name = document.getElementById(idForm).elements[i].name.replace(/\[/g, "--leftbrack");
		 document.getElementById(idForm).elements[i].name = document.getElementById(idForm).elements[i].name.replace(/\]/g, "rightbrack--");	
		if (document.getElementById(idForm).elements[i].type == "radio"  || document.getElementById(idForm).elements[i].type == "checkbox") {
               if (document.getElementById(idForm).elements[i].checked) {                
				 strReplaceTemp = document.getElementById(idForm).elements[i].name;
				 postData += "&aryFormData["+strReplaceTemp+"][]="+document.getElementById(idForm).elements[i].value;
	
			   }
        }else{			 
			  strReplaceTemp = document.getElementById(idForm).elements[i].name.replace(/\[\]/i, "");			
			  postData += "&aryFormData["+strReplaceTemp+"][]="+amptrim(document.getElementById(idForm).elements[i].value);
		}	
	}    
    postData += "&parm="+new Date().getTime();
	//alert(postData);
    try
    {
      XMLHttpRequestObject.send(postData);
	  ifLoading = false;
    }
    catch(e){document.write("sendFormData: XMLHttpRequestObject.send Error");}
  }
}
function amptrim(str) {
	s = new String(str);
	s = s.replace(/\?/g,"@@null;");
	s = s.replace(/&/g,"@@amp;");
	s = s.replace(/\+/g,"@@plush;");
	return s;
} 
function Refresher(t, refresh_url) {
   if(t) refresh = setTimeout("document.location='"+refresh_url+"';", t*1000);
}
$(function() {
  var page_size = $.cookie('page_size');
  if(page_size) {
    $('#page_size_selecter option[value="'+page_size+'"]').attr('selected', 'selected');
  }
  $("#page_size_selecter").live('change', function() {
    $.cookie('page_size', $(this).val());
    location.reload();
  });

    $("#search_clear_button").click(function () {
        $($(this).attr('data-bind')).find("input,select,textarea,radio")
            .not(':button, :submit, :reset, :hidden')
            .val('')
            .removeAttr('checked')
            .removeAttr('selected');
        if($($(this).attr('data-bind')).find('.country-selector').length > 0){
            $($(this).attr('data-bind')).find('.country-selector').text("请选择");
            $($(this).attr('data-bind')).find(".country-selector").parent().find("input").val('');
        }
    });
});