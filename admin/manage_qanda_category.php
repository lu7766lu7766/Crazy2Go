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
        $subject = "qanda_category";
        $subject_cht = "幫助中心分類";
        $sub_subject_cht = "";
		$permissions2visit	= (strpos($login_permissions, $subject."_list")	!==false||$login_permissions=="all")?true:false;
        $permissions2add	= (strpos($login_permissions, $subject."_add")	!==false||$login_permissions=="all")?true:false;
        $permissions2edit	= (strpos($login_permissions, $subject."_edit")	!==false||$login_permissions=="all")?true:false;
        $permissions2del	= (strpos($login_permissions, $subject."_del")	!==false||$login_permissions=="all")?true:false;
        
        $category_html = get_category_selection("qanda");
        $all_data_not_finish= $dba->query("select `fi_no`,`name`,`index`,`weights`,`show` from qanda order by `index`,`weights` desc");
        
        if(is_array($all_data_not_finish))
        foreach($all_data_not_finish as $per_data){
        
	        if($per_data["index"]=="0"){
	        	$per_data["jfloors"]=0;
		        $all_data[]=$per_data;
	        }else{
	        	$b_found=false;
	        	$offset=0;
		        foreach($all_data as $key=>$val){
			        if( $val["fi_no"]==$per_data["index"] ){
				        $offset=$key+1;
				        $self_floors=$val["jfloors"]+1;
				        $b_found=true;
			        }else if( $val["index"]==$per_data["index"] ){
			        	$offset=$key+1;
			        	$self_floors=$val["jfloors"];
			        	
			        }else if($b_found){
				        break;
			        }
		        }
		        
		        $clear_len = count($all_data)-$offset;
		        $per_data["jfloors"] = $self_floors;
		        if($clear_len==0){
					$all_data[] = $per_data;
		        }else{
			        $tmp_arr = array_slice($all_data,$offset);
			        array_unshift($tmp_arr,$per_data);
			        array_splice( $all_data,$offset,$clear_len,$tmp_arr );
		        }
	        }
        }
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
        <link rel="stylesheet" href="../backend/css/colpick.css" type="text/css" />
        <script type="text/javascript" src="../backend/js/colpick.js"></script>
        <script type="text/javascript" src="dialog_synchronization.js"></script>
        <script>
            /******************** user define js ********************/
            $(document).ready(function(){
            	
                //ajax
                //filename write in template.js
                function init(){
	                ////////////////////////////////////////////////////////////////////////////////////////////////////////start add
	                if($("#addPanel").length>0){
	                	var panel_id="addPanel";
	                	$("input.add_btn").unbind("click");
		                $("input.add_btn").click(function(){
		                	if( !chk_all_input(panel_id) ){
			                    return;
		                    }
		                    var $this		= $("#"+panel_id); 
		                    var name		= $this.find("input[name='add_name']").val();
		                    var index 	= $this.find("select[name='add_index']").val();
		                    //console.log("uer:"+administrator_add_user+"\npwd:"+administrator_add_password+"\nname:"+administrator_add_name);
		                    $.post(filename,{
		                			query_type:	"<?php echo $subject;?>_add",
		                			name:		name,
		                			index:	index
								},function(data){
									data=$.trim(data);
		                    		if(data=="success"){
		                    			alert("新增成功！");
			                    		location.reload();
		                    		}
		                    		console.log(data);
		                    });
		                 });
	                }
	                
	                ////////////////////////////////////////////////////////////////////////////////////////////////////////start del
	                if($("input.del_btn").length>0){
		                $("input.del_btn").unbind("click");
		                $("input.del_btn").bind('click',function(){
		                    if(!confirm("確定刪除？"))return;
		                    
		                    var fi_no = $(this).attr('now_no');
		                    var $data = $("#data_"+fi_no);
		                    var name = $data.attr("name");
		                    $.post(filename,{
		                    		query_type:"<?php echo $subject;?>_del",
		                    		fi_no:fi_no,
		                    		name:name
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
	        
	                //編輯
	                var $panel = $("#editPanel");
	                
	                $("#list_panel [data-open-dialog]").mouseenter(function(){
	                    var now_no=$(this).attr('now_no');
	                    $panel.attr('now_no',now_no);
	                    //console.log(now_no+"^^"+filename)
	                    ///////// ajax取資料
	                    var b_ajax=$("#data_"+now_no).data("b_get_ajax")||false;//<--不需要ajax改這裡(fasle=>不需要)//到資料庫分割去取資料
	                    if(b_ajax){
		                    $.post(filename,{
		                    		query_type:"get_member_info",
		                    		fi_no:now_no
								},function(data){
									data = $.parseJSON(data);
									if(data!==false){
										$("#data_"+now_no).data("b_get_ajax",false);
										for( key in data){
											$("#data_"+now_no).attr("edit_"+key,data[key]);
										}
										get_data(now_no,$panel);
									}
									
		                    });
	                    }else{
		                    get_data(now_no,$panel);
	                    }
	                    	
	                });
	                
	                function get_data(now_no,$panel){
	                	
	                	edit_no = now_no;
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
						//
						$panel.find("select.category_sel").remove();
						$panel.find("select:hidden").each(function(index){
							$selection = $(this);
							$.post(filename,{
		                    		 query_type:"get_category_back"
		                    		,fi_no:$selection.find("option[selected]").val()
								},function(data){
									$sel = $(data);
									$sel.find("option[value='"+edit_no+"']").remove();
									$panel.find("select:hidden").eq(index).after($sel);
									update_select();
		                    });
							
						})
						////////// upload pic
						$a_file = $data.data("$a_file")||[];
						$panel.find("input[type='file'][class='pic'][name]").each(function(){
							var name	= $(this).prop("name");
		                    var width	= $(this).attr("width") ||100;
		                    var height	= $(this).attr("height")||100;
		
		                    
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
								if(copy_name=="left_middle"){console.log($data.prop("id")+"^^"+$obj.html());alert2( $data.data(copy_name))}
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
						$panel.find("*[url]").each(function(){
							url_setting($(this));
						})
						
	                }
	                
	                
	                // dblclick call dialog
	                function url_setting($obj){
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
	                }
	                //無span之圖片(原有)包裝後加上span[default]，點下會進入$a_remove_file[]，剩下的span(新加入)點擊後加入$a_file[]
	                function img_setting($preview,now_no){
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
	                            	if( $a_file[k].name == title ){
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
	                function move_setting($obj){//每次箭頭重新生成數量多會耗效能，但寫法最直覺
	                	
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
					})
	                
	                
	                
	                //及時更新暫存資料(hidden暫存，沒存進資料庫)
	                $panel.find("input[type!='button'][type!='checkbox'][type!='radio']").change(function(){
		                var now_no=$panel.attr('now_no');
		                $("#data_"+now_no).attr($(this).prop("name"),$(this).val() );
	                })
	                $panel.find("input[type='radio']").click(function(){
	                    var now_no	= $panel.attr('now_no');
	                    var name	= $(this).prop("name");
	                    var val	=  $("input[name='"+name+"']:checked").val();
	                    $("#data_"+now_no).attr(name,val);
	                });
	                //全選
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
	                //$("*[id^='data_']").hide()
	                var $a_remove_file=[];//存放所有刪除檔案路徑
	                var $a_file=[];//存放所有檔案
	                //圖片上傳
	                $panel.find("input[type='file'][class='pic'][name]").change(function(evt){
	                    
	                    var $btn	= $(this);
	                    var name	= $btn.prop("name");
	                    var width	= $btn.attr("width") ||100;
	                    var height	= $btn.attr("height")||100;
	
	                    var now_no	= $panel.attr('now_no');
	                    var data_id = "data_"+now_no;
	                    var preview_id	= name+"_preview";
	                    var data_preview_id = data_id+"_"+name;
	                       
	                    var $data   		= $("#"+data_id);
	                    var $preview		= $("#"+preview_id);
	                    
	                    var max_len = $btn.attr("max_len");
	                    var new_len = $btn[0].files.length;
	                    var old_len = $a_file.length;
	                    var exists_len = $preview.find("img").length;
	                    
	                    $preview.html($data.data(data_preview_id)||"");
	                    //console.log(data_preview_id+"^^"+$data.data(data_preview_id))
	                    //img_setting($preview,now_no);
	                    /*$preview.find("span").click(function(){
		                    $(this).parent().remove();
		                    var rows = $a_file.length;
		                    for( k=0 ; k<rows ; k++ ){
		                    	try{
		                    		var title=unescape($(this).parent().find("img").attr("title"));
	                            	if( $a_file[k].name == title ){
	                                    $a_file.splice(k,1);
	                                    break;
	                                }
	                        	}catch(error){
	                        	}
		                    }
		                    $data.data("$a_file",$a_file);
		                    $data.data(name,$preview.html());
	                    })*/
	                    for(var i=0, f; f=evt.target.files[i]; i++) {
	                        if(!f.type.match('image.*')) {
	                            continue;
	                        }
	                        var file_size_limit = "<?php echo $file_size_limit?>";
	                        file_size_limit = file_size_limit==""? 0 : file_size_limit;
	                        if( f.size > file_size_limit ){
		                        alert2(f.name+"檔案超過1MB，請重新上傳。");
		                        if(evt.target.files[i+1]==undefined){
			                        evt.target.value="";
		                        }
		                        	
		                        continue;
	                        }
	                        
	                        if(i+1+exists_len>max_len){
		                        break;
	                        }
	                        var reader = new FileReader();
	                        reader.onload = (function(theFile) {
	                            return function(e) {
		                            //new屬性作用在於，圖檔先進preview，img_setting會偵測到沒有span，給加上default span
		                            $preview.append('<img new class="thumb" src="'+e.target.result+'" title="'+escape(theFile.name)+'" url/>');
	                                $preview.find("img:last")[0].onload = function(){
	                                    var $this = $(this);
	                                    var $preview = $this.parent();
	                                    var title = $this.attr("title");
	                                    var onload_len = $preview.find("img").length;
	                                    //var filename = this.width+"x"+this.height+"_@."+title.substring(title.lastIndexOf(".")+1);
	                                    $this.css({
	                                        "border":"1px solid gray",
	                                        "margin":"5px"
	                                    });
	                                    //$this.attr("title",filename);
	                                    $this.width(width);
	                                    $this.height(height);
	                                    $this.wrap("<div style='display:inline-block;text-align:center;margin-bottom:10px;'></div>");
	                                    $this.parent().append("<br/><span class='bg shadowRoundCorner' style='padding:3px;cursor:pointer;color:white'>delete</span>");
	                                    
	                                    
	                                    /*$this.parent().find("span").click(function(){
	                                        $this.parent().remove();
	                                        //btn.after($btn.clone(true)).remove();
	                                        var now_len = $a_file.length;
	                                        for( k=0 ; k<now_len ; k++ ){
	                                        	try{
	                                        		var title=unescape($(this).parent().find("img").attr("title"));
	                                        		//console.log("key:"+k+"\nname:"+$a_file[k].name+"\ncompare_name:"+title+"\nlen:"+now_len+"\narray:"+dump($a_file)+"\n")
		                                        	if( $a_file[k].name == title ){
				                                        $a_file.splice(k,1);
				                                        break;
			                                        }
	                                        	}catch(error){
	                                        		//console.log("key:"+k+"^^\narray:"+dump($a_file)+"\n")
	                                        	}
	                                        }
	                                        $data.data("$a_file",$a_file);
	                                        $data.data(data_preview_id,$preview.html());
	                                    });*/
	                                    
	                                    $a_file.push($btn.prop('files')[$a_file.length-old_len]);
	                                    //console.log("onload_len:"+onload_len+"^^old_len:"+old_len+"^^max_len:"+max_len);
	                                    if($a_file.length-old_len == new_len || onload_len==max_len){
		                                    img_setting($preview,now_no);
		                                    $preview.find("img").each(function(){
			                                    url_setting($(this))
		                                    });
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
	                $panel.find("input.edit_btn").click(function(){
	                    //權限
	                    if(!chk_all_input("#editPanel")){
		                    return;
	                    }
						var fi_no = $.trim($panel.attr("now_no"));
						var data_id = "data_"+fi_no;
						$data = $("#data_"+fi_no);
						var name		= $data.attr("edit_name");
	                    var weights 	= $data.attr("edit_weights");
	                    var index 		= $data.attr("edit_index");
	                    var show		= $data.attr("edit_show");
	                    var form_data = new FormData();
	                    
						form_data.append("query_type",	"<?php echo $subject;?>_edit");
						form_data.append("fi_no",		fi_no);
						form_data.append("name",		name);
						form_data.append("weights",		weights);
						form_data.append("index",		index);
						form_data.append("show",		show);
						
						
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
                
                //同名class寫回data
                function write2data($data,$obj){
	                var this_class = $obj.attr("class")||"";
	                if(this_class=="")return;
	                var $copy_obj = $obj.clone(true);
	                $data.data(this_class,$copy_obj);
                }
                
                //網址編輯
                if($("*[url]").length>0){
	                $("body").append(
	                	"<div id='url_edit_panel' class='shadowRoundCorner' style='position:absolute;display:inline-block;padding:5px;background:rgba(255,255,255,0.9);border:3px dashed #CCC;width:300px;text-align:center;'>"+
		                    "<div style='text-align:center;color:white;padding:5px;margin-bottom:5px;' class='bg shadowRoundCorner'>"+
		                        "<span style=‘color:white;'>編輯網址</span>"+
		                        "<span style='color:white;float:right;cursor:pointer;' onclick='$(this).parent().parent().hide();'>ｘ</span>"+
		                    "</div>"+
		                    "<table class='table-v'>"+
	                			"<tr>"+
	                				"<td>網址</td>"+
	                				"<td><input type='text' id='edit_url' style='width:95%' /></td>"+
	                			"</tr>"+
	                		"</table>"+
		                "</div>");
	                $("div#url_edit_panel").css({
		                "position":"absolute",
		                "z-index":10
	                }).hide();
	                init_style( $("#url_edit_panel,#url_edit_panel *") );
                }
				/////////////////////////////////////////////////////////////////////////////for this page
				$("#addPanel").find("select:hidden").each(function(index){
					$.post(filename,{
                    		 query_type:"get_category_back"
                    		,fi_no:$("select:hidden").eq(index).val()
						},function(data){
							$("#addPanel").find("select:hidden").eq(index).after(data);
							update_select("add");
                    });
				})
				$("td.category2cht").each(function(){
					$(this).html($("#add_category > option[value='"+$(this).html()+"']").text().replace(/-/g, ""));
				})
				//////////////////////////////////////////////////////////////////////////////////// init
                
				if( $("#show_list_panel").length == 1 ){
					$("#show_list_panel").change(function(){
						show_category_attr($(this),$(this).val());
					})
					show_category_attr($("#show_list_panel"),$("#show_list_panel").val());
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
								init()
	                    });
					}
					var query_type = "<?php echo "get_{$sub_subject}_info";?>";
				}else{
					init()
				}
				
				
            });
            
        </script>
        <style>
            /******************** user define css ********************/
			.menu ul li{
			    list-style-type: none;
			}
			
			.menu a{
			    color:#333333; 
			    text-decoration:none; 
			    font-size:12px;
			}
			
			.menu a:hover{
			    text-decoration:underline;
			}
			
			.menu  .submenu{
			    margin-left:5px;
			    color:#333333;
			    /*display:none;*/
			    list-style-type: none;
			}
			
			.menu  .lastmenu{
			    margin-left:10px;
			    color:#333333;
			    /*display:none;*/
			    list-style-type: none;
			}
        </style>
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
                        	echo "<input type='button' value='新增{$subject_cht}' data-open-dialog='新增{$subject_cht}'>";
							echo "<div id='addPanel' data-dialog='新增{$subject_cht}' style='dispaly:none;'>";
                        
                            echo "<table class='table-v'>";
							echo "	<tr>
                            			<td>母分類</td>
                            			<td>
                            				<select name='add_index' id='add_index' style='display:none;'/>
	                            				<option value='0' >最上層</option>
						    					{$category_html}
											</select>
                            			</td>
									</tr>";
							echo "	<tr>
                            			<td>{$subject_cht}名稱</td>
                            			<td><input type='text' 	name='add_name' id='add_name' class='must' /></td>
									</tr>";
                            
                            echo "	<tr><td></td>
                            			<td><input class='add_btn' type='button' value='送出'></td>
                            		</tr>";
                            echo "</table>";
                            
                            echo "</div>";
                        }else{
                            echo "你無權新增{$subject_cht}";
                        }
                                
                    ?>
                    
					<?php
						$prev_floors=-1;
						$len=count($all_data);
						echo "
					<table class='table-v' id='list_panel'>
                        <tr>
                        	<td width='250'>
                        		分類樹狀圖
                        	</td>
                        	<td>
                        		<div class='menu'>
                        			<ul>";
						
						if(is_array($all_data)){
							
							foreach( $all_data as $key=>$per_data ){
								
								$now_floors = $per_data["jfloors"];
								
								if( $key<$len-1 ){
									
									$next_floors=$all_data[$key+1]["jfloors"];
									
								}else{
									
									$next_floors=0;
									
								}
								
								switch($next_floors){
									
									case 1:
										$ul_class="submenu";
									break;
									
									case 2:
										$ul_class="lastmenu";
									break;
									
								}
								
								echo "<li>";
								//echo "prev_floors:{$prev_floors}^^now_floors:{$now_floors}^^next_floors:{$next_floors}<br>";
								$floors_sign = "";
								
								if( $now_floors<$next_floors ){
									
									$floors_sign = "▼ ";
									
								}
								
								$name	  = $per_data["name"];
								//
								$fi_no = $per_data["fi_no"];
								$index = $per_data["index"];
								$show  = $per_data["show"];
								$weights = $per_data["weights"];
								//"select `fi_no`,`name`,`subtitle`,`index`,`weights`,`show`
								$tmp_html= "<a href='#' id='data_{$fi_no}' 		now_no='$fi_no' 	name='$name'	edit_name='$name' 	edit_index='{$index}' 
											edit_weights='{$weights}' edit_show='{$show}' data-open-dialog='編輯{$subject_cht}'>
												{$floors_sign}{$name}
											</a>";
								$tmp_html = $show>0 ? $tmp_html : "<s>".$tmp_html."</s>"; 
								
								echo $tmp_html;
								
								if( $next_floors>$now_floors ){
									
									echo "<ul class='$ul_class'>";
									
								}
								
								if( $next_floors<=$now_floors ){
									
									echo "</li>";
									$diff_floors=$now_floors-$next_floors;
									
									while($diff_floors-->0){
										
										if( $next_floors<$now_floors ){
											
											echo "</ul>";
											echo "</li>";
											
										}
										
									}
									
								}
								
								$prev_floors=$now_floors;
							}
						}
						
						echo "</li>";
					?>
								</div>
                        	</td>
                        </tr>
                    </table>
                    
                    <div id="editPanel" data-dialog='編輯<?php echo $subject_cht;?>'>
                        <table class='table-v'>
                            <tr>
                            	<td>幫助中心名稱</td>
                                <td>
                                	<input type='text' 	name='edit_name' id='edit_name' />
                                </td>
                            </tr>
                            <tr>
                            	<td width="150px">母分類</td>
                                <td>
                                	<select name='edit_index' id='edit_index' style='display:none;'/>
						    			<?php 
							    		echo "<option value='0' >最上層</option>";
							    		echo $category_html; ?>
									</select>
                                </td>
                            </tr>
                            <tr>
                            	<td>權重</td>
                                <td>
                                	<input type='text' 	name='edit_weights' id='edit_weights' />
                                </td>
                            </tr>
                            <tr>
	                            <td>啟用設定</td>
	                            <td>
	                            	<input type="radio" name="edit_show" id="edit_show_on" value="1">
                                	<label for="edit_show_on">顯示</label>
                                	<input type="radio" name="edit_show" id="edit_show_off" value="0">
                                	<label for="edit_show_off">不顯示</label>
                                </td>
                            </tr>
                            <tr>
                            	<td></td>
                            	<td>
                            		<input type="button" value="儲存" class='edit_btn' style="cursor: pointer;">
                            		<input type="button" value="刪除" class='del_btn' style="cursor: pointer;" <php echo "now_no='$fi_no'" >
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
