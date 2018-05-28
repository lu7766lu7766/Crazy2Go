
<div class="template_center" style='height:530px;'>
                        <div class="full_div" style="position:absolute;background:#dddddd;height:540px;">
                            <div class="table" style='border-top:#eb454d solid 3px;'>
                                <div class="tr">
                                    <div class="td" style='width:720px;'><?php echo "<img class='ad' src='".$this->base["tpl"].$swop->login_ad."' style='margin-left:70px;'>";?></div>
                                    <div class="td" style="line-height:30px; text-align: center;">
                                            <form id="login" style="display:inline-block;border:1px solid #e62130;background: white;color:#707070;width:340px;height:420px;margin-top:50px;margin-left: 50px;float:left;-webkit-box-shadow: #666 0px 2px 3px;-moz-box-shadow: #666 0px 2px 3px;">                        
                                                    <div style='display:inline-block;width:100%;height:35px;color:white;background:#e62130;font-size:14pt;padding-top:2px;'><?php echo "<img src='".$this->base["tpl"]."signin_icon.png"."' style='position:relative;top:4px;'>";?> 會員登入</div>
                                                    <div class='table' style='margin-top:30px;'>
                                                        <div class='tr'>
                                                            <div class="td" style='text-align:left;padding:10px 0px 0px 20px;'><label for="email" style='font-size: 12pt;'>會員帳號</label></div>
                                                            <div class="td" style='text-align:left;padding:10px;'><input type="text" name="account" id="account" style="width:220px;height:25px;" /></div>
                                                        </div>
                                                        <div class='tr'>
                                                            <div class="td" style='text-align:left;padding:10px 0px 0px 20px;;'><label for="password" style='font-size: 12pt;'>會員密碼</label></div>
                                                            <div class="td" style='text-align:left;padding:10px;'><input type="password" name="password" id="password" style="width:220px;height:25px;" /></div>
                                                        </div>
                                                        <div class='tr'>
                                                            <div class="td" style='text-align:left;padding:10px 0px 0px 20px;'><label for="test" style='font-size: 12pt;'>驗證碼</label></div>
                                                            <div class="td" style='text-align:left;padding:10px;'><input type="text" name="verification" id="verification" style="width:50px;height:25px;vertical-align: top;" /> <img id='verification_img' src="http://www.crazy2go.com/member/code_login/" onclick="javascript:this.src='http://www.crazy2go.com/member/code_login/?n='+Math.random();" width="87" height="31" /><span onclick="$('#verification_img').attr('src','http://www.crazy2go.com/member/code_login/?n='+Math.random());" style="cursor:pointer;"><?php echo "<img src='".$this->base["tpl"]."/reflash.png' style='padding:0px 5px 8px 10px;'>";?><span style='display:inline-block;position:relative;color:#9B9B9B;bottom: 10px;'>換一張</span></span></div>
                                                        </div>
                                                        <div class='tr'>
                                                            <div class="td" style='text-align:left;padding:10px 0px 0px 20px;'></div>
                                                            <div class="td" style='text-align:left;padding:10px;padding-top:50px;'><input type="submit" value="登錄" style="cursor:pointer;width:160px; height:45px;border:0px;font-size:14pt; background:#FC5124;color:white; line-height:normal;-webkit-border-radius: 6px;-moz-border-radius: 6px;border-radius: 6px;-webkit-box-shadow: #666 0px 2px 3px;-moz-box-shadow: #666 0px 2px 3px;" /></div>
                                                        </div>
                                                        <div class='tr'>
                                                            <div class="td" style='text-align:left;padding:10px 0px 0px 20px;'></div>
                                                            <div class="td" style='text-align:right;padding:10px;padding-top: 40px;padding-right: 20px;'><a href="forget/" style='color:#8B8B8B;'>忘記密碼？</a>｜<a href="register/" style='color:#8B8B8B;'>免費註冊</a></div>
                                                        </div>
                                                    </div>
                                            </form>
                                    </div>
                                </div>
                            </div>
                        </div>
		</div>
