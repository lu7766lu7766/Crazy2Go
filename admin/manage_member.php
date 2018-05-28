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
		$permissions2visit	= (strpos($login_permissions,"member_list")	!==false||$login_permissions=="all")?true:false;
        
        $permissions2edit	= (strpos($login_permissions,"member_edit")	!==false||$login_permissions=="all")?true:false;
        $permissions2del	= (strpos($login_permissions,"member_del")	!==false||$login_permissions=="all")?true:false;
        $pager = new Pager();
        
        $all_store = $pager->query("select fi_no,id,name,email,phone,verification from member_index order by fi_no");
        $dba = $pager->get_dba();
           
    }else{
        header("Location:index.php");
    }
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title><?php echo $html_title;?></title>
        <link rel="stylesheet" type="text/css" href="../public/css/jquery.datetimepicker.css"/>
        <?php echo $html_resource;?>
        <script type="text/javascript" src="dialog_synchronization.js"></script>
        <script src="../public/js/jquery.datetimepicker.js"></script>
        <script src="date_setting.js"></script>
        <script>
            /******************** user define js ********************/
            $(document).ready(function(){
            	
                //ajax
                //filename write in template.js
                
                //刪除
                $("input.del_btn").bind('click',function(){
                    if(!confirm("確定刪除？"))return;
                    
                    var fi_no = $(this).attr('now_no');
                    var $data = $("#data_"+fi_no);
                    var id = $data.attr("member_id");
                    var name = $data.attr("name");
                    
                    $this.unbind('click');
                    $.post(filename,{
                    		query_type:"member_del",
                    		fi_no:fi_no,
                    		id:id,
                    		name:name
						},function(data){
							data=$.trim(data);
							if(data=="success"){
		                        alert("已刪除！");
		                        location.href = location.href;
	                        }
                    });
                });
        
                //編輯
                var panel = $("#editPanel");
                
                $("#list_panel input[type='button'][data-open-dialog]").mouseenter(function(){
                    var now_no=$(this).attr('now_no');
                    panel.attr('now_no',now_no);
                    //console.log(now_no+"^^"+filename)
                    ///////// ajax取資料
                    var b_ajax=$("#data_"+now_no).data("b_ajax")||true;//<--不需要ajax改這裡
                    if(b_ajax){
	                    $.post(filename,{
	                    		query_type:"get_member_info",
	                    		fi_no:now_no
							},function(data){
								data = $.parseJSON(data);
								if(data!==false){
									$("#data_"+now_no).data("b_ajax",false);
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
	                    	var checkbox_val=$("#data_"+now_no).attr(name);
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
                    	var radio_val = $("#data_"+now_no).attr(name);
                    	//console.log("name:"+name+"\n val:"+radio_val)
						panel.find("input[name='"+name+"'][value='"+radio_val+"']").prop("checked",true);
                    }
                    
                    ////////////password update
					panel.find("input[type='password']").each(function(){
						
						var password=$("#data_"+now_no).attr( $(this).prop("name") )||"";
						$(this).val(password);
						//console.log(password)
                    	if($(this).attr("name").search("confirm")>-1){
							$(this).val("");
						}
					})
					
					////////// text
					panel.find("input[type='text']").each(function(){
						var text_val=$("#data_"+now_no).attr( $(this).prop("name") )||"";
						$(this).val(text_val);
					})
					
					///////// not input
					panel.find("td[id]").each(function(){
						var targer_attr=$(this).prop("id");
						$(this).html($("#data_"+now_no).attr(targer_attr));
					})
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
                    console.log(name+"^^"+val)
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
				
                panel.find("input[type='button']").click(function(){
                    //權限
                    if(!chk_all_input("#editPanel")){
	                    return;
                    }
					var fi_no = $.trim(panel.attr("now_no"));
					$data = $("#data_"+fi_no);
					var id 			= $data.attr("member_id");
                    var name		= $data.attr("edit_name");
					var email		= $data.attr("edit_email");
					var phone 		= $data.attr("edit_phone");
					var sex 		= $data.attr("edit_sex");
					var qq 			= $data.attr("edit_qq");
					var password 	= $data.attr("edit_password");
					var birthday 	= $data.attr("edit_birthday");
					var verification = $data.attr("edit_verification");
					
					console.log("id:"+id+"\n name:"+name+"\n email:"+email+"\n phone:"+"\n sex:"+sex+"\n qq:"+qq+"\n password:"+password+"\n birthday:"+birthday+"\n verification:"+verification)
					$.post(filename,{
                    		query_type:"member_edit",
                    		fi_no:	fi_no,
                    		name:	name,
                    		email:	email,
                    		phone:	phone,
                    		sex:	sex,
                    		qq:		qq,
                    		password:	password,
                    		birthday:	birthday,
                    		verification: verification
						},function(data){
							data=$.trim(data);
							if(data=="success"){
		                        alert("已更新！");
		                        location.href = location.href;
	                        }
	                        console.log(data);
                    });
                });
            });
        </script>
        <style>
            /******************** user define css ********************/

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
	                <?php 
		                $pager->display();
		                echo "<br>";
		            ?>
                    <table class="table-h" id="list_panel">
                        <?php
                            echo "<tr>
                            		<td>ID</td>
                            		<td>帳號</td>
                            		<td>暱稱</td>
                            		<td>信箱</td>
                            		<td>電話</td>
                            		<td>啟用</td>
                            		<td>修改</td>
                            		<td>刪除</td>
                            	</tr>";
                            
                            $len = count($all_store);
                            for($i = 0; $i < $len; $i++){
                            	//display
                            	$tmp_no				= $all_store[$i]["fi_no"];
                            	$tmp_id				= $all_store[$i]["id"];
                            	$tmp_name			= $all_store[$i]["name"];
                            	$tmp_email			= $all_store[$i]["email"];
                            	$tmp_phone			= $all_store[$i]["phone"];
                            	$tmp_verification	= $all_store[$i]["verification"]?"1":"0";
                            	//none
                            	$tmp_password		= $all_store[$i]["password"];
                            	//member_info  ajax去撈
                            	//$tmp_sex			= $all_store[$i]["sex"];
                            	//$tmp_birthday		= $all_store[$i]["birthday"];
                            	//$tmp_qq				= $all_store[$i]["qq"];
                            	
                                echo "<tr>";
                                echo "<td>".$tmp_no."</td>";
                                echo "<td>".$tmp_id ."</td>";
                                echo "<td>".$tmp_name."</td>";
                                echo "<td>".$tmp_email."</td>";
                                echo "<td>".$tmp_phone."</td>";
                                echo "<td>".$tmp_verification."</td>";
                                
                                echo "<td>";
                                if($permissions2edit)
                                    echo "<input type='button' now_no='{$tmp_no}' value='編輯' data-open-dialog='編輯會員'>";
                                	
                                echo "</td>";
                                echo "<td>";
                                
                                if($permissions2del)
                                    echo "<input type='button' now_no='{$tmp_no}' class='del_btn' value='刪除'>";
                                echo "		<input type='hidden' 	id = 'data_{$tmp_no}' 	member_id = '{$tmp_id}'		edit_name  = '{$tmp_name}'
                                				   edit_sex = ''	edit_birthday = ''		edit_email = '{$tmp_email}'	edit_phone = '{$tmp_phone}'	
                                				   edit_qq  = ''	edit_password = ''		edit_verification = '{$tmp_verification}'>";
                                echo "</td>";
                                echo "</tr>";
                            }
                        ?> 
                    </table>
                    <?php 
	                    $pager->display();
                    ?>
                    <div id="editPanel" data-dialog='編輯會員'>
                        <table class='table-v'>
                            <tr>
                                <td>帳號</td>
                                <td id="member_id"></td>
                            </tr>
                            <tr>
                            	<td>暱稱</td>
                                <td><input type="text" name="edit_name" id="edit_name"></td>
                            </tr>
                            <tr>
                                <td>密碼修改<br>(若無密碼修改請留白)</td>
                                <td><input type="password" name="edit_password" id="edit_password" placeholder="請輸入密碼，至少8碼" minlength="8"></td>
                            </tr>
                            <tr>
                                <td>密碼確認</td>
                                <td><input type="password" name="edit_confirmpassowrd" id="edit_confirmpassowrd" placeholder="再次確認密碼" equal="edit_password"></td>
                            </tr>
                             <tr>
	                            <td>性別</td>
	                            <td>
	                            	<input type="radio" name="edit_sex" id="edit_sex_1" value="1">
                                	<label for="edit_sex_1">男性</label>
                                	<input type="radio" name="edit_sex" id="edit_sex_0" value="0">
                                	<label for="edit_sex_0">女性</label>
                                </td>
                            </tr>
                            <tr>
                            	<td>電話</td>
                                <td><input type="text" name="edit_phone" id="edit_phone"></td>
                            </tr>
                            <tr>
                            	<td>生日</td>
                                <td><input type="text" name="edit_birthday" id="edit_birthday" class="date"></td>
                            </tr>
                            <tr>
                            	<td>QQ</td>
                                <td><input type="text" name="edit_qq" id="edit_qq"></td>
                            </tr>
                            <tr>
	                            <td>啟用設定</td>
	                            <td>
	                            	<input type="radio" name="edit_verification" id="edit_verification_on" value="1">
                                	<label for="edit_verification_on">啟用</label>
                                	<input type="radio" name="edit_verification" id="edit_verification_off" value="0">
                                	<label for="edit_verification_off">停用</label>
                                </td>
                            </tr>
                            <tr><td></td><td><input type="button" value="儲存" style="cursor: pointer;"></td></tr>
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
