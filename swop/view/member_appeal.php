
<div class="template_center">
    <div style="margin-top: 40px;padding-bottom:18px;border-bottom:1px solid #D4D4D4;">
        <div style="display:inline-block;width:208px;margin-right: 28px;"><span id="member_center" style="font-size:23pt;font-weight:bold">我的跨域瘋</span></div>
        <div style="display:inline-block;font-weight: bold;">當前的位置 > <?php echo "<a href='".$this->base["url"]."member/center'>我的跨域瘋</a>";?> > 我要申訴</div>
    </div>
    <div style="margin-top: 15px;">
        <div id="member_menu" style="display:inline-block;width:208px;border:1px solid #D4D4D4;margin-right: 28px;font-size:11pt;font-weight: bold;">
                            
        </div>
        <div style="display:inline-block;width:986px;float:right;margin-bottom:30px;">
            <span style="font-size:15pt;">我要申訴</span>
            <?php 
                if(!empty($_POST))
                {
                    echo "<div style='background:#FAFEFF;width:614px;height:93px;border:1px solid #E5E5E5;margin-top:20px;color:#838486;line-height:30px;padding-left:15px;'>";
                        echo "<div>買家帳號：".$_POST["member_name"]."</div>";
                        echo "<div>投訴店家：".$_POST["store_name"]."</div>";
                        echo "<div>訂單編號：".$_POST["sn"]."</div>";
                    echo "</div>";
                }

                if(!empty($_POST))
                {
                    $form_params=" goback='1' sn='".$_POST["sn"]."' member='".$_POST["member"]."' store='".$_POST["store"]."' order='".$_POST["order"]."' ";
                }
                else
                {
                    $form_params=" goback='0' sn='-' member='".$_SESSION["info"]["fi_no"]."' store='0' order='0' ";
                }
            ?>
            <form id='appeal_form' <?php echo $form_params;?>>
                <div class="table" style="color:#B5B5B5;margin-top: 30px;">
                    <div class="tr"><div class="td" style="width:100px;">申訴內容<span style="color:red">＊</span></div><div class="td"><textarea id='appeal_content' name='appeal_content' style="width:510px;height:220px;border:1px solid #E5E5E5;"></textarea></div></div>
                    <div class="tr"><div class="td"></div><div class="td"><input id="button" type='submit' value='' style='width:156px;height:51px;margin-top:20px;cursor:pointer;background:#FF4500;color:white;border:0px;background:url(<?php echo $this->base["tpl"]."button3.png";?>)'/></div></div>
                </div>
            </form>
            
            <?php 
                $content = '';
                for($i=0; $i<count($swop->rows); $i++) {
                        $progress = "";
                        switch($swop->rows[$i]['progress'])
                        {
                            case 1:$progress = "賣家受理申訴中";break;
                            case 2:$progress = "申訴處理完成";break;
                            case 3:$progress = "申訴處理失敗";break;
                        }
                        $content = $content.'<div class="tr" style="border-top:1px solid #E5E5E5;" content="'.$swop->rows[$i]['content'].'" reply_content="'.$swop->rows[$i]['reply_content'].'">';
                        $content = $content.'<div class="td" style="padding:5px;">'.substr($swop->rows[$i]['date'], 0,10).'</div>';
                        $content = $content.'<div class="td" style="padding:5px;">'.$swop->rows[$i]['store_name'].'</div>';
                        $content = $content.'<div class="td" style="padding:5px;">'.$swop->rows[$i]['sn'].'</div>';
                        $content = $content.'<div class="td" style="padding:5px;"><img id="address_preset" src='.$this->base["tpl"]."click_icon.jpg".' style="position:relative;top:3px;">'.$progress.'&nbsp;</div>';
                        $content = $content.'<div class="td" style="padding:5px;"><input name="check_appeal" type="button" value="查看詳情" style="cursor:pointer;">&nbsp;</div>';
                        $content = $content.'</div>';
                }
            ?>
            
            <div id="address_table" class="table" style="line-height: 40px;border:1px solid #E5E5E5;margin-top: 40px;">
                    <div id="trHeader" class="tr" style="background:#F2F2F2;">
                            <div class="td" style="padding:5px;">申請日期</div>
                            <div class="td" style="padding:5px;">投訴店家</div>
                            <div class="td" style="padding:5px;">訂單編號</div>
                            <div class="td" style="padding:5px;">處理狀態</div>
                            <div class="td" style="padding:5px;">申請詳情</div>
                    </div>
                    <?php echo $content; ?>
            </div>
        </div>
    </div>
</div>
