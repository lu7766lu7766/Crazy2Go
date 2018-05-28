
<div class="template_center">
    <div style="margin-top: 40px;padding-bottom:18px;border-bottom:1px solid #D4D4D4;">
        <div style="display:inline-block;width:208px;margin-right: 28px;"><span id="member_center" style="font-size:23pt;font-weight:bold">我的跨域瘋</span></div>
        <div style="display:inline-block;font-weight: bold;">當前的位置 > <?php echo "<a href='".$this->base["url"]."member/center'>我的跨域瘋</a>";?> > 檢視我的訂單狀況 > 退貨退款</div>
    </div>
    <div style="margin-top: 15px;">
        <div id="member_menu" style="display:inline-block;width:208px;border:1px solid #D4D4D4;margin-right: 28px;font-size:11pt;font-weight: bold;">
                            
        </div>
        <div style="display:inline-block;width:984px;float:right;margin-bottom:30px;border:1px solid #DCD8D9;">
            <div>
                <div id="step_1" style='float:left;padding:14px 0px 15px 0px;width:313px;color:white;text-align:center;background:#A1A1A1;'>步驟1. 買家申請退貨退款</div>
                <div id="step_2" style="float:left;width:23px;height:45px;background:url(<?php echo $this->base["tpl"]."arrow2.jpg";?>);"></div>
                <div id="step_3" style='float:left;padding:14px 0px 15px 0px;width:312px;color:white;text-align:center;background:#A1A1A1;'>步驟2. 賣家處理申請退貨退款</div>
                <div id="step_4" style="float:left;width:23px;height:45px;background:url(<?php echo $this->base["tpl"]."arrow2.jpg";?>);"></div>
                <div id="step_5" style='float:left;padding:14px 0px 15px 0px;width:313px;color:white;text-align:center;background:#A1A1A1;'>步驟3. 退貨退款完成</div>
            </div>
            <div style='clear: both;padding:13px;'>
                <?php
                    $display_status = "";
                    if(isset($this->progress))
                    {
                        switch($this->progress)
                        {
                            case 1:
                                $display_status = "<span style='font-size:12pt'>退貨退款申請中</span>";
                                break;
                            case 2:
                                $display_status = "<span style='font-size:12pt'>申請成功</span>";
                                break;
                            case 3:
                                $display_status = "<span style='font-size:12pt'>申請失敗</span>";
                                break;
                        }
                    }
                    else
                    {
                        $display_status = "<span style='font-size:12pt'>申請服務：退貨退款</span>　　<span style='font-size:8pt;'>申請條件：若為商品問題且與賣家達成一致退貨，請選擇“退貨退款”選項，退貨後請保留物流底單。</span>";
                    }
                ?>
                <div id='progressStatus' style='padding:10px 0px;' progress='<?php echo $this->progress;?>'><?php echo $display_status; ?></div>
                <?php 
                    $_POST["images"] = str_replace('∴', '_', $_POST["images"]);
                    $_POST["images"] = str_replace('\\&quot;', '"', $_POST["images"]);
                    $_POST["images"] = json_decode($_POST["images"],true);
                    $_POST["images"] = $_POST["images"][0];
                    $_POST["specifications"] = json_decode(preg_replace('/∵/','"',$_POST["specifications"]),true);
                    $specifications_html = "";
                    foreach($_POST["specifications"] as $k => $v)
                    {
                        if($k=="default")continue;
                        $specifications_html .= "<span style='color:#979998;'>$k:$v</span>&nbsp;";
                    }
                    $_POST["specifications"]=$specifications_html;

                    echo "<div class='table' style='border: 1px solid #E5E5E5;background:#FAFEFF;'>";
                        echo "<div class='tr'>";
                            echo "<div class='td' style='padding:15px;width:70px;'><a href='".$this->base["url"]."goods?no=".$_POST["goods"]."'><img src='".$this->base["thu"].$_POST["images"]."' style='width:70px;height:70px;border:1px solid #DCD8D9;'></a></div>";
                            echo "<div class='td' style='padding:15px;width:180px;'><span>".$_POST["goods_name"]."<br/>".$_POST["specifications"]."</span></div>";
                            echo "<div class='td' style='padding:15px;'>"
                                    ."<span style='color:#979998;'>商品單價：</span><span style='font-family:Arial;'>¥</span>".$_POST["price"]."＊".$_POST["number"]."<br/><br/>"
                                    ."<span style='color:#979998;'>商品運費：</span><span style='font-family:Arial;'>¥</span>".$_POST["shipping_fee"]."<br/><br/>"
                                    ."<span style='color:#979998;'>訂單編號：</span>".$_POST["sn"]."<br/><br/>"
                                    ."<span style='color:#979998;'>訂購日期：</span>".$_POST["date"]."<br/><br/>"
                                    ."<span style='color:#979998;position:relative;top:-7px;'>賣　　家：</span>".$_POST["store_name"]." <span style='cursor:pointer;' onclick='win=window.open(\"\",\"_blank\");$.post(\"".$this->base["url"]."service/ajax_select_service/\",{type:3,store:".$_POST["store"]."},function(qq){openWin($.trim(qq));});'><img src='".$this->base["god"]."service_icon.png' style='position:relative;top:-3px;'> <span style='position:relative;top:-7px;'>連繫賣家</span></span>"
                                ."</div>";
                        echo "</div>";
                    echo "</div>"; 

                    if(!isset($this->progress))
                    {
                        echo "<form id='returns_form' member='".$_POST["member"]."' order='".$_POST["order"]."' goods='".$_POST["goods"]."'>";
                            echo "<div class='table' style='color:#848484;font-size:12pt;text-align:left; line-height:50px;margin-top:40px;'>";
                                echo "<div class='tr'><div class='td' style='width:190px;'>退貨退款原因<span style='color:red'>＊</span></div>"
                                        . "<div class='td'>"
                                            ."<div style='display:inline-block;position:relative;top:0px;width:340px;height:30px;border:1px solid #D4D4D4;'>"
                                                ."<select id='reason' style='display:inline-block;cursor:pointer;background:transparent;width:340px;height:30px;position:absolute;opacity:0;'><option value='0'>請選擇退款原因</option><option value='1'>缺貨</option><option value='2'>協商一致退款</option><option value='3'>未按約定時間發貨</option><option value='4'>拍錯/多拍/不想要</option><option value='5'>其他</option></select>"
                                                ."<div id='reason_text' style='display:inline-block;height:30px;line-height:30px;width:300px;text-align:left;font-size:8pt;padding-left:10px;'></div>"
                                                ."<div style='float:right;padding-right:5px;'>"
                                                    ."<img src='".$this->base['tpl'].'sharp_arrow.jpg'."' style='display:block;position:relative;top:10px;'>"
                                                ."</div>"
                                            ."</div>"
                                            ."&nbsp;"
                                        . "</div>"
                                   . "</div>";
                                echo "<div class='tr'><div class='td'>退款金額<span style='color:red'>＊</span></div><div class='td'><span style='font-size:8pt;color:black;font-weight:bold;'><span style='font-family:Arial;'>¥</span>".($_POST["price"]*$_POST["number"]+$_POST["shipping_fee"])."</span></div></div>";
                                echo "<div class='tr'><div class='td'>退款說明<span style='color:red'>＊</span></div><div class='td'><textarea id='explanation' name='explanation' style='width:635px;height:151px;border:1px solid #D4D4D4;margin-top:15px;'></textarea></div></div>";
                                echo "<div class='tr'><div class='td'>上傳憑證<span style='color:red'>＊</span></div><div class='td'><div style='display:inline-block;width:150px;height:30px;line-height:30px;background:#DADADA;color:white;font-size:8pt;text-align:center;position:relative;'>選擇上傳照片<input id='return_image' name='upload' type='file' style='position:absolute;left:0px;background:transparent;width:150px;height:30px;opacity:0;cursor:pointer;' /></div> <span style='color:gray;font-size:8pt;'>?上傳格式說明</span><br/><div id='show_img'></div></div></div>";
                                echo "<div class='tr'><div class='td'></div><div class='td'><input type='submit' value='' style='width:156px;height:51px;margin-top:20px;cursor:pointer;background:#FF4500;color:white;border:0px;background:url(".$this->base["tpl"]."button3.png".")'/></div></div>";
                            echo "</div>";
                        echo "</form>";
                    }
                ?>  
            </div>
        </div>
    </div>
</div>
