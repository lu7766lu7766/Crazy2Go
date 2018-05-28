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
        require_once "../swop/library/dba.php";
        $dba = new dba();
        $subject = "goods_attr";
		$permissions2visit	= (strpos($login_permissions, $subject."_list")	!==false||$login_permissions=="all")?true:false;
        $permissions2add	= (strpos($login_permissions, $subject."_add")	!==false||$login_permissions=="all")?true:false;
        $permissions2edit	= (strpos($login_permissions, $subject."_edit")	!==false||$login_permissions=="all")?true:false;
        $permissions2del	= (strpos($login_permissions, $subject."_del")	!==false||$login_permissions=="all")?true:false;
        
                
        $category_html = get_category_selection();
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
        <script type="text/javascript" src="dialog_synchronization.js"></script>
        <script>
            /******************** user define js ********************/
            $(document).ready(function(){
            	
                //ajax
                //filename write in template.js
                function init(){
	                //新增
	                if($("#addPanel").length>0){
	                	var panel_id="addPanel";
	                	$("input.add_btn").unbind("click");
		                $("input.add_btn").click(function(){
		                	if( !chk_all_input(panel_id) ){
			                    return;
		                    }
		                    var $this		= $("#"+panel_id); 
		                    var name		= $this.find("input[name='add_name']").val();
		                    var category 	= $this.find("select[name='add_category']").val();
		                    var type		= $this.find("input[name='add_type']:checked").val();
		                    var required	= $this.find("input[name='add_required']:checked").val();
		                    //console.log("uer:"+administrator_add_user+"\npwd:"+administrator_add_password+"\nname:"+administrator_add_name);
		                    $.post(filename,{
		                			query_type:	"goods_attr_add",
		                			name:		name,
		                			category:	category,
		                			type:		type,
		                			required:	required
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
	                
	                //刪除
	                if($("input.del_btn").length>0){
		                $("input.del_btn").unbind("click");
		                $("input.del_btn").bind('click',function(){
		                    if(!confirm("確定刪除？"))return;
		                    
		                    var fi_no = $(this).attr('now_no');
		                    var $data = $("#data_"+fi_no);
		                    var name = $data.attr("name");
		                    
		                    $.post(filename,{
		                    		query_type:"goods_attr_del",
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
	                var panel = $("#editPanel");
	                
	                $("#list_panel [data-open-dialog]").mouseenter(function(){
                    var now_no=$(this).attr('now_no');
                    panel.attr('now_no',now_no);
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
									get_data(now_no,panel);
								}
								
	                    });
                    }else{
	                    get_data(now_no,panel);
                    }
                    	
                });
	                
	                function get_data(now_no,panel){
	                
	                	var data_id = "data_"+now_no;               
		                var $data   = $("#"+data_id);
		                ////////////checkbox 更新
	                    panel.find("input[type='checkbox']").prop('checked',0);
	                    
	                    var arr_name = [];
	                    panel.find("input[type='checkbox']").each(function(){
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
							
							panel.find("input[type='checkbox'][name^='"+name+"']").each(function(){
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
	                    panel.find("input[type='radio']").each(function(){
		                    var name=$(this).prop("name").replace(/\W+/g, "");
		                    if( !in_array(name,arr_name) )
		                    	arr_name.push(name);
	                    })
	                    for(i=0; i<arr_name.length; i++){
	                    	var name=arr_name[i];
	                    	var radio_val = $data.attr(name);
	                    	//console.log("name:"+name+"\n val:"+radio_val)
							panel.find("input[name='"+name+"'][value='"+radio_val+"']").prop("checked",true);
	                    }
	                    
	                    ////////////password update
						panel.find("input[type='password']").each(function(){
							
							var password=$data.attr( $(this).prop("name") )||"";
							$(this).val(password);
							//console.log(password)
	                    	if($(this).attr("name").search("confirm")>-1){
								$(this).val("");
							}
						})
						
						////////// text
						panel.find("input[type='text']").each(function(){
							var text_val=$data.attr( $(this).prop("name") )||"";
							$(this).val(text_val);
						})
						
						////////// selected
						panel.find("select").each(function(){
							$(this).find("option[selected]").attr("selected",false)
							var select_val=$data.attr( $(this).prop("name") )||"";
							//console.log(select_val+"^^"+$(this).prop("name")+"^^"+$(this).prop("id"))
							//console.log($(this).find("option[value='"+select_val+"']").val())
							$(this).find("option[value='"+select_val+"']").attr("selected",true);
						})
						
						////////// upload pic
						$file_arr=$data.data("$file_arr")||[];
						panel.find("input[type='file'][class='pic']").each(function(){
							var name	= $(this).prop("name");
		                    var width	= $(this).attr("width") ||100;
		                    var height	= $(this).attr("height")||100;
		
		                    
		                    var preview_id	= name+"_preview";
		                    var data_preview_id = data_id+"_"+name
		                    //var data_file_name	= data_id+"_file_"+name;
		                    
		                    var $preview		= $("#"+preview_id);
		                    var $data_preview	= $("#"+data_preview_id)
		                    $preview.html($data.data("preview_html")||$data_preview.html()||"");
		                    $preview.find("span").click(function(){
			                    $(this).parent().remove();
			                    var now_len = $file_arr.length;
			                    for( k=0 ; k<now_len ; k++ ){
			                    	try{
			                    		var title=unescape($(this).parent().find("img").attr("title"));
		                            	if( $file_arr[k].name == title ){
		                                    $file_arr.splice(k,1);
		                                    break;
		                                }
		                        	}catch(error){
		                        	}
			                    }
			                    $data.data("$file_arr",$file_arr);
			                    $data.data("preview_html",$preview.html());
		                    })
						})
						///////// not input
						panel.find("td[id]").each(function(){
							var targer_attr=$(this).prop("id");
							$(this).html($data.attr(targer_attr));
						})
						//////// del_btn
						if(panel.find(".del_btn").length>0){
							panel.find(".del_btn").attr("now_no",now_no);
						}
						//////// copy
						panel.find("td[copy]").each(function(){
							var $this = $(this);
							var copy_name = $this.attr("copy");
							if( $data.data(copy_name) != undefined ){
								var $obj = $data.data(copy_name).clone(true);
								$this.html("");
								$this.append($obj);
								$obj.find(":text").each(function(){
									$(this).val($(this).attr("value"))
								})
							}else{
								$.post(filename,{
	                    				query_type:	query_type,
			                    		fi_no:		now_no
			                    	},function(data){
										data=$.trim(data);
										$obj = $(data)
										$this.html("")
										$this.append($obj);
										write2data($data,$obj);
										return get_data(now_no,panel);
			                    });
							}
						})
						
						///////// move
						panel.find("div[move]").each(function(){
							move_setting($(this));
						})
						
						///////// add_text
						panel.find(".add_move_text").click(function(){
							var $btn = $(this);
							var $new_input = $("<input type='text' index_no='"+now_no+"'>");
							$btn.before($new_input);
			                var $parent = $btn.parent();
			                move_setting($parent);
			                write2data($data,$parent);
							$new_input.change(function(){write2data($data,$parent)});
						})
	                }
	                
	                //所有input[type='text']後面加上上移下移箭頭
	                function move_setting($obj){//每次箭頭重新生成數量多會耗效能
                	
	                var total = $obj.find("input[type='text']").length;
	                
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
					})
					var now_no = $obj.attr("now_no");
					var $data = $("#data_"+now_no)
					write2data($data,$obj)
                }
	                
	                //該div寫入data內，並自動帶入edit
	                $("div[class][now_no]").each(function(){
						var $this = $(this);
						var now_no = $this.attr("now_no");
						var $data = $("input[id='data_"+now_no+"']");
						if($data.length==0)return;
						
						$this.find("input[type='text']").change(function(){
							write2data($data,$(this).parent())
						})
						$this.find(".add_text").click(function(){
							var $btn = $(this);
							var $new_input = $("<input type='text' index_no='"+now_no+"'>");
							$btn.before($new_input);
			                $btn.before("<br>");
			                var $parent = $btn.parent();
			                write2data($data,$parent);
							$new_input.change(function(){write2data($data,$parent)});
						})
						write2data($data,$this);
						$this.remove();
					})
	                
	                //同名class寫回data
	                function write2data($data,$obj){
		                var this_class = $obj.attr("class");
		                var $copy_obj = $obj.clone(true);
		                $data.data(this_class,$copy_obj);
	                }
	                
	                //及時更新暫存資料(hidden暫存，沒存進資料庫)
	                panel.find("input[type!='button'][type!='checkbox'][type!='radio']").change(function(){
	                var now_no=panel.attr('now_no');
	                $("#data_"+now_no).attr($(this).prop("name"),$(this).val() );
                })
	                panel.find("input[type='radio']").click(function(){
	                    var now_no	= panel.attr('now_no');
	                    var name	= $(this).prop("name");
	                    var val	=  $("input[name='"+name+"']:checked").val();
	                    $("#data_"+now_no).attr(name,val);
	                });
	                
	                //全選
	                panel.find("input[value='all']:checkbox").click(function(){
					var name=$(this).prop("name").replace(/\W+/g, "");
					var check=$(this).prop("checked");
					$("input[name^='"+name+"']").prop("checked",check);
					
					var now_no=panel.attr('now_no');
					if(check)
	                	$("#data_"+now_no).attr( name,$(this).val() );
	                else
	                	$("#data_"+now_no).attr( name,"" );
				})
					//checkbox 所有項目選取，全選一並選取。有項目取消，全選項目一並取消。任一動作直接存入data內
					panel.find("input[value!='all']:checkbox").click(function(){
					var name=$(this).prop("name").replace(/\W+/g, "");
					if(panel.find("input[value!='all'][name^='"+name+"']:checkbox").length==panel.find("input[value!='all'][name^='"+name+"']:checkbox:checked").length){
						//$("input[value='all']").prop("checked",true);
						panel.find("input[value='all']:checkbox").trigger("click");
					}else{
						var now_no=panel.attr('now_no');
						var tmp_checkbox_val="";
						var dot=","
						panel.find("input[value='all'][name^='"+name+"']").prop("checked",false);
						
						panel.find("input[value!='all'][name^='"+name+"']:checked").each(function(){
							tmp_checkbox_val+=$(this).val()+dot;
						})
						//console.log(tmp_permissions);
						$("#data_"+now_no).attr( name,tmp_checkbox_val.slice( 0,-1 ) );
					}
				})
					panel.find("select").change(function(){
					var now_no	= panel.attr('now_no');
                    var name	= $(this).prop("name");
                    var val	=  $(this).val();
                    //console.log(name+"^^"+val)
                    $("#data_"+now_no).attr(name,val);
				})
					
	                //加上預覽ＤＩＶ
	                panel.find("input[type='file'][class='pic']").each(function(){
                	var $this = $(this)
	                var preview_name = $this.prop("name")+"_preview";
	                if($("div#"+preview_name).length==0)
	                	$this.before("<div id='"+preview_name+"'></div>");
	                
                })
	                
	                
	                var $file_arr=[];//存放所有檔案
	                //圖片上傳
	                panel.find("input[type='file'][class='pic']").change(function(evt){
                    
                    var $btn	= $(this);
                    var name	= $btn.prop("name");
                    var width	= $btn.attr("width") ||100;
                    var height	= $btn.attr("height")||100;

                    var now_no	= panel.attr('now_no');
                    var data_id = "data_"+now_no;
                    var preview_id	= name+"_preview";
                    var data_preview_id = data_id+"_"+name
                                    
                    var $data   		= $("#"+data_id);
                    var $preview		= $("#"+preview_id);
                    var $data_preview	= $("#"+data_preview_id);
                    
                    var new_len = $btn[0].files.length;
                    var old_len = $file_arr.length;
                    
                    $preview.html($data.data("preview_html")||"");
                    $preview.find("span").click(function(){
	                    $(this).parent().remove();
	                    var rows = $file_arr.length;
	                    for( k=0 ; k<rows ; k++ ){
	                    	try{
	                    		var title=unescape($(this).parent().find("img").attr("title"));
                            	if( $file_arr[k].name == title ){
                                    $file_arr.splice(k,1);
                                    break;
                                }
                        	}catch(error){
                        	}
	                    }
	                    $data.data("$file_arr",$file_arr);
	                    $data.data("preview_html",$preview.html());
                    })
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
                        var reader = new FileReader();
                        reader.onload = (function(theFile) {
                            return function(e) {
                                $preview.append('<img class="thumb" src="'+e.target.result+'" title="'+escape(theFile.name)+'"/>');
                                $preview.find("img:last")[0].onload = function(){
                                    var $this = $(this);
                                    var title = $this.attr("title");
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
                                    $this.parent().find("span").click(function(){
                                        $this.parent().remove();
                                        //btn.after($btn.clone(true)).remove();
                                        var now_len = $file_arr.length;
                                        for( k=0 ; k<now_len ; k++ ){
                                        	try{
                                        		var title=unescape($(this).parent().find("img").attr("title"));
                                        		//console.log("key:"+k+"\nname:"+$file_arr[k].name+"\ncompare_name:"+title+"\nlen:"+now_len+"\narray:"+dump($file_arr)+"\n")
	                                        	if( $file_arr[k].name == title ){
			                                        $file_arr.splice(k,1);
			                                        break;
		                                        }
                                        	}catch(error){
                                        		//console.log("key:"+k+"^^\narray:"+dump($file_arr)+"\n")
                                        	}
                                        }
                                        $data.data("$file_arr",$file_arr);
                                        $data.data("preview_html",$preview.html());
                                    });
                                    $file_arr.push($btn.prop('files')[$file_arr.length-old_len]);
                                    if($file_arr.length-old_len == new_len){
                                    	$data.data("$file_arr",$file_arr);
                                    	$data.data("preview_html",$preview.html());
	                                    $btn.val("");
                                    }
                                }
                            };
                        })(f);
                        reader.readAsDataURL(f);
                    }
                    
                });
	                
	                panel.find("input.edit_btn").click(function(){
	                    //權限
	                    if(!chk_all_input("#editPanel")){
		                    return;
	                    }
						var fi_no = $.trim(panel.attr("now_no"));
						$data = $("#data_"+fi_no);
						var name		= $data.attr("edit_name");
	                    var type 		= $data.attr("edit_type");
						var required 	= $data.attr("edit_required");
						var a_attr_item = [];
						var len_attr_item = $data.data("edit_attr_item").find("input:text").length;
						if( len_attr_item >0)
						$data.data("edit_attr_item").find("input:text").each(function(){
							var per_data = [];
							per_data[0] = $(this).val();				//name
							per_data[1] = len_attr_item--;				//weights
							per_data[2] = $(this).attr("fi_no")||"";	//fi_no->update用
							a_attr_item.push(per_data);
							//console.log("ccccsdvsadfsafasdfsadfasdf")
						})
						//console.log(dump(a_attr_item));
						$.post(filename,{
	                    		query_type:"goods_attr_edit",
	                    		fi_no:		fi_no,
	                    		name:		name,
	                    		type:		type,
	                    		required:	required,
	                    		a_attr_item:a_attr_item
							},function(data){
								data=$.trim(data);
								if(data=="success"){
			                        alert("已更新！");
			                        location.reload();
		                        }
		                        console.log(data);
	                    });
											
	                });
				}
                
				/////////////////////////////////////////////////////////////////////////////for this page
				
				$.post(filename,{
                		 query_type:"get_category_back"
                		,fi_no:$("#show_list_panel").val()
					},function(data){
						$("#show_list_panel").after(data);
						update_select("add");
                });
                
				//$('.edit_attr_item input').bind('click.sortable mousedown.sortable',function(e){
				//	e.target.focus();
				//});
				if( $("#show_list_panel").length == 1 ){
					$("#show_list_panel").change(function(){
						show_category_attr($(this),$(this).val());
					})
					show_category_attr($("#show_list_panel"),$("#show_list_panel").val());
					function show_category_attr($obj,fi_no){
						
						$("#add_category").find("option[selected]").removeAttr("selected")
						$("#add_category").find("option[value="+fi_no+"]").attr("selected",true);
						$("#addPanel").find("select.category_sel").remove();
						$.post(filename,{
	                    		 query_type:"get_category_back"
	                    		,fi_no:fi_no
							},function(data){
								$("#add_category").after(data);
								update_select("add");
	                    });
						$("#list_panel").remove();
						$.post(filename,{
	                    		query_type:			"get_attr_info",
	                    		fi_no:				fi_no,
	                    		permissions2del:	<?php echo !$permissions2del?"false":"true";?>,
	                    		permissions2edit:	<?php echo !$permissions2edit?"false":"true";?>
							},function(data){
								data=$.trim(data);
								$table = $(data);
								while($obj.next()[0].tagName!=undefined&&$obj.next()[0].tagName=="SELECT"){ $obj = $obj.next()}
								$obj.after($table);
								init_style( $("#list_panel,#list_panel *") );
								init()
	                    });
					}
					var query_type = "get_attr_item_info";
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
			    //display:none;
			    list-style-type: none;
			}
			
			.menu  .lastmenu{
			    margin-left:10px;
			    color:#333333;
			    //display:none;
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
                        	echo "<input type='button' value='新增商品屬性' data-open-dialog='新增商品屬性'>";
							echo "<div id='addPanel' data-dialog='新增商品屬性' style='dispaly:none;'>";
                        
                            echo "<table class='table-v'>";
                            echo "	<tr>
                            			<td>商品分類<br>(新增後不可修改)</td>
                            			<td>
                            				<select name='add_category' id='add_category' style='display:none;'/>
                            					{$category_html}
                            				</select>
                            			</td>
									</tr>";
							echo "	<tr>
                            			<td>屬性名稱</td>
                            			<td><input type='text' 	name='add_name' id='add_name' class='must' /></td>
									</tr>";
                            echo "	<tr>
                            			<td>設定</td>
                            			<td>
                            				<fieldset>
												<legend>項目選擇設定:</legend>
												<input type='radio' name='add_type' id='add_type_one'  value='0' checked>
			                                	<label for='add_type_one' >單選</label>
			                                	<input type='radio' name='add_type' id='add_type_more' value='1'>
			                                	<label for='add_type_more'>多選</label>
											</fieldset>
                            				<fieldset>
												<legend>是否必填:</legend>
												<input type='radio' name='add_required' id='add_required_on'  value='1' checked>
			                                	<label for='add_required_on' >必填</label>
			                                	<input type='radio' name='add_required' id='add_required_off' value='0'>
			                                	<label for='add_required_off'>非必填</label>
											</fieldset>
		                                </td>
									</tr>";
                            
                            echo "	<tr><td></td>
                            			<td><input class='add_btn' type='button' value='送出'></td>
                            		</tr>";
                            echo "</table>";
                            
                            echo "</div>";
                        }else{
                            echo "你無權新增商品屬性";
                        }
                                
                    ?>
                    <?php 
                    echo "
                	<br>目現正在顯示的分類：
                	<select name='show_list_panel' id='show_list_panel' style='display:none;'/>
    					{$category_html}
					</select>";
                    ?>
                    
                    <div id="editPanel" data-dialog='編輯商品屬性'>
                        <table class='table-v'>
                            <tr>
                            	<td>屬性名稱</td>
                                <td>
                                	<input type='text' 	name='edit_name' id='edit_name' />
                                </td>
                            </tr>
                            <tr>
                            	<td>設定</td>
                                <td>
	                                <fieldset>
										<legend>項目選擇設定:</legend>
										<input type='radio' name='edit_type' id='edit_type_one'  value='0'>
	                                	<label for='edit_type_one' >單選</label>
	                                	<input type='radio' name='edit_type' id='edit_type_more' value='1'>
	                                	<label for='edit_type_more'>多選</label>
									</fieldset>
                    				<fieldset>
										<legend>是否必填:</legend>
										<input type='radio' name='edit_required' id='edit_required_on'  value='1'>
	                                	<label for='edit_required_on' >必填</label>
	                                	<input type='radio' name='edit_required' id='edit_required_off' value='0'>
	                                	<label for='edit_required_off'>非必填</label>
									</fieldset>
	                               
                                </td>
                            </tr>
                            <tr>
                            	<td>屬性項目</td>
                                <td copy='edit_attr_item'></td>
                            </tr>
                            <tr>
                            	<td></td>
                            	<td>
                            		<input type="button" value="儲存" class='edit_btn' style="cursor: pointer;">
                            		<input type="button" value="刪除" class='del_btn' style="cursor: pointer;">
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
