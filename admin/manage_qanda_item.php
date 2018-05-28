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
        $subject = "qanda_item";
        $subject_cht = "幫助中心項目";
        $sub_subject = "";
        $sub_subject_cht = "";
        //取得新刪修查權限
		$permissions2visit	= (strpos($login_permissions, $subject."_list")	!==false||$login_permissions=="all")?true:false;
        $permissions2add	= (strpos($login_permissions, $subject."_add")	!==false||$login_permissions=="all")?true:false;
        $permissions2edit	= (strpos($login_permissions, $subject."_edit")	!==false||$login_permissions=="all")?true:false;
        $permissions2del	= (strpos($login_permissions, $subject."_del")	!==false||$login_permissions=="all")?true:false;
        
        //$all_data = $dba->query("select `fi_no`,`qanda_no`,`issue`,`answers`,`weights`,`show` from `qanda_tiem` order by `category`,`weights` desc");
        
        $img_path = "../public/img/qanda/";
        $sel = $_GET["sel"];
        $sel = $sel==""?0:$sel;
        //取得分類
        $show_list_html = get_category_selection("qanda",$sel);
        //圖片檔案大小
        $file_size_limit = 1024*1024*1;//1M
        //圖片寬度上傳限制
        $pic_width_limit	= 800;
        //圖片高度上傳限制
		$pic_height_limit	= 0;//不限
		//圖片預覽寬度
		$pic_width = 100;
		//圖片預覽高度
		$pic_height = 0;
		//子屬性
		$v_url = "";
		//子屬性
		$v_resize = "resize";
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
            $(document).ready(function()
            {
                //ajax
                //filename write in template.js
                function init()
                {	
	                ////////////////////////////////////////////////////////////////////////////////////////////////////////start add
	                //資料新增
	                if($("#addPanel").length>0)
	                {
	                	var panel_id="addPanel";
	                	$("input.add_btn").unbind("click");
		                $("input.add_btn").click(function()
		                {
		                	if( !chk_all_input(panel_id) )
			                    return;
			                    
		                    var $this		= $("#"+panel_id); 
		                    var qanda_no	= $this.find("select[name='add_qanda_no']").val();
		                    var issue 		= $this.find("input[name='add_issue']").val();
		                    //console.log("uer:"+administrator_add_user+"\npwd:"+administrator_add_password+"\nname:"+administrator_add_name);
		                    $.post(filename,{
		                			query_type:	"<?php echo $subject;?>_add"
		                		   ,qanda_no:qanda_no
		                		   ,issue:	issue
								},function(data){
									
									data=$.trim(data);
									
		                    		if(data=="success")
		                    		{
		                    			alert("新增成功！");
			                    		//location.reload();
			                    		update_stay_type(qanda_no);
		                    		}
		                    		
		                    		console.log(data);
		                    		
		                    });
		                    
		                });
	                }
	                
	                ////////////////////////////////////////////////////////////////////////////////////////////////////////start del
	                //資料刪除
	                if($("input.del_btn").length>0)
	                {
		                $("input.del_btn").unbind("click");
		                $("input.del_btn").bind('click',function()
		                {
		                    if(!confirm("確定刪除？"))
		                    	return;
		                    
		                    var fi_no = $(this).attr('now_no');
		                    var $data = $("#data_"+fi_no);
		                    var issue = $data.attr("issue");
		                    var qanda_no = $("#show_list_panel").val();
		                    $.post(filename,{
		                    		query_type:"<?php echo $subject;?>_del"
		                    	   ,fi_no:fi_no
		                    	   ,issue:issue
								},function(data){
									data=$.trim(data);
									if(data=="success"){
				                        alert("已刪除！");
				                        //location.reload();
				                        update_stay_type(qanda_no);
			                        }
			                        console.log(data);
		                    });
		                });
	                }
					
					////////////////////////////////////////////////////////////////////////////////////////////////////////edit del
	                //編輯
	                var $panel = $("#editPanel");
	                //編輯按鈕移入即取得存在data中的資料
	                $("#list_panel [data-open-dialog]").unbind("mouseenter")
	                $("#list_panel [data-open-dialog]").mouseenter(function(){
	                    var now_no=$(this).attr('now_no');
	                    $panel.attr('now_no',now_no);
	                    //console.log(now_no+"^^"+filename)
	                    ///////// ajax取資料
	                    get_data(now_no,$panel);
	                    	
	                }).click(function(){
		                var $dialog = $("#dialog_content");
		                //內容變更即寫回data
		                $("#editPanel").find("textarea").jqte({
							change: function(){
								//console.log($dialog.find(".jqte_editor_x img").length)
								$dialog.find(".jqte_editor_x img[resize]").each(function(){
					                
					                resize_setting($(this));
				                })
				                $("#editPanel").find("textarea").each(function(){
					                if($(this).attr("id").indexOf("_jacsource")>-1){
						                $this = $( "#"+$(this).attr("id").replace("_jacsource", "") )
					                }else{
						                $this = $(this);
					                }
					                var now_no=$("#editPanel").attr('now_no')||"";
					                if(now_no=="")return;
									$("#data_"+now_no).data($this.prop("name"),$this.parent().prev().html() );
									//console.log($this.prop("name")+"^^"+$this.parent().prev().html())
				                })
				                
				            }
					    })
					    
					    $dialog.find(".jqte_editor").each(function(){
	                        var $this = $(this);
	                        $this.attr("class","jqte_editor_x");
	                        $this.css({"min-height":"120px"});
	                    });
		                $dialog.find(".jqte_tool_1,.jqte_tool_2,.jqte_tool_3").click(function(){
		                    var $this=$(this);
		                    $this.find("div").css({
		                        "position":"absolute",
		                        "left":$this.position().left+"px",
		                        "top":$this.position().top+"px"
		                    });
		                    $dialog.bind("scroll",function(){
			                    console.log($dialog.length)
		                        $dialog.unbind("scroll");
		                        $dialog.find(".jqte_tool_1,.jqte_tool_2,.jqte_tool_3").find("div").hide();
		                    });
		                });
		                //圖片加上雙擊編輯功能
		                $dialog.find(".jqte_editor_x img").each(function(){
			                $(this).attr("resize",true);
			                resize_setting($(this));
		                })
		                //編輯器加上貼圖功能，圖片上傳功能撰寫
		                $dialog.find(".jqte_toolbar").each(function(index){
		                    if(index!=0)return;
		                    var $this = $(this);
		                    $this.append($this.children(":last").clone());
		                    var last = $this.children(":last").find("a");
		                    last.text("貼圖");
		                    last.attr("class","");
		                    last.parent().css({"margin-top":"5px"});
		                    last.click(function(){
		                        if(!$("#image_choose_panel")[0])return;
		                        if($dialog.find("#image_choose_panel").length>0)return;
		                        var $panel = $("#image_choose_panel").clone();
		                        $this.parent().parent().prepend($panel);
		                        
		                        $panel.show();
		                        var $content = $panel.find("#content");
		                        var $imgs = $("#edit_images_preview img");
		                        if(!$imgs[0])
		                        {
		                            alert("請先上傳完整描述圖片！");
		                            $panel.remove();
		                            return;
		                        }
		                        $content.html("");
		                        $imgs.each(function(){
		                            var $this = $(this);
		                            var display="";
		                            var og_width = $this.attr("s_width")||$this.width();
		                            var og_height = $this.attr("s_height")||$this.height();
		                            display += "<div class='choose_item' style='display:inline-block;border:1px solid gray;padding:5px;margin:5px;width=110px;text-align:center;'>"
		                                    + "<p><img src='"+$this.attr("src")+"' title='"+$this.attr("title")+"' "
		                                    + "			style='max-height:100px;max-width:100px;'></p>"
		                                    + "<p>寬 <input name='width' type='text' style='width:80px' placeholder='請輸入寬' value='"+og_width+"'><br/>"
		                                    + "高 <input name='height' type='text' style='width:80px' placeholder='請輸入高' value='"+og_height+"'></p>"
		                                    + "<p><input name='select_btn' type='button' value='選取'></p>"
		                                    + "</div>";
		                            $content.append(display);
		                        });
		                        $content.find("input[name='select_btn']").click(function(){
		                            var $this = $(this);
		                            if($this.val()=='選取'){
		                                $this.val("取消選取");
		                                $this.parent().parent().css({"border":"3px solid #1E90FF"});
		                            }else{
		                                $this.val("選取");
		                                $this.parent().parent().css({"border":"1px solid gray"});
		                            }
		                        });
		                        $content.append("<div style='text-align:center;margin-top:10px;padding-top:10px;border-top:1px dashed gray;'><input name='choose' type='button' value='確定選取'/></div>");
		                        $content.find("input[name='choose']").click(function(){
		                            var display=$("textarea[name='edit_answers']").val();
		                            $content.find(".choose_item").each(function(){
		                                var $this = $(this);
		                                if($this.find("input[name='select_btn']").val()=='選取')return;
		                                var src = $this.find("img").attr("src");
		                                var title = $this.find("img").attr("title");
		                                var w = $this.find("input[name='width']").val();
		                                var h = $this.find("input[name='height']").val();
		                                
		                                var v_url = "<?php echo $v_url?>";
					                    v_url = v_url=="" ?"" :" "+v_url+" ";
					                    
					                    var v_resize = "<?php echo $v_resize?>";
					                    v_resize = v_resize=="" ?"" :" "+v_resize+" ";
					                    
					                    var attr = v_url + v_resize;
		                                display+="<img src='"+src+"' title='"+title+"' style='width:"+w+"px;height:"+h+"px' "+attr+">";
		                                
		                            })
		                            $("textarea[name='edit_answers']").val(display);
		                            $(".jqte_editor_x").eq(0).html( $("textarea[name='edit_answers']").val() );
		                            $panel.remove();
		                        });
		                    });
		                });
	                });
	                //資料取得
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
						$panel.find("select:not(.category_sel)").each(function(){
							$(this).find("option[selected]").attr("selected",false)
							var select_val=$data.attr( $(this).prop("name") )||"";
							//console.log(select_val+"^^"+$(this).prop("name")+"^^"+$(this).prop("id"))
							//console.log($(this).find("option[value='"+select_val+"']").val())
							$(this).find("option[value='"+select_val+"']").attr("selected",true);
						})
						//分類樣式表重新讀取
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
						//////// del_btn 主動加上now_no屬性
						if($panel.find(".del_btn").length>0){
							$panel.find(".del_btn").attr("now_no",now_no);
						}
						//////// copy 把$data.data[copy_name]內容匯入
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
						
						///////// move 有move子屬性自動加上順序編排箭頭
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
						
						///////// color picker 顏色選擇器
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
						
						///////// url setting url編輯器
						$panel.find("*[url]").each(function(){
							url_setting($(this));
						})
						
						///////// resize setting resize編輯器
						$panel.find("*[resize]").each(function(){
							resize_setting($(this));
						})
						
						//////// textarea 內容匯入
						$panel.find("textarea").each(function(){
							
							var text_val = $data.data( $(this).prop("name") )|| $data.attr( $(this).prop("name") ) ||"";
							$(this).html(text_val)//.jqteVal(text_val);
							//console.log($(this).prop("name")+"^^"+text_val);
							
						})
						
	                }
	                
	                // dblclick call dialog
	                function url_setting($obj)
	                {
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
	                }
	                // dblclick call dialog
	                function resize_setting($obj)
	                {
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
	                }
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
	                
	                //欄位值寫入data
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
	                $("*[id^='data_']").hide()
	                var $a_remove_file=[];//存放所有刪除檔案路徑
	                var $a_file=[];//存放所有檔案
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
	                    
	                    var v_url = "<?php echo $v_url?>";
	                    v_url = v_url=="" ?"" :" "+v_url+" ";
	                    
	                    var v_resize= "<?php echo $v_resize?>";
	                    v_resize = v_resize=="" ?"" :" "+v_resize+" ";
	                    
	                    var attr 	= v_url + v_resize;
	
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
	                    
	                    for(var i=0, f; f=evt.target.files[i]; i++) {
	                        if(!f.type.match('image.*'))
	                            continue;
	                        
	                        if( f.size > file_size_limit )
	                        {
		                        alert2(f.name+"檔案超過1MB，請重新上傳。");
		                        
		                        if(evt.target.files[i+1]==undefined)
			                        evt.target.value="";
		                        	
		                        continue;
	                        }
	                        
	                        if(i+1+exists_len>max_len)
		                        break;
	                        
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
	                                    
	                                    $(this).attr("s_width",$(this).width());
	                                    $(this).attr("s_height",$(this).height());
	                                    //var filename = "_"+$this.index()+"_"+this.width+"x"+this.height+"_@."+$this.attr("title").split(".")[$this.attr("title").split(".").length-1];
	                                    //var filename = $this.index()+"_"+$this.attr("title");
                                        //$this.attr("title",filename);
	                                    var title = $this.attr("title");
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
	                                    //存入陣列	                                    
	                                    $a_file.push($btn.prop('files')[$a_file.length-old_len]);
	                                    if($a_file.length-old_len == new_len || onload_len==max_len){
		                                    //設定子屬性功能
		                                    img_setting($preview,now_no);
		                                    $preview.find("img").each(function(){
			                                    url_setting($(this));
			                                    resize_setting($(this));
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
	                //編輯
	                $panel.find("input.edit_btn").unbind("click");
	                $panel.find("input.edit_btn").click(function(){
	                    //權限
	                    if(!chk_all_input("#editPanel")){
		                    return;
	                    }
						var fi_no = $.trim($panel.attr("now_no"));
						var data_id = "data_"+fi_no;
						$data = $("#data_"+fi_no);
						var qanda_no 	= $data.attr("edit_qanda_no");
						var issue		= $data.attr("edit_issue");
						var answers 	= $data.data("edit_answers");
	                    var weights 	= $data.attr("edit_weights");
	                    var show 		= $data.attr("edit_show");
	                    var data_preview_id = data_id+"_"+"edit_images";
	                    var $img_view	= $($data.data(data_preview_id)) || $($("#"+data_preview_id).html());
	                    
	                    
	                    var form_data = new FormData();
	                    var now_len = $a_file.length;
	                    
						for( j=0 ; j<now_len ; j++ )
							form_data.append('file[]', form_data.append('file[]', $a_file[j]));
							
						now_len = $a_remove_file.length;
						
						for( j=0 ; j<now_len ; j++ )
						{
							form_data.append( 'a_remove_file['+j+'][title]',$a_remove_file[j]["title"] );
							form_data.append( 'a_remove_file['+j+'][src]',$a_remove_file[j]["src"] );
						}
						
						$tmp = $("<div>"+answers+"</div>");
						$tmp.find("img").each(function()
						{
							if($(this).attr("src").indexOf("data:")>-1)
								$(this).removeAttr("src");
						})
						answers = $tmp.html();
						//console.log($tmp.html());
						//return;
						form_data.append("query_type",	"<?php echo $subject;?>_edit");
						form_data.append("fi_no",		fi_no);
						form_data.append("qanda_no",	qanda_no);
						form_data.append("issue",		issue);
						form_data.append("answers",		answers);
						form_data.append("weights",		weights);
						form_data.append("show",		show);
						$img_view.find("img").each(function(index){
		                    form_data.append( 'images['+index+'][title]',$(this).attr("title") );
	                    })
	                    
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
			                        //location.reload();
			                        update_stay_type(qanda_no);
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
		        $("body").append(
                	"<div id='url_edit_panel' class='shadowRoundCorner' style='position:absolute;display:inline-block;padding:5px;background:rgba(255,255,255,0.9);border:3px dashed #CCC;width:300px;text-align:center;' float>"+
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
	                
	            //長寬編輯
		        $("body").append(
                	"<div id='resize_edit_panel' class='shadowRoundCorner' style='position:absolute;display:inline-block;padding:5px;background:rgba(255,255,255,0.9);border:3px dashed #CCC;width:300px;text-align:center;' float>"+
	                    "<div style='text-align:center;color:white;padding:5px;margin-bottom:5px;' class='bg shadowRoundCorner'>"+
	                        "<span style=‘color:white;'>貼圖尺寸設定</span>"+
	                        "<span style='color:white;float:right;cursor:pointer;' onclick='$(this).parent().parent().hide();'>ｘ</span>"+
	                    "</div>"+
	                    "<table class='table-v'>"+
                			"<tr>"+
                				"<td>寬</td>"+
                				"<td><input type='text' id='edit_width' style='width:95%' /></td>"+
                			"</tr>"+
                			"<tr>"+
                				"<td>高</td>"+
                				"<td><input type='text' id='edit_height' style='width:95%' /></td>"+
                			"</tr>"+
                		"</table>"+
	                "</div>");
                $("div#resize_edit_panel").css({
	                "position":"absolute",
	                "z-index":10
                }).hide();
                init_style( $("#resize_edit_panel,#resize_edit_panel *") );
				/////////////////////////////////////////////////////////////////////////////for this page
				
				$.post(filename,{
                		 query_type:"get_category_back"
                		,fi_no:$("#show_list_panel").val()
					},function(data){
						$("#show_list_panel").after(data);
						update_select("add");
                });
				//$("textarea").jqte();
				
				//////////////////////////////////////////////////////////////////////////////////// init
                
				if( $("#show_list_panel").length == 1 )
				{
					$("#show_list_panel").change(function(){
						show_category_attr($(this),$(this).val());
					})
					show_category_attr($("#show_list_panel"),$("#show_list_panel").val());
					//切換不同分類，頁面(內容)AJAX重新讀取
					function show_category_attr($obj,fi_no){
						$("#add_qanda_no").find("option[selected]").removeAttr("selected")
						$("#add_qanda_no").find("option[value="+fi_no+"]").attr("selected",true);
						$("#addPanel").find("select.category_sel").remove();
						$.post(filename,{
	                    		 query_type:"get_category_back"
	                    		,fi_no:fi_no
							},function(data){
								$("#add_qanda_no").after(data);
								update_select("add");
	                    });
						
						$("#list_panel").remove();
						$.post(filename,{
	                    		query_type:			"<?php echo "get_{$subject}_info";?>",
	                    		fi_no:				fi_no,
	                    		permissions2del:	<?php echo !$permissions2del?"false":"true";?>,
	                    		permissions2edit:	<?php echo !$permissions2edit?"false":"true";?>
							},function(data){
								data=$.trim(data);
								$table = $(data);
								while($obj.next()[0].tagName!=undefined&&$obj.next()[0].tagName=="SELECT"){ $obj = $obj.next()}
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
				
				//$("#_btn").mouseenter(function(){
					//$("#edit_issue").val("123")
					//console.log($("#edit_issue").html()+"^^"+$("#edit_issue").val())
				//})
				
            });
            function update_stay_type(type){
	            if(location.search.indexOf("sel=")>-1)
                	location.href = location.href.replace(/sel=[0-9]+/g,"sel="+type);
                else
                	if(location.search=='')
                		location.href = location.href+"?sel="+type;
                	else
                		location.href = location.href+"&sel="+type;
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
                        	echo "<input type='button' value='新增{$subject_cht}' data-open-dialog='新增{$subject_cht}'>";
							echo "<div id='addPanel' data-dialog='新增{$subject_cht}' style='dispaly:none;'>";
                        
                            echo "<table class='table-v'>";
							echo "	<tr>
                            			<td>分類</td>
                            			<td>
                            				<select name='add_qanda_no' id='add_qanda_no' style='display:none;'/>
						    					{$show_list_html}
											</select>
                            			</td>
									</tr>
									<tr>
                            			<td>問題</td>
                            			<td><input type='text' id='add_issue' name='add_issue' /></td>
									</tr>
									<tr><td></td>
                            			<td><input class='add_btn' type='button' value='送出'></td>
                            		</tr>";
                            echo "</table>";
                            
                            echo "</div>";
                        }else{
                            echo "你無權新增{$subject_cht}";
                        }
                                
                    ?>
                    <?php 
                    if($show_list_html!=""){
                    	echo "
                	<br>目現正在顯示的頁面：
                	<select name='show_list_panel' id='show_list_panel' style='display:none;'/>
    					{$show_list_html}
					</select>";
					
					}
                    ?>
                    
                    <div id="editPanel" data-dialog='編輯<?php echo $subject_cht;?>'>
                        <table class='table-v'>
                            <tr>
                            	<td width="150px">分類</td>
                                <td>
                                	<select name='edit_qanda_no' id='edit_qanda_no' style='display:none;'/>
						    			<?php echo $show_list_html; ?>
									</select>
                                </td>
                            </tr>
                            <tr>
                            	<td>問題</td>
                                <td>
                                	<input type="text" id='edit_issue' name='edit_issue' />
                                </td>
                            </tr>
                            <tr>
                            	<td>解答</td>
                                <td>
                                	<textarea id='edit_answers' name='edit_answers'></textarea>
                                </td>
                            </tr>
                            <tr>
                            	<td>完整描述圖片<br>(無限制大小)</td>
                                <td>
                                	<input type='file' class='pic' name='edit_images' max_len='999' multiple='multiple'/>
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
                            	<td></td>
                            	<td>
                            		<input type="button" value="儲存" class='edit_btn' style="cursor: pointer;">
                            		<!--<input type="button" value="你有完沒完" style="cursor: pointer;" id='_btn'/>-->
                            	</td>
                            </tr>
                        </table>
                    </div>
                    <!--圖片選取視窗-->
                    <div id="image_choose_panel" class='shadowRoundCorner' style='display:inline-block;padding:5px;background:rgba(255,255,255,0.9);border:3px dashed #CCC;width:778px;display:none'>
	                    <div style='text-align:center;color:white;padding:5px;margin-bottom:5px;' class='bg shadowRoundCorner'>
	                        <span>選擇貼圖</span>
	                        <span style='float:right;cursor:pointer;' onclick="$(this).parent().parent().remove();">ｘ</span>
	                    </div>
	                    <div id="content">
	                        請先上傳圖片！
	                    </div>
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
