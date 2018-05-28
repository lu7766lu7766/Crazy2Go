<div class="template_center" style="text-align:center;">		
    <div style="text-align:left;margin-top:20px;"><img src="<?php echo $this->base["tpl"]."icon.png";?>" style='position:relative;top:10px;'> <span style="font-size:23pt;font-weight:bold">註冊程序</span></div>
    <div style='border:1px solid #D4D4D4;margin:10px 0px 10px 0px;padding-bottom:40px;'>
        <div>
            <div style='float:left;padding:14px 0px 15px 0px;width:392px;color:white;text-align:center;background:#A5A5A5;'>步驟1.輸入登入名稱</div>
            <div style="float:left;width:23px;height:45px;background:url(<?php echo $this->base["tpl"]."arrow2.jpg";?>);"></div>
            <div style='float:left;padding:14px 0px 15px 0px;width:393px;color:white;text-align:center;background:#A5A5A5;'>步驟2.填寫帳戶註冊資訊</div>
            <div style="float:left;width:23px;height:45px;background:url(<?php echo $this->base["tpl"]."arrow3.jpg";?>);"></div>
            <div style='float:left;padding:14px 0px 15px 0px;width:392px;color:white;text-align:center;background:#EE3B3B;'>步驟3.完成註冊</div>
        </div>
        <div style="margin-top: 20px;padding-right: 450px;display:inline-block;text-align:left; line-height:50px; color:#848484;">
            <form id="register2">
                <div class="table">
                    <div class="tr"><div class="td" style="width:450px;"><h3 style="color:#000;">步驟3.完成註冊</h3></div><div class="td"></div></div>
                    <div class="tr"><div class="td">請於E-mail中複製系統發送的驗證碼貼於下方空格。</div><div class="td"></div></div>
                    <div class="tr"><div class="td">驗證碼<input id="valiCode" name="valiCode" type="text" style="margin-left:100px;width:121px;height:30px;" value="<?php echo $_GET["key"];?>"> <span id="valiMsg"></span></div><div class="td"></div></div>
                    <div class="tr"><div class="td"></div><div class="td"><input type="submit" value="" style="width:156px;height:51px;margin-top:20px;cursor:pointer;background:#FF4500;color:white;border:0px;background:url(<?php echo $this->base["tpl"]."button2.png";?>)"></div></div>
                </div>
            </form>
        </div>
    </div>
</div>
