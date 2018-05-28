<div class="template_center" style="text-align:center;">		
    <div style="text-align:left;margin-top:20px;"><img src="<?php echo $this->base["tpl"]."icon.png";?>" style='position:relative;top:10px;'> <span style="font-size:23pt;font-weight:bold">註冊程序</span></div>
    <div style='border:1px solid #D4D4D4;margin:10px 0px 10px 0px;padding-bottom:40px;'>
        <div>
            <div id="step1" style='float:left;padding:14px 0px 15px 0px;width:392px;color:white;text-align:center;background:#A5A5A5;'>步驟1.輸入登入名稱</div>
            <div id="step2" style="float:left;width:23px;height:45px;background:url(<?php echo $this->base["tpl"]."arrow2.jpg";?>);"></div>
            <div id="step3" style='float:left;padding:14px 0px 15px 0px;width:393px;color:white;text-align:center;background:#A5A5A5;'>步驟2.填寫帳戶註冊資訊</div>
            <div id="step4" style="float:left;width:23px;height:45px;background:url(<?php echo $this->base["tpl"]."arrow2.jpg";?>);"></div>
            <div id="step5" style='float:left;padding:14px 0px 15px 0px;width:392px;color:white;text-align:center;background:#A5A5A5;'>步驟3.完成註冊</div>
        </div>
        <div style="margin-top: 20px;display:inline-block;text-align:left; line-height:50px; color:#848484;">
            <form id="register">
                <div class="table">
                    <div class="tr"><div class="td" style="width:250px;"><h3 style="color:#000;">步驟1.輸入登入名稱</h3></div><div class="td" style="width:100px;"></div><div class="td"></div></div>
                    <div class="tr"><div class="td"></div><div class="td">會員帳號<span style="color:red">＊</span></div><div class="td"><input id="id" name="id" type="text" style="width:185px;height:30px;">&nbsp;</div></div>
                    <div class="tr"><div class="td"></div><div class="td"></div><div class="td"></div></div>
                    <div class="tr"><div class="td"><h3 style="color:#000;">步驟2.填寫帳戶註冊資訊</h3></div><div class="td"></div><div class="td"></div></div>
                    <div class="tr"><div class="td"></div><div class="td">自定密碼<span style="color:red">＊</span></div><div class="td"><input id="password" name="password" type="password" style="width:185px;height:30px;"> 8-16字符，密碼請使用英文及數字組合</div></div>
                    <div class="tr"><div class="td"></div><div class="td">確認密碼<span style="color:red">＊</span></div><div class="td"><input name="repassword" type="password" style="width:185px;height:30px;">&nbsp;</div></div>
                    <div class="tr"><div class="td"></div><div class="td">手機號碼<span style="color:red">＊</span></div><div class="td"><input id="phone_head" name="phone_head" type="text" placeholder="國際號" style="width:50px;height:30px;" > - <input id="phone_body" name="phone_body" type="text" placeholder="手機號碼" style="width:185px;height:30px;"> 範例：86-91234567899</div></div>
                    <div class="tr"><div class="td"></div><div class="td">電子信箱<span style="color:red">＊</span></div><div class="td"><input id="email" name="email" type="text" style='width:185px;height:30px;'>&nbsp;</div></div>
                    <div class="tr"><div class="td"></div><div class="td">性別</div>
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
                    <div class="tr"><div class="td"></div><div class="td">生日</div>
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
                            <img src="<?php echo $this->base["tpl"]."birth.png";?>" style='position:relative;top:3px;'> 壽星即享 <span style="color:red;">《當月生日跨寶通點數紅利加倍》</span>
                        </div>
                    </div>
                    <div class="tr"><div class="td"></div><div class="td">ＱＱ帳號</div><div class="td"><input id="qq" name="qq" type="text" style="width:185px;height:30px;">&nbsp;</div></div>
                    <div class="tr"><div class="td"></div><div class="td"></div><div class="td"><img src="<?php echo $this->base["tpl"]."point.png";?>" style='position:relative;top:3px;'> 新加入會員即免費贈送跨寶通 <span style="color:red;font-size:12pt;">10,000</span> 點（關於跨寶通會員使用相關方式請至我的跨域瘋查詢）。</div></div>
                    <div class="tr"><div class="td"></div><div class="td"></div><div class="td"><input id="agree" name="agree" type="checkbox" class="icheckbox"><label for="agree" style="line-height:20px;" >&nbsp 我同意 </label><span style="color:red;">〈跨域瘋服務協議〉</span></div></div>
                    <div class="tr"><div class="td"></div><div class="td"></div><div class="td"><input type="submit" value="" style="width:156px;height:51px;margin-top:20px;cursor:pointer;background:#FF4500;color:white;border:0px;background:url(<?php echo $this->base["tpl"]."button.png";?>)"></div></div>
                </div>
            </form>
        </div>
    </div>
</div>
