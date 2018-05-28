
<div class="template_center">
    <div style="margin-top: 40px;padding-bottom:18px;border-bottom:1px solid #D4D4D4;">
        <div style="display:inline-block;width:208px;margin-right: 28px;"><span id="member_center" style="font-size:23pt;font-weight:bold">我的跨域瘋</span></div>
        <div style="display:inline-block;font-weight: bold;">當前的位置 > <?php echo "<a href='".$this->base["url"]."member/center'>我的跨域瘋</a>";?> > 檢視我的訂單狀況</div>
    </div>
    <div style="margin-top: 15px;">
        <div id="member_menu" style="display:inline-block;width:208px;border:1px solid #D4D4D4;margin-right: 28px;font-size:11pt;font-weight: bold;">
                            
        </div>
        <div style="display:inline-block;width:986px;float:right;margin-bottom:30px;">
            <div>
                <a href='<?php echo $this->base["url"]."member/order_wait2pay"?>'><div style="display:inline-block;padding:10px 15px 10px 15px;margin:1px 0px;font-weight: bold;background:#F5F5F5;color:#838383;position:relative;top:1px;">待付款訂單</div></a>
                <div style="display:inline-block;padding:10px 15px 10px 15px;margin:1px 0px;font-weight: bold;background:#FFFFFF;color:#000000;border-left:2px solid #D92F19;border-top:2px solid #D92F19;border-right:2px solid #D92F19;position:relative;top:3px;">已付款未出貨訂單</div>
                <a href='<?php echo $this->base["url"]."member/order_sendout"?>'><div style="display:inline-block;padding:10px 15px 10px 15px;margin:1px 0px;font-weight: bold;background:#F5F5F5;color:#838383;position:relative;top:1px;">已發貨訂單</div></a>
                <a href='<?php echo $this->base["url"]."member/order_confirm"?>'><div style="display:inline-block;padding:10px 15px 10px 15px;margin:1px 0px;font-weight: bold;background:#F5F5F5;color:#838383;position:relative;top:1px;">已收貨待確認訂單</div></a>
                <a href='<?php echo $this->base["url"]."member/order_history"?>'><div style="display:inline-block;padding:10px 15px 10px 15px;margin:1px 0px;font-weight: bold;background:#F5F5F5;color:#838383;position:relative;top:1px;">歷史訂單</div></a>
            </div>
            <div style="border:1px solid #D4D4D4;border-top:2px solid #D92F19;padding:30px;">
                <div class="table" style="background:#F5F5F5;color:#838383;border:1px solid #D4D4D4;">
                    <div class="tr" style="line-height:30px;font-size: 8pt;">
                        <div class="td" style="padding-left: 10px;width:455px;">商品名稱/規格/尺寸</div>
                        <div class="td" style="width:170px;">單價</div>
                        <div class="td" style="width:30px;">數量</div>
                        <div class="td" style="width:135px;">總計</div>
                        <div class="td" style="width:123px;">狀態操作</div>
                    </div>
                </div>
                
                <?php
                    $len = count($this->order);
                    /*if($len > 0)
                    {
                        echo    '<div style="padding:10px 0px;">'.
                                    '<div id="cancelSelectAll" style="display:inline-block;cursor: pointer;width:55px;height:18px;border:1px solid #A9ABAA;font-size:8pt;color:black;text-align: center;-webkit-border-radius: 3px;-moz-border-radius: 3px;border-radius: 3px;">選擇全部</div>&nbsp;'.
                                    '<div id="cancelOrder" style="display:inline-block;cursor: pointer;width:55px;height:18px;border:1px solid #A9ABAA;font-size:8pt;color:black;text-align: center;-webkit-border-radius: 3px;-moz-border-radius: 3px;border-radius: 3px;">取消訂單</div>&nbsp;'.
                                    '<div id="combinePay" style="display:inline-block;cursor: pointer;width:55px;height:18px;border:1px solid #A9ABAA;font-size:8pt;color:black;text-align: center;-webkit-border-radius: 3px;-moz-border-radius: 3px;border-radius: 3px;">合併付款</div>'.
                                '</div>';
                    }*/
                    echo "<div style='height:13px;'></div>";
                    for($i = 0;$i<$len;$i++)
                    {
                        echo    '<div class="table" style="background:#F9FFFF;color:#000000;border:1px solid #D4D4D4;margin-bottom:10px;">'.
                                    '<div class="tr" style="background:#EEFCFF;line-height:30px;font-size: 8pt;">'.
                                        //'<div class="td" style="padding-left: 10px;width:455px;">'.$this->order[$i]["date"].'&nbsp <input class="icheckbox order_check" name="order_check" type="checkbox"><label for="order_check" style="line-height:12px;" >賣家：'.$this->order[$i]["name"].' </label> <img src="'.$this->base["god"].'service_icon.png" style="position:relative;top:5px;"> 連繫賣家</div>'.
                                        '<div class="td" style="padding-left: 10px;width:455px;">'.$this->order[$i]["date"].'　　●賣家：'.$this->order[$i]["name"]." <span style='cursor:pointer;' onclick='win=window.open(\"\",\"_blank\");$.post(\"".$this->base["url"]."service/ajax_select_service/\",{type:2,store:".$this->order[$i]["store"]."},function(qq){openWin($.trim(qq));});'><img src='".$this->base["god"]."service_icon.png' style='position:relative;top:5px;'> 連繫賣家</span></div>".
                                        '<div class="td" style="width:170px;float:left;"><span style="white-space:nowrap;">訂單編號：'.$this->order[$i]["sn"].'</span></div>'.
                                        '<div class="td" style="width:30px;"></div>'.
                                        '<div class="td" style="width:135px;"></div>'.
                                        '<div class="td" style="width:123px;"></div>'.
                                    '</div>';
                        $len2 = count($this->order[$i]["goods"]);
                        for($j=0; $j<$len2; $j++)
                        {
                            $btnValue = "";
                            switch($this->order[$i]["goods"][$j]["application_progress"])
                            {
                                case 0:$btnValue="退款/退貨";break;
                                case 1:$btnValue="申請中";break;
                                case 2:$btnValue="申請成功";break;
                                case 3:$btnValue="申請失敗";break;
                            }       
                            $returnBtn = "<input class='returns_button' type='button' value='$btnValue' ".
                                         "order='".$this->order[$i]["goods"][$j]["order"]."' ".
                                         "goods='".$this->order[$i]["goods"][$j]["goods"]."' ".
                                         "member='".$_SESSION["info"]["fi_no"]."' ".
                                         "images='".$this->order[$i]["goods"][$j]["images"]."' ".
                                         "goods_name='".$this->order[$i]["goods"][$j]["name"]."' ".
                                         "specifications='".$this->order[$i]["goods"][$j]["specifications"]."' ".
                                         "price='".($this->order[$i]["goods"][$j]["discount"]!=0?$this->order[$i]["goods"][$j]["discount"]:$this->order[$i]["goods"][$j]["promotions"])."' ".
                                         "number='".$this->order[$i]["goods"][$j]["number"]."' ".
                                         "shipping_fee='".$this->order[$i]["shipping_fee"]."' ".
                                         "date='".$this->order[$i]["date"]."' ".
                                         "sn='".$this->order[$i]["sn"]."' ".
                                         "store_name='".$this->order[$i]["name"]."' ".
                                         "/>";
                            $btnValue = "";
                            switch($this->order[$i]["remind"])
                            {
                                case 0:$btnValue="提醒發貨";break;
                                case 1:$btnValue="已提醒發貨";break;
                            } 
                            $remindBtn = '<input class="remind_button" type="button" value="'.$btnValue.'" '.($this->order[$i]["remind"]==1?"disabled":"").' order="'.$this->order[$i]["fi_no"].'">';
                            $this->order[$i]["goods"][$j]["specifications"] = json_decode($this->order[$i]["goods"][$j]["specifications"],true);
                            $specifications_html = "";
                            foreach($this->order[$i]["goods"][$j]["specifications"] as $k => $v)
                            {
                                if($k=="default")continue;
                                $specifications_html .= "<span style='color:#979998;'>$k:$v</span>&nbsp;";
                            }
                            $this->order[$i]["goods"][$j]["images"] = json_decode($this->order[$i]["goods"][$j]["images"]);
                            $this->order[$i]["goods"][$j]["images"] = $this->order[$i]["goods"][$j]["images"][0];
                            echo    '<div class="tr" style="background:#F9FFFF;line-height:30px;font-size: 8pt;">'.
                                        '<div class="td" style="border-bottom:1px solid #D4D4D4;padding:10px;width:435px;height:140px;"><div style="padding-right:10px;display:inline-block;float:left;"><a href="'.$this->base["url"].'goods?no='.$this->order[$i]["goods"][$j]["goods"].'"><img src="'.$this->base["thu"].$this->order[$i]["goods"][$j]["images"].'" style="width:70px;height:70px;border:1px solid #D4D4D4;"></a></div><span style="font-size:bold;">'.$this->order[$i]["goods"][$j]["name"].'<br/>'.$specifications_html.'</span></div>'.
                                        '<div class="td" style="border-bottom:1px solid #D4D4D4;padding:10px 0px;width:170px;height:140px;position:relative;top:1px;float:left;"><span><span style="font-family:Arial;">¥</span>'.($this->order[$i]["goods"][$j]["discount"]!=0?$this->order[$i]["goods"][$j]["discount"]:$this->order[$i]["goods"][$j]["promotions"]).'</span><br/>'.$returnBtn.'</div>'.
                                        '<div class="td" style="border-bottom:1px solid #D4D4D4;padding:10px 0px;width:30px;height:140px;position:relative;top:1px;text-align:center;">'.$this->order[$i]["goods"][$j]["number"].'</div>';
                            if($j == 0)
                            {
                                echo    '<div class="td" style="border-left:1px solid #D4D4D4;padding:10px;width:115px;"><span style="color:#E93439;font-weight:bold;"><span style="font-family:Arial;">¥</span>'.$this->order[$i]["payments"].'</span><br/><span style="color:#838383;">(含運費<span style="font-family:Arial;">¥</span>'.$this->order[$i]["shipping_fee"].')</span></div>'.
                                        '<div class="td" style="border-left:1px solid #D4D4D4;padding:10px;width:103px;"><input class="icheckbox order_success" name="order_success" type="checkbox"><label for="order_success" style="line-height:12px;" >買家已付款</label> <br/>'.$remindBtn.'</div>';
                            }
                            else
                            {
                                echo    '<div class="td" style="border-left:1px solid #D4D4D4;width:135px;"></div>'.
                                        '<div class="td" style="border-left:1px solid #D4D4D4;width:123px;"></div>';
                            }
                            echo    '</div>';
                        }
                        echo    '</div>';
                    }
                    /*
                    Array
                    (
                        [0] => Array
                            (
                                [store] => 2
                                [subtotal] => 25
                                [shipping_fee] => 210
                                [fi_no] => 57
                                [date] => 2015-01-05
                                [sn] => 15010515011959908202
                                [goods] => Array
                                    (
                                        [0] => Array
                                            (
                                                [order] => 57
                                                [goods] => 2
                                                [name] => 台湾老字号顺泰牌凤梨酥 台湾进口特产食品休闲零食 传统糕点
                                                [promotions] => 25
                                                [discount] => 0
                                                [number] => 1
                                                [specifications] => {"顏色":"藍色","尺碼":"175/96(l)"}
                                                [application_progress] => 1
                                                [images] => http://gi3.md.alicdn.com/bao/uploaded/i3/TB1jMihGXXXXXbiXpXXXXXXXXXX_!!0-item_pic.jpg_430x430q90.jpg
                                            )

                                    )

                                [name] => 高雄分店
                            )

                    )
                     */
                ?>
            </div>
        </div>
    </div>
</div>
