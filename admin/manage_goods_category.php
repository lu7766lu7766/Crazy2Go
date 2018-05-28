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
		$permissions2visit	= (strpos($permissions,"goods_category_list")	!==false||$login_permissions=="all")?true:false;
        $permissions2add	= (strpos($permissions,"goods_category_add")	!==false||$login_permissions=="all")?true:false;
        $permissions2edit	= (strpos($permissions,"goods_category_edit")	!==false||$login_permissions=="all")?true:false;
        $permissions2del	= (strpos($permissions,"goods_category_del")	!==false||$login_permissions=="all")?true:false;
        
        $icon_path = "../public/img/template/";
        $icon_height = 18;
        $icon_width  = 18;
        $file_size_limit = 1024*1024;
        
        $all_data_not_finish=$dba->query("select `fi_no`,`name`,`subtitle`,`icon`,`index`,`weights`,`show` from category where `delete`=0 order by `index`,`weights` desc");
        
        if(is_array($all_data_not_finish))
        foreach($all_data_not_finish as $per_data){
        
        	if( file_exists($icon_path.$per_data["icon"]) ){
	        	list($img_width,$img_height,$ext,$img_info) = getimagesize($icon_path.$per_data["icon"]);
	        	if( isset($icon_height)&&isset($icon_width) ){
		        	$per_data["width"] = $icon_width;
		        	$per_data["height"] = $icon_height;
	        	}else if( isset($icon_width) ){
		        	$per_data["width"] = $icon_width;
		        	$per_data["height"] = $icon_width/$img_width*$img_height;
	        	}else if( isset($icon_height) ){
		        	$per_data["width"] = $icon_heght/$img_height*$img_width;
		        	$per_data["height"] = $icon_height;
	        	}else{
		        	$per_data["width"] = $img_width;
		        	$per_data["height"] = $img_height;
	        	}
        	}
	        
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
        <script type="text/javascript" src="dialog_synchronization.js"></script>
        <script>
            /******************** user define js ********************/
            $(document).ready(function(){
            	
                //ajax
                //filename write in template.js
                //新增
                if($("#addPanel").length>0){
                	var panel_id="addPanel";
	                $("input.add_btn").click(function(){
	                	if( !chk_all_input(panel_id) ){
		                    return;
	                    }
	                    var $this=$("#addPanel"); 
	                    var name		= $this.find("input[name='add_name']").val();
	                    var subtitle 	= $this.find("input[name='add_subtitle']").val();
	                    //console.log("uer:"+administrator_add_user+"\npwd:"+administrator_add_password+"\nname:"+administrator_add_name);
	                    $.post(filename,{
	                			query_type:	"category_add",
	                			name:name,
	                			subtitle:subtitle
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
                if($("input.del_btn").length>0)
                $("input.del_btn").bind('click',function(){
                    if(!confirm("確定刪除？"))return;
                    
                    var fi_no = $(this).attr('now_no');
                    var $data = $("#data_"+fi_no);
                    var name = $data.attr("name");
                    
                    $.post(filename,{
                    		query_type:"category_del",
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
                	edit_no = now_no;
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
					//
					$("select.category_sel").remove();
					panel.find("select:hidden").each(function(index){
						$.post(filename,{
	                    		 query_type:"get_category_back"
	                    		,fi_no:panel.find("select:hidden").eq(index).val()
							},function(data){
								$sel = $(data);
								$sel.find("option[value='"+edit_no+"']").remove();
								panel.find("select:hidden").eq(index).after($sel);
								update_select();
	                    });
						
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
				
                panel.find("input.edit_btn").click(function(){
                    //權限
                    if(!chk_all_input("#editPanel")){
	                    return;
                    }
					var fi_no = $.trim(panel.attr("now_no"));
					$data = $("#data_"+fi_no);
					$data_preview	= $data.next(); 
                    var name		= $data.attr("edit_name");
                    var subtitle 	= $data.attr("edit_subtitle");
					var index 		= $data.attr("edit_index");
					var weights 	= $data.attr("edit_weights");
					var show	 	= $data.attr("edit_show");
					//var $img 		= $data_preview.find("img");
					var $file		= $data
					var img_path	= "<?php echo $icon_path?>";
					//var base64_src	= "";
					//var pic_filename= "";
					var form_data = new FormData();
					var now_len = $file_arr.length;
					for( j=0 ; j<now_len ; j++ )
						form_data.append('file[]', form_data.append('file[]', $file_arr[j]));
					
					form_data.append("query_type"	,"category_edit");
					form_data.append("fi_no"		,fi_no);
					form_data.append("img_path"		,img_path);
					form_data.append("name"			,name);
					form_data.append("subtitle"		,subtitle);
					form_data.append("index"		,index);
					form_data.append("weights"		,weights);
					form_data.append("show"			,show);
					
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
	                        console.log(data);
						}
					});
					
                });
                
                //加上預覽ＤＩＶ
                panel.find("input[type='file'][class='pic']").each(function(){
                	var $this = $(this)
	                var preview_name = $this.prop("name")+"_preview";
	                if($("div#"+preview_name).length==0)
	                	$this.before("<div id='"+preview_name+"'></div>");
	                
	                
                })
                var $file_arr=[];//存放所有檔案
                //檔案上傳
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
                        if(f.size>(<?php echo $file_size_limit?>)){
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
                $("#show_file_arr").click(function(){
	                console.log(dump($file_arr));
                })
              
                /////menu
                /*$(".menu > ul > li > a").click(function(){
		            var _this=$(this);
		            if(_this.next("ul").length>0){
		                if(_this.next().is(":visible")){
		                    
		                    _this.html(_this.html().replace("▼","►")).next().hide();
		                }else{
		                    
		                    _this.html(_this.html().replace("►","▼")).next().show();
		                }
		               
		                return false;
		            }
		        });
		        
		        $(".submenu > li > a").click(function(){
		            var _this=$(this);
		            if(_this.next("ul").length>0){
		                if(_this.next().is(":visible")){
		                    
		                    _this.html(_this.html().replace("▼","►")).next().hide();
		                }else{
		                    
		                    _this.html(_this.html().replace("►","▼")).next().show();
		                }
		                
		                return false;
		            }
		        });*/
		        
		       
		        $("a").focus( function(){
		            $(this).blur();
		        });
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
                        	echo "<input type='button' value='新增商品分類' data-open-dialog='新增商品分類'>";
							echo "<div id='addPanel' data-dialog='新增商品分類'>";
                        
                            echo "<table class='table-v'>";
                            echo "	<tr>
                            			<td>分類名稱</td>
                            			<td><input type='text' 	name='add_name' id='add_name' class='must' /></td>
									</tr>";
                            echo "	<tr>
                            			<td>分類描述<br>(只有最上層菜單需要設定)</td>
                            			<td><input type='text' 	name='add_subtitle' id='add_subtitle' /></td>
									</tr>";
                            
                            echo "	<tr><td></td>
                            			<td><input class='add_btn' type='button' value='送出'></td>
                            		</tr>";
                            echo "</table>";
                            
                            echo "</div>";
                        }else{
                            echo "你無權新增分類";
                        }
                                
                    ?>
                    <table class="table-v" id="list_panel">
                        <tr>
                        	<td width='250'>
                        		分類樹狀圖
                        	</td>
                        	<td>
                        		<div class="menu">
								    
					<?php
						$prev_floors=-1;
						$len=count($all_data);
						echo "<ul>";
						if(is_array($all_data))
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
							$subtitle = $per_data["subtitle"];
							$dispaly_subtitle = $per_data["subtitle"]?"（{$subtitle}）":"";
							//
							$fi_no = $per_data["fi_no"];
							$index = $per_data["index"];
							$show  = $per_data["show"];
							$weights = $per_data["weights"];
							//"select `fi_no`,`name`,`subtitle`,`index`,`weights`,`show`
							$tmp_html= "<a href='#' id='data_{$fi_no}' 		now_no='$fi_no' 	name='$name'	edit_name='$name' 	edit_subtitle='$subtitle' 
											  		edit_index='$index' 	edit_weights='{$weights}' edit_show='{$show}' index='$index' data-open-dialog='編輯商品分類'>
											  		{$floors_sign}{$name}{$dispaly_subtitle}
										</a>";
							$tmp_html = $show>0 ? $tmp_html : "<s>".$tmp_html."</s>"; 
							$pic_src = $per_data["icon"];
							$pic_width = $per_data["width"];
							$pic_height = $per_data["height"];
							if( $pic_src!="" ){
								$pic_html = "<div id='data_{$fi_no}_icon_upload' style='display:none'>
												<img class='thumb' src='$icon_path$pic_src' width='{$pic_width}' height='{$pic_height}'/>
											</div>";
							}
								
							echo $tmp_html.$pic_html;
							
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
						echo "</li>";
					?>
								        
								</div>
                        	</td>
                        </tr>
                    </table>
                    <div id="editPanel" data-dialog='編輯商品分類'>
                        <table class='table-v'>
                            <tr>
                            	<td>Icon<?php echo "({$icon_width}x{$icon_height})"?><br>(只有最上層菜單需要設定)</td>
                                <td>
                                	<input type='file' id='icon_upload' name='icon_upload' class='pic' accept="image/*" width='<?php echo $icon_width?>' height='<?php echo $icon_height?>'>
                                </td>
                            </tr>
                            <tr>
                            	<td>名稱</td>
                                <td><input type="text" name="edit_name" id="edit_name"></td>
                            </tr>
                            <tr>
                            	<td>描述<br>(只有最上層菜單需要設定)</td>
                                <td><input type="text" name="edit_subtitle" id="edit_subtitle"></td>
                            </tr>
                            <tr>
                            	<td>母分類</td>
                                <td>
                                <?php
                                echo "<select name='edit_index' id='edit_index' style='display:none'>";
                                echo "<option value='0' >最上層</option>";
                            	foreach( $all_data as $per_data ){
                            		$tmp_no = $per_data["fi_no"];
                            		$tmp_floors = $per_data["jfloors"];
                            		$tmp_name = $per_data["name"];
                            		$space = "";
                            		while($tmp_floors-->0){
	                            		$space.="--";
                            		}
                                	echo "<option value='$tmp_no'>{$space}{$tmp_name}</option>";
                                }
								echo "</select>";
                                ?>
                                </td>
                            </tr>
                            <tr>
                            	<td>權重<br>(數字越大越先顯示)</td>
                                <td><input type="text" name="edit_weights" id="edit_weights"></td>
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
                            <tr><td></td><td>
                            		<input type="button" value="儲存" class='edit_btn' style="cursor: pointer;">
                            		<input type="button" value="刪除" class='del_btn' style="cursor: pointer;">
                            		</td></tr>
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
