/*20141002 Jac欄位驗證
 *class=user table=db's table feild=table's feild(可不給則用預設) 可查詢該table feild欄位是否有相同值
 *class=user || type=password 只能輸入英文
 *class=must 必須有值
 *minlength class=user提示增加“此帳號太短”字樣
 *equal=目標id
 */

$(document).ready(function(){
	///css setting
	$("input[type!='button'][type!='checkbox'][type!='radio'][type!='hidden']").each(function(){
		var id=$(this).prop("id")||$(this).prop("name");
		id+="_help";			
		if($("div#"+id).length==0){
			$(this).after("<div id='"+id+"' help></div>");
		}
	})
	$("input[type='checkbox'],input[type='checkbox']").each(function(){
		var name = $(this).prop("name");
		var pattern = new RegExp("[~'!@#$%^&*()-+_=:]");  
		name=name.replace(/\W+/g, "");
		var id = name+"_help";
		if($("div#"+id).length==0){
			$("input[name='"+name+"']:last").after("<div id='"+id+"' style='display:inline;' help></div>");
			//console.log(id);
		}
	})
	
	$("div[help]").each(function(){
		$(this).css({
			"position"	:"absolute",
			"margin-left": "2px",
			"font-size"	: "8pt",
			"color"		: "#f00",
			"-webkit-transform-origin"	: "top left",
			"-moz-transform-origin"		: "top left",
			"-webkit-transform"			: "scale(0.85)",
			"-moz-transform"			: "scale(0.85)",
			"z-index"	: "2"
		})
		if( $(this).prev().is("p") ){
			$(this).css({
				"margin-top": "-14px"
			})
		}
	})
	
 	/////////////////////////////////////////////////////////////////////////////////chk input
	
	//可用placeholder取代
	/*
	$("input[minlength]").each(function(){
		var tmp_default_val=$(this).attr("default_val")||"";
		if(tmp_default_val=="")
    		$(this).attr("default_val","請至少輸入"+$(this).attr("minlength")+"碼");
	})
	$("input[default_val]").bind({
		"focus":function(){
			//input type=password 預設字樣
			$(this).prop("type",$(this).attr("source_type"));
    		if($(this).val()==$(this).attr("default_val")){
        		$(this).val("");
        		$(this).css({"color":"#000"});
    		}
		},
		"blur":function(){
			$(this).prop("type",$(this).attr("source_type"));
			$(this).css({"color":"#000"});
			if($(this).val()=="" || $(this).attr("default_val")==$(this).val()){
				$(this).prop("type","text");
        		$(this).val($(this).attr("default_val"));
        		$(this).css({"color":"#aaa"});
    		}
		}
	})
	$("input[default_val]").each(function(){
		$(this).attr("source_type",$(this).prop("type"));
	})
	$("input[default_val]").trigger('blur');
	*/
	//欄位檢查(單獨)
	/*$("input[type='checkbox']").click(function(){
		var id=$(this).prop("id")||$(this).prop("name");
		id+="_help";
		$("#"+id).css("color","#f00");
		$("div#"+id).html("");
		if($("input[name='"+$(this).prop("name")+"']").prop("class").search("must")>-1){
			if( $("input[name='"+$(this).prop("name")+"']:checked").length==0 ){
				$("div#"+id+":last").html("此為必選欄位");
				b_pass_chk=false;
				return;
			}
		}
	})
	
	$("input[type='redio']").click(function(){
		var id=$(this).prop("id")||$(this).prop("name");
		id+="_help";
		$("#"+id).css("color","#f00");
		$("div#"+id).html("");
		if($("input[name='"+$(this).prop("name")+"']").prop("class").search("must")>-1){
			if( $("input[name='"+$(this).prop("name")+"']:checked").length==0 ){
				$("div#"+id+":last").html("此為必選欄位");
				b_pass_chk=false;
				return;
			}
		}
	})*/
	$("input[type='button'][data-open-dialog]").click(function(){
		$("div[id$=_help][help]").html("");
	})
	
	$("input[type!='button'][type!='checkbox'][type!='radio']").bind("blur",function(){
		var id=$(this).prop("id")||$(this).prop("name");
		id+="_help";
		//var default_val	 = $(this).attr("default_val")||"";
		//var b_default_val=default_val==$(this).val()?true:false	
		$("div#"+id).css("color","#f00");
		
		//最少字元
		$("div#"+id).html("");
		if( $(this).val().length<$(this).attr("minlength") &&
			$(this).val().length>0 
			//&&!b_default_val
			){
			
			var tmp_word=""
			if($(this).prop("class").search("user")>-1){
				tmp_word="此帳號太短，"
			}
			$("div#"+id).html(tmp_word+"請至少輸入"+$(this).attr("minlength")+"碼");
			//console.log("id:"+id+"^^html:"+$("div#"+id).html()+"^^"+tmp_word+"請至少輸入"+$(this).attr("minlength")+"碼")
			b_pass_chk=false;
			return;
		}
		
		//必填欄位
		if($(this).prop("class").search("must")>-1){
			//if( $(this).val()=="" || b_default_val ){
			if( $(this).val()=="" ){
				$("div#"+id).html("此為必填欄位");
				b_pass_chk=false;
				return;
			}
		}
		
		//身分證驗證
		
		//Mail驗證
		if( $(this).prop("class").search("mail")>-1 && $(this).val().search(/^[\w-]+(\.[\w-]+)*@[\w-]+(\.[\w-]+)+$/)==-1){
		   $("div#"+id).html("Email格式不正確");
			b_pass_chk=false;
			return;
		} 
		
		//指定相符欄位
		var target_id = $(this).attr('equal')||"";
		if(	target_id!="" && $("#"+target_id).length>0 ) {//have target input 
			console.log($(this).val()+"^^"+$("#"+target_id).val())
			//if($(this).val()!=$("#"+target_id).val() && !b_default_val ){//this have val
			if($(this).val()!=$("#"+target_id).val()){
				console.log("right_here??"+id+"^^"+$(this).prop("id"));
				//console.log($("#dialog_container #"+target_id).val()+"^^");
				$("div#"+id).html("和指定欄位值不吻合");
				b_pass_chk=false;
				return;
			}
		}
		
		if( $(this).prop("class").search("user")>-1 ){
			
			re = /^[a-zA-Z0-9]*$/;
			if(!re.test($(this).val())){
				$("div#"+id).html("內容含有特殊字元");
				b_pass_chk=false;
				return;
			}
			//欄位檢查(user(帳號))
			if( $(this).attr("table") ){
				//console.log($(this).attr("table")+$(this).attr("feild"))
				$.post("sql_chk_account.php", { 
					query_type:"chk_account",
					table:$(this).attr("table"),
					feild:$(this).attr("feild")||"",
					user:$(this).val()
					},function(data) {
						data=$.trim(data);
						if(data=='true'){
							$("div#"+id).css("color","#0b0");
							$("div#"+id).html("此帳號可以使用");
							console.log(data)
							//b_pass_chk=true;
						}else{
							$("div#"+id).html("此帳號已被使用，請改用其他帳號");
							//console.log(data)
							b_pass_chk=false;
							return;
						}
						
				});
			}
		}
		
		//if( $(this).attr("source_type")=="password" && !b_default_val ){
		if( $(this).attr("type")=="password" ){
			
			re = /^[a-zA-Z0-9]*$/;
			if(!re.test($(this).val())){
				$("div#"+id).html("密碼內請勿含有特殊字元");
				b_pass_chk=false;
				return;
			}
			
		}
		
		
		/*re=/^[\u4e00-\u9fa5a-zA-Z0-9，。,.\(\)\（\）\:\：\`\-]*$/;
		if(!re.test($(this).val())){
			$("div#"+id).html("內容含有特殊字元");
			b_pass_chk=false;
			return;
		}*/
		
	})/////////////////////////////////////////////////////////////////////////////end input
})
//檢測所有輸入欄位
var b_pass_chk=true;

function chk_all_input(form_id){
	b_pass_chk=true;
	form_id=form_id||"";
	form_id=form_id==""?"":"#"+form_id;
	if(form_id==""){return false;}
	form_id=form_id.replace(/##/, "#")
	$(form_id+" input[type!='button']").each(function(){
    	$(this).trigger("blur");
    	//if(!chk_input($(this))){
    	//	b_pass_chk=false;
    	//}
	})
	return b_pass_chk;
}
