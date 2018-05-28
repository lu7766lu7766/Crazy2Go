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
        
        //所有供應商資料
        if(isset($_GET["oname"]))
        {
            $_GET["oname"] = str_replace("∵", "_", $_GET["oname"]);
            $_GET["oname"] = str_replace("\\_", "_", $_GET["oname"]);
        }
        $orderby = isset($_GET["oname"])?"order by ".$_GET["oname"]." ".$_GET["order"]:"order by fi_no asc";
        $query = "select * from store_supplier where store=".$login_store." ".$orderby;//and `delete`=0 
        if(isset($_GET['keyword']))
        {
            switch($_GET['type'])
            {
                case '1'://供應商ID
                    $orderby = isset($_GET["oname"])?"order by ".$_GET["oname"]." ".$_GET["order"]:"order by fi_no asc";
                    $query = "select * from store_supplier where store=".$login_store." and fi_no >= ".(int)$_GET['keyword']." ".$orderby;
                    break;
                case '2'://供應商名稱
                    $orderby = isset($_GET["oname"])?"order by ".$_GET["oname"]." ".$_GET["order"]:"order by fi_no asc";
                    $query = "select * from store_supplier where store=".$login_store." and name like '%".$_GET['keyword']."%' ".$orderby;
                    break;
                case '3'://供應商簡稱
                    $orderby = isset($_GET["oname"])?"order by ".$_GET["oname"]." ".$_GET["order"]:"order by fi_no asc";
                    $query = "select * from store_supplier where store=".$login_store." and nickname like '%".$_GET['keyword']."%' ".$orderby;
                    break;
            }
        }
        
        include '../backend/class_pager2.php';
        $pager = new Pager();
        $result = $pager->query($query);
        $len = count($result);
        for($i = 0; $i < $len; $i++)
        {
            $all_supplier[]=array(
                fi_no => $result[$i]['fi_no'],
                name => $result[$i]['name'],
                nickname => $result[$i]['nickname'],
                company => json_decode($result[$i]['company'],true),
                returns => json_decode($result[$i]['returns'],true),
                operate => json_decode($result[$i]['operate'],true),
                license => json_decode($result[$i]['license'],true),
                bank => json_decode($result[$i]['bank'],true),
                service => json_decode($result[$i]['service'],true),
                shipper => json_decode($result[$i]['shipper'],true),
                check => json_decode($result[$i]['check'],true)
            );
        }
        
        //依權限顯示
        $display_add = strpos($login_permissions,"supplier_add")!==false || strpos($login_permissions,"all")!==false?true:false;
        $display_delete = false;// = strpos($login_permissions,"supplier_del")!==false || strpos($login_permissions,"all")!==false?true:false;
        $display_edit = strpos($login_permissions,"supplier_edit")!==false || strpos($login_permissions,"all")!==false?true:false;
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
        <link rel="stylesheet" type="text/css" href="../public/css/jquery.datetimepicker.css"/>
        <script src="../public/js/jquery.datetimepicker.js"></script>
        
        <script>
            /******************** user define js ********************/
            $(function(){
                
                <?php if(isset($_GET['type'])&&isset($_GET['keyword'])){?>
                $("#supplier_search select[name='type']").val(<?php echo $_GET['type'];?>);
                $("#supplier_search input[name='keyword']").val(<?php echo "'".$_GET['keyword']."'";?>);
                <?php }?>
                
                $("#body_right").show();
                $("#image_choose_panel").hide();
                $("#image_resize_panel").hide();
                
                //列表排序
                $("#supplier_list_table tr:first td[name]").each(function(){
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
                /*$("#supplier_list_table .supplier_del").click(function(){
                    if(!confirm("確定刪除？"))return;
                    var $this = $(this);
                    var fi_no = $this.parent().parent().attr("fi_no");
                    var name = $this.parent().parent().attr("name");
                    $.post(filename,{
                        query_type:"product_del",
                        name:name,
                        fi_no:fi_no
                    },function(){
                        alert("已刪除！");
                        location.href = location.href;
                    });
                });*/
                
                //新增,編輯供應商
                $("#supplier_add_btn,#supplier_list_table .supplier_edit").click(function(){
                    var $dialog = $("#dialog_content");
                    $dialog.data("fi_no",$(this).parent().parent().attr("fi_no"));
                    if($dialog.data("fi_no"))
                    {
                        $("#dialog_title").text($("#dialog_title").text()+" "+$dialog.data("fi_no")+" - "+$(this).parent().parent().attr("name"));
                    }
                    $dialog.find("input:first").focus();
                    //時間選擇器
                    $dialog.find(   "input[group='bank'][name='contract_start']"+
                                    ",input[group='bank'][name='contract_end']"+
                                    ",input[group='company'][name='date_establishment']"
                    ).datetimepicker({
                        lang:'ch',
                        timepicker:false,
                        format:'Y-m-d',
                        formatDate:'Y/m/d'
                    });
                    
                    //營業方式其他
                    $dialog.find("textarea[group='operate'][name='other_context']").focus(function(){
                        $dialog.find("input[group='operate'][name='other']").prop("checked",true);
                    }).blur(function(){
                        if($(this).val()==""){
                            $dialog.find("input[group='operate'][name='other']").prop("checked",false);
                        }else{
                            $dialog.find("input[group='operate'][name='other']").prop("checked",true);
                        }
                    });
                    
                    //證照影本其他
                    $dialog.find("textarea[group='license'][name='other_context']").focus(function(){
                        $dialog.find("input[group='license'][name='other']").prop("checked",true);
                    }).blur(function(){
                        if($(this).val()==""){
                            $dialog.find("input[group='license'][name='other']").prop("checked",false);
                        }else{
                            $dialog.find("input[group='license'][name='other']").prop("checked",true);
                        }
                    });

                    // 儲存
                    $dialog.find("input[name='supplier_add_save'],input[name='supplier_edit_save']").click(function(){
                        var p_form = {};
                        p_form.supplier_name = $dialog.find("input[name='supplier_name']").val();
                        p_form.supplier_nickname = $dialog.find("input[name='supplier_nickname']").val();
                        p_form.company_key = [
                            "corporate",
                            "date_establishment",
                            "registered_capital",
                            "organization_code",
                            "zip",
                            "address",
                            "invoice",
                            "invoice_zip",
                            "invoice_address",
                            "http",
                            "phone",
                            "mobile",
                            "fax"
                        ];
                        p_form.company_val = [
                            $dialog.find("input[group='company'][name='corporate']").val(),
                            $dialog.find("input[group='company'][name='date_establishment']").val(),
                            $dialog.find("input[group='company'][name='registered_capital']").val(),
                            $dialog.find("input[group='company'][name='organization_code']").val(),
                            $dialog.find("input[group='company'][name='zip']").val(),
                            $dialog.find("input[group='company'][name='address']").val(),
                            $dialog.find("input[group='company'][name='invoice']").val(),
                            $dialog.find("input[group='company'][name='invoice_zip']").val(),
                            $dialog.find("input[group='company'][name='invoice_address']").val(),
                            $dialog.find("input[group='company'][name='http']").val(),
                            $dialog.find("input[group='company'][name='phone']").val(),
                            $dialog.find("input[group='company'][name='mobile']").val(),
                            $dialog.find("input[group='company'][name='fax']").val()
                        ];
                        p_form.returns_key = [
                            "zip",
                            "address",
                            "phone",
                            "fax"
                        ];
                        p_form.returns_val = [
                            $dialog.find("input[group='returns'][name='zip']").val(),
                            $dialog.find("input[group='returns'][name='address']").val(),
                            $dialog.find("input[group='returns'][name='phone']").val(),
                            $dialog.find("input[group='returns'][name='fax']").val()
                        ];
                        p_form.operate_key = [
                            "production_plant",
                            "merchant",
                            "agents",
                            "dealer",
                            "other",
                            "other_context"
                        ];
                        p_form.operate_val = [
                            $dialog.find("input[group='operate'][name='production_plant']").prop("checked")?1:0,
                            $dialog.find("input[group='operate'][name='merchant']").prop("checked")?1:0,
                            $dialog.find("input[group='operate'][name='agents']").prop("checked")?1:0,
                            $dialog.find("input[group='operate'][name='dealer']").prop("checked")?1:0,
                            $dialog.find("input[group='operate'][name='other']").prop("checked")?1:0,
                            $dialog.find("textarea[group='operate'][name='other_context']").val()
                        ]
                        p_form.license_key = [
                            "produce",
                            "license",
                            "agent_distributor",
                            "patent",
                            "other",
                            "other_context"
                        ];
                        p_form.license_val = [
                            $dialog.find("input[group='license'][name='produce']").prop("checked")?1:0,
                            $dialog.find("input[group='license'][name='license']").prop("checked")?1:0,
                            $dialog.find("input[group='license'][name='agent_distributor']").prop("checked")?1:0,
                            $dialog.find("input[group='license'][name='patent']").prop("checked")?1:0,
                            $dialog.find("input[group='license'][name='other']").prop("checked")?1:0,
                            $dialog.find("textarea[group='license'][name='other_context']").val()
                        ];
                        p_form.bank_key = [
                            "bank",
                            "branch",
                            "account",
                            "username",
                            "contract_start",
                            "contract_end",
                            "name",
                            "position",
                            "phone",
                            "fax",
                            "mobile",
                            "email"
                        ];
                        p_form.bank_val = [
                            $dialog.find("input[group='bank'][name='bank']").val(),
                            $dialog.find("input[group='bank'][name='branch']").val(),
                            $dialog.find("input[group='bank'][name='account']").val(),
                            $dialog.find("input[group='bank'][name='username']").val(),
                            $dialog.find("input[group='bank'][name='contract_start']").val(),
                            $dialog.find("input[group='bank'][name='contract_end']").val(),
                            $dialog.find("input[group='bank'][name='name']").val(),
                            $dialog.find("input[group='bank'][name='position']").val(),
                            $dialog.find("input[group='bank'][name='phone']").val(),
                            $dialog.find("input[group='bank'][name='fax']").val(),
                            $dialog.find("input[group='bank'][name='mobile']").val(),
                            $dialog.find("input[group='bank'][name='email']").val()
                        ];
                        p_form.service_0_key = [
                            "name",
                            "position",
                            "phone",
                            "fax",
                            "mobile",
                            "email"
                        ];
                        p_form.service_0_val = [
                            $dialog.find("input[group='service'][p_no='0'][name='name']").val(),
                            $dialog.find("input[group='service'][p_no='0'][name='position']").val(),
                            $dialog.find("input[group='service'][p_no='0'][name='phone']").val(),
                            $dialog.find("input[group='service'][p_no='0'][name='fax']").val(),
                            $dialog.find("input[group='service'][p_no='0'][name='mobile']").val(),
                            $dialog.find("input[group='service'][p_no='0'][name='email']").val()
                        ];
                        p_form.service_1_key = [
                            "name",
                            "position",
                            "phone",
                            "fax",
                            "mobile",
                            "email"
                        ];
                        p_form.service_1_val = [
                            $dialog.find("input[group='service'][p_no='1'][name='name']").val(),
                            $dialog.find("input[group='service'][p_no='1'][name='position']").val(),
                            $dialog.find("input[group='service'][p_no='1'][name='phone']").val(),
                            $dialog.find("input[group='service'][p_no='1'][name='fax']").val(),
                            $dialog.find("input[group='service'][p_no='1'][name='mobile']").val(),
                            $dialog.find("input[group='service'][p_no='1'][name='email']").val()
                        ];
                        p_form.shipper_key = [
                            "name",
                            "position",
                            "phone",
                            "fax",
                            "mobile",
                            "email"
                        ];
                        p_form.shipper_val = [
                            $dialog.find("input[group='shipper'][name='name']").val(),
                            $dialog.find("input[group='shipper'][name='position']").val(),
                            $dialog.find("input[group='shipper'][name='phone']").val(),
                            $dialog.find("input[group='shipper'][name='fax']").val(),
                            $dialog.find("input[group='shipper'][name='mobile']").val(),
                            $dialog.find("input[group='shipper'][name='email']").val()
                        ];
                        p_form.check_key = [
                            "name",
                            "position",
                            "phone",
                            "fax",
                            "mobile",
                            "email"
                        ];
                        p_form.check_val = [
                            $dialog.find("input[group='check'][name='name']").val(),
                            $dialog.find("input[group='check'][name='position']").val(),
                            $dialog.find("input[group='check'][name='phone']").val(),
                            $dialog.find("input[group='check'][name='fax']").val(),
                            $dialog.find("input[group='check'][name='mobile']").val(),
                            $dialog.find("input[group='check'][name='email']").val()
                        ];
                        
                        if($(this).attr("name") == "supplier_add_save")
                        {
                            p_form.query_type="supplier_add";
                        }
                        
                        if($(this).attr("name") == "supplier_edit_save")
                        {
                            p_form.query_type="supplier_edit";
                            p_form.fi_no=$dialog.data("fi_no");
                        }

                        $.post(filename,p_form,function(data){
                            alert($.trim(data));
                            location.href = location.href;
                        });
                        
                    });

                    
                    if($dialog.has("input[name='supplier_edit_save']").length){                       
                        // 填表
                        $.post(filename,{
                            query_type:"get_supplier_detail",
                            fi_no:$dialog.data("fi_no")
                        },function(data){
                            data = data.split("`");
                            //資料
                            var p_name = data[0],
                                p_nickname= data[1],
                                p_company = data[2]==""?{}:$.parseJSON(data[2]),
                                p_returns = data[3]==""?{}:$.parseJSON(data[3]),
                                p_operate = data[4]==""?{}:$.parseJSON(data[4]),
                                p_license = data[5]==""?{}:$.parseJSON(data[5]),
                                p_bank = data[6]==""?{}:$.parseJSON(data[6]),
                                p_service = data[7]==""?[]:$.parseJSON(data[7]),
                                p_shipper = data[8]==""?{}:$.parseJSON(data[8]),
                                p_check = data[9]==""?{}:$.parseJSON(data[9]);
                            
                            $dialog.find("input[name='supplier_name']").val(p_name);
                            $dialog.find("input[name='supplier_nickname']").val(p_nickname);
                            
                            $dialog.find("input[group='company'][name='corporate']").val(p_company.corporate),
                            $dialog.find("input[group='company'][name='date_establishment']").val(p_company.date_establishment),
                            $dialog.find("input[group='company'][name='registered_capital']").val(p_company.registered_capital),
                            $dialog.find("input[group='company'][name='organization_code']").val(p_company.organization_code),
                            $dialog.find("input[group='company'][name='zip']").val(p_company.zip),
                            $dialog.find("input[group='company'][name='address']").val(p_company.address),
                            $dialog.find("input[group='company'][name='invoice']").val(p_company.invoice),
                            $dialog.find("input[group='company'][name='invoice_zip']").val(p_company.invoice_zip),
                            $dialog.find("input[group='company'][name='invoice_address']").val(p_company.invoice_address),
                            $dialog.find("input[group='company'][name='http']").val(p_company.http),
                            $dialog.find("input[group='company'][name='phone']").val(p_company.phone),
                            $dialog.find("input[group='company'][name='mobile']").val(p_company.mobile),
                            $dialog.find("input[group='company'][name='fax']").val(p_company.fax);
                    
                            $dialog.find("input[group='returns'][name='zip']").val(p_returns.zip),
                            $dialog.find("input[group='returns'][name='address']").val(p_returns.address),
                            $dialog.find("input[group='returns'][name='phone']").val(p_returns.phone),
                            $dialog.find("input[group='returns'][name='fax']").val(p_returns.fax);
                    
                            $dialog.find("input[group='operate'][name='production_plant']").prop("checked",parseInt(p_operate.production_plant)),
                            $dialog.find("input[group='operate'][name='merchant']").prop("checked",parseInt(p_operate.merchant)),
                            $dialog.find("input[group='operate'][name='agents']").prop("checked",parseInt(p_operate.agents)),
                            $dialog.find("input[group='operate'][name='dealer']").prop("checked",parseInt(p_operate.dealer)),
                            $dialog.find("input[group='operate'][name='other']").prop("checked",parseInt(p_operate.other)),
                            $dialog.find("textarea[group='operate'][name='other_context']").val(p_operate.other_context);
                    
                            $dialog.find("input[group='license'][name='produce']").prop("checked",parseInt(p_license.produce)),
                            $dialog.find("input[group='license'][name='license']").prop("checked",parseInt(p_license.license)),
                            $dialog.find("input[group='license'][name='agent_distributor']").prop("checked",parseInt(p_license.agent_distributor)),
                            $dialog.find("input[group='license'][name='patent']").prop("checked",parseInt(p_license.patent)),
                            $dialog.find("input[group='license'][name='other']").prop("checked",parseInt(p_license.other)),
                            $dialog.find("textarea[group='license'][name='other_context']").val(p_license.other_context);
                    
                            $dialog.find("input[group='bank'][name='bank']").val(p_bank.bank),
                            $dialog.find("input[group='bank'][name='branch']").val(p_bank.branch),
                            $dialog.find("input[group='bank'][name='account']").val(p_bank.account),
                            $dialog.find("input[group='bank'][name='username']").val(p_bank.username),
                            $dialog.find("input[group='bank'][name='contract_start']").val(p_bank.contract_start),
                            $dialog.find("input[group='bank'][name='contract_end']").val(p_bank.contract_end),
                            $dialog.find("input[group='bank'][name='name']").val(p_bank.name),
                            $dialog.find("input[group='bank'][name='position']").val(p_bank.position),
                            $dialog.find("input[group='bank'][name='phone']").val(p_bank.phone),
                            $dialog.find("input[group='bank'][name='fax']").val(p_bank.fax),
                            $dialog.find("input[group='bank'][name='mobile']").val(p_bank.mobile),
                            $dialog.find("input[group='bank'][name='email']").val(p_bank.email);
                    
                            $dialog.find("input[group='service'][p_no='0'][name='name']").val(p_service[0].name),
                            $dialog.find("input[group='service'][p_no='0'][name='position']").val(p_service[0].position),
                            $dialog.find("input[group='service'][p_no='0'][name='phone']").val(p_service[0].phone),
                            $dialog.find("input[group='service'][p_no='0'][name='fax']").val(p_service[0].fax),
                            $dialog.find("input[group='service'][p_no='0'][name='mobile']").val(p_service[0].mobile),
                            $dialog.find("input[group='service'][p_no='0'][name='email']").val(p_service[0].email);
                            
                            $dialog.find("input[group='service'][p_no='1'][name='name']").val(p_service[1].name),
                            $dialog.find("input[group='service'][p_no='1'][name='position']").val(p_service[1].position),
                            $dialog.find("input[group='service'][p_no='1'][name='phone']").val(p_service[1].phone),
                            $dialog.find("input[group='service'][p_no='1'][name='fax']").val(p_service[1].fax),
                            $dialog.find("input[group='service'][p_no='1'][name='mobile']").val(p_service[1].mobile),
                            $dialog.find("input[group='service'][p_no='1'][name='email']").val(p_service[1].email);
                    
                            $dialog.find("input[group='shipper'][name='name']").val(p_shipper.name),
                            $dialog.find("input[group='shipper'][name='position']").val(p_shipper.position),
                            $dialog.find("input[group='shipper'][name='phone']").val(p_shipper.phone),
                            $dialog.find("input[group='shipper'][name='fax']").val(p_shipper.fax),
                            $dialog.find("input[group='shipper'][name='mobile']").val(p_shipper.mobile),
                            $dialog.find("input[group='shipper'][name='email']").val(p_shipper.email);
                    
                            $dialog.find("input[group='check'][name='name']").val(p_check.name),
                            $dialog.find("input[group='check'][name='position']").val(p_check.position),
                            $dialog.find("input[group='check'][name='phone']").val(p_check.phone),
                            $dialog.find("input[group='check'][name='fax']").val(p_check.fax),
                            $dialog.find("input[group='check'][name='mobile']").val(p_check.mobile),
                            $dialog.find("input[group='check'][name='email']").val(p_check.email);
                        });
                    }
                });
                
                //搜尋
                $("#supplier_search input[name='search']").click(function(){
                    var type = $("#supplier_search select[name='type']").val();
                    var keyword = $("#supplier_search input[name='keyword']").val();
                    location.href = location.href.split("?")[0] + "?type=" + type + "&keyword=" + keyword;
                });
                $("#supplier_search input[name='keyword']").keypress(function(e){
                    if(e.keyCode==13)
                    {
                        $("#supplier_search input[name='search']").trigger("click");
                    }
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
                <div id="body_right" style='display:none;'>
                    <?php
                        
                        //搜尋
                        echo "<div id='supplier_search' style='display:inline-block;float:right;padding:2px;background:rgba(255,255,255,0.2);' class='shadowRoundCorner'><select name='type'><option value='1'>供應商ID</option><option value='2'>供應商名稱</option><option value='3'>供應商簡稱</option></select>&nbsp;<input name='keyword' type='text' placeholder='請輸入關鍵字'/>&nbsp;<input name='search' type='button' value='搜尋'></div><table></table>";
                        
                        //新增
                        if($display_add)
                        {
                            echo "<table id='supplier_add_table' class='table-v' data-dialog='新增供應商'>";
                            echo "<tr><td>供貨商名稱</td><td><input type='text' name='supplier_name' style='width:60%;' /></td></tr>";
                            echo "<tr><td>供貨商簡稱</td><td><input type='text' name='supplier_nickname' style='width:60%;' /></td></tr>";
                            echo "<tr><td style='width:200px;'><div style='background:black;font-size:12pt;color:red;'>公司資訊</div></td><td><div style='background:black;'>.</div></td></tr>";
                            echo "<tr><td>公司法人</td><td><input type='text' group='company' name='corporate' style='width:60%;' /></td></tr>";
                            echo "<tr><td>成立日期</td><td><input type='text' group='company' name='date_establishment' style='width:20%;' /></td></tr>";
                            echo "<tr><td>註冊資本</td><td><input type='text' group='company' name='registered_capital' style='width:60%;' /></td></tr>";
                            echo "<tr><td>組織機構代碼證號</td><td><input type='text' group='company' name='organization_code' style='width:60%;' /></td></tr>";
                            echo "<tr><td>公司聯絡位址</td><td>郵編 <input type='text' group='company' name='zip' style='width:10%;' /> 地址 <input type='text' group='company' name='address' style='width:60%;' /></td></tr>";
                            echo "<tr><td>電話</td><td><input type='text' group='company' name='phone' style='width:60%;' /></td></tr>";
                            echo "<tr style='display:none;'><td>移動電話</td><td><input type='text' group='company' name='mobile' style='width:60%;' /></td></tr>";
                            echo "<tr><td>傳真</td><td><input type='text' group='company' name='fax' style='width:60%;' /></td></tr>";
                            echo "<tr><td>http</td><td><input type='text' group='company' name='http' style='width:60%;' /></td></tr>";
                            echo "<tr><td>發票抬頭</td><td><input type='text' group='company' name='invoice' style='width:60%;' /></td></tr>";
                            echo "<tr><td>發票聯絡位址</td><td>郵編 <input type='text' group='company' name='invoice_zip' style='width:10%;' /> 地址 <input type='text' group='company' name='invoice_address' style='width:60%;' /></td></tr>";
                            echo "<tr><td><div style='background:black;font-size:12pt;color:red;'>退貨資訊</div></td><td><div style='background:black;'>.</div></td></tr>";
                            echo "<tr><td>退貨聯絡位址</td><td>郵編 <input type='text' group='returns' name='zip' style='width:10%;' /> 地址 <input type='text' group='returns' name='address' style='width:60%;' /></td></tr>";
                            echo "<tr><td>電話</td><td><input type='text' group='returns' name='phone' style='width:60%;' /></td></tr>";
                            echo "<tr><td>傳真</td><td><input type='text' group='returns' name='fax' style='width:60%;' /></td></tr>";
                            echo "<tr><td><div style='background:black;font-size:12pt;color:red;'>營業方式</div></td><td><div style='background:black;'>.</div></td></tr>";
                            echo "<tr><td>生產工廠</td><td><input type='checkbox' group='operate' name='production_plant' /></td></tr>";
                            echo "<tr><td>貿易商</td><td><input type='checkbox' group='operate' name='merchant' /></td></tr>";
                            echo "<tr><td>代理商</td><td><input type='checkbox' group='operate' name='agents' /></td></tr>";
                            echo "<tr><td>經銷商</td><td><input type='checkbox' group='operate' name='dealer' /></td></tr>";
                            echo "<tr><td>其他</td><td><input type='checkbox' group='operate' name='other' style='vertical-align:top;' /> <textarea group='operate' name='other_context' style='height:100px;width:60%;'></textarea></td></tr>";
                            echo "<tr><td><div style='background:black;font-size:12pt;color:red;'>證照影本</div></td><td><div style='background:black;'>.</div></td></tr>";
                            echo "<tr><td>生產許可證</td><td><input type='checkbox' group='license' name='produce' /></td></tr>";
                            echo "<tr><td>營業執照</td><td><input type='checkbox' group='license' name='license' /></td></tr>";
                            echo "<tr><td>代理或經銷證明</td><td><input type='checkbox' group='license' name='agent_distributor' /></td></tr>";
                            echo "<tr><td>專利證明</td><td><input type='checkbox' group='license' name='patent' /></td></tr>";
                            echo "<tr><td>其他證明</td><td><input type='checkbox' group='license' name='other' style='vertical-align:top;' /> <textarea group='license' name='other_context' style='height:100px;width:60%;'></textarea></td></tr>";
                            echo "<tr><td><div style='background:black;font-size:12pt;color:red;'>銀行資訊</div></td><td><div style='background:black;'>.</div></td></tr>";
                            echo "<tr><td>銀行名稱</td><td><input type='text' group='bank' name='bank' style='width:60%;' /></td></tr>";
                            echo "<tr><td>分行</td><td><input type='text' group='bank' name='branch' style='width:60%;' /></td></tr>";
                            echo "<tr><td>帳號</td><td><input type='text' group='bank' name='account' style='width:60%;' /></td></tr>";
                            echo "<tr><td>戶名</td><td><input type='text' group='bank' name='username' style='width:60%;' /></td></tr>";
                            echo "<tr><td>合同起日</td><td><input type='text' group='bank' name='contract_start' style='width:20%;' /></td></tr>";
                            echo "<tr><td>合同止日</td><td><input type='text' group='bank' name='contract_end' style='width:20%;' /></td></tr>";
                            echo "<tr><td>合同聯絡人</td><td><input type='text' group='bank' name='name' style='width:60%;' /></td></tr>";
                            echo "<tr><td>職位</td><td><input type='text' group='bank' name='position' style='width:60%;' /></td></tr>";
                            echo "<tr><td>電話</td><td><input type='text' group='bank' name='phone' style='width:60%;' /></td></tr>";
                            echo "<tr><td>傳真</td><td><input type='text' group='bank' name='fax' style='width:60%;' /></td></tr>";
                            echo "<tr><td>手機</td><td><input type='text' group='bank' name='mobile' style='width:60%;' /></td></tr>";
                            echo "<tr><td>E-mail</td><td><input type='text' group='bank' name='email' style='width:60%;' /></td></tr>";
                            echo "<tr><td><div style='background:black;font-size:12pt;color:red;'>客服聯絡人資訊</div></td><td><div style='background:black;'>.</div></td></tr>";
                            echo "<tr><td>客服聯絡人1</td><td><input type='text' group='service' p_no='0' name='name' style='width:60%;' /></td></tr>";
                            echo "<tr><td>職位</td><td><input type='text' group='service' p_no='0' name='position' style='width:60%;' /></td></tr>";
                            echo "<tr><td>電話</td><td><input type='text' group='service' p_no='0' name='phone' style='width:60%;' /></td></tr>";
                            echo "<tr><td>傳真</td><td><input type='text' group='service' p_no='0' name='fax' style='width:60%;' /></td></tr>";
                            echo "<tr><td>手機</td><td><input type='text' group='service' p_no='0' name='mobile' style='width:60%;' /></td></tr>";
                            echo "<tr><td>E-mail</td><td><input type='text' group='service' p_no='0' name='email' style='width:60%;' /></td></tr>";
                            echo "<tr><td>客服聯絡人2</td><td><input type='text' group='service' p_no='1' name='name' style='width:60%;' /></td></tr>";
                            echo "<tr><td>職位</td><td><input type='text' group='service' p_no='1' name='position' style='width:60%;' /></td></tr>";
                            echo "<tr><td>電話</td><td><input type='text' group='service' p_no='1' name='phone' style='width:60%;' /></td></tr>";
                            echo "<tr><td>傳真</td><td><input type='text' group='service' p_no='1' name='fax' style='width:60%;' /></td></tr>";
                            echo "<tr><td>手機</td><td><input type='text' group='service' p_no='1' name='mobile' style='width:60%;' /></td></tr>";
                            echo "<tr><td>E-mail</td><td><input type='text' group='service' p_no='1' name='email' style='width:60%;' /></td></tr>";
                            echo "<tr><td><div style='background:black;font-size:12pt;color:red;'>出貨聯絡人資訊</div></td><td><div style='background:black;'>.</div></td></tr>";
                            echo "<tr><td>出貨聯絡人</td><td><input type='text' group='shipper' name='name' style='width:60%;' /></td></tr>";
                            echo "<tr><td>職位</td><td><input type='text' group='shipper' name='position' style='width:60%;' /></td></tr>";
                            echo "<tr><td>電話</td><td><input type='text' group='shipper' name='phone' style='width:60%;' /></td></tr>";
                            echo "<tr><td>傳真</td><td><input type='text' group='shipper' name='fax' style='width:60%;' /></td></tr>";
                            echo "<tr><td>手機</td><td><input type='text' group='shipper' name='mobile' style='width:60%;' /></td></tr>";
                            echo "<tr><td>E-mail</td><td><input type='text' group='shipper' name='email' style='width:60%;' /></td></tr>";
                            echo "<tr><td><div style='background:black;font-size:12pt;color:red;'>對帳聯絡人資訊</div></td><td><div style='background:black;'>.</div></td></tr>";
                            echo "<tr><td>對帳聯絡人</td><td><input type='text' group='check' name='name' style='width:60%;' /></td></tr>";
                            echo "<tr><td>職位</td><td><input type='text' group='check' name='position' style='width:60%;' /></td></tr>";
                            echo "<tr><td>電話</td><td><input type='text' group='check' name='phone' style='width:60%;' /></td></tr>";
                            echo "<tr><td>傳真</td><td><input type='text' group='check' name='fax' style='width:60%;' /></td></tr>";
                            echo "<tr><td>手機</td><td><input type='text' group='check' name='mobile' style='width:60%;' /></td></tr>";
                            echo "<tr><td>E-mail</td><td><input type='text' group='check' name='email' style='width:60%;' /></td></tr>";
                            echo "<tr><td></td><td><input name='supplier_add_save' type='button' value='儲存' /></td></tr>";
                            echo "</table>";
                            echo "<input id='supplier_add_btn' type='button' value='新增供應商' data-open-dialog='新增供應商' /><table></table>";                            
                        }
                        
                        //編輯
                        if($display_edit)
                        {
                            echo "<table id='supplier_edit_table' class='table-v' data-dialog='編輯供應商'>";
                            echo "<tr><td>供貨商名稱</td><td><input type='text' name='supplier_name' style='width:60%;' /></td></tr>";
                            echo "<tr><td>供貨商簡稱</td><td><input type='text' name='supplier_nickname' style='width:60%;' /></td></tr>";
                            echo "<tr><td style='width:200px;'><div style='background:black;font-size:12pt;color:red;'>公司資訊</div></td><td><div style='background:black;'>.</div></td></tr>";
                            echo "<tr><td>公司法人</td><td><input type='text' group='company' name='corporate' style='width:60%;' /></td></tr>";
                            echo "<tr><td>成立日期</td><td><input type='text' group='company' name='date_establishment' style='width:20%;' /></td></tr>";
                            echo "<tr><td>註冊資本</td><td><input type='text' group='company' name='registered_capital' style='width:60%;' /></td></tr>";
                            echo "<tr><td>組織機構代碼證號</td><td><input type='text' group='company' name='organization_code' style='width:60%;' /></td></tr>";
                            echo "<tr><td>公司聯絡位址</td><td>郵編 <input type='text' group='company' name='zip' style='width:10%;' /> 地址 <input type='text' group='company' name='address' style='width:60%;' /></td></tr>";
                            echo "<tr><td>電話</td><td><input type='text' group='company' name='phone' style='width:60%;' /></td></tr>";
                            echo "<tr style='display:none;'><td>移動電話</td><td><input type='text' group='company' name='mobile' style='width:60%;' /></td></tr>";
                            echo "<tr><td>傳真</td><td><input type='text' group='company' name='fax' style='width:60%;' /></td></tr>";
                            echo "<tr><td>http</td><td><input type='text' group='company' name='http' style='width:60%;' /></td></tr>";
                            echo "<tr><td>發票抬頭</td><td><input type='text' group='company' name='invoice' style='width:60%;' /></td></tr>";
                            echo "<tr><td>發票聯絡位址</td><td>郵編 <input type='text' group='company' name='invoice_zip' style='width:10%;' /> 地址 <input type='text' group='company' name='invoice_address' style='width:60%;' /></td></tr>";
                            echo "<tr><td><div style='background:black;font-size:12pt;color:red;'>退貨資訊</div></td><td><div style='background:black;'>.</div></td></tr>";
                            echo "<tr><td>退貨聯絡位址</td><td>郵編 <input type='text' group='returns' name='zip' style='width:10%;' /> 地址 <input type='text' group='returns' name='address' style='width:60%;' /></td></tr>";
                            echo "<tr><td>電話</td><td><input type='text' group='returns' name='phone' style='width:60%;' /></td></tr>";
                            echo "<tr><td>傳真</td><td><input type='text' group='returns' name='fax' style='width:60%;' /></td></tr>";
                            echo "<tr><td><div style='background:black;font-size:12pt;color:red;'>營業方式</div></td><td><div style='background:black;'>.</div></td></tr>";
                            echo "<tr><td>生產工廠</td><td><input type='checkbox' group='operate' name='production_plant' /></td></tr>";
                            echo "<tr><td>貿易商</td><td><input type='checkbox' group='operate' name='merchant' /></td></tr>";
                            echo "<tr><td>代理商</td><td><input type='checkbox' group='operate' name='agents' /></td></tr>";
                            echo "<tr><td>經銷商</td><td><input type='checkbox' group='operate' name='dealer' /></td></tr>";
                            echo "<tr><td>其他</td><td><input type='checkbox' group='operate' name='other' style='vertical-align:top;' /> <textarea group='operate' name='other_context' style='height:100px;width:60%;'></textarea></td></tr>";
                            echo "<tr><td><div style='background:black;font-size:12pt;color:red;'>證照影本</div></td><td><div style='background:black;'>.</div></td></tr>";
                            echo "<tr><td>生產許可證</td><td><input type='checkbox' group='license' name='produce' /></td></tr>";
                            echo "<tr><td>營業執照</td><td><input type='checkbox' group='license' name='license' /></td></tr>";
                            echo "<tr><td>代理或經銷證明</td><td><input type='checkbox' group='license' name='agent_distributor' /></td></tr>";
                            echo "<tr><td>專利證明</td><td><input type='checkbox' group='license' name='patent' /></td></tr>";
                            echo "<tr><td>其他證明</td><td><input type='checkbox' group='license' name='other' style='vertical-align:top;' /> <textarea group='license' name='other_context' style='height:100px;width:60%;'></textarea></td></tr>";
                            echo "<tr><td><div style='background:black;font-size:12pt;color:red;'>銀行資訊</div></td><td><div style='background:black;'>.</div></td></tr>";
                            echo "<tr><td>銀行名稱</td><td><input type='text' group='bank' name='bank' style='width:60%;' /></td></tr>";
                            echo "<tr><td>分行</td><td><input type='text' group='bank' name='branch' style='width:60%;' /></td></tr>";
                            echo "<tr><td>帳號</td><td><input type='text' group='bank' name='account' style='width:60%;' /></td></tr>";
                            echo "<tr><td>戶名</td><td><input type='text' group='bank' name='username' style='width:60%;' /></td></tr>";
                            echo "<tr><td>合同起日</td><td><input type='text' group='bank' name='contract_start' style='width:20%;' /></td></tr>";
                            echo "<tr><td>合同止日</td><td><input type='text' group='bank' name='contract_end' style='width:20%;' /></td></tr>";
                            echo "<tr><td>合同聯絡人</td><td><input type='text' group='bank' name='name' style='width:60%;' /></td></tr>";
                            echo "<tr><td>職位</td><td><input type='text' group='bank' name='position' style='width:60%;' /></td></tr>";
                            echo "<tr><td>電話</td><td><input type='text' group='bank' name='phone' style='width:60%;' /></td></tr>";
                            echo "<tr><td>傳真</td><td><input type='text' group='bank' name='fax' style='width:60%;' /></td></tr>";
                            echo "<tr><td>手機</td><td><input type='text' group='bank' name='mobile' style='width:60%;' /></td></tr>";
                            echo "<tr><td>E-mail</td><td><input type='text' group='bank' name='email' style='width:60%;' /></td></tr>";
                            echo "<tr><td><div style='background:black;font-size:12pt;color:red;'>客服聯絡人資訊</div></td><td><div style='background:black;'>.</div></td></tr>";
                            echo "<tr><td>客服聯絡人1</td><td><input type='text' group='service' p_no='0' name='name' style='width:60%;' /></td></tr>";
                            echo "<tr><td>職位</td><td><input type='text' group='service' p_no='0' name='position' style='width:60%;' /></td></tr>";
                            echo "<tr><td>電話</td><td><input type='text' group='service' p_no='0' name='phone' style='width:60%;' /></td></tr>";
                            echo "<tr><td>傳真</td><td><input type='text' group='service' p_no='0' name='fax' style='width:60%;' /></td></tr>";
                            echo "<tr><td>手機</td><td><input type='text' group='service' p_no='0' name='mobile' style='width:60%;' /></td></tr>";
                            echo "<tr><td>E-mail</td><td><input type='text' group='service' p_no='0' name='email' style='width:60%;' /></td></tr>";
                            echo "<tr><td>客服聯絡人2</td><td><input type='text' group='service' p_no='1' name='name' style='width:60%;' /></td></tr>";
                            echo "<tr><td>職位</td><td><input type='text' group='service' p_no='1' name='position' style='width:60%;' /></td></tr>";
                            echo "<tr><td>電話</td><td><input type='text' group='service' p_no='1' name='phone' style='width:60%;' /></td></tr>";
                            echo "<tr><td>傳真</td><td><input type='text' group='service' p_no='1' name='fax' style='width:60%;' /></td></tr>";
                            echo "<tr><td>手機</td><td><input type='text' group='service' p_no='1' name='mobile' style='width:60%;' /></td></tr>";
                            echo "<tr><td>E-mail</td><td><input type='text' group='service' p_no='1' name='email' style='width:60%;' /></td></tr>";
                            echo "<tr><td><div style='background:black;font-size:12pt;color:red;'>出貨聯絡人資訊</div></td><td><div style='background:black;'>.</div></td></tr>";
                            echo "<tr><td>出貨聯絡人</td><td><input type='text' group='shipper' name='name' style='width:60%;' /></td></tr>";
                            echo "<tr><td>職位</td><td><input type='text' group='shipper' name='position' style='width:60%;' /></td></tr>";
                            echo "<tr><td>電話</td><td><input type='text' group='shipper' name='phone' style='width:60%;' /></td></tr>";
                            echo "<tr><td>傳真</td><td><input type='text' group='shipper' name='fax' style='width:60%;' /></td></tr>";
                            echo "<tr><td>手機</td><td><input type='text' group='shipper' name='mobile' style='width:60%;' /></td></tr>";
                            echo "<tr><td>E-mail</td><td><input type='text' group='shipper' name='email' style='width:60%;' /></td></tr>";
                            echo "<tr><td><div style='background:black;font-size:12pt;color:red;'>對帳聯絡人資訊</div></td><td><div style='background:black;'>.</div></td></tr>";
                            echo "<tr><td>對帳聯絡人</td><td><input type='text' group='check' name='name' style='width:60%;' /></td></tr>";
                            echo "<tr><td>職位</td><td><input type='text' group='check' name='position' style='width:60%;' /></td></tr>";
                            echo "<tr><td>電話</td><td><input type='text' group='check' name='phone' style='width:60%;' /></td></tr>";
                            echo "<tr><td>傳真</td><td><input type='text' group='check' name='fax' style='width:60%;' /></td></tr>";
                            echo "<tr><td>手機</td><td><input type='text' group='check' name='mobile' style='width:60%;' /></td></tr>";
                            echo "<tr><td>E-mail</td><td><input type='text' group='check' name='email' style='width:60%;' /></td></tr>";
                            echo "<tr><td></td><td><input name='supplier_edit_save' type='button' value='儲存' /></td></tr>";
                            echo "</table>";
                        }
                    
                        //列表
                        $pager->display();echo "<br/>";
                        echo "<table id='supplier_list_table' class='table-h'>";
                        echo "<tr><td name='fi_no'>ID</td><td>供應商名稱</td><td>簡稱</td>";
                        if($display_edit) echo "<td>編輯</td>";
                        if($display_delete) echo"<td>刪除</td>";
                        echo "</tr>";

                        $len = count($all_supplier);
                        for($i = 0; $i < $len; $i++)
                        {
                            echo "<tr fi_no='".$all_supplier[$i]["fi_no"]."' name='".$all_supplier[$i]["name"]."' nickname='".$all_supplier[$i]["nickname"]."'>";
                            echo "<td>".$all_supplier[$i]["fi_no"]."</td>";
                            echo "<td>".$all_supplier[$i]["name"]."</td>";
                            echo "<td>".$all_supplier[$i]["nickname"]."</td>";
                            if($display_edit)echo "<td><input class='supplier_edit' type='button' value='編輯' data-open-dialog='編輯供應商' /></td>";
                            if($display_delete)echo "<td><input class='supplier_del' type='button' value='刪除' /></td>";
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
