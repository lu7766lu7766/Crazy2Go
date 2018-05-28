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
        $permissions2visit	=	(strpos($login_permissions,"administrator_list")!==false||$login_permissions=="all")?true:false;
        $permissions2add	=	(strpos($login_permissions,"administrator_add")	!==false||$login_permissions=="all")?true:false;
        $permissions2edit	=	(strpos($login_permissions,"administrator_edit")!==false||$login_permissions=="all")?true:false;
        $permissions2del	=	(strpos($login_permissions,"administrator_del")	!==false||$login_permissions=="all")?true:false;
        
        $pager = new Pager();
        $all_user = $pager->query("select * from administrator");
        
        $permissions_assoc=$_SESSION['admin']['permissions_assoc'];
        
        
                
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
	                	
	                	var $this=$("#addPanel"); 
	                    var administrator_add_user 		= $this.find("input[name='user']").val();
	                    var administrator_add_password 	= $this.find("input[name='password']").val();
	                    var administrator_add_name 		= $this.find("input[name='name']").val();
	                    
	                    if( !chk_all_input(panel_id) ){
		                    return;
	                    }
	                    //console.log("add_user:"+administrator_add_user+"^^add+password:"+administrator_add_password+"^^add_name:"+administrator_add_name)
	                    //console.log("uer:"+administrator_add_user+"\npwd:"+administrator_add_password+"\nname:"+administrator_add_name);
	                    $.post(filename,{
	                			query_type:"administrator_add",
								user:administrator_add_user,
								password:administrator_add_password,
								name:administrator_add_name
							},function(data){
								
								data=$.trim(data);
	                    		if(data=="success"){
	                    			alert("新增成功！");
		                    		location.href = location.href;
	                    		}
	                    });
	                 });
                }
                
                //刪除
                $("input.del_btn").bind('click',function(){
                    if(!confirm("確定刪除？"))return;
                    var $this = $(this);
                    var fi_no = $this.attr('now_no');
                    var user = $("#data_"+fi_no).attr("user");
                    var name = $("#data_"+fi_no).attr("name");
                    
                    $this.unbind('click');
                    $.post(filename,{
                    		query_type:"administrator_del",
                    		fi_no:fi_no,
                    		user:user,
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
                    //permissions 更新
                    panel.find("input[type='checkbox'][name^='permission']").prop('checked',0);
                    panel.find("input[type='checkbox'][name^='permission']").each(function(){
                    	var permissions=$("#data_"+now_no).attr("permissions");
                    	if(permissions=="all"){
	                    	return $(this).prop("checked",true);
                    	}
	                    if(permissions.search( $(this).val() )>-1){ 
		                    $(this).prop("checked",true);
	                    }
                    })
                    //active更新
                    var active = $("#data_"+now_no).attr("active");
                    panel.find("input[type='radio'][value='"+active+"']").prop("checked",true);
                    //password update
					panel.find("input[name*='password']").each(function(){
						
						var password=$("#data_"+now_no).attr( $(this).prop("name") )||"";
						$(this).val(password);
						//console.log(password)
                    	if($(this).attr("name").search("confirm")>-1){
							$(this).val("");
						}
					})
                });
                //及時更新暫存資料(hidden暫存，沒存進資料庫)
                panel.find("input[type!='button'][type!='checkbox'][type!='radio']").keyup(function(){
	                var now_no=panel.attr('now_no');
	                $("#data_"+now_no).attr($(this).prop("name"),$(this).val() );
                })
                panel.find("input[type='radio']").click(function(){
                    var now_no=panel.attr('now_no');
                    $("#data_"+now_no).attr("active",$(this).val() );
                });
                //全選
                panel.find("input[value='all']:checkbox").click(function(){
					var name=$(this).prop("name");
					var check=$(this).prop("checked");
					$("input[name='"+name+"']").prop("checked",check);
					
					var now_no=panel.attr('now_no');
					if(check)
	                	$("#data_"+now_no).attr( "permissions",$(this).val() );
	                else
	                	$("#data_"+now_no).attr( "permissions","" );
				})
				//checkbox 所有項目選取，全選一並選取。有項目取消，全選項目一並取消。任一動作直接存入data內
				panel.find("input[value!='all']:checkbox").click(function(){
					//console.log(panel.find("input[value!='all']:checkbox").length+"^^aaaa^^"+panel.find("input[value!='all']:checked").length)
					if(panel.find("input[value!='all']:checkbox").length==panel.find("input[value!='all']:checkbox:checked").length){
						//$("input[value='all']").prop("checked",true);
						panel.find("input[value='all']:checkbox").trigger("click");
					}else{
						var now_no=panel.attr('now_no');
						var tmp_permissions="";
						var dot=","
						panel.find("input[value='all']").prop("checked",false);
						panel.find("input[value!='all']:checked").each(function(){
							tmp_permissions+=$(this).val()+dot;
						})
						//console.log(tmp_permissions);
						$("#data_"+now_no).attr( "permissions",tmp_permissions.slice( 0,-1 ) );
					}
				})
				
                panel.find("input[type='button']").click(function(){
                    //權限
                    if(!chk_all_input("#editPanel")){
	                    return;
                    }
                    var fi_no = $.trim(panel.attr("now_no"));
                    var active = $("#data_"+fi_no).attr("active");
                    var permissions = $("#data_"+fi_no).attr("permissions");
					var password = $("#data_"+fi_no).attr("edit_password");
					var user = $("#data_"+fi_no).attr("user");
					var name = $("#data_"+fi_no).attr("name");
					
                    //console.log("fi_no:"+fi_no+" active:"+active+" permissions:"+permissions+" password:"+password)
                    $.post(filename,{
                    		query_type:"administrator_edit",
                    		fi_no:fi_no,
                    		user:user,
                    		name:name,
                    		password:password,
                    		permissions:permissions,
                    		active:active
						},function(data){
							data=$.trim(data);
							if(data=="success"){
		                        alert("已更新！");
		                        location.href = location.href;
	                        }
	                        //console.log(data);
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
                        if($permissions2add){
                            echo "<input type='button' value='新增管理者' data-open-dialog='新增管理者'>";
                            echo "<div id='addPanel' data-dialog='新增管理者'>";
                            
                            echo "<table class='table-v'>";
                            echo 	"<tr><td>帳號<br>(新增後不可修改)</td>
                            			<td><input type='text' name='user' id='user' class='must user' table='administrator' minlength='8'/></td></tr>";
                            echo 	"<tr><td>密碼</td><td><input type='password' name='password' id='password' class='must' minlength='8'/></td></tr>";
                            echo 	"<tr><td>密碼確認</td><td><input type='password' name='confirm_password' id='confirm_password' class='must' equal='password'/></td></tr>";
                            echo 	"<tr><td>暱稱</td><td><input type='text' name='name' id='name' class='must'/></td></tr>";
                            echo 	"<tr><td></td><td><input class='add_btn' type='button' value='送出'></td></tr>";
                            echo "</table>";
                            
                            echo "</div>";
                        }else{
                            echo "你無權新增管理者";
                        }
                                    
                    ?>
                    <?php
	                	$pager->display();
						echo "<br>";
	                ?>
                    <table class="table-h" id="list_panel">
                        <?php
                            function build_permissions($acc_data){
                            	global $permissions_assoc;
                            	if($acc_data=="all"){
	                            	return "全權";
                            	}
                            	if(strpos($acc_data,",")!==false){
	                            	$dot=",";
                            	}else{
	                            	$dot=";";
                            	}
                            	
                                $permissions_cht = "";
                                $acc_data=explode($dot,$acc_data);
                                $last_permission=end($acc_data);
                                foreach ($acc_data as $value) {
                                	if(!isset($permissions_assoc[$value]))continue;
                                    $permissions_cht .= $permissions_assoc[$value];
                                    if($last_permission!=$value)$permissions_cht .= $dot;
                                }
                                if($permissions_cht=="")return "無任何權限";
                                return $permissions_cht;
                            }
                            
                            
                            echo "<tr>
                            		<td>ID</td>
                            		<td>暱名</td>
                            		<td>啟用</td>
                            		<td style='width:220px;'>權限</td>
                            		<td>修改</td>
                            		<td>刪除</td>
                            	</tr>";
                            
                            $len = count($all_user);
                            for($i = 0; $i < $len; $i++){
                            	$tmp_no=$all_user[$i]["fi_no"];
                            	$tmp_user=$all_user[$i]['user'];
                            	$tmp_name=$all_user[$i]['name'];
                            	$tmp_active=$all_user[$i]["active"];
                            	$tmp_permission=$all_user[$i]['permissions'];
                            	
                                echo "<tr>";
                                echo "<td width='10%'>".$tmp_no."</td>";
                                echo "<td width='15%'>".$tmp_name."</td>";
                                echo "<td width='10%'>".$tmp_active."</td>";
                                echo "<td>".build_permissions($tmp_permission)."</td>";
                                
                                echo "<td width='10%'>";
                                
                                if($permissions2edit)
                                    echo "<input type='button' now_no='{$tmp_no}' value='編輯' data-open-dialog='編輯管理者'>";	
                                echo "</td>";
                                
                                echo "<td width='10%'>";
                                if($permissions2del)
                                    echo "<input type='button' now_no='{$tmp_no}' class='del_btn' value='刪除'>";
                                
                                echo "<input type='hidden' id='data_{$tmp_no}' fi_no='{$tmp_no}' name='{$tmp_name}' user='{$tmp_user}' 
                                							permissions='{$tmp_permission}' active='{$tmp_active}' edit_password=''>";
                                echo "</td>";
                                echo "</tr>";
                            }
                        ?>
                    </table>
                    <?php 
	                    $pager->display();
	                ?>
                    <div id="editPanel" data-dialog="編輯管理者">
                        <table class='table-v'>
                            <tr>
                                <td width="30%">密碼修改<br>(若無密碼修改請留白)</td>
                                <td><input type="password" name="edit_password" id="edit_password" placeholder="請輸入密碼，至少8碼" minlength="8"></td>
                            </tr>
                            <tr>
                                <td>密碼確認</td>
                                <td><input type="password" name="edit_confirmpassowrd" id="edit_confirmpassowrd" placeholder="再次確認密碼" equal="edit_password"></td>
                            </tr>
                            <tr>
                                <td>啟用設定</td>
                                <td>
                                	<input type="radio" name="edit_active" id="edit_active_on" value="1">
                                	<label for="edit_active_on">啟用</label>
                                	<input type="radio" name="edit_active" id="edit_active_off" value="0">
                                	<label for="edit_active_off">停用</label>
                                </td>
                            </tr>
                            <tr>
                            	<td>權限設定</td>
                                <td>
                                <?php 
                                	echo "<table class='table-n'>";
                                	echo "<tr><td><input type='checkbox' class='must' name='permissions[]' value='all' id='all'/><label for='all'>全選</label></td></tr>";
                                	$i=1;
                                    foreach($permissions_assoc as $key => $value){
                                    	if($i%4==1 && floor($i/4)!=0)
                                    		echo "<tr>";
                                        echo "<td>";
                                        if($value!='')
                                        	echo "<input type='checkbox' name='permissions[]' value='{$key}' id='{$key}'/><label for='{$key}'>{$value}</label>";
                                        echo "</td>";
                                        if($i%4==0)
                                    		echo "</tr>";
										$i++;
                                    }
                                    echo "</table>";
                                ?>
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
