function rowOverEffect(object) {
  if (object.className == 'dataTableRow') object.className = 'dataTableRowOver';
}

function rowOutEffect(object) {
  if (object.className == 'dataTableRowOver') object.className = 'dataTableRow';
}
function toggel_div(divid)
{
     if(eval("document.getElementById('" +  divid + "').style.display") == '')
       eval("document.getElementById('" +  divid + "').style.display = 'none'");
     else
       eval("document.getElementById('" +  divid + "').style.display = ''");
}
