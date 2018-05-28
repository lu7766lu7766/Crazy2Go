<?php 
    session_start();
    include '../swop/setting/config.php';
    include '../backend/template.php';
    
    if(isset($_POST['account']) && isset($_POST['password']))
    {
        //帳密
        $account = $_POST['account'];
        $password = $_POST['password'];
        
        //撈員工資料
        require_once '../swop/library/dba.php';
        $dba = new dba();
        $query = 'select fi_no,store,active,permissions,name,user,password from store_account order by fi_no asc';
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
                $login_fi_no = $result[$i]['fi_no'];
                $login_store = $result[$i]['store'];
                $login_name = $result[$i]['name'];
                $login_permissions = $result[$i]['permissions'];
                $login_user = $result[$i]['user'];
                $login_store_name = $dba->query("select name from store where fi_no=".$login_store);
                $login_store_name = $login_store_name[0]["name"];
                
                
                $_SESSION['backend']['login_fi_no'] = $login_fi_no;
                $_SESSION['backend']['login_store'] = $login_store;
                $_SESSION['backend']['login_store_name'] = $login_store_name;
                $_SESSION['backend']['login_name'] = $login_name;
                $_SESSION['backend']['login_permissions'] = $login_permissions;
                $_SESSION['backend']['login_user'] = $login_user;
                $_SESSION['backend']['login_username'] = $login_user."(".$login_name.")";
                $_SESSION['backend']['login_title'] = "<div>管理員".$login_name."先生/小姐您好!".(strpos($login_permissions, "all")!==false?"您目前為商家權限":"您目前為員工權限")."</div>";
                $login_username = $_SESSION['backend']['login_username'];
                $login_title = $_SESSION['backend']['login_title'];
                
                //製作畫面左側按鈕清單
                $menu="";
                $menu .= "<p><a href='manage_welcome.php'>首頁</a></p>";
                $menu .= strpos($login_permissions,"all")!==false?"<p><a href='manage_store.php'>店家設定</a></p>":"";
                $menu .= strpos($login_permissions,"employee")!==false || strpos($login_permissions,"all")!==false?"<p><a href='manage_employee.php'>員工管理</a></p>":"";
                $menu .= strpos($login_permissions,"supplier")!==false || strpos($login_permissions,"all")!==false?"<p><a href='manage_supplier.php'>供應商管理</a></p>":"";
                $menu .= strpos($login_permissions,"product")!==false || strpos($login_permissions,"all")!==false?"<p><a href='manage_product.php'>商品管理</a></p>":"";
                $menu .= strpos($login_permissions,"evaluate")!==false || strpos($login_permissions,"all")!==false?"<p><a href='manage_evaluate.php'>評論管理</a></p>":"";
                $menu .= strpos($login_permissions,"appeal")!==false || strpos($login_permissions,"all")!==false?"<p><a href='manage_appeal.php'>申訴管理</a></p>":"";
                $menu .= strpos($login_permissions,"service")!==false || strpos($login_permissions,"all")!==false?"<p><a href='manage_service.php'>客服管理</a></p>":"";
                $menu .= strpos($login_permissions,"picking")!==false || strpos($login_permissions,"all")!==false?"<p><a href='manage_picking.php'>自訂揀貨單模板</a></p>":"";
                $menu .= strpos($login_permissions,"logi")!==false || strpos($login_permissions,"all")!==false?"<p><a href='manage_logi_form.php'>自訂物流單模板</a></p>":"";
                $menu .= strpos($login_permissions,"order")!==false || strpos($login_permissions,"all")!==false?"<p><a href='manage_order.php'>訂單管理</a></p>":"";
                $menu .= strpos($login_permissions,"returns")!==false || strpos($login_permissions,"all")!==false?"<p><a href='manage_returns.php'>退貨列表</a></p>":"";
                $menu .= strpos($login_permissions,"exchange")!==false || strpos($login_permissions,"all")!==false?"<p><a href='manage_exchange.php'>換貨列表</a></p>":"";
                $menu .= strpos($login_permissions,"rework")!==false || strpos($login_permissions,"all")!==false?"<p><a href='manage_rework.php'>返修列表</a></p>":"";
                $menu .= "<p><a href='javascript:void(0);'>登出</a></p>";
                $_SESSION['backend']['menu'] = $menu;
                
                //更新登入時間
                $query = "insert into store_log (store,account,action,action_date) values (".$login_store.",".$login_fi_no.",'".$login_username." 登入', NOW())";
                $dba->query($query);
                break;
            }
        }
        //帳號或密碼錯誤
        if(empty($_SESSION['backend']['login_user'])){
            header("Location:index.php?status=帳號或密碼錯誤");
        } 
    }else{
        //沒Session資料
        if(!isset($_SESSION)){
            header("Location:index.php");
        }else{
            $login_fi_no = $_SESSION['backend']['login_fi_no'];
            $login_store = $_SESSION['backend']['login_store'];
            $login_store_name = $_SESSION['backend']['login_store_name'];
            $login_name = $_SESSION['backend']['login_name'];
            $login_permissions = $_SESSION['backend']['login_permissions'];
            $login_user = $_SESSION['backend']['login_user'];
            $login_username = $_SESSION['backend']['login_username'];
            $login_title = $_SESSION['backend']['login_title'];
            $menu = $_SESSION['backend']['menu'];
        }
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
                <h3><?php echo $html_title."(業務)-".$login_store_name;?></h3>
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
