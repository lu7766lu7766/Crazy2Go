
<div class="template_center">
    <div style="margin-top: 40px;padding-bottom:18px;border-bottom:1px solid #D4D4D4;">
        <div style="display:inline-block;width:208px;margin-right: 28px;"><span id="member_center" style="font-size:23pt;font-weight:bold">我的跨域瘋</span></div>
        <div style="display:inline-block;font-weight: bold;">當前的位置 > <?php echo "<a href='".$this->base["url"]."member/center'>我的跨域瘋</a>";?> > 變更帳戶資料</div>
    </div>
    <div style="margin-top: 15px;">
        <div id="member_menu" style="display:inline-block;width:208px;border:1px solid #D4D4D4;margin-right: 28px;font-size:11pt;font-weight: bold;">
                            
        </div>
        <div style="display:inline-block;width:986px;float:right;margin-bottom:30px;">
            <span style="font-size:15pt;">會員帳戶資料</span>
            <form id="register">
                <div class="table" style="text-align:left; line-height:50px; color:#848484;">
                    <div class="tr"><div class="td">會員帳號</div><div class="td"><input id="id" name="id" type="text" style="width:185px;height:30px;">&nbsp;</div></div>
                    <div class="tr"><div class="td">自訂密碼</div><div class="td"><input id="password" name="password" type="password" style="width:185px;height:30px;"> 8-16字符，密碼請使用英文及數字組合</div></div>
                    <div class="tr"><div class="td">確認密碼</div><div class="td"><input id="repassword" name="repassword" type="password" style="width:185px;height:30px;">&nbsp;</div></div>
                    <div class="tr"><div class="td">手機號碼</div><div class="td"><input id="phone_head" name="phone_head" type="text" placeholder="國際號" style="width:50px;height:30px;" > - <input id="phone_body" name="phone_body" type="text" placeholder="手機號碼" style="width:185px;height:30px;"> <!--範例：86-91234567899--></div></div>
                    <div class="tr"><div class="td">電子信箱</div><div class="td"><input id="email" name="email" type="text" style='width:185px;height:30px;'>&nbsp;</div></div>
                    <div class="tr"><div class="td">性別</div>
                        <div class="td">
                            <div style="display:inline-block;position:relative;top:0px;width:52px;height:30px;border:1px solid #D4D4D4;-webkit-border-radius: 4px;-moz-border-radius: 4px;border-radius: 4px;">
                                <select id="sex" style="display:inline-block;cursor:pointer;background:transparent;width:52px;height:30px;position:absolute;opacity:0;"><option value="1">男</option><option value="0">女</option></select>
                                <div id="sex_text" style="display:inline-block;height:30px;line-height:30px;width:30px;text-align:center;"></div>
                                <div style='float:right;padding-right:5px;'>
                                    <img src="<?php echo $this->base["tpl"]."sharp_arrow.jpg"; ?>" style='position:relative;top:4px;margin-bottom:7px;display:block;-ms-transform:rotate(180deg); -moz-transform:rotate(180deg); -webkit-transform:rotate(180deg); -o-transform:rotate(180deg); transform:rotate(180deg);'>
                                    <img src="<?php echo $this->base["tpl"]."sharp_arrow.jpg"; ?>" style='display:block;'>
                                </div>
                            </div>
                            &nbsp;
                        </div>
                    </div>
                    <div class="tr"><div class="td">生日</div>
                        <div class="td">
                            <div id="birthday_container" style="display:inline;" >
                                <div style="display:inline-block;position:relative;top:0px;width:68px;height:30px;border:1px solid #D4D4D4;-webkit-border-radius: 4px;-moz-border-radius: 4px;border-radius: 4px;">
                                    <select id="bdy" name="year" style="display:inline-block;cursor:pointer;background:transparent;width:68px;height:30px;position:absolute;opacity:0;"></select>
                                    <div id="bdy_text" style="display:inline-block;height:30px;line-height:30px;width:50px;text-align:center;"></div>
                                    <div style='float:right;padding-right:5px;'>
                                        <img src="<?php echo $this->base["tpl"]."sharp_arrow.jpg"; ?>" style='position:relative;top:4px;margin-bottom:7px;display:block;-ms-transform:rotate(180deg); -moz-transform:rotate(180deg); -webkit-transform:rotate(180deg); -o-transform:rotate(180deg); transform:rotate(180deg);'>
                                        <img src="<?php echo $this->base["tpl"]."sharp_arrow.jpg"; ?>" style='display:block;'>
                                    </div>
                                </div>
                                <div style="display:inline-block;position:relative;top:0px;width:52px;height:30px;border:1px solid #D4D4D4;-webkit-border-radius: 4px;-moz-border-radius: 4px;border-radius: 4px;">
                                    <select id="bdm" name="month" style="display:inline-block;cursor:pointer;background:transparent;width:52px;height:30px;position:absolute;opacity:0;"></select>
                                    <div id="bdm_text" style="display:inline-block;height:30px;line-height:30px;width:30px;text-align:center;"></div>
                                    <div style='float:right;padding-right:5px;'>
                                        <img src="<?php echo $this->base["tpl"]."sharp_arrow.jpg"; ?>" style='position:relative;top:4px;margin-bottom:7px;display:block;-ms-transform:rotate(180deg); -moz-transform:rotate(180deg); -webkit-transform:rotate(180deg); -o-transform:rotate(180deg); transform:rotate(180deg);'>
                                        <img src="<?php echo $this->base["tpl"]."sharp_arrow.jpg"; ?>" style='display:block;'>
                                    </div>
                                </div>
                                <div style="display:inline-block;position:relative;top:0px;width:52px;height:30px;border:1px solid #D4D4D4;-webkit-border-radius: 4px;-moz-border-radius: 4px;border-radius: 4px;">
                                    <select id="bdd" name="day" style="display:inline-block;cursor:pointer;background:transparent;width:52px;height:30px;position:absolute;opacity:0;"></select>
                                    <div id="bdd_text" style="display:inline-block;height:30px;line-height:30px;width:30px;text-align:center;"></div>
                                    <div style='float:right;padding-right:5px;'>
                                        <img src="<?php echo $this->base["tpl"]."sharp_arrow.jpg"; ?>" style='position:relative;top:4px;margin-bottom:7px;display:block;-ms-transform:rotate(180deg); -moz-transform:rotate(180deg); -webkit-transform:rotate(180deg); -o-transform:rotate(180deg); transform:rotate(180deg);'>
                                        <img src="<?php echo $this->base["tpl"]."sharp_arrow.jpg"; ?>" style='display:block;'>
                                    </div>
                                </div>
                            </div>
                            <!--<img src="<?php echo $this->base["tpl"]."birth.png";?>" style='position:relative;top:3px;'> 壽星即享 <span style="color:red;">《當月生日跨寶通點數紅利加倍》</span>-->
                        </div>
                    </div>
                    <div class="tr"><div class="td">ＱＱ帳號</div><div class="td"><input id="qq" name="qq" type="text" style="width:185px;height:30px;">&nbsp;</div></div>
                    <div class="tr"><div class="td"></div><div class="td"><input type="submit" value="" style="width:156px;height:51px;margin-top:20px;cursor:pointer;background:#FF4500;color:white;border:0px;background:url(<?php echo $this->base["tpl"]."button3.png";?>)"></div></div>
                </div>
            </form>
        </div>
    </div>
</div>
