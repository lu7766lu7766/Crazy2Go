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
        $query = 'select fi_no,permissions,store,type,name,user,password,qq from service order by fi_no asc';
        $result = $dba->query($query);
        
        //檢查帳密
        $len = count($result);
        for($i = 0; $i < $len; $i++){
            if($result[$i]['user'] == $account && $result[$i]['password'] == md5(md5($password))){
                
                //服務種類
                $service_type = array(
                    1 => "售前服務",
                    2 => "售後服務",
                    3 => "產品問題"
                );
                
                //記錄登入者資料
                $login_fi_no = $result[$i]['fi_no'];
                $login_permissions = $result[$i]['permissions'];
                $login_store = $result[$i]['store'];
                $login_type = $service_type[$result[$i]['type']];
                $login_name = $result[$i]['name'];
                $login_user = $result[$i]['user'];
                $login_qq = $result[$i]['qq'];
                
                //存Session
                $_SESSION['serviceqq']['login_fi_no'] = $login_fi_no;
                $_SESSION['serviceqq']['login_permissions'] = $login_permissions;
                $_SESSION['serviceqq']['login_store'] = $login_store;
                $_SESSION['serviceqq']['login_type'] = $login_type;
                $_SESSION['serviceqq']['login_name'] = $login_name;  
                $_SESSION['serviceqq']['login_user'] = $login_user;
                $_SESSION['serviceqq']['login_qq'] = $login_qq;
                $_SESSION['serviceqq']['login_username'] = $login_user."(".$login_name.")";
                $_SESSION['serviceqq']['login_title'] = "<div style='color:white;background:rgba(255,255,255,0.2);padding:3px;' class='bg shadowRoundCorner'>管理員 ".$login_name."(QQ:".$login_qq.") 先生/小姐您好!您的服務種類為".$login_type."<input id='logout' type='button' value='登出'></div>";
                $login_username = $_SESSION['serviceqq']['login_username'];
                $login_title = $_SESSION['serviceqq']['login_title'];
                
                //更新登入時間
                $query = "insert into service_log (service,action,action_date) values (".$login_fi_no.",'".$login_username." 登入', NOW())";
                $dba->query($query);
                
                //依權限顯示
                $display_modify_order = strpos($login_permissions,"service_modify_order")!==false || strpos($login_permissions,"all")!==false?true:false;
                $display_discounts = strpos($login_permissions,"service_discounts")!==false || strpos($login_permissions,"all")!==false?true:false;
                break;
            }
        }
        //帳號或密碼錯誤
        if(empty($_SESSION['serviceqq']['login_user'])){
            header("Location:index.php?status=帳號或密碼錯誤");
        } 
    }else{
        //沒Session資料
        if(!isset($_SESSION)){
            header("Location:index.php");
        }else{
            $login_fi_no = $_SESSION['serviceqq']['login_fi_no'];
            $login_permissions = $_SESSION['serviceqq']['login_permissions'];
            $login_store = $_SESSION['serviceqq']['login_store'];
            $login_type = $_SESSION['serviceqq']['login_type'];
            $login_name = $_SESSION['serviceqq']['login_name'];
            $login_user = $_SESSION['serviceqq']['login_user'];
            $login_qq = $_SESSION['serviceqq']['login_qq'];
            $login_username = $_SESSION['serviceqq']['login_username'];
            $login_title = $_SESSION['serviceqq']['login_title'];
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
                var php_store = <?php echo $login_store;?>;
                var pickup_id="";
                
                var status_order_list = [
                    "無",
                    "訂單成立",
                    "訂單取消",
                    "訂單完成"
                ];
                
                var status_pay_list = [
                    "無",
                    "尚未付款",
                    "已付款",
                    "免付款"
                ];
                
                var status_transport_list =[
                    "無",
                    "尚未出貨",
                    "已發貨"
                ];
                
                var status_receiving_list = [
                    "無",
                    "待確認收貨",
                    "確認收貨"
                ];
                
                var application_returns_list = [
                    "無",
                    "有商品退貨"
                ];
                
                var application_exchanges_list = [
                    "無",
                    "有商品換貨"
                ];
                
                var application_rework_list = [
                    "無",
                    "有商品換貨"
                ];
                
                var status_list = [
                    "無",
                    "退貨",
                    "換貨",
                    "返修"
                ];
                
                var application_status_list = [
                    "無",
                    "申請中",
                    "申請成功",
                    "申請失敗"
                ];
                
                //登出
                $("#logout").click(function(){
                    $.post("sql_logout.php",{
                        query_type:"logout"
                    },function(){
                        location.href='index.php';
                    })
                });
                
                //記錄ＱＱ號
                $("#service_table input[name=qq]").bind('blur',function(){
                    $.post("sql_welcome.php",{
                        query_type:"save_qq",
                        item_id:pickup_id,
                        qq:$(this).val()
                    },function(){
                        updatelist();
                    });
                });
                
                //記錄暱稱
                $("#service_table input[name=name]").bind('blur',function(){
                    $.post("sql_welcome.php",{
                        query_type:"save_nickname",
                        item_id:pickup_id,
                        name:$(this).val()
                    },function(){
                        updatelist();
                    });
                });
                
                //聊天記錄備份
                $("#service_table input[name=save]").click(function(){
                    var qq = $("#service_table input[name=qq]").val();
                    var name = $("#service_table input[name=name]").val();
                    var content = $("#service_table textarea[name=content]").val();
                    if(content ==="" || qq==="" || name===""){
                        alert("請填寫所有欄位！");
                        return;
                    }
                    if(pickup_id=="")
                    {
                        alert("尚未選擇服務對象！");
                        return;
                    }
                    $.post("sql_welcome.php",{
                        query_type:"save_content",
                        item_id:pickup_id,
                        qq:qq,
                        name:name,
                        content:content
                    },function(){
                        alert("儲存成功！");
                        location.href = location.href;
                    });
                });
                
                //重複更新時間與名單
                function update(){
                    $.post("sql_welcome.php",{
                        query_type:"wait_date"
                    },function(data){
                        data = $.trim(data);
                        if(data == "")return;
                        data = data.split("@");
                        var i = 0;
                        var l = data.length;
                        var content=[];
                        if($("#body_left p")[0]){
                            $("#body_left p").each(function(){
                                $(this).unbind("click");
                                $(this).remove();
                            });
                        }
                        //排列使用者清單
                        for(i = 0; i < l; i++)
                        {
                            var content = data[i].split("`"),
                                fi_no = content[0],
                                start_date = content[1],
                                qq = content[2],
                                name = content[3];
                            $("#body_left").append("<p class='member_list' style='text-align:left;' data='"+fi_no+"'>"+fi_no+":"+start_date+"<br />QQ：<span class='qq'>"+(qq?qq:"請填寫！")+"</span><br />暱稱：<span class='nickname'>"+(name?name:"")+"</span></p>");
                        }
                        //選取者填色
                        if(pickup_id!=="")
                        {
                            $("#body_left p[data="+pickup_id+"]").css({"background":"#AAA"});
                        }
                        //未填寫紅色警告
                        $("#body_left p span.qq").each(function(){
                           if($(this).html()=="請填寫！")
                               $(this).css({'color':'red'});
                        });
                        //左側按鈕處理
                        $("#body_left p").bind('click',function(){
                            $("#service_table textarea[name=content]").val("");
                            $("#service_table input[name=qq]").val($(this).find('.qq').html());
                            $("#service_table input[name=name]").val($(this).find('.nickname').html());
                            pickup_id = $(this).attr("data");
                            $("#body_left p").css({"background":"#CCC"});
                            $(this).css({"background":"#AAA"});
                        });
                    });
                }
                var time_sid = setInterval(update,10000);
                update();
                
                $("#body_right #service_table input[name='order_check']").click(function(){
                    var sn = $("#body_right #service_table input[name='order_sn']").val();
                    if(sn==""){
                        alert("請輸入訂單編號！");
                        return;
                    }
                    $("#body_right #service_table #order_content").html("");
                    $.post("sql_welcome.php",{
                        query_type:"get_order_index",
                        sn:sn
                    },function(data){
                        data = $.trim(data);
                        var $order_content = $("#body_right #service_table #order_content");
                        if(data=="")
                        {
                            alert("無此訂單！");
                            return;
                        }
                        data = data.split("`");
                        var fi_no = data[0],
                            member = data[1];

                        var $show = "<input type='button' name='order_detail' value='訂單細目' data-open-dialog='訂單細目' /><input type='button' name='order_goods_detail' value='產品細目' data-open-dialog='產品細目' /><br/><input name='discount' type='text' placeholder='可給予折扣價'/><input name='discount_save' type='button' value='儲存折扣價'>";
                        $order_content.html($show);
                        init_style($order_content.find("input[name=order_detail]"));
                        init_style($order_content.find("input[name=order_goods_detail]"));
                        
                        $order_content.find("input[name='discount_save']").click(function(){
                            var discount = $order_content.find("input[name='discount']").val();
                            if(discount == "")
                            {
                                alert("請填入折扣價！");
                                return;
                            }
                            $.post("sql_welcome.php",{
                                query_type:"update_discount",
                                discount:discount,
                                fi_no:fi_no,
                                member:member
                            },function(){
                                alert("折扣已儲存！");
                                $("#body_right #service_table input[name='order_check']").trigger('click');
                            });
                        });
                        
                        $order_content.find("input[name=order_detail]").click(function(){
                            var $this = $(this);
                            var $dialog = $("#dialog_content");
                            var content = $this.data("content");

                            function getContent(data){
                                $this.data("content",data);
                                data = data.split("`");
                                var fi_no=data[0],
                                    member = data[1],
                                    sn = data[2],
                                    date = data[3],
                                    store = data[4],
                                    subtotal = data[5],
                                    shipping_fee = data[6],
                                    discounts = data[7],
                                    payments = data[8],
                                    checkout = data[9],
                                    exchange_rate = data[10],
                                    status_order = data[11],
                                    status_pay = data[12],
                                    status_transport = data[13],
                                    status_receiving = data[14],
                                    application_returns = data[15],
                                    application_exchanges = data[16],
                                    application_rework = data[17],
                                    trace = data[18],
                                    remind = data[19],
                                    invoice = data[20],
                                    remarks = data[21],
                                    consignee = data[22],
                                    postal_code = data[23],
                                    province = data[24],
                                    city = data[25],
                                    district = data[26],
                                    street = data[27],
                                    address = data[28],
                                    contact_phone = data[29],
                                    contact_mobile = data[30],
                                    identity = data[31],
                                    transport = data[32],
                                    message = data[33];
                                    
                                var display=""
                                +"<table id='order_form_table' class='table-v' data-dialog='訂單細目'>"
                                +"<tr><td>訂單ID</td><td>"+fi_no+"</td></tr>"
                                +"<tr><td>訂單序號</td><td>"+sn+"</td></tr>"
                                +"<tr><td>商品原價</td><td>"+subtotal+"</td></tr>"
                                +"<tr><td>運輸費</td><td>"+shipping_fee+"</td></tr>"
                                +"<tr><td>折扣價</td><td style='color:red'>"+discounts+"(商店編號: 折扣價 客服編號 給予折扣時間)</td></tr>"
                                +"<tr><td>總價</td><td>"+payments+"</td></tr>"
                                +"<tr><td>實際花費</td><td>"+checkout+"</td></tr>"
                                +"<tr><td>匯率</td><td>"+exchange_rate+"</td></tr>"
                                +"<tr><td>訂單狀態</td><td>"+status_order_list[status_order]+"</td></tr>"
                                +"<tr><td>付款狀態</td><td>"+status_pay_list[status_pay]+"</td></tr>"
                                +"<tr><td>運輸狀態</td><td>"+status_transport_list[status_transport]+"</td></tr>"
                                +"<tr><td>收貨狀態</td><td>"+status_receiving_list[status_receiving]+"</td></tr>"
                                +"<tr><td>有商品退貨</td><td>"+application_returns_list[application_returns]+"</td></tr>"
                                +"<tr><td>有商品換貨</td><td>"+application_exchanges_list[application_exchanges]+"</td></tr>"
                                +"<tr><td>有商品反修</td><td>"+application_rework_list[application_rework]+"</td></tr>"
                                +"<tr><td>追蹤代碼</td><td>"+json_trans_display(trace)+"</td></tr>"
                                +"<tr><td>提醒發貨</td><td>"+remind+"</td></tr>"
                                +"<tr><td>發票號碼</td><td>"+invoice+"</td></tr>"
                                +"<tr><td>訂單備註</td><td>"+remarks+"</td></tr>"
                                +"<tr><td>收件人姓名</td><td>"+consignee+"</td></tr>"
                                +"<tr><td>郵遞區號</td><td>"+postal_code+"</td></tr>"
                                +"<tr><td>收件省份</td><td>"+province+"</td></tr>"
                                +"<tr><td>市</td><td>"+city+"</td></tr>"
                                +"<tr><td>區</td><td>"+district+"</td></tr>"
                                +"<tr><td>街</td><td>"+street+"</td></tr>"
                                +"<tr><td>詳細地址</td><td>"+address+"</td></tr>"
                                +"<tr><td>聯絡電話</td><td>"+contact_phone+"</td></tr>"
                                +"<tr><td>手機號碼</td><td>"+contact_mobile+"</td></tr>"
                                +"<tr><td>身分證號</td><td>"+identity+"</td></tr>"
                                +"<tr><td>運送方式</td><td>"+transport+"</td></tr>"
                                +"<tr><td>留言給賣家</td><td>"+message+"</td></tr>"
                                +"</table>";

                               $dialog.append(display);
                               var $oft = $("#order_form_table");
                               init_style($oft);
                               $oft.show();
                            }

                            if(content == undefined){
                                $.post("sql_welcome.php",{
                                    query_type:"get_order_detail",
                                    member:member,
                                    fi_no:fi_no
                                },getContent);
                            }else{
                                getContent($this.data("content"));
                            }
                        });
                        
                        $order_content.find("input[name=order_goods_detail]").click(function(){
                            var $this = $(this);
                            var $dialog = $("#dialog_content");
                            var content = $this.data("content");

                            function getContent(data)
                            {
                                $this.data("content",data);
                                data = data.split("@");
                                var l = data.length;
                                var display = "<table id='order_goods_table' class='table-h' data-dialog='產品細目'>";
                                display +=  "<tr>"+
                                            "<td>ID</td>"+
                                            "<td>產品ID</td>"+
                                            "<td style='width:100px;'>產品名</td>"+
                                            "<td>市價</td>"+
                                            "<td>促銷價</td>"+
                                            "<td>折扣價</td>"+
                                            "<td>數量</td>"+
                                            "<td>規格</td>"+
                                            "<td>尺寸</td>"+
                                            "<td>商品狀態</td>"+
                                            "</tr>";
                                for(var i=0;i<l;i++)
                                {
                                    var d = data[i].split("`");
                                    var id = d[0],
                                        member = d[1],
                                        order = d[2],
                                        goods = d[3],
                                        name = d[4],
                                        price = d[5],
                                        promotions = d[6],
                                        discount = d[7],
                                        number = d[8],
                                        specifications = d[9],
                                        volumetric_weight = d[10],
                                        status = d[11],
                                        store = d[12];
                                    display += "<tr>"
                                    +"<td>"+id+"</td>"
                                    +"<td>"+goods+"</td>"
                                    +"<td>"+name+"</td>"
                                    +"<td>"+price+"</td>"
                                    +"<td>"+promotions+"</td>"
                                    +"<td>"+discount+"</td>"
                                    +"<td>"+number+"</td>"
                                    +"<td>"+json_trans_display(specifications)+"</td>"
                                    +"<td>"+json_trans_display(volumetric_weight)+"</td>"
                                    +"<td>"+status_list[status]+"</td>"
                                    +"</tr>";
                                }
                                display += "</table>";
                                $dialog.append(display);
                                var $ogt = $("#order_goods_table");
                                init_style($ogt);
                                $ogt.show();
                            }

                            if(content == undefined){
                                $.post("sql_welcome.php",{
                                    query_type:"get_order_goods",
                                    member:member,
                                    fi_no:fi_no
                                },getContent);
                            }else{
                                getContent($this.data("content"));
                            }
                        });
                    });
                });
            });
            
            function json_trans_display(json){
                
                if(json == "" || json==undefined)return "";
                
                json = $.parseJSON(json);
                var display = "";
                for(var i in json)
                {
                    display+=i+":"+json[i]+"<br/>";
                }
                return display;
            }
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
                    
                </div>
                <!-- /.body_left -->
                <div id="body_right">
                    <?php echo $login_title; ?>
                    <table></table>
                    <table id='service_table' class='table-v'>
                        <tr><td>訂單查詢</td><td><input name="order_sn" type="text" placeholder="請輸入訂單序號"><input name="order_check" type="button" value="查訊訂單"><div id='order_content'></div></td></tr>
                        <tr><td>客戶QQ</td><td><input name='qq' type='text' placeholder="請輸入QQ" /></td></tr>
                        <tr><td>客戶暱稱</td><td><input name='name' type='text' placeholder="請輸入暱稱" /></td></tr>
                        <tr><td>對話記錄</td><td><textarea name='content' style='width:99%;height:200px;' placeholder="請輸入聊天內容"></textarea></td></tr>
                        <tr><td></td><td><input name='save' type='button' value="儲存" /></td></tr>
                    </table>
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