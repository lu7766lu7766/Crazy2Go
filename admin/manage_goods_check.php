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
        $subject = "goods_check";
        $subject_cht = "商品審核";
		$permissions2visit	= (strpos($login_permissions, $subject."_list")	!==false||$login_permissions=="all")?true:false;
        $permissions2edit	= (strpos($login_permissions, $subject."_edit")	!==false||$login_permissions=="all")?true:false;
        $_SESSION['admin']['check'] = $permissions2edit?1:0;
        $pager = new Pager();
        $all_data = $pager->query("select `fi_no`,`images`,`store`,`name`,`status_shelves`,`status_audit` 
        							from `goods_index` order by `status_audit` asc, `status_shelves` desc");
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
        <?php echo $html_resource;?>
        <script type="text/javascript" src="dialog_synchronization.js"></script>
        <script>
            /******************** user define js ********************/
			
            $(document).ready(function()
            {	
                //ajax
                //filename write in template.js
                /////////////////////////////////////////////////////////////////////////////for this page
				
				//////////////////////////////////////////////////////////////////////////////////// init
                
            });//jquery
            function pass_check(obj)
            {
	            fi_no = $(obj).attr("fi_no");
	            //console.log(fi_no);
	            //console.log($("#list_"+fi_no+" .name").html());
	            //return;
                $.post(filename,{
	                			query_type:	"<?php echo $subject;?>_add"
	                		   ,fi_no:fi_no
	                		   ,name:$("#list_"+fi_no+" .name").html()
					},function(data){
						
						data=$.trim(data);
						
                		if(data=="success")
                		{
                			alert("審核通過！");
                    		location.reload();
                		}
                		console.log(data);
                });
            }
            
            function fail_check(obj)
            {
	            fi_no = $(obj).attr("fi_no");
	            //console.log(fi_no);
	            //console.log($("#list_"+fi_no+" .name").html());
	            //return;
                $.post(filename,{
	                			query_type:	"<?php echo $subject;?>_del"
	                		   ,fi_no:fi_no
	                		   ,name:$("#list_"+fi_no+" .name").html()
					},function(data){
						
						data=$.trim(data);
						
                		if(data=="success")
                		{
                			alert("審核撤消！");
                    		location.reload();
                		}
                		console.log(data);
                });
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
	                $pager->display();
	                	////ID、images(產品圖)、store(店名)、傷品名(name)、上架狀態(status_shelves)、審核狀態(status_audit)
                	echo " 
                	<br>
					<table class='table-h' id='list_panel'>
						<tr>
                    		<td>ID</td>
                    		<td>產品圖</td>
                    		<td>店名</td>
                    		<td>商品名</td>
							<td>上架狀態</td>
							<td>審核狀態</td>
							<td>審核</td>
							<td>預覽商品</td>
						</tr>";
						foreach($all_data as $key=>$per_data)
						{
							//break;
							//if($key>=10)break;
							$tmp_no = $per_data["fi_no"];
							$a_images 	= (array)json_decode($per_data["images"]);
							$tmp_images = $a_images[0];
							
							$tmp_store	= $per_data["store"];
							$result = $dba->query("select `name` from `store` where `fi_no`='$tmp_store' limit 1");
							$tmp_store_cht 	= $result[0]['name'];
							$tmp_name 		= $per_data["name"];
							$tmp_status_shelves 	= $per_data["status_shelves"];
							$tmp_status_shelves_cht	= $tmp_status_shelves==1?"上架":"未上架";
							$tmp_status_audit  		= $per_data["status_audit"];
							if($tmp_status_audit==1)
								$tmp_status_audit_cht = "審核通過";
							else
								if($tmp_status_audit==2)
									$tmp_status_audit_cht = "審核失敗";
								else
									$tmp_status_audit_cht = "未審核";
							echo "
						<tr id='list_{$tmp_no}'>
							<td>{$tmp_no}</td>
							<td><img src='../public/img/thumbnail/{$tmp_images}' style='max-height:150px;max-width:150px;'></td>
							<td>$tmp_store_cht</td>
							<td class='name'>$tmp_name</td>
							<td>$tmp_status_shelves_cht</td>
							<td>$tmp_status_audit_cht</td>
							<td>";
							if($permissions2edit)
							{
								echo "<input type='button' fi_no='$tmp_no' value='通過' onclick='javascript:pass_check(this)'><br><br>";
								echo "<input type='button' fi_no='$tmp_no' value='撤消' onclick='javascript:fail_check(this)'>";
							}
								
							echo "
							</td>
							<td>
								<a href='http://www.crazy2go.com/goods?no={$tmp_no}&nohistory=1' target='_blank'>預覽</a>
							</td>
						</tr>
							";
						}
					echo "
					</table>
					<br>";
					$pager->display();
                    ?>
                   
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
