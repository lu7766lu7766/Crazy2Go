<?php 
    session_start();
    include '../swop/setting/config.php';
    include '../backend/template.php';
    
    if(!empty($_SESSION['backend']['login_user'])){
        
        //取Session資料
        $login_fi_no = $_SESSION['backend']['login_fi_no'];
        $login_store = $_SESSION['backend']['login_store'];
        $login_store_name = $_SESSION['backend']['login_store_name'];
        $login_name = $_SESSION['backend']['login_name'];
        $login_permissions = $_SESSION['backend']['login_permissions'];
        $login_user = $_SESSION['backend']['login_user'];
        $login_username = $_SESSION['backend']['login_username'];
        $login_title = $_SESSION['backend']['login_title'];
        $menu = $_SESSION['backend']['menu'];
        
        //取資料
        require_once "../swop/library/dba.php";
        $dba = new dba();

        //存員工資料
        if(isset($_GET["oname"]))
        {
            $_GET["oname"] = str_replace("∵", "_", $_GET["oname"]);
            $_GET["oname"] = str_replace("\\_", "_", $_GET["oname"]);
        }
        $orderby = isset($_GET["oname"])?"order by ".$_GET["oname"]." ".$_GET["order"]:"order by fi_no asc";
        $query = "select fi_no,active,permissions,name,user from store_account where store=".$login_store." ".$orderby;
        include '../backend/class_pager2.php';
        $pager = new Pager();
        $result = $pager->query($query);
        $all_account = array();
        $len = count($result);
        for($i = 0; $i < $len; $i++)
        {
            if($result[$i]['fi_no'] == $login_fi_no)continue;
            $all_account[]=array(
                fi_no => $result[$i]['fi_no'],
                name => $result[$i]['user']."(".$result[$i]['name'].")",
                active => $result[$i]['active'],
                employee_manager => strpos($result[$i]['permissions'],"all")!==false?1:0,
                employee_add => strpos($result[$i]['permissions'],"employee_add")!==false?"員工新增":"",
                employee_del => strpos($result[$i]['permissions'],"employee_del")!==false?"員工刪除":"",
                employee_edit => strpos($result[$i]['permissions'],"employee_edit")!==false?"員工編輯":"",
                supplier_add => strpos($result[$i]['permissions'],"supplier_add")!==false?"供應商新增":"",
                supplier_del => strpos($result[$i]['permissions'],"supplier_del")!==false?"供應商刪除":"",
                supplier_edit => strpos($result[$i]['permissions'],"supplier_edit")!==false?"供應商編輯":"",
                product_add => strpos($result[$i]['permissions'],"product_add")!==false?"產品新增":"",
                product_del => strpos($result[$i]['permissions'],"product_del")!==false?"產品刪除":"",
                product_copy => strpos($result[$i]['permissions'],"product_copy")!==false?"產品複製":"",
                product_edit => strpos($result[$i]['permissions'],"product_edit")!==false?"產品編輯":"",
                evaluate_add => strpos($result[$i]['permissions'],"evaluate_add")!==false?"留言新增":"",
                evaluate_del => strpos($result[$i]['permissions'],"evaluate_del")!==false?"留言刪除":"",
                appeal_add => strpos($result[$i]['permissions'],"appeal_add")!==false?"申訴新增":"",
                appeal_del => strpos($result[$i]['permissions'],"appeal_del")!==false?"申訴刪除":"",
                service_add => strpos($result[$i]['permissions'],"service_add")!==false?"客服新增":"",
                service_del => strpos($result[$i]['permissions'],"service_del")!==false?"客服刪除":"",
                service_edit => strpos($result[$i]['permissions'],"service_edit")!==false?"客服編輯":"",
                picking_add => strpos($result[$i]['permissions'],"picking_add")!==false?"揀貨單模板新增":"",
                picking_copy => strpos($result[$i]['permissions'],"picking_copy")!==false?"揀貨單模板複製":"",
                picking_del => strpos($result[$i]['permissions'],"picking_del")!==false?"揀貨單模板刪除":"",
                picking_edit => strpos($result[$i]['permissions'],"picking_edit")!==false?"揀貨單模板編輯":"",
                logi_add => strpos($result[$i]['permissions'],"logi_add")!==false?"物流單模板新增":"",
                logi_copy => strpos($result[$i]['permissions'],"logi_copy")!==false?"物流單模板複製":"",
                logi_del => strpos($result[$i]['permissions'],"logi_del")!==false?"物流單模板刪除":"",
                logi_edit => strpos($result[$i]['permissions'],"logi_edit")!==false?"物流單模板編輯":"",
                order_edit => strpos($result[$i]['permissions'],"order_edit")!==false?"訂單編輯":"",
                returns => strpos($result[$i]['permissions'],"returns")!==false?"退貨查詢":"",
                exchange => strpos($result[$i]['permissions'],"exchange")!==false?"換貨查詢":"",
                rework => strpos($result[$i]['permissions'],"rework")!==false?"返修查詢":""
            );
        }
        
        //權限中英對照表
        $permissions_assoc = array(
            employee_add => "員工新增",
            employee_del => "員工刪除",
            employee_edit => "員工編輯",
            supplier_add => "供應商新增",
            supplier_del => "供應商刪除",
            supplier_edit => "供應商編輯",
            product_add => "產品新增",
            product_del => "產品刪除",
            product_copy => "產品複製",
            product_edit => "產品編輯",
            appeal_add => "申訴新增",
            appeal_del => "申訴刪除",
            evaluate_add => "留言新增",
            evaluate_del => "留言刪除",
            service_add => "客服新增",
            service_del => "客服刪除",
            service_edit => "客服編輯",
            picking_add => "揀貨單模板新增",
            picking_copy => "揀貨單模板複製",
            picking_del => "揀貨單模板刪除",
            picking_edit => "揀貨單模板編輯",
            logi_add => "物流單模板新增",
            logi_copy => "物流單模板複製",
            logi_del => "物流單模板刪除",
            logi_edit => "物流單模板編輯",
            order_edit => "訂單編輯",
            returns => "退貨查詢",
            exchange => "換貨查詢",
            rework => "返修查詢"
        );
        
        //依權限顯示
        $display_add = strpos($login_permissions,"employee_add")!==false || strpos($login_permissions,"all")!==false?true:false;
        $display_delete = strpos($login_permissions,"employee_del")!==false || strpos($login_permissions,"all")!==false?true:false;
        $display_edit = strpos($login_permissions,"employee_edit")!==false || strpos($login_permissions,"all")!==false?true:false;
        
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
        <script>
            /******************** user define js ********************/
            $(function(){
                //新增
                $("#employee_add_table #employee_add").click(function(){
                    var $dialog = $("#dialog_content");
                    var account = $dialog.find("input[name='account']").val();
                    var password = $dialog.find("input[name='password']").val();
                    var name = $dialog.find("input[name='name']").val();
                    if(account=="" && password=="" && name==""){
                        alert("欄位不能空白！");
                        return;
                    }
                    $.post(filename,{
                        query_type:"employee_add",
                        active:1,
                        permissions:"",
                        account:account,
                        password:password,
                        name:name
                    },function(data){
                        alert($.trim(data));
                        location.href = location.href;
                    });
                });
                
                //列表排序
                $("#employee_list_table tr:first td[name]").each(function(){
                    var $this = $(this);
                    var addr = location.href.split("?");
                    var params = [];
                    var get = [];
                    var order = "";
                    var oname = "";
                    if(addr[1] && addr[1]!="")
                    {
                        params = addr[1].split("&");
                        for(var i in params)
                        {
                            if(params[i].search("oname")!=-1){
                                oname = params[i].split("=")[1];
                                oname = decodeURI(oname).replace(/∵/g,"_")
                                oname = oname == $(this).attr("name");
                                continue;
                            }
                            if(params[i].search("order")!=-1){
                                order = params[i].split("=")[1];
                                continue;
                            }
                            get.push(params[i]);
                        }
                    }
                    get.push(("oname="+$this.attr("name")).replace(/_/g,"∵"));
                    var order_item = "<div style='position:absolute;top:3px;right:0px;'>"+
                                        "<div class='order_asc' style='cursor:pointer;display:inline-block;width:0px;height:0px;border-style: solid;border-width: 0 5px 8px 5px;border-color: transparent transparent #"+(order=="asc"&&oname?"FFCC00":"FFFFFF")+" transparent;'></div><br>"+
                                        "<div class='order_desc' style='cursor:pointer;display:inline-block;width:0px;height:0px;border-style: solid;border-width: 8px 5px 0 5px;border-color: #"+(order=="desc"&&oname?"FFCC00":"FFFFFF")+" transparent transparent transparent;'></div>"+
                                     "</div>";
                    $this.css({"position":"relative"});
                    $this.append(order_item);
                    $this.find(".order_asc").click(function(){
                        get.push("order=asc");
                        location.href = addr[0]+"?"+get.join("&");
                    });
                    $this.find(".order_desc").click(function(){
                        get.push("order=desc");
                        location.href = addr[0]+"?"+get.join("&");
                    });
                });
                
                //刪除
                $("#employee_list_table .employee_del").click(function(){
                    if(!confirm("確定刪除？"))return;
                    var $this = $(this);
                    var fi_no = $this.parent().parent().attr("fi_no");
                    var name = $this.parent().parent().attr("name");
                    $.post(filename,{
                        query_type:"employee_del",
                        name:name,
                        fi_no:fi_no
                    },function(){
                        alert("已刪除！");
                        location.href = location.href;
                    });
                });
                
                //編輯
                $("#employee_list_table .employee_edit").click(function(){
                    var $this = $(this);
                    var $dialog = $("#dialog_content");
                    $dialog.find("input:first").focus();
                    var fi_no = $this.parent().parent().attr("fi_no");
                    var name = $this.parent().parent().attr("name");
                    var permissions = $this.parent().parent().attr("permissions");
                    var active = $this.parent().parent().attr("active");
                    $dialog.find("input[name='active']").prop('checked',parseInt(active));
                    $dialog.find("span[name='permissions'] input").each(function(){
                        var $this = $(this);
                        if(permissions.indexOf($this.val())!=-1)$this.prop('checked',1);
                    });
                    $dialog.data("fi_no",fi_no);
                    $dialog.data("name",name);
                });
                
                $("#employee_edit_table #employee_edit").click(function(){
                    var $dialog = $("#dialog_content");
                    var fi_no = $dialog.data("fi_no");
                    var name = $dialog.data("name");
                    var password = $dialog.find("input[name='password']").val();
                    var checkpassword = $dialog.find("input[name='checkpassword']").val();
                    var active = $dialog.find("input[name='active']").prop('checked');
                    var permissions = "";
                    if(password!="" || checkpassword!="")
                    {
                        if(password!=checkpassword)
                        {
                            alert("請再確認一次密碼！");
                            return;
                        }
                    }
                    $dialog.find("span[name='permissions'] input").each(function(){
                        var $this = $(this);
                        if($this.prop('checked'))permissions += $this.attr('name')+",";
                    });
                    permissions = permissions.slice(0,permissions.length-1);
                    $.post(filename,{
                        query_type:"employee_edit",
                        name:name,
                        fi_no:fi_no,
                        password:password,
                        permissions:permissions,
                        active:active
                    },function(){
                        alert("已更新！");
                        location.href = location.href;
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
                <h3><?php echo $html_title."(業務)-".$login_store_name; ?></h3>
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
                        function build_permissions($acc_data){
                            $permissions = "";
                            foreach ($acc_data as $key => $value) {
                                if( strpos($key,"employee")!==false || 
                                    strpos($key,"supplier")!==false || 
                                    strpos($key,"product")!==false || 
                                    strpos($key,"evaluate")!==false || 
                                    strpos($key,"appeal")!==false || 
                                    strpos($key,"service")!==false || 
                                    strpos($key,"picking")!==false || 
                                    strpos($key,"logi")!==false ||
                                    strpos($key,"order")!==false || 
                                    strpos($key,"returns")!==false || 
                                    strpos($key,"exchange")!==false || 
                                    strpos($key,"rework")!==false )
                                {
                                    if($value=="")continue;
                                    $permissions .= $value.",";
                                }
                            }
                            return substr($permissions, 0, strlen($permissions)-1);
                        }
                    
                        if($display_add){
                            echo "<table id='employee_add_table' class='table-v' data-dialog='新增帳號'>";
                            echo "<tr><td>帳號</td><td><input name='account' type='text' placeholder='請填入帳號'/></td></tr>";
                            echo "<tr><td>密碼</td><td><input name='password'  type='password' placeholder='請填入密碼' /></td></tr>";
                            echo "<tr><td>暱稱</td><td><input name='name' type='text' placeholder='請填入暱稱' /></td></tr>";
                            echo "<tr><td></td><td><input id='employee_add' type='button' value='儲存'></td></tr>";
                            echo "</table>";
                            echo "<input type='button' value='新增帳號' data-open-dialog='新增帳號' /><table></table>";
                        }
                        
                        if($display_edit){
                            $permissions = "";
                            $i = -3;
                            foreach($permissions_assoc as $key => $value)
                            {
                                $permissions.= "<input type='checkbox' name='{$key}' value='{$value}' />{$value}&nbsp;";
                                if($i++%4==0)$permissions.= "<br/>";
                            }
                            echo "<table id='employee_edit_table' class='table-v' data-dialog='編輯'>";
                            echo "<tr><td>新密碼</td><td><input name='password' type='password' placeholder='請填入新密碼'/>&nbsp;(若無密碼修改請留白)</td></tr>";
                            echo "<tr><td>驗證</td><td><input name='checkpassword' type='password' placeholder='請填入新密碼' />&nbsp;(若無密碼修改請留白)</td></tr>";
                            echo "<tr><td>啟用</td><td><input name='active' type='checkbox'/></td></tr>";
                            echo "<tr><td>權限</td><td><span name='permissions'>".$permissions."</span></td></tr>";
                            echo "<tr><td></td><td><input id='employee_edit' type='button' value='儲存'></td></tr>";
                            echo "</table>";
                        }

                        $pager->display();echo "<br/>";
                        echo "<table id='employee_list_table' class='table-h'>";
                        echo "<tr><td name='fi_no'>ID</td><td>暱名</td><td>啟用</td><td style='width:300px;'>權限</td>";
                        if($display_edit)echo "<td>修改</td>";
                        if($display_delete)echo "<td>刪除</td>";
                        echo "</tr>";

                        $len = count($all_account);
                        
                        for($i = 0; $i < $len; $i++)
                        {
                            if($all_account[$i]["employee_manager"] != 1)
                            {
                                echo "<tr fi_no='".$all_account[$i]["fi_no"]."' name='".$all_account[$i]["name"]."' active='".$all_account[$i]["active"]."' permissions='".build_permissions($all_account[$i])."'>";
                                echo "<td>".$all_account[$i]["fi_no"]."</td>";
                                echo "<td>".$all_account[$i]["name"]."</td>";
                                echo "<td>".$all_account[$i]["active"]."</td>";
                                echo "<td>".build_permissions($all_account[$i])."</td>";
                                if($display_edit)echo "<td><input class='employee_edit' type='button' value='編輯' data-open-dialog='編輯'></td>";
                                if($display_delete)echo "<td><input class='employee_del' type='button' value='刪除'></td>";
                                echo "</tr>";
                            }
                        }
                        echo "</table>";
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
