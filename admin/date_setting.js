/*
class='date' 有include jquery.datetimepicker.js就會觸發日曆（css也要匯入）

/**/
$(document).ready(function(){
	$("input[class*='date']").attr({
					"date_plugin":"0",
					"readonly":true
	})
	$("input[class*='date']").mouseenter(function(){
		if( $(this).attr("date_plugin")=="0" ){
	    	if ( $(this).datetimepicker) {
	        	$(this).datetimepicker({
					yearOffset:-20,
					lang:'ch',
					timepicker:false,
					format:'Y-m-d',
					formatDate:'Y/m/d'
				});
			}
	    	//console.log("0")
		}
	    $(this).attr("plugin","1")
	})
	$("input[class*='time']").attr({
					"date_plugin":"0"
					//,"readonly":true
	})
	$("input[class*='time']").mouseenter(function(){
		if( $(this).attr("date_plugin")=="0" ){
	    	if ( $(this).datetimepicker) {
	        	$(this).datetimepicker({
					lang:'ch'
					,format:'Y-m-d H:i'
					,onChangeDateTime:function(dp,$input){
						var str = $input.val();
						if( str == "" )return;
						var big_month = [1,3,5,7,8,10,12];
						var small_month = [4,6,9,11];
						var reg = /^((19|20|21|22)[\d]{2})[-|\/](0[1-9]|10|11|12)[-|\/]([0-2][0-9]|3[0-1]) ([0-1][0-9]|2[0-3]):([0-5][0-9])$/; 
						var r = str.match(reg); 
						if( r==null )
						{
							alert("時間格式錯誤");
							$input.val("");
						}else
						{
							var month_days = 0
							for( key in big_month )
							{
								if( Number(r[3]) == big_month[key] )
								{
									month_days = 31;
									break;
								} 
							}
							
							for( key in small_month )
							{
								if( Number(r[3]) == small_month[key] )
								{
									month_days = 30;
									break;
								} 
							}
							
							if( Number(r[3]) == 2 )
							{
								if( Number(r[1]) % 4 ==0 )
								{
									month_days = 29;
								}else
								{
									month_days = 28;
								}
							}
							
							if( Number(r[4]) > month_days )
							{
								//console.log( Number(r[1])+"-"+Number(r[3])+"-"+Number(r[4]) +"^^"+ month_days)
								alert("時間格式錯誤");
								$input.val("");
							}
							
						}
						//console.log(r+"ccc")
					}
				});
			}
	    	//console.log("0")
		}
	    $(this).attr("plugin","1")
	})
})
