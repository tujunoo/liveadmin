var dateOp = {
	_padLeft:function(str){
		str = ''+str;
		if(str.length<2){
			return '0'+str;
		}
		return str;
	},
	strtotime:function(d){
		var d=d.replace(/-/g, "/");
		if(isNaN(Date.parse(d)))return 0;
		return (new Date(Date.parse(d)).getTime())/1000;
	},
	isBetween:function(date,begin,end){
		date = dateOp.strtotime(date);
		if(dateOp.strtotime(begin)<=date && dateOp.strtotime(end)>=date){
			return true;
		}
		return false;
	},
	toDate:function(time){
	   if(time==undefined){
		   time = dateOp.time();
	   }
	   var s ='';
	   d = new Date(time*1000);
	   s += d.getFullYear() + "-";
	   var m = d.getMonth() + 1+"";
	   if(m.length<2)m='0'+m;
	   s += m + "-";
	   var day = d.getDate()+"";
	   if(day.length<2)day='0'+day;
	   s += day;	   
	   return s;
	},
	dateDiff:function(date1,date2){	
		date1 = dateOp.strtotime(dateOp.toDate(dateOp.strtotime(date1))+' 00:00:00');
		date2 = dateOp.strtotime(dateOp.toDate(dateOp.strtotime(date2))+' 23:59:59');
		return Math.floor(Math.abs(date1-date2)/86400)+1;
	},
	time:function(){
		return (new Date().getTime())/1000;		
	},
	zeroTime:function(){
		return dateOp.strtotime(dateOp.toDate(dateOp.time())+' 00:00:00');
	},
	nowTime:function(){
		var now= new Date();
	    var year=this._padLeft(now.getFullYear());
	    var month=this._padLeft(now.getMonth()+1);
	    var day=this._padLeft(now.getDate());
	    var hour=this._padLeft(now.getHours());
	    var minute=this._padLeft(now.getMinutes());
	    var second=this._padLeft(now.getSeconds());	    
	    return year+'-'+month+'-'+day+' '+hour+':'+minute+':'+second;
	}
}