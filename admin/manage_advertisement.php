<?php 
    session_start();
    include '../swop/setting/config.php';
    include '../backend/template.php';
    if(!empty($_SESSION['admin']['login_user'])){
        
        //取資料
        $login_fi_no = $_SESSION['admin']['login_fi_no'];
        $login_store = $_SESSION['admin']['login_store'];
        $login_name = $_SESSION['admin']['login_name'];
        $login_permissions = $_SESSION['admin']['login_permissions'];
        $login_user = $_SESSION['admin']['login_user'];
        $login_username = $_SESSION['admin']['login_username'];
        $login_title = $_SESSION['admin']['login_title'];
        $login_php_vars = $_SESSION['admin']['login_php_vars'];
        $menu = $_SESSION['admin']['menu'];
        
        //取資料
        include '../backend/class_pager.php';
        require_once "../swop/library/dba.php";
        $dba = new dba();
        $subject = "advertisement";
        $subject_cht = "廣告";
        $sub_subject = "";
        $sub_subject_cht = "";
		$permissions2visit	= (strpos($login_permissions, $subject."_list")	!==false||$login_permissions=="all")?true:false;
        $permissions2add	= (strpos($login_permissions, $subject."_add")	!==false||$login_permissions=="all")?true:false;
        $permissions2edit	= (strpos($login_permissions, $subject."_edit")	!==false||$login_permissions=="all")?true:false;
        $permissions2del	= (strpos($login_permissions, $subject."_del")	!==false||$login_permissions=="all")?true:false;
        
        $page_data = $dba->query("select distinct `page` from `advertisement`");
        
        foreach($page_data as $per_data)
        {
	    	$tmp_page = $per_data["page"];
	    	switch($tmp_page){
		    	case "main":
		    		$tmp_page_cht = "首頁";
		    	break;
		    	case "brand":
		    		$tmp_page_cht = "品牌大街";
		    	break;
		    	case "member_center":
		    		$tmp_page_cht = "我的跨域瘋";
		    	break;
		    	case "login":
		    		$tmp_page_cht = "登入頁";
		    	break;
		    	case "top":
		    		$tmp_page_cht = "全域頁首";
		    	break;
		    	case "search":
		    		$tmp_page_cht = "熱門關鍵字";
		    	break;
		    	default:
		    		$tmp_page_cht = $tmp_page;
		    	break;
	    	}
	        $show_list_html.="<option value='$tmp_page'>$tmp_page_cht</option>";
	    }
	    $index_data = $dba->query("select `fi_no`,`images` from `advertisement` where `page`='brand' and `index`='0' and `type`='2'");
        
        $img_path = "../public/img/advertisement/";
        $file_size_limit = 1024*1024*1;//1M
        $pic_width_limit	= 0;
		$pic_height_limit	= 0;//不限
		$pic_width = 0;
		$pic_height = 100;
    }else{
        header("Location:index.php");
    }
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title><?php echo $html_title;?></title>
        <?php echo $html_resource;?>
        <link rel="stylesheet" type="text/css" href="../public/css/jquery.datetimepicker.css"/>
        <link rel="stylesheet" href="../backend/css/colpick.css" type="text/css" />
        <script type="text/javascript" src="../backend/js/colpick.js"></script>
        <script type="text/javascript" src="dialog_synchronization.js"></script>
        <script src="../public/js/jquery.datetimepicker.js"></script>
        <script src="date_setting.js"></script>
        <script>
            /******************** user define js ********************/
            var img_path = "<?php echo $img_path; ?>";
            var file_size_limit = "<?php echo $file_size_limit?>";
	        file_size_limit = file_size_limit=="" ?0 :file_size_limit;
            
            var v_name = v_url = v_resize = v_depiction = v_price = v_icon = v_color = "";
            
            var iframe_parameter = "?nohistory=1"
            var iframe_link = "";
            var iframe_target_class = "";
			
			var edit_no 
			
            $(document).ready(function()
            {	
                //ajax
                //filename write in template.js
                function init()
                {	
	                ////////////////////////////////////////////////////////////////////////////////////////////////////////start add
	                if($("#addPanel").length>0)
	                {
	                	var panel_id="addPanel";
	                	$("input.add_btn").unbind("click");
		                $("input.add_btn").click(function()
		                {
		                	if( !chk_all_input(panel_id) )
			                    return;
			                    
		                    var $this		= $("#"+panel_id); 
		                    var page		= $this.find("select[name='add_page']").val();
		                    var type 		= $this.find("input[name='add_type']:checked").val();
		                    var item 		= $this.find("input[name='add_item']").val();
		                    var index 		= $this.find("input[name='add_index']:checked").val();
		                    var url 		= $this.find("input[name='add_url']").val();
		                    var start_date 	= $this.find("input[name='add_start_date']").val();
		                    var end_date 	= $this.find("input[name='add_end_date']").val();
		                    if( start_date > end_date ){alert2("開始日期大於結束日期，請重新輸入。");return ;}
		                    
		                    //console.log("uer:"+administrator_add_user+"\npwd:"+administrator_add_password+"\nname:"+administrator_add_name);
		                    $.post(filename,{
		                			query_type:	"<?php echo $subject;?>_add"
		                		   ,page:page
		                		   ,type:type
		                		   ,item:item
		                		   ,index:index
		                		   ,url:url
		                		   ,start_date:start_date
		                		   ,end_date:end_date
								},function(data){
									
									data=$.trim(data);
									
		                    		if(data=="success")
		                    		{
		                    			alert("新增成功！");
			                    		location.reload();
		                    		}
		                    		console.log(data);
		                    });
		                    
		                });
	                }
	                
	                ////////////////////////////////////////////////////////////////////////////////////////////////////////start del
	                if($("input.del_btn").length>0)
	                {
		                $("input.del_btn").unbind("click");
		                $("input.del_btn").bind('click',function()
		                {
		                    if(!confirm("確定刪除？"))
		                    	return;
		                    
		                    var fi_no = $(this).attr('now_no');
		                    var $data = $("#data_"+fi_no);
		                    var title = $data.attr("title");
		                    var page = $("#show_list_panel").val();
		                    $.post(filename,{
		                    		query_type:"<?php echo $subject;?>_del"
		                    	   ,fi_no:fi_no
		                    	   ,title:title
		                    	   ,page:page
								},function(data){
									data=$.trim(data);
									if(data=="success"){
				                        alert("已刪除！");
				                        location.reload();
			                        }
			                        console.log(data);
		                    });
		                });
	                }
					
					////////////////////////////////////////////////////////////////////////////////////////////////////////edit del
	                //編輯
	                var $panel = $("#editPanel");
	                
	                $("#list_panel [data-open-dialog]").unbind("mouseenter")
	                $("#list_panel [data-open-dialog]").mouseenter(function(){
	                    edit_no = $(this).attr('now_no');
	                    $panel.attr('now_no',edit_no);
	                    //console.log(now_no+"^^"+filename)
	                    ///////// ajax取資料
	                    var b_ajax=$("#data_"+edit_no).data("b_get_ajax")||false;//<--不需要ajax改這裡(fasle=>不需要)//到資料庫分割去取資料
	                    if(b_ajax){
		                    $.post(filename,{
		                    		query_type:"get_member_info",
		                    		fi_no:edit_no
								},function(data){
									data = $.parseJSON(data);
									if(data!==false){
										$("#data_"+edit_no).data("b_get_ajax",false);
										for( key in data){
											$("#data_"+edit_no).attr("edit_"+key,data[key]);
										}
										get_data($panel);
									}
									
		                    });
	                    }else{
		                    get_data($panel);
	                    }
	                })
	                
	                function get_data($panel){
	                
	                	now_no = edit_no
	                	var data_id = "data_"+now_no;               
		                var $data   = $("#"+data_id);
		                ////////////checkbox 更新
	                    $panel.find("input[type='checkbox']").prop('checked',0);
	                    
	                    var arr_name = [];
	                    $panel.find("input[type='checkbox']").each(function(){
		                    var name=$(this).prop("name").replace(/\W+/g, "");
		                    if( !in_array(name,arr_name) )
		                    	arr_name.push(name);
	                    })
	                    function in_array(stringToSearch, arrayToSearch) {
							for (s = 0; s < arrayToSearch.length; s++) {
								thisEntry = arrayToSearch[s].toString();
								if (thisEntry == stringToSearch) {
									return true;
								}
							}
							return false;
						}
						
						for(i=0; i<arr_name.length; i++){
							
							$panel.find("input[type='checkbox'][name^='"+name+"']").each(function(){
		                    	var checkbox_val=$data.attr(name);
		                    	if(checkbox_val=="all"){
			                    	return $(this).prop("checked",true);
		                    	}
			                    if(checkbox_val.search( $(this).val() )>-1){ 
				                    $(this).prop("checked",true);
			                    }
		                    })
						}
						
	                    /////////////radio更新
	                    arr_name = [];
	                    $panel.find("input[type='radio']").each(function(){
		                    var name=$(this).prop("name").replace(/\W+/g, "");
		                    if( !in_array(name,arr_name) )
		                    	arr_name.push(name);
	                    })
	                    for(i=0; i<arr_name.length; i++){
	                    	var name=arr_name[i];
	                    	var radio_val = $data.attr(name);
	                    	//console.log("name:"+name+"\n val:"+radio_val)
							$panel.find("input[name='"+name+"'][value='"+radio_val+"']").prop("checked",true);
							$panel.find("input[name='"+name+"'][value='"+radio_val+"']").trigger("change");
	                    }
	                    
	                    ////////////password update
						$panel.find("input[type='password']").each(function(){
							
							var password=$data.attr( $(this).prop("name") )||"";
							$(this).val(password);
							//console.log(password)
	                    	if($(this).attr("name").search("confirm")>-1){
								$(this).val("");
							}
						})
						
						////////// text
						$panel.find("input[type='text']").each(function(){
							var text_val=$data.attr( $(this).prop("name") )||"";
							$(this).val(text_val);
						})
						
						////////// selected
						$panel.find("select").each(function(){
							$(this).find("option[selected]").attr("selected",false)
							var select_val=$data.attr( $(this).prop("name") )||"";
							//console.log(select_val+"^^"+$(this).prop("name")+"^^"+$(this).prop("id"))
							//console.log($(this).find("option[value='"+select_val+"']").val())
							$(this).find("option[value='"+select_val+"']").attr("selected",true);
						})
						
						////////// upload pic
						$a_file = $data.data("$a_file")||[];
						$panel.find("input[type='file'][class='pic'][name]").each(function(){
							var name	= $(this).prop("name");
		                    
		                    var preview_id		= name+"_preview";
		                    var data_preview_id = data_id+"_"+name;
		                    
		                    var $preview		= $("#"+preview_id);
		                    var $data_preview	= $("#"+data_preview_id);
		                    
		                    $preview.html('');
		                    $preview.append( $data.data(data_preview_id)||$data_preview.html()||"" );
		                    img_setting($preview,now_no)
						})
						///////// not input
						$panel.find("td[id]").each(function(){
							var targer_attr=$(this).prop("id");
							$(this).html($data.attr(targer_attr));
						})
						//////// del_btn
						if($panel.find(".del_btn").length>0){
							$panel.find(".del_btn").attr("now_no",now_no);
						}
						//////// copy
						$panel.find("td[copy]").each(function(){
							var $this = $(this);
							var copy_name = $this.attr("copy");
							if( $data.data(copy_name) != undefined ){
								var $obj = $data.data(copy_name).clone(true);
								$this.html("");
								$this.append($data.data(copy_name));
								$obj.find(":text").each(function(){
									$(this).val($(this).attr("value"))
								})
								
							}else{
								$.post(filename,{
	                    				query_type:	query_type,
			                    		fi_no:		now_no
			                    	},function(data){
										data=$.trim(data);
										if(data!=""){
											$obj = $(data)
											$this.html("")
											$this.append($obj);
											write2data($data,$obj);
											return get_data(now_no,$panel);
										}
			                    });
							}
						})
						
						///////// move
						$panel.find("div[move]").each(function(){
							move_setting($(this));
						})
						
						///////// add_move_text
						$panel.find(".add_move_text").click(function(){
							var $btn = $(this);
							var $new_input = $("<input type='text' index_no='"+now_no+"' url=''>");
							$btn.before($new_input);
			                var $parent = $btn.parent();
			                move_setting($parent);
			                url_setting($new_input);
			                resize_setting($new_input);
			                write2data($data,$parent);
							$new_input.change(function(){write2data($data,$parent)});
						})
						
						///////// color picker
						$panel.find("*[colorpicker]").each(function(){
							$(this).val($data.attr($(this).attr("id"))||"ff0000").css({
								'width':'20px',
								'height':'20px',
								'background':function(){ return '#'+$(this).val(); }
							}).colpick({
								color:$(this).val(),
								layout:'hex',
								colorScheme:'dark',
								submit:0,
								onChange:function(hsb,hex,rgb,el,bySetColor) {
									var name = $(el).attr("name")||$(el).attr("id");
									$this = $("*[name='"+name+"']")||$("#"+name);
									$this.css('background','#'+hex);
									if(!bySetColor){
										$this.val(hex);
										$data.attr(name,hex);
									}
								}
							}).click(function(){
								$("div.colpick").css("z-index",1);
							});
						})
						
						///////// url setting
						//$panel.find("*[url]").each(function(){
						//	url_setting($(this));
						//})
						
						///////// resize setting
						//$panel.find("*[resize]").each(function(){
						//	resize_setting($(this));
						//})
						
						// url resize name depiction price icon colorpicker
						var selector = get_selector();
						
						if(selector!="")
						{
							$panel.find(selector).each(function(){
								$(this).unbind("dblclick");
								$(this).bind("dblclick",function(){
									show_detail_setting($(this),$data)
								})
							})
						}
						
						
						//////// textarea
						$panel.find("textarea").each(function(){
							
							var text_val = $data.data( $(this).prop("name") )|| $data.attr( $(this).prop("name") ) ||"";
							$(this).html(text_val)//.jqteVal(text_val);
							//console.log($(this).prop("name")+"^^"+text_val);
							
						})
						
	                }
	                
	                // dblclick call dialog
	                /*function url_setting($obj)
	                {
		                if($("div#url_edit_panel").length==0)return;
		                $obj.unbind("dblclick");
		                $obj.dblclick(function(){
							$this = $(this);
			                $("div#url_edit_panel").css({
				                "left":function(){
					                if( mouse_x+30+$(this).width()>$(window).width() )
					                	return mouse_x-30-$(this).width();
					                else
					                	return mouse_x+30;
				                },
				                "top":mouse_y
			                }).show().find("input:text:first").focus();
			                $("div#url_edit_panel").find("input#edit_url").val($this.attr("url")).change(function(){
				                $this.attr("url",$(this).val())
				                $this.trigger("change");
			                })
			                $("#dialog_content").bind("scroll",function(){
				                $("div#url_edit_panel").hide();
				                $(this).unbind("click")
				                $(this).unbind("scroll")
			                })
			                $("#dialog_content").bind("click",function(){
				                $("div#url_edit_panel").hide();
				                $(this).unbind("click")
				                $(this).unbind("scroll")
			                })
			                
		                })
	                }*/
	                // dblclick call dialog
	                /*function resize_setting($obj)
	                {
		                if($("div#resize_edit_panel").length==0)return;
		                $obj.unbind("dblclick");
		                $obj.dblclick(function(){
			                
			                $this = $(this).width()==0?$obj:$(this);
			                
			                $("div#resize_edit_panel").css({
				                "left":function(){
					                if( mouse_x+30+$(this).width()>$(window).width() )
					                	return mouse_x-30-$(this).width();
					                else
					                	return mouse_x+30;
				                },
				                "top":mouse_y
			                }).show().find("input:text:first").focus();
			                
			                $this.width($this.width());
			                $("div#resize_edit_panel").find("input#edit_width").unbind("change");
			                $("div#resize_edit_panel").find("input#edit_width").val($this.width()).change(function(){
				                $this.width($(this).val());
			                })
			                
			                $this.height($this.height());
			                $("div#resize_edit_panel").find("input#edit_height").unbind("change");
			                $("div#resize_edit_panel").find("input#edit_height").val($this.height()).change(function(){
				                $this.height($(this).val());
				                
			                })
			                
			                $("#dialog_content").bind("scroll",function(){
				                $("div#resize_edit_panel").hide();
				                $(this).unbind("click");
				                $(this).unbind("scroll");
				                $("#dialog_bg").unbind("click");
				                $this.attr("modify",true).removeAttr("modify");//觸發change
			                })
			                
			                $("#dialog_content").bind("click",function(){
				                $("div#resize_edit_panel").hide();
				                $(this).unbind("click");
				                $(this).unbind("scroll");
				                $("#dialog_bg").unbind("click");
				                $this.attr("modify",true).removeAttr("modify");//觸發change
			                });
			                
			                $("div[id$=dialog_bg]").click(function(){
				                $("div#resize_edit_panel").hide();
				                $(this).unbind("click");
				                $("#dialog_content").unbind("click");
				                $("#dialog_content").unbind("scroll");
				                $this.attr("modify",true).removeAttr("modify");//觸發change
			                });
			                
		                })
	                }*/
	                //無span之圖片(原有)包裝後加上span[default]，點下會進入$a_remove_file[]，剩下的span(新加入)點擊後加入$a_file[]
	                function img_setting($preview,now_no)
	                {
	                	var total = $preview.find("img").length;
		                var $add_img_btn = $preview.parent().children("input[type='file']");
		                //console.log(total+"^^"+$add_img_btn.attr("max_len")+$preview.html());
		                if( total>=$add_img_btn.attr("max_len") ){
			                $add_img_btn.unbind("click");
			                $add_img_btn.hide()
		                }else{
			                $add_img_btn.bind("click");
			                $add_img_btn.show()
		                }
		                
		                var name	= $preview.attr("btn_name");
		                var data_id = "data_"+now_no
	                    var data_preview_id = data_id+"_"+name
	                    
	                    var $data 		= $("#"+data_id);
	                    var $a_file		= $data.data("$a_file")||[];
	                    
		                $preview.find("img:not([new])").each(function(index){
		                    $img = $(this);
		                    if( $img.parent().children("span").length==0 ){//原先圖片
			                    //console.log("img_parent.html:\n"+$img.parent().html())
			                    $img.wrap("<div style='display:inline-block;text-align:center;margin-bottom:10px;'></div>");
                                $img.parent().append("<br/><span default class='bg shadowRoundCorner' style='padding:3px;cursor:pointer;color:white'>delete</span>");
                            }
	                    })
	                    $preview.find("img").change(function(){$data.data(data_preview_id,$(this).parent().parent().html());})
	                    //console.log($preview.html());
	                    // img
						$preview.find("span[default]").click(function(){
							$preview = $(this).parent().parent();
                            var tmp_arr = [];
                            $img = $(this).parent().find("img");
                            src = $img.prop("src")||"";
                            tmp_arr["src"]	 = src;
                            tmp_arr["title"] = src.substring(src.lastIndexOf('/')+1);
                            $a_remove_file = $data.data("$a_remove_file")||[]
                            $a_remove_file.push(tmp_arr);
                            $(this).parent().remove();
                            //console.log(src);
                            img_setting($preview,now_no)
                            $data.data("$a_remove_file",$a_remove_file);
                            $data.data(data_preview_id,$preview.html());
                        })
                        $preview.find("span:not([default])").click(function(){
	                        $preview = $(this).parent().parent();
		                    $(this).parent().remove();
		                    var now_len = $a_file.length;
		                    for( k=0 ; k<now_len ; k++ ){
		                    	try{
		                    		var title=unescape($(this).parent().find("img").attr("title"));
	                            	if( $a_file[k].fname == title ){
	                                    $a_file.splice(k,1);
	                                    break;
	                                }
	                        	}catch(error){
	                        	}
		                    }
		                    img_setting($preview,now_no)
		                    $data.data("$a_file",$a_file);
		                    $data.data(data_preview_id,$preview.html());
	                    })
	                    
	                    $data.data(data_preview_id,$preview.html());
						//console.log(data_preview_id+"^"+$data.data(data_preview_id))
	                }
	                //所有input[type='text']後面加上上移下移箭頭
	                function move_setting($obj)
	                {//每次箭頭重新生成數量多會耗效能，但寫法最直覺
	                	
	                	var now_no = $obj.attr("now_no");
						var $data = $("#data_"+now_no)
						
		                var total = $obj.find("input[type='text']").length;
		                var $add_text_btn = $obj.find(":button");
		                if( total>=$add_text_btn.attr("max_len") ){
			                $add_text_btn.unbind("click");
			                $add_text_btn.hide()
		                }else{
			                $add_text_btn.bind("click");
			                $add_text_btn.show()
		                }
		                var $del_btn = $("<span del>✘</span>").css({
		                		"position":"relative",
		                		"opacity": 0,
			                	"color":"#fff",
			                	"background-color":"#333",
			                	"height":$obj.find("input[type='text']").height(),
			                	"width":15,
			                	"margin":"0 5px 0 -15px",
			                	"z-index":2
		                	})
						var $move_up = $("<span move_up>⬆︎</span>").css("margin","0 5px 0 5px");
						var $move_down = $("<span move_down>⬇︎</span>").css("margin","0 5px 0 5px");
						$obj.find("span").remove();
						
						$obj.find("input[type='text']").each(function(index){
							if( $(this).parent().prop('class') != "move_container"){
								$container = $("<div class='move_container'></div>");
								$(this).after($container);
								$container.append($(this));
							}
							if( total<= 1 )return;
							//console.log(index+$(this).val())
							if(index==0){
								$(this).after($move_down.clone());
							}else if(index == total-1){
								$(this).after($move_up.clone());
							}else{
								$(this).after($move_down.clone()).after($move_up.clone());
							}
							$(this).after($del_btn.clone());
							
							$(this).parent().hover(function(){
									$(this).find("span[del]").clearQueue().stop().css("opacity",0).animate({"opacity":1},100)//toggle("slide",{"direction":"right"});
								},function(){
									$(this).find("span[del]").animate({"opacity":0},50)////.toggle("slide",{"direction":"left"});
							})
							$(this).parent().find("span[del]").click(function(){
								var $parent = $(this).parent();
								var $parent_parent = $parent.parent(); 
								$parent.remove();
								move_setting($parent_parent)
							}).css({"cursor":"pointer"})
							$(this).parent().find("span[move_up]").click(function(){ 
								$(this).parent().prev().before ($(this).parent());
								
								move_setting($(this).parent().parent())
							}).css({"cursor":"pointer"})
							$(this).parent().find("span[move_down]").click(function(){ 
								$(this).parent().next().after($(this).parent());
								
								move_setting($(this).parent().parent())
							}).css({"cursor":"pointer"})
							$(this).change(function(){write2data($data,$(this).parent().parent());})
						})
						
						write2data($data,$obj);
						
	                }
	                
	                //該div寫入data內，並自動帶入edit
	                $("div[class][now_no]").each(function(){
						var $this = $(this);
						var now_no = $this.attr("now_no");
						var $data = $("#data_"+now_no);
						if($data.length==0)return;
						
						$this.find("input[type='text']").change(function(){
							write2data($data,$this)
						})
						
						write2data($data,$this);
						$this.remove();
					});
					//該textarea內容寫入data內
	                $("textarea[class][now_no]").each(function(){
						var $this = $(this);
						var now_no = $this.attr("now_no");
						var class_name = $this.attr("class");
						var $data = $("#data_"+now_no);
						if($data.length==0)return;
						$data.data(class_name,$this.val());
						//console.log(class_name+"^^"+$this.val())
						$this.remove();
					});
	                
	                //及時更新暫存資料(hidden暫存，沒存進資料庫)
	                $panel.find("textarea").unbind("change")
	                $panel.find("textarea").change(function(){
		                var now_no=$panel.attr('now_no');
		                $("#data_"+now_no).data($(this).prop("name"),$(this).val() );
	                })
	                
	                
	                $panel.find("input[type!='button'][type!='checkbox'][type!='radio']").unbind("change")
	                $panel.find("input[type!='button'][type!='checkbox'][type!='radio']").change(function(){
		                var now_no=$panel.attr('now_no');
		                $("#data_"+now_no).attr($(this).prop("name"),$(this).val() );
	                })
	                
	                $panel.find("input[type='radio']").unbind("click");
	                $panel.find("input[type='radio']").click(function(){
	                    var now_no	= $panel.attr('now_no');
	                    var name	= $(this).prop("name");
	                    var val	=  $("input[name='"+name+"']:checked").val();
	                    $("#data_"+now_no).attr(name,val);
	                });
	                //全選
	                $panel.find("input[value='all']:checkbox").unbind("click")
	                $panel.find("input[value='all']:checkbox").click(function(){
						var name=$(this).prop("name").replace(/\W+/g, "");
						var check=$(this).prop("checked");
						$("input[name^='"+name+"']").prop("checked",check);
						
						var now_no=$panel.attr('now_no');
						if(check)
		                	$("#data_"+now_no).attr( name,$(this).val() );
		                else
		                	$("#data_"+now_no).attr( name,"" );
					})
					//checkbox 所有項目選取，全選一並選取。有項目取消，全選項目一並取消。任一動作直接存入data內
					$panel.find("input[value!='all']:checkbox").unbind("click")
					$panel.find("input[value!='all']:checkbox").click(function(){
						var name=$(this).prop("name").replace(/\W+/g, "");
						if($panel.find("input[value!='all'][name^='"+name+"']:checkbox").length==$panel.find("input[value!='all'][name^='"+name+"']:checkbox:checked").length){
							//$("input[value='all']").prop("checked",true);
							$panel.find("input[value='all']:checkbox").trigger("click");
						}else{
							var now_no=$panel.attr('now_no');
							var tmp_checkbox_val="";
							var dot=","
							$panel.find("input[value='all'][name^='"+name+"']").prop("checked",false);
							
							$panel.find("input[value!='all'][name^='"+name+"']:checked").each(function(){
								tmp_checkbox_val+=$(this).val()+dot;
							})
							//console.log(tmp_permissions);
							$("#data_"+now_no).attr( name,tmp_checkbox_val.slice( 0,-1 ) );
						}
					})
					
					$panel.find("select").unbind("change");
					$panel.find("select").change(function(){
						var now_no	= $panel.attr('now_no');
	                    var name	= $(this).prop("name");
	                    var val	=  $(this).val();
	                    //console.log(name+"^^"+val)
	                    $("#data_"+now_no).attr(name,val);
					})
					
	                //加上預覽ＤＩＶ
	                $panel.find("input[type='file'][class='pic'][name]").each(function(){
	                	var $this = $(this);
	                	var name = $this.prop("name");
		                var preview_id = name+"_preview";
		                if($("div#"+preview_id).length==0)
		                	$this.before("<div id='"+preview_id+"' btn_name='"+name+"'></div>");
	                })
	                //所有開頭是data_都隱藏
	                $("*[id^='data_']").hide();
	                //圖片上傳
	                $panel.find("input[type='file'][class='pic'][name]").unbind("change")
	                $panel.find("input[type='file'][class='pic'][name]").change(function(evt){
	                    
	                    var $btn	= $(this);
	                    var name	= $btn.prop("name");
	                    var width	= "<?php echo $pic_width?>";
	                    width = width=="" ?0 :width;
	                    
	                    var height	= "<?php echo $pic_height?>";
	                    height = height=="" ?0 :height;
	                    
	                    var max_width = "<?php echo $pic_width_limit?>";
	                    max_width = max_width=="" ?0 :max_width;
	                    
	                    var max_height = "<?php echo $pic_height_limit?>";
	                    max_height = max_height=="" ?0 :max_height;
	                    
	                    var file_size_limit = "<?php echo $file_size_limit?>";
	                    file_size_limit = file_size_limit=="" ?0 :file_size_limit;
	                    
	                    var attr 	= v_url + v_resize + v_name + v_depiction + v_price + v_icon + v_color;
	
	                    var now_no	= $panel.attr('now_no');
	                    var data_id = "data_"+now_no;
	                    var preview_id	= name+"_preview";
	                    var data_preview_id = data_id+"_"+name;
	                       
	                    var $data   = $("#"+data_id);
	                    var $preview= $("#"+preview_id);
	                    var $a_file = $data.data("$a_file")||[];//存放所有檔案
	                    
	                    var max_len = $btn.attr("max_len");
	                    var new_len = $btn[0].files.length;
	                    var old_len = $a_file.length;
	                    var exists_len = $preview.find("img").length;
	                    
	                    $preview.html($data.data(data_preview_id)||"");
	                    
	                    for(var i=0, f; f=evt.target.files[i]; i++) {
	                        if(!f.type.match('image.*'))
	                        {
		                        continue;
	                        }
	                        
	                        if( f.size > file_size_limit )
	                        {
		                        alert2(f.name+"檔案超過1MB，請重新上傳。");
		                        if(evt.target.files[i+1]==undefined)
			                        evt.target.value="";
		                        continue;
	                        }
	                        
	                        if(i+1+exists_len>max_len)
	                        {
		                        alert2("上傳數量已超過上限。");break;
	                        }
		                        
	                        
	                        var reader = new FileReader();
	                        reader.onload = (function(theFile)
	                        {
	                            return function(e)
	                            {
		                            //new屬性作用在於，圖檔先進preview，img_setting會偵測到沒有span，給加上default span
		                            $preview.append('<img new class="thumb" src="'+e.target.result+'" title="'+escape(theFile.name)+'" '+attr+' />');
	                                $preview.find("img:last")[0].onload = function(){
		                                
	                                    var $this = $(this);
	                                    var b_return = false;
	                                    
	                                    if( (max_width>0&&$this.width()>max_width) )
		                                    alert2(this.name+"寬度超過"+max_width+"，請重新上傳。");
		                                    
	                                    if( max_height>0&&$this.height()>max_height )
		                                    alert2(this.name+"長度超過"+max_height+"，請重新上傳。");
		                                    
		                                if( b_return )
		                                {
			                                $this.remove();
		                                    return;
		                                }
		                                
	                                    var $preview = $this.parent();
	                                    var title = $this.attr("title");
	                                    var filename = new Date().getTime()+"_"+$this.index()+"_+"+title+"."+ title.substring( title.lastIndexOf('.')+1);//only one
										$this.attr("title",filename);
										theFile.fname = filename;//相對應值
										
	                                    var onload_len = $preview.find("img").length;
	                                    $this.css({
	                                        "border":"1px solid gray",
	                                        "margin":"5px"
	                                    });
	                                    
	                                    if(width>0)
	                                    	$this.width(width);
	                                    if(height>0)
	                                    	$this.height(height);
	                                    $this.wrap("<div style='display:inline-block;text-align:center;margin-bottom:10px;'></div>");
	                                    $this.parent().append("<br/><span class='bg shadowRoundCorner' style='padding:3px;cursor:pointer;color:white'>delete</span>");
	                                    	                                    
	                                    $a_file.push(theFile);
	                                    //console.log("onload_len:"+onload_len+"^^old_len:"+old_len+"^^max_len:"+max_len);
	                                    if($a_file.length-old_len == new_len || onload_len==max_len){
		                                    img_setting($preview,now_no);
		                                    
											var selector = get_selector();
											
											if(selector!="")
											{
												$preview.find(selector).each(function(){
													$(this).unbind("dblclick");
													$(this).bind("dblclick",function(){
														show_detail_setting($(this),$data)
													})
												})
											}
	                                    	$data.data("$a_file",$a_file);
	                                    	$data.data(data_preview_id,$preview.html());
		                                    $btn.val("");
	                                    }
	                                    
	                                }
	                                
	                            };
	                            
	                        })(f);
	                        
	                        reader.readAsDataURL(f);
	                    }
	                    
	                });
	                
	                ////////////////////////////////////////////////////////////////////////////////////////////////////////start edit
	                $panel.find("input.edit_btn").unbind("click");
	                $panel.find("input.edit_btn").click(function(){
	                    //權限
	                    if(!chk_all_input("#editPanel")){
		                    return;
	                    }
						var fi_no = $.trim($panel.attr("now_no"));
						var data_id = "data_"+fi_no;
						$data = $("#data_"+fi_no);
						
						var $a_file 		= $data.data("$a_file")||[];
						var $a_remove_file 	= $data.data("$a_remove_file")||[];
						var $d_file			= $data.data("add_filed_icon")||[];
						
						var page 		= $("#show_list_panel").val();
						var type		= $data.attr("edit_type");
						var item		= $data.attr("edit_item");
						var index 		= $data.attr("edit_index");
						var url 		= $data.attr("edit_url");
	                    var weights 	= $data.attr("edit_weights");
	                    var show 		= $data.attr("edit_show");
	                    var start_date	= $data.attr("edit_start_date");
	                    var end_date	= $data.attr("edit_end_date");
	                    var d_name 		= $data.data("d_name")||"";
	                    var d_depiction = $data.data("d_depiction")||"";
	                    var d_price 	= $data.data("d_price")||"";
	                    var d_color		= $data.data("d_color")||"";
	                    var d_url		= $data.data("d_url")||"";
	                    var d_icon 		= "";
	                    if( ($data.data("srcd_icon")||[]).length >0 )
	                    	for( key in $data.data("srcd_icon") )
	                    		if( key == -1)
	                    			d_icon = $data.data("srcd_icon")[-1].substring($data.data("srcd_icon")[-1].lastIndexOf('/')+1);
	                    
            			if( ($data.data("add_filed_icon")||[]).length >0 )
            				d_icon = $data.data("add_filed_icon")[0].name;
	                    				
	                    var form_data 	= new FormData();
	                    
	                    if($a_file.length>0)
	                    {
		                    form_data.append('file[0]', $a_file[0] );
	                    }
						if($d_file.length>0)
						{
							form_data.append('file[1]', $d_file[0] );
						}
						
						var now_len = $a_remove_file.length;
						for( j=0 ; j<now_len ; j++ )
						{
							form_data.append( 'a_remove_file['+j+']',$a_remove_file[j]["title"] );
						}
						
						now_len = ($data.data("del_src")||[]).length;
						for( j=0 ; j<now_len ; j++ )
						{
							form_data.append( 'del_src['+j+']',$data.data("del_src")[j] );
						}
						
						
						//return;
						form_data.append("query_type",	"<?php echo $subject;?>_edit");
						form_data.append("fi_no",		fi_no);
						form_data.append("page",		page);
						form_data.append("type",		type);
						form_data.append("item",		item);
						form_data.append("index",		index);
						form_data.append("url",			url);
						form_data.append("weights",		weights);
						form_data.append("show",		show);
						form_data.append("start_date",	start_date);
						form_data.append("end_date",	end_date);
						
						form_data.append("d_name",		d_name);
						form_data.append("d_depiction",	d_depiction);
						form_data.append("d_price",		d_price);
						form_data.append("d_url",		d_url);
						form_data.append("d_color",		d_color);
						form_data.append("d_icon",		d_icon);
						
	                    
						$.ajax({
							url: filename,
							dataType: 'text',
							cache: false,
							contentType: false,
							processData: false,
							data: form_data,                         
							type: 'post',
							success: function(data){
								data=$.trim(data);
								if(data=="success"){
			                        alert("已更新！");
			                        location.reload();
		                        }
		                        //alert2(data);
		                        console.log(data)
							}
						});
											
	                });
	                
				}
                //取得子屬性selector
                function get_selector(){
	                var selector = "";
					selector += v_url		!=""?",*["+v_url		+"]":"";
					selector += v_resize	!=""?",*["+v_resize		+"]":"";
					selector += v_name		!=""?",*["+v_name		+"]":"";
					selector += v_depiction	!=""?",*["+v_depiction	+"]":"";
					selector += v_price		!=""?",*["+v_price		+"]":"";
					selector += v_icon		!=""?",*["+v_icon		+"]":"";
					selector += v_color		!=""?",*["+v_color		+"]":"";
					
					if(selector!="")return selector.substr(1);
					else return "";
                }
                //同名class寫回data
                function write2data($data,$obj){
	                var this_class = $obj.attr("class")||"";
	                if(this_class=="")return;
	                var $copy_obj = $obj.clone(true);
	                $data.data(this_class,$copy_obj);
                }
                
                //子屬性編輯視窗
                $("body").append(
                	"<div id='detail_setting' class='shadowRoundCorner' style='position:absolute;display:inline-block;padding:5px;background:rgba(255,255,255,0.9);border:3px dashed #CCC;width:300px;text-align:center;' float>"+
	                    "<div style='text-align:center;color:white;padding:5px;margin-bottom:5px;' class='bg shadowRoundCorner'>"+
	                        "<span style=‘color:white;'>細節設定</span>"+
	                    "</div>"+
	                    "<div id='detail_content'>"+
                		"</div>"+
	                "</div>");
                $("div#detail_setting").css({
	                "position":"absolute",
	                "z-index":10
                }).hide();
                
				/////////////////////////////////////////////////////////////////////////////for this page
				
				//前台位置顯示
				$("input.preview_adpos").click(function()
				{
					var $dialog = $("#dialog_content")
					if( $(this).attr("class").search("add")>-1 )
					{
						var page = $("#add_page").val();
						var type = $("input[name='add_type']:checked").val();
						var index = $("input[name='add_index']:checked").val();
					}else if( $(this).attr("class").search("edit")>-1 )
					{
						var page = $("#show_list_panel").val();
						var type = $dialog.find("input[name='edit_type']:checked").val();
						var index = $dialog.find("input[name='edit_index']:checked").val();
					}
					
					if( page=='main')
					{
						iframe_link = "http://www.crazy2go.com/"+iframe_parameter;
						iframe_target_class = ".ad_main";
					}
					else if( page=='brand')
					{
						iframe_link = "http://www.crazy2go.com/brand/"+iframe_parameter;
						if( type==1 )
						{
							iframe_target_class = ".ad_circle";
						}else if( type==2 )
						{
							if( index==0 )
							{
								iframe_target_class = ".ad_center";
							}else
							{
								iframe_target_class = ".right_ad";
							}
						}
						
					}
					else if( page=='member_center')
					{
						iframe_link = "http://www.crazy2go.com/member/center_demo/"+iframe_parameter;
						iframe_target_class = ".ad_tab";
					}
					else if( page=='login')
					{
						iframe_link = "http://www.crazy2go.com/member/"+iframe_parameter;
						iframe_target_class = ".ad";
					}
					else if( page=='top')
					{
						iframe_link = "http://www.crazy2go.com/"+iframe_parameter;
						iframe_target_class = ".atopd";
					}
					else if( page=='search')
					{
						iframe_link = "http://www.crazy2go.com/"+iframe_parameter;
						iframe_target_class = ".asearchd";
					}
					
					
					show_red_region(iframe_link, iframe_target_class);
				})
				function show_red_region(href, region){
					
					var $dialog = $("#dialog_content").css("position","relative");
                    $dialog.append("<iframe src='"+href+"' id='ifr_preview_adpos'></iframe>");
                    $dialog.find("table").hide();
                    $dialog.append($("<input enabled='false' value='返回編輯' onclick='$(\"#ifr_preview_adpos\").remove();$(this).remove();$(\"#dialog_content\").find(\"table\").show()'/>").css({
	                    "position":"absolute"
	                    ,"bottom":"50px"
	                    ,"text-align":"center"
	                    ,"cursor":"pointer"
                    }));
                    var $iframe = $("#ifr_preview_adpos")
                    $iframe.css({
	                    "position":"absolute"
	                    ,"display":"inline-block"
	                    ,"top":"0px"
	                    ,"left":"0px"
                        ,"width":"100%"
                        ,"height":"100%"
                        ,"margin-top":"auto"
                        ,"margin-left":"auto"
                        ,"background":"white"
                    }).show(1000);
                    $("#ifr_preview_adpos").load(function(){
                        var $rect = $iframe.contents().find(region);
                        var $body = $iframe.contents().find("body");
                        $body.css({
                            "background":"white"
                        })
                        $body.append($("<div id='cover'></div>").css({
                            "position":"absolute",
                            "display":"inline-block",
                            "top":"0px",
                            "left":"0px",
                            "width":$iframe.contents().width()+"px",
                            "height":$iframe.contents().height()+"px",
                            "background":"rgba(0,0,0,0.6)",
                            "z-index":"99999"
                        }));
                        var mt = parseInt($rect.css("margin-top").replace(/px/,""));
                        var pb = parseInt($rect.css("padding-bottom").replace(/px/,""));
                        $($('#ifr_preview_adpos').contents()).scroll(function(){
	                        $body.find("#cover").css({
	                            "width":$iframe.contents().width()+"px",
	                            "height":$iframe.contents().height()+"px"
	                        })
	                        $body.find(".red_rect").each(function(i){
		                        $(this).css({
		                        	"top":($rect.eq(i).offset().top+ parseInt($rect.eq(i).css("padding-top").replace(/px/,"")) ) +"px",
									"left":$rect.eq(i).offset().left+ parseInt($rect.eq(i).css("padding-left").replace(/px/,"")) +"px"
								})
	                        })
                        })
                        var st = setTimeout(function(){
	                        $($('#ifr_preview_adpos').contents()).trigger("scroll");
	                        $($('#ifr_preview_adpos').contents()).scrollLeft(
		                        function(){
			                        if( (Number($rect.offset().left)+Number($rect.width()))<$iframe.width())
			                        {
				                        return 0;
			                        }
			                        else
			                        {
				                        return $rect.offset().left+$rect.width()-$iframe.width();
			                        }
		                        }
	                        );
	                        $($('#ifr_preview_adpos').contents()).scrollTop(
	                        	function(){
		                        	if( (Number($rect.offset().top)+Number($rect.height()))<$iframe.height())
		                        	{
			                        	return 0;
		                        	}
		                        	else
		                        	{
			                        	return $rect.offset().top-$iframe.height()+$rect.height()
		                        	}
		                        }
		                    );
	                    }, 200);
                        $rect.each(function(i){
		                    //console.log($rect.eq(i).offset().top+"^^"+$rect.eq(i).offset().left)
	                        $body.append($("<div class='red_rect'>圖片區塊</div>").css({
	                            "position":"absolute",
	                            "display":"inline-block",
	                            "top":($rect.eq(i).offset().top+mt)+"px",
	                            "left":$rect.eq(i).offset().left+"px",
	                            "width":$rect.eq(i).width()+"px",
	                            "height":$rect.eq(i).height()+"px",
	                            "line-height":$rect.eq(i).height()+"px",
	                            "text-align":"center",
	                            "background":"rgba(255,0,0,0.5)",
	                            "color":"white",
	                            "z-index":"100000"
	                        }));
                        })
                        
                    });
                }
				
				//////////////////////////////////////////////////////////////////////////////////// init
                
				if( $("#show_list_panel").length == 1 )
				{
					$("#show_list_panel").change(function(){
						show_category_attr($(this),$(this).val());
					})
					//show_category_attr($("#show_list_panel"),$("#show_list_panel").val());
					function show_category_attr($obj,fi_no){
						$("#list_panel").remove();
						$.post(filename,{
	                    		query_type:			"<?php echo "get_{$subject}_info";?>",
	                    		fi_no:				fi_no,
	                    		permissions2del:	<?php echo !$permissions2del?"false":"true";?>,
	                    		permissions2edit:	<?php echo !$permissions2edit?"false":"true";?>
							},function(data){
								data=$.trim(data);
								$table = $(data);
								$obj.after($table);
								init_style( $("#list_panel,#list_panel *") );
								init();
	                    });
					}
					var query_type = "<?php echo "get_{$sub_subject}_info";?>";
				}else
				{
					init()
				}
				
				
            });//jquery
            
            //子屬性轉換
            function chg_attr($obj,mode){
				mode = mode || 0 ;
				$data = $("#data_"+edit_no);
				remove_attr($obj);
				if(mode==0)
				{
					$obj.attr("d_name",true);
					$obj.attr("d_depiction",true);
					$obj.attr("d_price",true);
					$obj.attr("d_url",true);
				}else if(mode==1)
				{
					$obj.attr("d_icon",true);
					$obj.attr("d_color",true);
					$obj.attr("d_name",true);
				}
				$obj.unbind("dblclick");
				$obj.bind("dblclick",function(){
					show_detail_setting($(this),$data)
				})
			}
            //子屬性清除
			function remove_attr($obj){
				$obj.removeAttr("d_name");
				$obj.removeAttr("d_depiction");
				$obj.removeAttr("d_price");
				$obj.removeAttr("d_url");
				$obj.removeAttr("d_icon");
				$obj.removeAttr("d_color");
			}
			//子屬性編輯
			function show_detail_setting($obj,$data)
            {
                $detail_content = $("#detail_content");
                $detail_setting_panel = $detail_content.parent();
                
                if($detail_content.length==0)return;
                
                if( 	$obj.attr("d_url")		==	undefined 
	                && 	$obj.attr("d_name")		==	undefined 
	                && 	$obj.attr("d_depiction")==	undefined 
	                && 	$obj.attr("d_price")	==	undefined 
	                && 	$obj.attr("d_resize")	==	undefined 
	                && 	$obj.attr("d_icon")		==	undefined
	                && 	$obj.attr("d_pic")		==	undefined
	                && 	$obj.attr("d_color")==	undefined )return;
	            
	            //$obj.unbind("dblclick")
	            $detail_content.html("");
	            $detail_content.append("<table class='table-v'></table>");
	            $detail_panel = $detail_content.children("table");
	            
	            //d_name編輯
	            if($obj.attr("d_name")!=undefined)
            	{
	            	$detail_panel.append(
	            		"<tr>"+
            				"<td>名稱</td>"+
            				"<td><input type='text' class='d_name' style='width:95%' /></td>"+
            			"</tr>"
	            	)
	            	$detail_panel.find(".d_name").val($data.data("d_name")).unbind("change");
	            	$detail_panel.find(".d_name").change(function(){
		            	$data.data("d_name",$(this).val());
	            	});
	            }
	            //d_depiction編輯
	            if($obj.attr("d_depiction")!=undefined)
            	{
	            	$detail_panel.append(
	            		"<tr>"+
            				"<td>描述</td>"+
            				"<td><input type='text' class='d_depiction' style='width:95%' /></td>"+
            			"</tr>"
	            	)
	            	$detail_panel.find(".d_depiction").val($data.data("d_depiction")).unbind("change");
	            	$detail_panel.find(".d_depiction").change(function(){
		            	$data.data("d_depiction",$(this).val());
	            	});
	            }
	            //圖片寬高編輯
	            if($obj.attr("d_resize")!=undefined)
            	{
	            	$detail_panel.append(
	            		"<tr>"+
            				"<td>寬</td>"+
            				"<td><input type='text' class='d_width' style='width:95%' /></td>"+
            			"</tr>"+
            			"<tr>"+
            				"<td>高</td>"+
            				"<td><input type='text' class='d_height' style='width:95%' /></td>"+
            			"</tr>"
	            	)
	            	$detail_panel.find(".d_width").val($obj.width()).unbind("change");
	            	$detail_panel.find(".d_width").change(function(){
		            	$obj.width($(this).val());
	            	});
	            	$detail_panel.find(".d_height").val($obj.height()).unbind("change");
	            	$detail_panel.find(".d_height").change(function(){
		            	$obj.height($(this).val());
	            	});
	            }
	            //d_price編輯
	            if($obj.attr("d_price")!=undefined)
            	{
	            	$detail_panel.append(
	            		"<tr>"+
            				"<td>價錢</td>"+
            				"<td><input type='text' class='d_price' style='width:95%' /></td>"+
            			"</tr>"
	            	);
	            	$detail_panel.find(".d_price").val($data.data("d_price")).unbind("change");
	            	$detail_panel.find(".d_price").change(function(){
		            	$data.data("d_price",$(this).val());
	            	});
	            }
	            //d_url編輯
            	if($obj.attr("d_url")!=undefined)
            	{
	            	$detail_panel.append(
	            		"<tr>"+
            				"<td>網址</td>"+
            				"<td><input type='text' class='d_url' style='width:95%' /></td>"+
            			"</tr>"
	            	);
	            	$detail_panel.find(".d_url").val($data.data("d_url")).unbind("change");
	            	$detail_panel.find(".d_url").change(function(){
		            	$data.data("d_url",$(this).val());
	            	});
	            }
	            //子圖片上傳
	            if($obj.attr("d_icon")!=undefined || $obj.attr("d_pic")!=undefined)
            	{
	            	if($obj.attr("d_icon")!=undefined)
	            	{
		            	$detail_panel.append(
		            		"<tr>"+
                				"<td>Icon<br>(20x25)</td>"+
                				"<td>"+
                					"<div class='d_icon_preview'></div>"+
                					"<input type='file' class='d_icon' max_len='1' style='width:90%'/>"+
                				"</td>"+
                			"</tr>"
		            	);
	            	}
	            	if($obj.attr("d_pic")!=undefined)
	            	{
		            	$detail_panel.append(
		            		"<tr>"+
                				"<td>Icon<br>(20x25)</td>"+
                				"<td>"+
                					"<div class='d_pic_preview'></div>"+
                					"<input type='file' class='d_pic' max_len='99' multiple='multiple' style='width:90%'/>"+
                				"</td>"+
                			"</tr>"
		            	);
	            	}
	            	
	            	$detail_panel.find(".d_pic,.d_icon").each(function(){
		            	var class_name = $(this).attr("class");
		            	var $preview = $detail_panel.find("."+class_name+"_preview");
		            	if( Array.isArray($data.data("src"+class_name)))
		            		for( key in $data.data("src"+class_name) )
			            		if( key<0 )
				            		$preview.append("<img src='"+$data.data("src"+class_name)[key]+"'/>"); 
								else
				            		$preview.append("<img new src='"+$data.data("src"+class_name)[key]+"'/>");
		            	
		            	$data.data("add_file"+class_name,$data.data("add_file"+class_name)||[]);
		                $data.data("src"+class_name,$data.data("src"+class_name)||[]);
		                $data.data("del_src",$data.data("del_src")||[]);
		            	
		            	d_img_setting($(this),$data);
	            	})
	            	$detail_panel.find(".d_pic,.d_icon").unbind("change");
	            	$detail_panel.find(".d_pic,.d_icon").change(function(evt){
		            	
		            	var class_name = $(this).attr("class");
		            	var $btn = $detail_panel.find("."+class_name);
		            	var $preview = $detail_panel.find("."+class_name+"_preview");
		            	
		            	var max_len = $btn.attr("max_len");
	                    var max_width = "20";
	                    var max_height = "25";
	                    if( class_name == "d_pic" )
	                    {
		                    max_width = 0;
		                    max_height = 0;
	                    }
	                    
	                    for(var i=0, f; f=evt.target.files[i]; i++)
	                    {
	                        if(!f.type.match('image.*'))
	                            continue;
	                        
	                        if( f.size > file_size_limit )
	                        {
		                        alert2(f.name+"檔案超過1MB，請重新上傳。");
		                        
		                        if(evt.target.files[i+1]==undefined)
			                        evt.target.value="";
		                        	
		                        continue;
	                        }
	                        
	                        var reader = new FileReader();
	                        reader.onload = (function(theFile)
	                        {
	                            return function(e)
	                            {
		                            //new屬性作用在於，圖檔先進preview，img_setting會偵測到沒有span，給加上default span
		                            $preview.append('<img new src="'+e.target.result+'" />');
	                                $preview.find("img:last")[0].onload = function(){
		                                
	                                    var $this = $(this);
	                                    var b_return = false;
	                                    if( (max_width>0&&$this.width()>max_width) )
	                                    {
		                                    alert2(this.name+"寬度超過"+max_width+"，請重新上傳。");
		                                    b_return = true;
	                                    }
	                                    if( max_height>0&&$this.height()>max_height )
	                                    {
		                                    alert2(this.name+"長度超過"+max_height+"，請重新上傳。");
		                                    b_return = true;
	                                    }
		                                if( b_return )
		                                {
			                                $btn.val("");
			                                $this.remove();
		                                    return;
		                                }
		                                
		                                var title = escape(theFile.name);
		                                var filename = new Date().getTime()+"_"+$this.index()+"_+"+title+"."+ title.substring( title.lastIndexOf('.')+1);//only one
										$this.attr("title",filename);
										theFile.fname = filename;//相對應值
										
	                                    var $preview = $this.parent();
	                                    var title = $this.attr("title");
	                                    var onload_len = $preview.find("img").length;
	                                    $this.css({
	                                        "border":"1px solid gray",
	                                        "margin":"5px"
	                                    });
	                                    
	                                    $this.wrap("<div style='display:inline-block;text-align:center;margin-bottom:10px;'></div>");
	                                    $this.parent().append("<br/><span class='bg shadowRoundCorner' style='padding:3px;cursor:pointer;color:white'>delete</span>");
	                                    
	                                    $data.data("add_file"+class_name).push(theFile);
	                                    $data.data("src"+class_name).push($this.attr("src"));
										//console.log("add_file"+class_name);
										//console.log($data.data("add_file"+class_name))
	                                    $btn.val("");
	                                    
	                                    d_img_setting($btn,$data);
	                                }
	                                
	                            };
	                            
	                        })(f);
	                        
	                        reader.readAsDataURL(f);
	                    }
	            	});
	            }
	            //紫顏色選擇器
	            if($obj.attr("d_color")!=undefined)
            	{
	            	$detail_panel.append(
	            		"<tr>"+
            				"<td>顏色</td>"+
            				"<td><input type='text' class='d_color' style='width:20px' /></td>"+
            			"</tr>"
	            	);
	            	$detail_panel.find(".d_color").unbind("change");
	            	$detail_panel.find(".d_color").each(function(){
		            	var tmp_color = $data.data("d_color")||""
		            	tmp_color = tmp_color==""?"ff0000":tmp_color.replace(/#/, "");
		            	$(this).css({
							'width':'20px'
							,'height':'20px'
							,'background':'#'+tmp_color
						}).colpick({
							color:tmp_color,
							layout:'hex',
							colorScheme:'dark',
							submit:0,
							onChange:function(hsb,hex,rgb,el,bySetColor) {
								$this = $detail_panel.find(".d_color");
								$this.css('background','#'+hex);
								if(!bySetColor){
									$data.data("d_color",hex);
								}
							}
						}).click(function(){
							$("div.colpick").css({
								"z-index":11
								,"left":mouse_x+30
								,"top":mouse_y-100
							});
						});
	            	});
	            }
	            
	            $("#dialog_content").unbind("scroll");
	            $("#dialog_content").bind("scroll",function(){
	                $detail_setting_panel.hide();
                })
                
                $("#dialog_content").unbind("click");
                $("#dialog_content").bind("click",function(){
	                $detail_setting_panel.hide();
                });
                
                $("div[id$=dialog_bg]").unbind("click");
                $("div[id$=dialog_bg]").click(function(){
	                $detail_setting_panel.hide();
                });
	            
	            
	            init_style( $detail_setting_panel );
	            init_style( $detail_setting_panel.find("*") );
	            $detail_setting_panel.css({
		                "left":function(){
			                if( mouse_x+30+$(this).width()>$(window).width() )
			                	return mouse_x-30-$(this).width();
			                else
			                	return mouse_x+30;
		                },
		                "top":mouse_y
	                }).show().find("input:text:first").focus();
	            return $detail_setting_panel;
            }
            //子圖片上傳
            function d_img_setting($btn,$data)
            {
                //////init
                var class_name = $btn.attr("class");
                var $preview = $("."+class_name+"_preview");
            	var total = $preview.find("img").length;
                if( total>=$btn.attr("max_len") ){
	                $btn.unbind("click");
	                $btn.hide();
                }else{
	                $btn.bind("click");
	                $btn.show();
                }
                
                //console.log("add_file")
                //console.log($data.data("add_file"+class_name));
                //console.log("src")
                //console.log($data.data("src"+class_name));
                //console.log("del_file")
                //console.log($data.data("del_src"));
                
                $preview.find("img").each(function(index){
                    $img = $(this);
                    var v_default = $img.attr("new")==undefined?"default":"";
                    if( $img.parent().children("span").length==0 ){//新增圖片
	                    //console.log("img_parent.html:\n"+$img.parent().html())
	                    $img.wrap("<div style='display:inline-block;text-align:center;margin-bottom:10px;'></div>");
                        $img.parent().append("<br/><span "+v_default+" class='bg shadowRoundCorner' style='padding:3px;cursor:pointer;color:white'>delete</span>");
                    }
                })
                // img
                $preview.find("span[default]").unbind("click");
				$preview.find("span[default]").click(function(){
                    var $img = $(this).parent().find("img");
                    $(this).parent().remove();
                    
                    $data.data("del_src").push( $img.attr("src").substring( $img.attr("src").lastIndexOf('/')+1 ) );
                    delete $data.data("src"+class_name)[ $data.data("src"+class_name).indexOf($img.prop("src")) ];
                    
                    d_img_setting($btn,$data)
                })
                $preview.find("span:not([default])").unbind("click");
                $preview.find("span:not([default])").click(function(){
                    var $img = $(this).parent().find("img");
                    $(this).parent().remove();
                    
                    var k = $data.data("src"+class_name).indexOf($img.attr("src"));
                    delete $data.data("add_file"+class_name)[k];
                    delete $data.data("src"+class_name)[k];
                    
                    d_img_setting($btn,$data)
                })
            	/////end init
            }
        </script>
        
    </head>
    <body>
	    
        <div id="wrapper">
            <!-- ******************** header ******************** -->
            <div id="header">
                <h3><?php echo $html_title; ?></h3>
            </div>
            <!-- /.header -->
            <!-- ******************** body ******************** -->
            <div id="body">
                <div id="body_left">
                    <?php echo $menu; ?>
                </div>
                <!-- /.body_left --> 
                <div id="body_right">
                	<!--<input type='button' id="show_file_arr" value="display file_arr"/>-->
                	<?php
                        if($permissions2add){
                        	echo "<input type='button' id='addPanel_btn' value='新增{$subject_cht}' data-open-dialog='新增{$subject_cht}'>";
							echo "<div id='addPanel' data-dialog='新增{$subject_cht}' style='dispaly:none;'>";
                        
                            echo "<table class='table-v'>";
							echo "	<tr>
                            			<td>Page<br>
                            				<input type='button' value='預覽位置' class='preview_adpos add'/>
                            			</td>
                            			<td>
                            				<select name='add_page' id='add_page'/>
						    					{$show_list_html}
											</select>
                            			</td>
									</tr>
									<tr class='add_images'>
                            			<td>圖片<br></td>
                            			<td>
                            				請新增後再到編輯區上傳
                            			</td>
									</tr>
									<tr class='add_type' style='display:none;'>
                            			<td class='add_type'>顯示位置</td>
                            			<td>
	                            			<input type='radio' name='add_type' id='add_type_left' value='1'>
		                                	<label for='add_type_left' class='add_type_left'>左方</label>
		                                	<input type='radio' name='add_type' id='add_type_right' value='2'>
		                                	<label for='add_type_right' class='add_type_right'>右方</label>
                            			</td>
									</tr>
									<tr class='add_item' style='display:none;'>
		                            	<td>關鍵字</td>
		                                <td>
		                                	<input type='text' id='add_item' name='add_item' />
		                                </td>
		                            </tr>
									<tr class='add_index' style='display:none;'>
		                            	<td>Index</td>
		                                <td>
			                                <label for='add_index_top'>
				                                <input type='radio' name='add_index' id='add_index_top' value='0'>
												最上層
			                                </label><br>";
							    	foreach($index_data as $key=>$per_data)
						    		{
							    		$tmp_no = $per_data["fi_no"];
							    		$tmp_img = $per_data["images"];
							    		
							    		echo "<label for='edit_index_{$key}'>";
							    		echo "<input type='radio' name='add_index' id='add_index_{$key}' value='$tmp_no' style='display:none'>";
	                                	echo "<img src='{$img_path}{$tmp_img}'/>";
	                                	echo "</label>";
							    	} 
							    	echo "
									    </td>
		                            </tr>
									<tr class='add_url'>
		                            	<td>URL</td>
		                                <td>
		                                	<input type='text' id='add_url' name='add_url' />
		                                </td>
		                            </tr>
		                            <tr>
		                            	<td>開始時間</td>
		                                <td>
		                                	<input type='text' class='time'	name='add_start_date' id='add_start_date' />
		                                </td>
		                            </tr>
		                            <tr>
		                            	<td>結束時間</td>
		                                <td>
		                                	<input type='text' class='time' name='add_end_date' id='add_end_date' />
		                                </td>
		                            </tr>
									<tr><td></td>
                            			<td><input class='add_btn' type='button' value='送出'></td>
                            		</tr>";
                            echo "</table>";
                            
                            echo "</div>";
							echo "
	<script>
		$(document).ready(function(){
	    	$(\"select[name='add_page']\").change(function(){
	        	
	        	$('#add_type_left').trigger('click');
	        	$('#add_index_top').trigger('click');
	        	$('tr.add_index').hide();
	        	$('tr.add_item').hide();
	        	$('tr.add_images').show();
	        	$('tr.add_url').show();
	        	
	        	if($(this).val()=='brand')
	        	{
		        	$('label.add_type_left').html('新進品牌');
					$('label.add_type_right').html('推薦品牌');
		        	$('td.add_type').html('品牌分類');
		        	$('tr.add_type').show();
		        }	
	        	else if($(this).val()=='search')
	        	{
		        	$('td.add_type').html('關鍵字分類');
		        	$('label.add_type_left').html('產品');
					$('label.add_type_right').html('品牌');
		        	$('tr.add_type').show();
		        	$('tr.add_item').show();
		        	$('tr.add_images').hide();
		        	$('tr.add_url').hide();
	        	}	
	        	else
	        	{
		        	$('tr.add_type').hide();
	        	}
	    	})
	    	$(\"input[name='add_type']\").change(function(){
	        	
	        	$('#add_index_top').trigger('click');
	        	if($(\"select[name='add_page']\").val()=='brand')
	        	{
		        	if($(this).val()=='2')
		        		$('tr.add_index').show();
		        	else
		        		$('tr.add_index').hide();
	        	}
	    	})
	    	$('tr.add_index img').click(function(){
	        	$('tr.add_index img').css({'border':'0px'});
	        	$(this).prev().trigger('click');
	        	$(this).css({'border':'3px solid #00B3FF'});
	    	})
	    	$(\"input[name='add_index']\").change(function(){
	        	
	        	if($(this).val()==0)
	        	{
	        		$('tr.add_index img').css({'border':'0px'});
	        	}
	    	})
	    	
		});
	</script>
									";
                        }else{
                            echo "你無權新增{$subject_cht}";
                        }
                                
                    ?>
                    <?php 
                    if($show_list_html!=""){
                    	echo "
                	<br>目現正在顯示的頁面：
                	<select name='show_list_panel' id='show_list_panel'/>
    					{$show_list_html}
					</select>";
					
					}
                    ?>
                    
                    <div id="editPanel" data-dialog='編輯<?php echo $subject_cht;?>'>
                        <table class='table-v'>
                            <tr class='edit_images'>
                            	<td width="150px">
	                            	圖片<span class='pic_size'></span><br><span class='pic_tip'></span>
	                            	<input type='button' value='預覽位置' class='preview_adpos edit'/>
	                            </td>
                                <td>
                                	<input type='file' class='pic' name='edit_images' max_len='1' multiple='multiple'/>
                                </td>
                            </tr>
                            <tr class='edit_type' style='display:none;'>
                            	<td class='edit_type'>
	                            	顯示位置
                            	</td>
                                <td>
                                	<input type="radio" name="edit_type" id="edit_type_left" value="1">
                                	<label for="edit_type_left" class='edit_type_left'>左方</label>
                                	<input type="radio" name="edit_type" id="edit_type_right" value="2">
                                	<label for="edit_type_right" class='edit_type_right'>右方</label>
                                </td>
                            </tr>
                            <tr class='edit_index' style='display:none;'>
                            	<td>Index</td>
                                <td>
	                                <label for='edit_index_top'>
		                                <input type='radio' name='edit_index' id='edit_index_top' value='0'>
										最上層
	                                </label><br>
						    	<?php 
							    	foreach($index_data as $key=>$per_data)
						    		{
							    		$tmp_no = $per_data["fi_no"];
							    		$tmp_img = $per_data["images"];
							    		if(file_exists($img_path.$tmp_img))
							    		{
								    		echo "<label for='edit_index_{$key}' now_no='$tmp_no'>\r\n";
								    		echo "<input type='radio' name='edit_index' id='edit_index_{$key}' value='$tmp_no' style='display:none'>\r\n";
		                                	echo "<img src='{$img_path}{$tmp_img}' style='max-width:180px;max-height:150px;'/>\r\n";
		                                	echo "</label>\r\n";
							    		}
							    	}
							    ?>
	<script>
		$(document).ready(function(){
	    	$("select[name='show_list_panel']").change(function(){
		    	
		    	v_url = v_resize = v_name = v_depiction = v_price = v_icon = v_color = "";
		    	
		    	$('#addPanel_btn').val('新增廣告');
		    	$("#add_page option").attr("selected",false);
	        	$("#add_page option[value='"+$(this).val()+"']").attr("selected",true);
	        	$("#add_page").trigger("change")
	        	$('#edit_type_left').trigger('click');
	        	$('#edit_index_top').trigger('click');
	        	$('tr.edit_index').hide();
	        	$('tr.edit_item').hide();
	        	$('tr.edit_images').show();
	        	$('tr.edit_url').show();
	        	$('span.pic_tip').html('');
	        	//不同分類切換不同屬性
	        	if($(this).val()=='brand')
	        	{
		        	v_url = " d_url ";
		        	v_name = " d_name ";
		        	v_depiction = " d_depiction ";
		        	v_price = " d_price ";
		        	$("#edit_images_preview").find("img").each(function(){
			        	chg_attr($(this),0);
		        	})
		        	$("label.edit_type_left").html('新進品牌');
					$("label.edit_type_right").html('推薦品牌');
					$('td.edit_type').html('品牌分類');
		        	$('tr.edit_type').show();
		        	$('span.pic_tip').html('(可雙擊圖片進行編輯)<br>');
		        	
	        	}
	        	else if($(this).val()=='search')
	        	{
		        	$('#addPanel_btn').val('新增關鍵字');
		        	$('td.edit_type').html('關鍵字分類');
		        	$("label.edit_type_left").html('產品');
					$("label.edit_type_right").html('品牌');
		        	$('tr.edit_type').show();
		        	$('tr.edit_item').show();
		        	$('tr.edit_images').hide();
		        	$('tr.edit_url').hide();
		        	$('span.pic_tip').html('(可雙擊圖片進行編輯)<br>');
	        	}
	        	else
	        	{
		        	$('tr.edit_type').hide();
	        	}
		        //圖片大小限制
		        var pic_size = Array();
	        	pic_size.push(Array(1225,90))
	        	pic_size.push(Array(142,142))
	        	pic_size.push(Array(208,75))
	        	pic_size.push(Array(720,530))
	        	pic_size.push(Array(208,90))
	        	pic_size.push(Array(0,0))
	        	//console.log($(this).find("option:selected").index())
	        	index = $(this).find("option:selected").index()
	        	if(pic_size[index][0]==0||pic_size[index][1]==0)
	        	{
		        	$("span.pic_size").html('')
	        	}
	        	else
	        	{
		        	$("span.pic_size").html( "("+pic_size[index][0]+"x"+pic_size[index][1]+")" );
		        	//console.log(pic_size[index][0]+"x"+pic_size[index][1])
	        	}	
	    	}).trigger("change");
	    	
	    	$("input[name='edit_type']").change(function(){
	        	
	        	v_url = v_resize = v_name = v_depiction = v_price = v_icon = v_color = "";
	        	$('#edit_index_top').trigger('click');
	        	$('label:hidden').show();
	        	
	        	if($("select[name='show_list_panel']").val()=="brand")
	        	{
		        	if($(this).val()=='2')
		        	{	
			        	v_icon = " d_icon ";
			        	v_color = " d_color ";
			        	v_name = " d_name ";
			        	$('tr.edit_index').show();
			        	$('tr.edit_index label[now_no=\''+edit_no+'\']').hide();
			        	$("#edit_images_preview").find("img").each(function(){
				        	chg_attr($(this),1);
			        	})
		        	}
		        	else
		        	{
			        	v_url = " d_url ";
			        	v_name = " d_name ";
			        	v_depiction = " d_depiction ";
			        	v_price = " d_price ";
			        	$('tr.edit_index').hide();
			        	$("#edit_images_preview").find("img").each(function(){
				        	chg_attr($(this),0);
			        	})
		        	}
	        	}
	    	})
	    	$('tr.edit_index img').click(function(){
	        	$('tr.edit_index img').css({'border':'0px'});
	        	$(this).prev().trigger('click');
	        	$(this).css({'border':'3px solid #00B3FF'});
	    	})
	    	$("input[name='edit_index']").change(function(){
	        	
	        	if($(this).val()==0)
	        	{
	        		$('tr.edit_index img').css({'border':'0px'});
	        	}
	    	})
	    	
		});
	</script>
                                </td>
                            </tr>
                            <tr class='edit_item' style='display:none;'>
                            	<td>關鍵字</td>
                                <td>
                                	<input type="text" id='edit_item' name='edit_item' />
                                </td>
                            </tr>
                            <tr class='edit_url'>
                            	<td>URL</td>
                                <td>
                                	<input type="text" id='edit_url' name='edit_url' />
                                </td>
                            </tr>
                            <tr>
                            	<td>是否顯示</td>
                                <td>
                                	<input type="radio" name="edit_show" id="edit_show_on" value="1">
                                	<label for="edit_show_on">顯示</label>
                                	<input type="radio" name="edit_show" id="edit_show_off" value="0">
                                	<label for="edit_show_off">不顯示</label>
                                </td>
                            </tr>
                            <tr>
                            	<td>權重</td>
                                <td>
                                	<input type='text' 	name='edit_weights' id='edit_weights' />
                                </td>
                            </tr>
                            <tr>
                            	<td>開始時間</td>
                                <td>
                                	<input type='text' class='time'	name='edit_start_date' id='edit_start_date' />
                                </td>
                            </tr>
                            <tr>
                            	<td>結束時間</td>
                                <td>
                                	<input type='text' class='time' name='edit_end_date' id='edit_end_date' />
                                </td>
                            </tr>
                            <tr>
                            	<td></td>
                            	<td>
                            		<input type="button" value="儲存" class='edit_btn' style="cursor: pointer;">
                            		<!--<input type="button" value="你有完沒完" style="cursor: pointer;" id='_btn'/>-->
                            	</td>
                            </tr>
                        </table>
                    </div>
                    
                    
                </div>
                <!-- /.body_right -->
            </div>
            <!-- /.body -->
            <!-- ******************** footer ******************** -->
            <div id="footer">
                <span><?php echo $html_copyright; ?></span>
            </div>
            <!-- /.footer -->
        </div>
        <!-- /.wrapper -->
    </body>
</html>
