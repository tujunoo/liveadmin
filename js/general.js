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
