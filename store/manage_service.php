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
        
        require_once "../swop/library/dba.php";
        $dba = new dba();
        
        //存服務人員資料
        if(isset($_GET["oname"]))
        {
            $_GET["oname"] = str_replace("∵", "_", $_GET["oname"]);
            $_GET["oname"] = str_replace("\\_", "_", $_GET["oname"]);
        }
        $orderby = isset($_GET["oname"])?"order by ".$_GET["oname"]." ".$_GET["order"]:"order by fi_no asc";
        $query = "select fi_no,user,name,type,qq,permissions from service where store=".$login_store." ".$orderby;
        include '../backend/class_pager2.php';
        $pager = new Pager();
        $result = $pager->query($query);
        $all_service = array();
        $len = count($result);
        for($i = 0; $i < $len; $i++)
        {
            $all_service[]=array(
                fi_no => $result[$i]['fi_no'],
                name => $result[$i]['user']."(".$result[$i]['name'].")",
                type => $result[$i]['type'],
                qq => $result[$i]['qq'],
                service_modify_order => strpos($result[$i]['permissions'],"service_modify_order")!==false?"修改訂單":"",
                service_discounts => strpos($result[$i]['permissions'],"service_discounts")!==false?"給予折扣":""
            );
        }
        
        //權限中英對照表
        $permissions_assoc = array(
            service_modify_order => "修改訂單",
            service_discounts => "給予折扣"
        );
        
        //所有服務總類
        $all_service_type = array(
            array(id => 1,name => "售前服務"),
            array(id => 2,name => "售後服務"),
            array(id => 3,name => "產品問題")
        );
        
        //依權限顯示
        $display_add = strpos($login_permissions,"service_add")!==false || strpos($login_permissions,"all")!==false?true:false;
        $display_delete = strpos($login_permissions,"service_del")!==false || strpos($login_permissions,"all")!==false?true:false;
        $display_edit = strpos($login_permissions,"service_edit")!==false || strpos($login_permissions,"all")!==false?true:false;
        
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
                //列表排序
                $("#service_list_table tr:first td[name]").each(function(){
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
                
                //新增
                $("#service_add_table #service_add").click(function(){
                    var $dialog = $("#dialog_content");
                    var account = $dialog.find("input[name='account']").val();
                    var password = $dialog.find("input[name='password']").val();
                    var name = $dialog.find("input[name='name']").val();
                    var qq = $dialog.find("input[name='qq']").val();
                    var type = $dialog.find("select[name='type']").val();
                    if(account=="" && password=="" && nickname=="" && qq==""){
                        alert("欄位不能空白！");
                        return;
                    }
                    $.post(filename,{
                        query_type:"service_add",
                        permissions:"",
                        type:type,
                        name:name,
                        account:account,
                        password:password,
                        qq:qq
                    },function(data){
                        alert($.trim(data));
                        location.href = location.href;
                    });
                });
                
                //刪除
                $("#service_list_table .service_del").click(function(){
                    if(!confirm("確定刪除？"))return;
                    var $this = $(this);
                    var fi_no = $this.parent().parent().attr("fi_no");
                    var name = $this.parent().parent().attr("name");
                    $.post(filename,{
                        query_type:"service_del",
                        name:name,
                        fi_no:fi_no
                    },function(){
                        alert("已刪除！");
                        location.href = location.href;
                    });
                });
                
                //編輯
                $("#service_list_table .service_edit").click(function(){
                    var $this = $(this);
                    var $dialog = $("#dialog_content");
                    $dialog.find("input:first").focus();
                    var fi_no = $this.parent().parent().attr("fi_no");
                    var name = $this.parent().parent().attr("name");
                    var permissions = $this.parent().parent().attr("permissions");
                    var qq = $this.parent().parent().attr("qq");
                    var type = $this.parent().parent().attr("type");
                    $dialog.find("input[name='qq']").val(qq);
                    $dialog.find("select[name='type']").val(type);
                    $dialog.find("span[name='permissions'] input").each(function(){
                        var $this = $(this);
                        if(permissions.indexOf($this.val())!=-1)$this.prop('checked',1);
                    });
                    $dialog.data("fi_no",fi_no);
                    $dialog.data("name",name);
                });
                
                $("#service_edit_table #service_edit").click(function(){
                    var $dialog = $("#dialog_content");
                    var fi_no = $dialog.data("fi_no");
                    var name = $dialog.data("name");
                    var password = $dialog.find("input[name='password']").val();
                    var checkpassword = $dialog.find("input[name='checkpassword']").val();
                    var qq = $dialog.find("input[name='qq']").val();
                    var type = $dialog.find("select[name='type']").val();
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
                        query_type:"service_edit",
                        permissions:permissions,
                        qq:qq,
                        type:type,
                        fi_no:fi_no,
                        password:password,
                        name:name
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
                        function build_service_type_selection($all_service_type,$name,$pick_up_id){
                            $len = count($all_service_type);
                            $selection = "<select name='".$name."'>";
                            for($i = 0; $i < $len; $i++)
                            {
                                $selection .= "<option ".($all_service_type[$i]['id']==$pick_up_id?"selected":"")." value='".$all_service_type[$i]['id']."'>".$all_service_type[$i]['id'].":".$all_service_type[$i]['name']."</option>";
                            }
                            $selection .= "</select>";
                            return $selection;
                        }
                        
                        function build_permissions($acc_data){
                            $permissions = "";
                            foreach ($acc_data as $key => $value) {
                                if(strpos($key,"service")!==false)
                                {
                                    if($value=="")continue;
                                    $permissions .= $value.",";
                                }
                            }
                            return substr($permissions, 0, strlen($permissions)-1);
                        }

                        function build_service_type($all_service_type,$type){
                            foreach($all_service_type as $v)
                            {
                                if($v['id'] == $type)return $v['name'];
                            }
                            return "";
                        }
                    
                        if($display_add){
                            echo "<table id='service_add_table' class='table-v' data-dialog='新增客服'>";
                            echo "<tr><td>帳號</td><td><input type='text' name='account' placeholder='請輸入帳號' /></td></tr>";
                            echo "<tr><td>密碼</td><td><input type='password' name='password' placeholder='請輸入密碼' /></td></tr>";
                            echo "<tr><td>暱稱</td><td><input type='text' name='name' placeholder='請輸入暱稱' /></td></tr>";
                            echo "<tr><td>QQ</td><td><input type='text' name='qq' placeholder='請輸入QQ' /></td></tr>";
                            echo "<tr><td>客服種類</td><td>".build_service_type_selection($all_service_type,"type",1)."</td></tr>";
                            echo "<tr><td></td><td><input id='service_add' type='button' value='儲存'></td></tr>";
                            echo "</table>";
                            echo "<input type='button' value='新增客服' data-open-dialog='新增客服' /><table></table>";
                        }
                        
                        if($display_edit){
                            $permissions = "";
                            $i = -3;
                            foreach($permissions_assoc as $key => $value)
                            {
                                $permissions.= "<input type='checkbox' name='{$key}' value='{$value}' />{$value}&nbsp;";
                                if($i++%4==0)$permissions.= "<br/>";
                            }
                            echo "<table id='service_edit_table' class='table-v' data-dialog='編輯'>";
                            echo "<tr><td>新密碼</td><td><input name='password' type='password' placeholder='請填入新密碼' />&nbsp;(若無密碼修改請留白)</td></tr>";
                            echo "<tr><td>驗證</td><td><input name='checkpassword' type='password' placeholder='請填入新密碼' />&nbsp;(若無密碼修改請留白)</td></tr>";
                            echo "<tr><td>QQ</td><td><input name='qq' type='text' placeholder='請輸入QQ' /></td></tr>";
                            echo "<tr><td>客服種類</td><td>".build_service_type_selection($all_service_type,"type",1)."</td></tr>";
                            echo "<tr><td>權限</td><td><span name='permissions'>".$permissions."</span></td></tr>";
                            echo "<tr><td></td><td><input id='service_edit' type='button' value='儲存'></td></tr>";
                            echo "</table>";
                        }

                        $pager->display();echo "<br/>";
                        echo "<table id='service_list_table' class='table-h'>";
                        echo "<tr><td name='fi_no'>ID</td><td>暱名</td><td>服務種類</td><td>QQ</td><td style='width:120px;'>權限</td>";
                        if($display_edit)echo "<td>修改</td>";
                        if($display_delete)echo "<td>刪除</td>";
                        echo "</tr>";

                        $len = count($all_service);
                        for($i = 0; $i < $len; $i++)
                        {
                            echo "<tr fi_no='".$all_service[$i]["fi_no"]."' name='".$all_service[$i]["name"]."' type='".$all_service[$i]["type"]."' qq='".$all_service[$i]["qq"]."' permissions='".build_permissions($all_service[$i])."'>";
                            echo "<td>".$all_service[$i]["fi_no"]."</td>";
                            echo "<td>".$all_service[$i]["name"]."</td>";
                            echo "<td>".build_service_type($all_service_type,$all_service[$i]["type"])."</td>";
                            echo "<td>".$all_service[$i]["qq"]."</td>";
                            echo "<td>".build_permissions($all_service[$i])."</td>";
                            if($display_edit)echo "<td><input class='service_edit' type='button' value='編輯' data-open-dialog='編輯'></td>";
                            if($display_delete)echo "<td><input class='service_del' type='button' value='刪除'></td>";
                            echo "</tr>";
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
