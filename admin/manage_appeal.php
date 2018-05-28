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
        include '../backend/class_pager2.php';
        $subject = "appeal";
        $subject_cht = "會員申訴";
        $sub_subject = "";
        $sub_subject_cht = "";
		$permissions2visit	= (strpos($login_permissions, $subject."_list")	!==false||$login_permissions=="all")?true:false;
        $permissions2edit	= (strpos($login_permissions, $subject."_edit")	!==false||$login_permissions=="all")?true:false;
        
        $pager = new Pager();
        $all_data = $pager->query("select `fi_no`,`member`,`content`,`date`,`reply_content`,`reply_date`,`progress` from `appeal_index` where `store`='0'");
        
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
			var edit_no 
			
            $(document).ready(function()
            {	
                //ajax
                //filename write in template.js
                function init()
                {	
					////////////////////////////////////////////////////////////////////////////////////////////////////////edit del
	                //編輯
	                var $panel = $("#editPanel");
	                
	                $("#list_panel [data-open-dialog]").unbind("mouseenter")
	                $("#list_panel [data-open-dialog]").mouseenter(function(){
	                    edit_no = $(this).attr('now_no');
	                    get_data($panel);
	                })
	                
	                function get_data($panel){
	                              
		                var $data   = $("#data_"+edit_no);
		                var $dialog = $("#dialog_content")
		                
						////////// text
						$panel.find("input[type='text']").each(function(){
							$(this).val( $data.attr( $(this).prop("name") ) || "" );
						})
						
						///////// not input
						$panel.find("td[id]").each(function()
						{
							var tmp_val = $data.attr($(this).prop("id"));
							if( $(this).attr("input")!=undefined || $(this).attr("unreply_date")!=undefined )
							{
								var tmp_attr = $(this).attr("input_with")||"";
								if( tmp_attr!='' )
								{
									var b_progress = Number($data.attr(tmp_attr))==1?false:true;
									$panel.find("input.edit_btn").show()
									if( tmp_val=="0000-00-00 00:00:00" && !b_progress )
									{
										tmp_val = "尚未回覆";
									}
									else if ( !b_progress )
									{
										tmp_val = $("<input type='text' name='"+$(this).prop("id")+"' />").val(tmp_val).change(function(){
											$data.attr( $(this).prop("name") ,$(this).val() );
										});
									}
									else if( b_progress )
									{
										$panel.find("input.edit_btn").hide();
									}
								}
							}
							$(this).html( tmp_val );
						})
	                }
	                
	                $panel.find("input[type!='button'][type!='checkbox'][type!='radio']").unbind("change")
	                $panel.find("input[type!='button'][type!='checkbox'][type!='radio']").change(function(){
		                $("#data_"+edit_no).attr($(this).prop("name"),$(this).val() );
	                })
	                
	                //所有開頭是data_都隱藏
	                $("*[id^='data_']").hide();
	                
	                ////////////////////////////////////////////////////////////////////////////////////////////////////////start edit
	                $panel.find("input.edit_btn").unbind("click");
	                $panel.find("input.edit_btn").click(function(){
	                    //
	                    if(!chk_all_input("#editPanel")){
		                    return;
	                    }
						$data = $("#data_"+edit_no);
						
						var reply_content 	= $data.attr("edit_reply_content");
						var member			= $data.attr("member")
						
	                    var form_data 	= new FormData();
	                    
						//return;
						form_data.append("query_type",	"<?php echo $subject;?>_edit");
						form_data.append("fi_no",		edit_no);
						form_data.append("reply_content",reply_content);
						form_data.append("member"		,member);
						
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
                
				/////////////////////////////////////////////////////////////////////////////for this page
				
				//////////////////////////////////////////////////////////////////////////////////// init
                
				init();
				
				
            });//jquery
            
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
	                $pager->display();
                	echo " 
                	<br>
					<table class='table-h' id='list_panel'>
						<tr>
                    		<td>ID</td>
                    		<td>會員編號</td>
                    		<td>反應內容</td>
                    		<td>處理狀態</td>
							<td>修改</td>
						</tr>";
						foreach($all_data as $per_data)
						{
							$tmp_no = $per_data["fi_no"];
							$tmp_member 	= $per_data["member"];
							$tmp_content 	= $per_data["content"];
							$tmp_date 		= $per_data["date"];
							$tmp_reply_content 	= $per_data["reply_content"];
							$tmp_reply_date  	= $per_data["reply_date"];
							$tmp_progress 		= $per_data["progress"];
							$tmp_progress_cht	= $tmp_progress==1?"未處理":"已處理";
							$tmp_edit_cht		= $tmp_progress==1?"回應":"查閱";
							echo "
						<tr>
							<td>{$tmp_no}</td>
							<td>{$tmp_member}</td>
							<td>$tmp_content</td>
							<td>{$tmp_progress_cht}</td>
							<td>";
							
							if($permissions2edit)
								echo "
								<input type='button' now_no='$tmp_no' value='{$tmp_edit_cht}' data-open-dialog='編輯{$subject_cht}'>
								<input type='hidden' id='data_{$tmp_no}' member='$tmp_member' content='$tmp_content' date='$tmp_date' 
													 edit_reply_content='$tmp_reply_content' reply_date='$tmp_reply_date' progress='$tmp_progress'/>
								";
							echo "
							</td>
						</tr>
							";
						}
					echo "
					</table>";
					$pager->display();
                    ?>
                    <div id="editPanel" data-dialog='編輯<?php echo $subject_cht;?>'>
                        <table class='table-v'>
	                        <tr>
                            	<td width="150px">
	                            	會員編號
	                            </td>
                                <td id='member'></td>
                            </tr>
                            <tr>
                            	<td>
	                            	反應內容
	                            </td>
                                <td id='content'></td>
                            </tr>
                            <tr>
                            	<td>
	                            	反應時間
                            	</td>
                                <td id='date'></td>
                            </tr>
                            <tr>
                            	<td>回復內容</td>
                                <td id='edit_reply_content' input input_with='progress'>
                                </td>
                            </tr>
                            <tr>
                            	<td>回復時間</td>
                                <td id='reply_date' unreply_date input_with='progress'></td>
                            </tr>
                            <tr>
                            	<td></td>
                            	<td>
                            		<input type="button" value="確認回復" class='edit_btn' style="cursor: pointer;">
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
