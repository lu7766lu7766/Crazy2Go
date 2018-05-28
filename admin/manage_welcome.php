<?php 
    session_start();
    include '../swop/setting/config.php';
    include '../backend/template.php';
    require_once '../swop/library/dba.php';
    $dba = new dba();
    if(isset($_POST['account']) && isset($_POST['password']))
    {
        //帳密
        $account = $_POST['account'];
        $password = $_POST['password'];
        
        //撈員工資料
        
        $query = "select * from administrator where user='$account'";
        $result = $dba->query($query);
        //檢查帳密
        $len = count($result);
        for($i = 0; $i < $len; $i++){
            if($result[$i]['user'] == $account && $result[$i]['password'] == md5(md5($password))){
                //未啟用返回登入頁
                if($result[$i]['active']==0){
	                
                    header("Location:index.php?status=該帳號已遭到停權，請聯繫管理員");
                    
                }
                
                //記錄登入者資料
                $login_fi_no 		= $result[$i]['fi_no'];
                $login_name 		= $result[$i]['name'];
                $login_permissions 	= $result[$i]['permissions'];
                $login_user 		= $result[$i]['user'];
                $b_all 				= $login_permissions=="all" ? true : false;
                //session寫入
                $_SESSION['admin']['login_fi_no'] = $login_fi_no;
                $_SESSION['admin']['login_name'] = $login_name;
                $_SESSION['admin']['login_permissions'] = $login_permissions;
                $_SESSION['admin']['login_user'] = $login_user;
                $_SESSION['admin']['login_username'] = $login_user."(".$login_name.")";
                $_SESSION['admin']['login_title'] = "<div>管理員".$login_name."先生/小姐您好!".($b_all?"您目前為最高管理權限":"您目前為普通管理者權限")."</div>";
                
                $login_username = $_SESSION['admin']['login_username'];
                $login_title = $_SESSION['admin']['login_title'];
                                
                //製作畫面左側按鈕清單
	            $menu  = "";
	            $menu .= "<p><a href='manage_welcome.php'>首頁</a></p>";
	            $menu .= (strpos($login_permissions,"administrator_list")	!==false||$b_all)? "<p><a href='manage_administrator.php'>	管理者管理</a></p>":"";
	            $menu .= (strpos($login_permissions,"store_lsit")		 	!==false||$b_all)? "<p><a href='manage_store.php'>			店家管理</a></p>":"";
	            $menu .= (strpos($login_permissions,"member_list")		 	!==false||$b_all)? "<p><a href='manage_member.php'>			會員管理</a></p>":"";
	            $menu .= (strpos($login_permissions,"goods_category_list")	!==false||$b_all)? "<p><a href='manage_goods_category.php'>	商品分類管理</a></p>":"";
	            $menu .= (strpos($login_permissions,"goods_attr_list")		!==false||$b_all)? "<p><a href='manage_goods_attr.php'>		商品屬性管理</a></p>":"";
	            $menu .= (strpos($login_permissions,"floors_list")			!==false||$b_all)? "<p><a href='manage_floors.php'>			首頁樓層列表管理</a></p>":"";
	            $menu .= (strpos($login_permissions,"brand_list")			!==false||$b_all)? "<p><a href='manage_brand.php'>			品牌列表管理</a></p>":"";
	            $menu .= (strpos($login_permissions,"goods_check_list")		!==false||$b_all)? "<p><a href='manage_goods_check.php'>	商品審核</a></p>":"";
	            $menu .= (strpos($login_permissions,"qanda_category_list")	!==false||$b_all)? "<p><a href='manage_qanda_category.php'>	幫助中心分類管理</a></p>":"";
	            $menu .= (strpos($login_permissions,"qanda_item_list")		!==false||$b_all)? "<p><a href='manage_qanda_item.php'>		幫助中心項目管理</a></p>":"";
	            $menu .= (strpos($login_permissions,"advertisement_list")	!==false||$b_all)? "<p><a href='manage_advertisement.php'>	廣告管理</a></p>":"";
	            $menu .= (strpos($login_permissions,"appeal_list")			!==false||$b_all)? "<p><a href='manage_appeal.php'>			會員申訴管理</a></p>":"";
	            $menu .= (strpos($login_permissions,"main_item_list")		!==false||$b_all)? "<p><a href='manage_main_item.php'>		首頁焦點項目管理</a></p>":"";
	            $menu .= (strpos($login_permissions,"brand_group_list")		!==false||$b_all)? "<p><a href='manage_brand_group.php'>	品牌列表管理-group</a></p>":"";
	            $menu .= (strpos($login_permissions,"global_item_list")		!==false||$b_all)? "<p><a href='manage_global_item.php'>	全域項目管理</a></p>":"";
	            
	            $menu .= "<p><a href='javascript:void(0);'>登出</a></p>";
	            
	            $_SESSION['admin']['menu'] = $menu;
	            //權限中英對照表，空值為了使管理員權限管理對齊
		        $_SESSION['admin']['permissions_assoc'] = array(
		        	'administrator_list'=> "管理者查詢"
		           ,'administrator_add'	=> "管理者新增"
		           ,'administrator_edit'=> "管理者編輯"
		           ,'administrator_del' => "管理者刪除"
		            //
		           ,'store_list'	=> "店家查詢"
		           ,'store_add'		=> "店家新增"
		           ,'store_edit'	=> "店家編輯"
		           ,'store_del' 	=> "店家刪除"
		            //會員新增在前台
		           ,'member_list'		=> "會員查詢"
		           ,'member_add' 		=> ""
		           ,'member_edit'		=> "會員編輯"
		           ,'member_del' 		=> "會員刪除"
		            //
		           ,'goods_category_list'	=> "商品分類查詢"
		           ,'goods_category_add'	=> "商品分類新增"
		           ,'goods_category_edit'	=> "商品分類編輯"
		           ,'goods_category_del'	=> "商品分類刪除"
		           
		            //
		           ,'goods_attr_list'	=> "商品屬性查詢"
		           ,'goods_attr_add'	=> "商品屬性新增"
		           ,'goods_attr_edit'	=> "商品屬性編輯"
		           ,'goods_attr_del'	=> "商品屬性刪除"
		            //
		           ,'floors_list'	=> "首頁樓層列表查詢"
		           ,'floors_add'	=> "首頁樓層列表新增"
		           ,'floors_edit'	=> "首頁樓層列表編輯"
		           ,'floors_del'	=> "首頁樓層列表刪除"
		            //
		           ,'brand_list'	=> "品牌列表查詢"
		           ,'brand_add'		=> "品牌列表新增"
		           ,'brand_edit'	=> "品牌列表編輯"
		           ,'brand_del'		=> "品牌列表刪除"
		            //
		           ,'goods_check_list'	=> "商品審核查詢"
		           ,'goods_check_add'	=> ""
		           ,'goods_check_edit'	=> "商品審核權限"
		           ,'goods_check_del'	=> ""
		            //
		           ,'qanda_category_list'	=> "幫助中心分類查詢"
		           ,'qanda_category_add'	=> "幫助中心分類新增"
		           ,'qanda_category_edit'	=> "幫助中心分類編輯"
		           ,'qanda_category_del'	=> "幫助中心分類刪除"
		            //
		           ,'qanda_item_list'	=> "幫助中心項目查詢"
		           ,'qanda_item_add'	=> "幫助中心項目新增"
		           ,'qanda_item_edit'	=> "幫助中心項目編輯"
		           ,'qanda_item_del'	=> "幫助中心項目刪除"
		           //
		           ,'advertisement_list'	=> "廣告查詢"
		           ,'advertisement_add'		=> "廣告新增"
		           ,'advertisement_edit'	=> "廣告編輯"
		           ,'advertisement_del'		=> "廣告刪除"
		           //
		           ,'appeal_list'	=> "申訴查詢"
		           ,'appeal_add'	=> ""
		           ,'appeal_edit'	=> "申訴回應"
		           ,'appeal_del'	=> ""
		           //
		           ,'main_item_list'	=> "首頁焦點項目查詢"
		           ,'main_item_add'		=> "首頁焦點項目新增"
		           ,'main_item_edit'	=> "首頁焦點項目編輯"
		           ,'main_item_del'		=> "首頁焦點項目刪除"
		           //
		           ,'brand_group_list'	=> "品牌列表(group)查詢"
		           ,'brand_group_add'	=> "品牌列表(group)新增"
		           ,'brand_group_edit'	=> "品牌列表(group)編輯"
		           ,'brand_group_del'	=> "品牌列表(group)刪除"
		           //get_history
		           ,'get_history'	=> "取得歷史點擊數"
		        );
                
                //更新登入時間
                $query = "insert into administrator_log (administrator,action,action_date) values ('$login_store','$login_fi_no','$login_username 登入', NOW())";
                $dba->query($query);
                
                $analysis_html = flow_analysis();
                break;
                
            }
            
        }
        //帳號或密碼錯誤
        if(empty($_SESSION['admin']['login_user']))
        {
            header("Location:index.php?status=帳號或密碼錯誤");
        }
    }else{
        //沒Session資料
        if(!isset($_SESSION)){
	        
            header("Location:index.php");
            
        }else{
	        
            $login_fi_no = $_SESSION['admin']['login_fi_no'];
            $login_name = $_SESSION['admin']['login_name'];
            $login_permissions = $_SESSION['admin']['login_permissions'];
            $login_user = $_SESSION['admin']['login_user'];
            $login_username = $_SESSION['admin']['login_username'];
            $login_title = $_SESSION['admin']['login_title'];
            $menu = $_SESSION['admin']['menu'];
            $analysis_html = flow_analysis();
            
        }
        
    }
    
    function flow_analysis(){
	    
	    global $dba;
	    //幾天星期幾
	    $day_is_today = date('w',time());
	    //php識別星期天為一星期的結束，在此星期天為一星期的開始
	    $last_week = $day_is_today==0?-1:-2;
	    $this_week = $last_week+1;
	    //上星期天開始
	    $last_week_start = date("Y-m-d H:i:s",strtotime("Sunday {$last_week} week"));
	    //上上星期六的結束
	    $last2week_end = date("Y-m-d H:i:s",strtotime("-1 seconds",$last_week_start));
	    //上星期六的結束
	    $last_week_end = $day_is_today==0?date("Y-m-d H:i:s",strtotime("today -1 seconds")):date("Y-m-d H:i:s",strtotime("Sunday {$this_week} week -1 seconds"));
	    //昨日的開始
	    $yesterday_start = date("Y-m-d H:i:s",strtotime("today -1 day"));
	    //昨日的結束
        $yesterday_end = date("Y-m-d H:i:s",strtotime("today -1 seconds"));
        //這星期的開始
        $this_week_start = $day_is_today == 0 ?date("Y-m-d H:i:s",strtotime("today")) :date("Y-m-d H:i:s",strtotime("Sunday -1 week"));
        //這星期六的結束
        $this_week_end = $day_is_today==0 ?date("Y-m-d H:i:s",strtotime("Sunday -1 week -1 second")):date("Y-m-d H:i:s",strtotime("Sunday -1 seconds"));
        //今天的開始
        $today_start = date("Y-m-d H:i:s",strtotime("today"));
        //金梯的結束
        $today_end = date("Y-m-d H:i:s",strtotime("today +1 day -1 seconds"));
        
        //今日流量
        $sql = "select count(1) as ct 
        			from (select distinct date_format(`time_enter`, '%Y-%m-%d'), session_id 
        				from `history` where `time_enter`>='$today_start' and type='1') as temp";
        $result = $dba->query($sql);
        $today_flow = $result[0]["ct"];
        
        //昨日流量
        $sql = "select count(1) as ct 
        			from (select distinct date_format(`time_enter`, '%Y-%m-%d'), session_id 
        				from `history` where `time_enter` between '$yesterday_start' and '$yesterday_end' and type='1') as temp";
        $result = $dba->query($sql);
        $yesterday_flow = $result[0]["ct"];
        
        //本週流量
        $sql = "select count(1) as ct 
        			from (select distinct date_format(`time_enter`, '%Y-%m-%d'), session_id 
        				from `history` where `time_enter` between '$this_week_start' and '$yesterday_end' and type='1') as temp";
        $result = $day_is_today==0 ?0 :$dba->query($sql);
        $this_week_flow = $today_flow + $day_is_today==0 ?0 :$result[0]["ct"];
        
        //上週流量
        $sql = "select count(1) as ct 
        			from (select distinct date_format(`time_enter`, '%Y-%m-%d'), session_id 
        				from `history` where `time_enter` between '$last_week_start' and '$last_week_end' and type='1') as temp";
        $result = $dba->query($sql);
        $last_week_flow = $result[0]["ct"];
        
        //總流量
        $sql = "select count(1) as ct 
        			from (select distinct date_format(`time_enter`, '%Y-%m-%d'), session_id 
        				from `history` where `time_enter` <= '$last2week_end' and type='1') as temp";
        $result = $dba->query($sql);
        $total_flow = $result[0]["ct"] + $last_week_flow + $this_week_flow;
        
        //今日註冊數
        $sql = "select count(1) as ct from member_index where `date_added`>='$today_start'";
        $result = $dba->query($sql);
        $today_apply = $result[0]["ct"];
        
        //昨日註冊數
        $sql = "select count(1) as ct from member_index where `date_added` between '$yesterday_start' and '$yesterday_end'";
        $result = $dba->query($sql);
        $yesterday_apply = $result[0]["ct"];
        
        //總會員數
        $sql = "select count(1) as ct from member_index where `date_added` < '$yesterday_start'";
        $result = $dba->query($sql);
        $total_apply = $result[0]["ct"] + $yesterday_apply + $today_apply;
        
        $analysis_html .= "<br><table>";
	    $analysis_html .= "<tr><td width='100px' >本日流量：{$today_flow}</td><td>(".date("Y/m/d H:i",strtotime($today_start))
					   .  "&nbsp;至&nbsp;".date("H:i",strtotime($today_end)).")</td></tr>"
					   .  "<tr><td>昨日流量：{$yesterday_flow}</td><td>(".date("Y/m/d H:i",strtotime($yesterday_start))
					   .  "&nbsp;至&nbsp;".date("H:i",strtotime($yesterday_end)).")</td></tr>"
					   .  "<tr><td>本週流量：{$this_week_flow}</td><td>(".date("Y/m/d H:i",strtotime($this_week_start))
					   .  "&nbsp;至&nbsp;".date("Y/m/d H:i",strtotime($this_week_end)).")</td></tr>"
					   .  "<tr><td>上週流量：{$last_week_flow}</td><td>(".date("Y/m/d H:i",strtotime($last_week_start))
					   .  "&nbsp;至&nbsp;".date("Y/m/d H:i",strtotime($last_week_end)).")</td></tr>"
					   .  "<tr><td>總流量：  {$total_flow}</td><td>(2014/12/28 00:00&nbsp;至&nbsp;".date("Y/m/d H:i",strtotime($today_end)).")</td></tr>"
					   .  "<tr><td>本日註冊：{$today_apply}</td><td></td></tr>"
					   .  "<tr><td>昨日註冊：{$yesterday_apply}</td><td></td></tr>"
					   .  "<tr><td>總會員數：{$total_apply}</td><td></td></tr>"
					   .  "</table>";
		
		return $analysis_html;
    }
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title><?php echo $html_title;?></title>
        <?php echo $html_resource;?>
        <script>
            /******************** user define js ********************/
            $(function(){
                
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
                    <?php echo $login_title; ?>
	                <?php echo $analysis_html; ?>
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
