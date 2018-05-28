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

        //取有申訴的資料
        if(isset($_GET["oname"]))
        {
            $_GET["oname"] = str_replace("∵", "_", $_GET["oname"]);
            $_GET["oname"] = str_replace("\\_", "_", $_GET["oname"]);
        }
        $orderby = isset($_GET["oname"])?"order by ".$_GET["oname"]." ".$_GET["order"]:"order by fi_no asc";
        $query = "select fi_no,sn,content,date,reply_date from appeal_index where store=".$login_store." ".$orderby;
        $result = $dba->query($query);
        $allappeal = array();
        
        include '../backend/class_pager2.php';
        $pager = new Pager();
        $result = $pager->query($query);
        $len = count($result);
        
        for($i = 0; $i < $len; $i++)
        {
            $allappeal[] = array(
                fi_no => $result[$i]["fi_no"],
                sn => $result[$i]["sn"],
                content => $result[$i]["content"],
                date => $result[$i]["date"],
                reply_date => $result[$i]["reply_date"]
            );
        }
        
        //依權限顯示
        $display_add = strpos($login_permissions,"appeal_add")!==false || strpos($login_permissions,"all")!==false?true:false;
        $display_del = strpos($login_permissions,"appeal_del")!==false || strpos($login_permissions,"all")!==false?true:false;
        
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
                var php_display_add = <?php echo $display_add?1:0;?>;
                var php_display_del = <?php echo $display_del?1:0;?>;
                var php_login_fi_no = <?php echo $login_fi_no;?>;
                
                //列表排序
                $("#appeal_list_table tr:first td[name]").each(function(){
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
                
                $("#appeal_list_table .appeal_check").click(function(){
                    var $check = $(this);
                    var $dialog = $("#dialog_content");
                    var fi_no = $check.parent().parent().attr("fi_no");
                    $.post(filename,{
                        query_type:"show_appeal",
                        fi_no:fi_no
                    },function(data){
                        data = $.parseJSON(data);
                        var display = "";
                        display+="<div style='padding:10px;border-bottom:1px dashed #333;'>";
                        var answer_textarea = "";
                        if(data.answer.reply_content=="")
                        {
                            answer_textarea = (php_display_add?"<p><div style='border-top:1px dashed #333;margin-top:40px;text-align:left;'></div><textarea member='"+data.member+"' appeal='"+data.fi_no+"' style='width:100%;height:70px;border:0px;' placeholder='在想些什麼 (按Enter留言)'></textarea></p>":"");
                        }
                        display+="<table class='table-h' style='width:870px;'><tr><td><span style='float:left'>"+data.date+"</span><span style='float:right'>"+data.member_name+"</span></td></tr><tr><td><span style='float:left;'>"+data.content+"</span>"+answer_textarea+"</td></tr></table>";
                        display+="<ol style='margin-left:20px;'>";
                        if(data.answer.reply_content!="")display+="<li><table class='table-h' style='width:100%;opacity:0.6;'><tr><td><span style='float:left'>"+data.answer.reply_date+"</span><span style='float:right'>"+data.answer.store_account_name+"</span></td></tr><tr><td><span style='float:left;'>"+data.answer.reply_content+"</span>"+(php_display_del?"<p><span style='float:right;'><input name='appeal' member='"+data.member+"' appeal='"+data.fi_no+"' type='button' value='刪除'/></span></p>":"")+"</td></tr></table></li>";
                        display+="</ol>";
                        display+="</div>";
                        
                        $dialog.html(display);
                        init_style($dialog.find("table"));
                        
                        //新增申訴
                        $dialog.find('textarea').bind('keypress', function(e) {
                            var code = (e.keyCode ? e.keyCode : e.which);
                            var $this = $(this);
                            if($this.val()=="")return;
                            if(code == 13) {//Enter
                                $.post(filename,{
                                    query_type:"save_appeal",
                                    fi_no:$this.attr("appeal"),
                                    content:$this.val(),
                                    member:$this.attr("member")
                                },function(data){
                                    closeDialog();
                                    $check.trigger("click");
                                    $check.parent().parent().find("td[name='reply_date']").text(data);
                                });
                            }
                        });
                        //刪除申訴
                        $dialog.find('input[name=appeal]').click(function(){
                            var $this = $(this);
                            $.post(filename,{
                                query_type:"delete_appeal",
                                fi_no:$this.attr("appeal"),
                                member:$this.attr("member")
                            },function(data){
                                closeDialog();
                                $check.trigger("click");
                                $check.parent().parent().find("td[name='reply_date']").text(data);
                            });
                        });
                    })
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
                        $pager->display(); echo "<br/>";
                        echo "<table id='appeal_list_table' class='table-h'>";
                        echo "<tr><td name='fi_no'>ID</td><td>訂單序號</td><td>申訴內容</td><td>申訴日期</td><td>回應日期</td><td>檢視/回應申訴</td></tr>";
                        $len = count($allappeal);
                        for($i = 0; $i < $len; $i++)
                        {
                            echo "<tr fi_no='".$allappeal[$i]["fi_no"]."'>";
                            echo "<td>".$allappeal[$i]["fi_no"]."</td>";
                            echo "<td>".$allappeal[$i]["sn"]."</td>";
                            echo "<td>".$allappeal[$i]["content"]."</td>";
                            echo "<td>".$allappeal[$i]["date"]."</td>";
                            echo "<td name='reply_date'>".$allappeal[$i]["reply_date"]."</td>";
                            echo "<td><input class='appeal_check' type='button' value='檢視申訴' data-open-dialog='檢視申訴'></td>";
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
