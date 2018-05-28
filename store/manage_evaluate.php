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

        //取有評論的資料
        if(isset($_GET["oname"]))
        {
            $_GET["oname"] = str_replace("∵", "_", $_GET["oname"]);
            $_GET["oname"] = str_replace("\\_", "_", $_GET["oname"]);
        }
        $orderby = isset($_GET["oname"])?"order by ".$_GET["oname"]." ".$_GET["order"]:"order by fi_no asc";
        $query = "select fi_no,view,respond from goods_vevaluate where store=".$login_store." ".$orderby;
        include '../backend/class_pager2.php';
        $pager = new Pager();
        $result = $pager->query($query);
        $allevaluate = array();
        $pickup = array();
        $len = count($result);
        
        for($i = 0; $i < $len; $i++)
        {
            $allevaluate[] = array(
                fi_no => $result[$i]["fi_no"],
                view => $result[$i]["view"],
                respond => $result[$i]["respond"]
            );
            $pickup[] = $result[$i]["fi_no"];
        }
        
        //取出產品名
        $pickup = implode(",", $pickup);
        $query = "select name from goods_index where fi_no in (".$pickup.") order by field(fi_no,".$pickup.")";
        $result = $dba->query($query);
        $len = count($result);
        for($i = 0; $i < $len; $i++)
        {
            $allevaluate[$i]["name"] = $result[$i]["name"];
        }
        
        //依權限顯示
        $display_add = strpos($login_permissions,"evaluate_add")!==false || strpos($login_permissions,"all")!==false?true:false;
        $display_del = strpos($login_permissions,"evaluate_del")!==false || strpos($login_permissions,"all")!==false?true:false;
        
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
                $("#evaluate_list_table tr:first td[name]").each(function(){
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
                
                $("#evaluate_list_table .evaluate_check").click(function(){
                    var $check = $(this);
                    var $dialog = $("#dialog_content");
                    var fi_no = $check.parent().parent().attr("fi_no");
                    $.post(filename,{
                        query_type:"show_evaluate",
                        fi_no:fi_no
                    },function(data){
                        data = $.parseJSON(data);
                        var len = data.length,
                            i = 0,
                            j = 0,
                            display = "";
                        
                        for(i=0; i<len; i++)
                        {
                            var score =  '給予此評論 <input type="radio" name="star'+data[i].member+'" value="1">1 '+
                                    '<input type="radio" name="star'+data[i].member+'" value="2">2 '+
                                    '<input type="radio" name="star'+data[i].member+'" value="3">3 '+
                                    '<input type="radio" name="star'+data[i].member+'" value="4">4 '+
                                    '<input type="radio" name="star'+data[i].member+'" value="5">5 '+
                                    '<input type="radio" name="star'+data[i].member+'" value="6" checked>6 顆星';
                            display+="<div style='padding:10px;border-bottom:1px dashed #333;'>";
                            var answer_textarea = "";
                            if(!data[i].answer.length)
                            {
                                answer_textarea = (php_display_add?"<p><div style='border-top:1px dashed #333;margin-top:40px;text-align:left;'>"+score+"</div><textarea member='"+data[i].member+"' fi_no='"+fi_no+"' evaluate='"+data[i].fi_no+"' account='"+php_login_fi_no+"' style='width:100%;height:70px;border:0px;' placeholder='在想些什麼 (按Enter評論)'></textarea></p>":"");
                            }
                            display+="<table class='table-h' style='width:870px;'><tr><td><span style='float:left'>"+data[i].evaluate_date+"</span><span style='float:right'>"+data[i].member_name+"</span></td></tr><tr><td><span style='float:left;'>"+data[i].content+"</span>"+answer_textarea+"</td></tr></table>";
                            display+="<ol style='margin-left:20px;'>";
                            for(j in data[i].answer)
                            {
                                display+="<li><table class='table-h' style='width:100%;opacity:0.6;'><tr><td><span style='float:left'>"+data[i].answer[j].respond_date+"&nbsp;&nbsp;&nbsp;"+data[i].answer[j].score+" 顆星</span><span style='float:right'>"+data[i].answer[j].store_account_name+"</span></td></tr><tr><td><span style='float:left;'>"+data[i].answer[j].content+"</span>"+(php_display_del?"<p><span style='float:right;'><input name='evaluate' score='"+data[i].answer[j].score+"' member='"+data[i].member+"' evaluate='"+data[i].fi_no+"' fi_no='"+data[i].answer[j].fi_no+"' goods='"+fi_no+"' type='button' value='刪除'/></span></p>":"")+"</td></tr></table></li>";
                            }
                            display+="</ol>";
                            display+="</div>";
                        }
                        $dialog.html(display);
                        init_style($dialog.find("table"));
                        
                        //新增評論
                        $dialog.find('textarea').bind('keypress', function(e) {
                            var code = (e.keyCode ? e.keyCode : e.which);
                            var $this = $(this);
                            var score = $this.parent().find("input:radio[name='star"+$this.attr("member")+"']:checked").val();
                            if($this.val()=="")return;
                            if(code == 13) {//Enter
                                $.post(filename,{
                                    query_type:"save_evaluate",
                                    fi_no:$this.attr("fi_no"),
                                    evaluate:$this.attr("evaluate"),
                                    content:$this.val(),
                                    account:$this.attr("account"),
                                    score:score,
                                    member:$this.attr("member")
                                },function(){
                                    closeDialog();
                                    $check.trigger("click");
                                });
                            }
                        });
                        //刪除評論
                        $dialog.find('input[name=evaluate]').click(function(){
                            var $this = $(this);
                            $.post(filename,{
                                query_type:"delete_evaluate",
                                fi_no:$this.attr("fi_no"),
                                evaluate:$this.attr("evaluate"),
                                goods:$this.attr("goods"),
                                score:$this.attr("score"),
                                member:$this.attr("member")
                            },function(){
                                closeDialog();
                                $check.trigger("click");
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
                        $url = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
                        $url = substr($url,0,strpos($url,"store"));
                        
                        $pager->display(); echo "<br/>";
                        echo "<table id='evaluate_list_table' class='table-h'>";
                        echo "<tr><td name='fi_no'>ID</td><td>產品名稱</td><td>評論已檢視</td><td>評論已回應</td><td>檢視產品</td><td>檢視/回應評論</td></tr>";
                        $len = count($allevaluate);
                        for($i = 0; $i < $len; $i++)
                        {
                            echo "<tr fi_no='".$allevaluate[$i]["fi_no"]."'>";
                                echo "<td>".$allevaluate[$i]["fi_no"]."</td>";
                                echo "<td>".$allevaluate[$i]["name"]."</td>";
                                echo "<td>".$allevaluate[$i]["view"]."</td>";
                                echo "<td>".$allevaluate[$i]["respond"]."</td>";
                                echo "<td>"."<a href='".$url."goods?no=".$allevaluate[$i]["fi_no"]."' target='_blank'>顯示</a>"."</td>";
                                echo "<td><input class='evaluate_check' type='button' value='檢視評論' data-open-dialog='檢視評論'></td>";
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
