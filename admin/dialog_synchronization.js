$(document).ready(function(){
	$("input[type!='button'][type!='checkbox'][type!='redio']").keyup(function(){
		var value = $(this).val();
		var id= $(this).prop("id");
		$("input[id^='"+id+"'][id!='"+id+"']").each(function(){
			$(this).val(value);
		})
	})
	$("input[type='checkbox']").click(function(){
		var checked=$(this).prop("checked");
		var id= $(this).prop("id");
		$("input[id^='"+id+"'][id!='"+id+"']").each(function(){
			$(this).prop("checked",checked);
		})
	})
	$("input[type='radio']").click(function(){
		var id= $(this).prop("id");
		$("input[id^='"+id+"'][id!='"+id+"']").attr("checked",true);
	})
	$("select").click(function(){
		var id= $(this).prop("id");
		$("input[id^='"+id+"'][id!='"+id+"']").val($(this).val());
	})
	
})