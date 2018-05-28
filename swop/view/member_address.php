<div class="template_center">
    <div style="margin-top: 40px;padding-bottom:18px;border-bottom:1px solid #D4D4D4;">
        <div style="display:inline-block;width:208px;margin-right: 28px;"><span id="member_center" style="font-size:23pt;font-weight:bold">我的跨域瘋</span></div>
        <div style="display:inline-block;font-weight: bold;">當前的位置 > <?php echo "<a href='".$this->base["url"]."member/center'>我的跨域瘋</a>";?> > 設置常用收貨地址</div>
    </div>
    <div style="margin-top: 15px;">
        <div id="member_menu" style="display:inline-block;width:208px;border:1px solid #D4D4D4;margin-right: 28px;font-size:11pt;font-weight: bold;">
                            
        </div>
        <div style="display:inline-block;width:986px;float:right;margin-bottom:30px;line-height: 50px;">
            <span style="font-size:15pt;">常用收貨地址</span>
            <form id="consign">
                <input type="hidden" name="fi_no" id="fi_no" />
                <div class="table" style="color:#B5B5B5;">
                    <div class="tr">
                        <div class="td">收貨地址<span style="color:red">＊</span></div>
                        <div class="td">
                            <div style="display:inline-block;position:relative;top:0px;width:152px;height:30px;border:1px solid #D4D4D4;">
                                <select id="province" name="province" style="display:inline-block;cursor:pointer;background:transparent;width:152px;height:30px;position:absolute;opacity:0;"><option value="">省份</option></select>
                                <div id="province_text" style="display:inline-block;height:30px;line-height:30px;width:130px;text-align:left;padding-left: 5px;"></div>
                                <div style='float:right;padding-right:5px;'>
                                    <img src="<?php echo $this->base["tpl"]."sharp_arrow.jpg"; ?>" style='position:relative;top:4px;margin-bottom:7px;display:block;-ms-transform:rotate(180deg); -moz-transform:rotate(180deg); -webkit-transform:rotate(180deg); -o-transform:rotate(180deg); transform:rotate(180deg);'>
                                    <img src="<?php echo $this->base["tpl"]."sharp_arrow.jpg"; ?>" style='display:block;'>
                                </div>
                            </div>
                            <div style="display:inline-block;position:relative;top:0px;width:152px;height:30px;border:1px solid #D4D4D4;">
                                <select id="city" name="city" style="display:inline-block;cursor:pointer;background:transparent;width:152px;height:30px;position:absolute;opacity:0;"><option value="">城市</option></select>
                                <div id="city_text" style="display:inline-block;height:30px;line-height:30px;width:130px;text-align:left;padding-left: 5px;"></div>
                                <div style='float:right;padding-right:5px;'>
                                    <img src="<?php echo $this->base["tpl"]."sharp_arrow.jpg"; ?>" style='position:relative;top:4px;margin-bottom:7px;display:block;-ms-transform:rotate(180deg); -moz-transform:rotate(180deg); -webkit-transform:rotate(180deg); -o-transform:rotate(180deg); transform:rotate(180deg);'>
                                    <img src="<?php echo $this->base["tpl"]."sharp_arrow.jpg"; ?>" style='display:block;'>
                                </div>
                            </div>
                            <div style="display:inline-block;position:relative;top:0px;width:152px;height:30px;border:1px solid #D4D4D4;">
                                <select id="district" name="district" style="display:inline-block;cursor:pointer;background:transparent;width:152px;height:30px;position:absolute;opacity:0;"><option value="">縣區</option></select>
                                <div id="district_text" style="display:inline-block;height:30px;line-height:30px;width:130px;text-align:left;padding-left: 5px;"></div>
                                <div style='float:right;padding-right:5px;'>
                                    <img src="<?php echo $this->base["tpl"]."sharp_arrow.jpg"; ?>" style='position:relative;top:4px;margin-bottom:7px;display:block;-ms-transform:rotate(180deg); -moz-transform:rotate(180deg); -webkit-transform:rotate(180deg); -o-transform:rotate(180deg); transform:rotate(180deg);'>
                                    <img src="<?php echo $this->base["tpl"]."sharp_arrow.jpg"; ?>" style='display:block;'>
                                </div>
                            </div>
                            <div style="display:inline-block;position:relative;top:0px;width:152px;height:30px;border:1px solid #D4D4D4;">
                                <select id="street" name="street" style="display:inline-block;cursor:pointer;background:transparent;width:152px;height:30px;position:absolute;opacity:0;"><option value="">街道</option></select>
                                <div id="street_text" style="display:inline-block;height:30px;line-height:30px;width:130px;text-align:left;padding-left: 5px;"></div>
                                <div style='float:right;padding-right:5px;'>
                                    <img src="<?php echo $this->base["tpl"]."sharp_arrow.jpg"; ?>" style='position:relative;top:4px;margin-bottom:7px;display:block;-ms-transform:rotate(180deg); -moz-transform:rotate(180deg); -webkit-transform:rotate(180deg); -o-transform:rotate(180deg); transform:rotate(180deg);'>
                                    <img src="<?php echo $this->base["tpl"]."sharp_arrow.jpg"; ?>" style='display:block;'>
                                </div>
                            </div>
                            &nbsp;
                        </div>
                    </div>
                    <div class="tr"><div class="td"></div><div class="td"><input type="text" id="address" name="address" style="width:350px;height:30px;" placeholder="不需要重複填寫省市區，必須大於5個字符，小於120個字符">&nbsp;</div></div>
                    <div class="tr"><div class="td">郵地區號<span style="color:red">＊</span></div><div class="td"><input type="text" name="postal_code" id="postal_code" style="width:185px;height:30px;" placeholder="郵政編碼僅能由數字、字母組成"/>&nbsp;</div></div>
                    <div class="tr"><div class="td">姓名<span style="color:red">＊</span></div><div class="td"><input type="text" name="consignee" id="consignee" style="width:185px;height:30px;" placeholder="請輸入收件者姓名"/>&nbsp;</div></div>
                    <div class="tr"><div class="td">聯絡電話<span style="color:red">＊</span><span style='font-size: 6pt;'>(手機或電話號碼至少填寫一項)</span></div><div class="td"><input type="text" name="contact_mobile_1" id="contact_mobile_1" style="width:50px;height:30px;" placeholder="國際號" /> – <input type="text" class="group_number" name="contact_mobile" id="contact_mobile" style="width:120px;height:30px;" placeholder="手機號碼" />　範例：86-91234567899</div></div>
                    <div class="tr"><div class="td"></div><div class="td"><input type="text" name="contact_phone_1" id="contact_phone_1" style="width:50px;height:30px;" placeholder="國際號" /> – <input type="text" name="contact_phone_2" id="contact_phone_2" style="width:50px;height:30px;" placeholder="區號" /> – <input type="text" class="group_number" name="contact_phone" id="contact_phone" style="width:120px;height:30px;" placeholder="電話號碼" />　區碼的0請去掉，範例：86-2-23456789</div></div>
                    <div class="tr"><div class="td"></div><div class="td"><input id="preset" name="preset" type="checkbox" class="icheckbox" style="position:relative;"><label for="preset" style="line-height:20px;">&nbsp; 儲存至我的常用地址 </label></div></div>
                    <div class="tr"><div class="td"></div><div class="td"><input id="button" type='submit' value='' style='width:156px;height:51px;margin-top:20px;cursor:pointer;background:#FF4500;color:white;border:0px;background:url(<?php echo $this->base["tpl"]."button3.png";?>)'/></div></div>
                </div>
            </form>
            <?php
            $content = '';
            for($i=0; $i<count($swop->rows); $i++) {
                    $content = $content.'<div class="tr" style="border-top:1px solid #E5E5E5;" id="tr'.$swop->rows[$i]['fi_no'].'">';
                    $content = $content.'<div class="td" style="padding:5px;">'.$swop->rows[$i]['consignee'].'</div>';
                    $swop->rows[$i]['province'] = explode("＿", $swop->rows[$i]['province']);
                    $swop->rows[$i]['city'] = explode("＿", $swop->rows[$i]['city']);
                    $swop->rows[$i]['district'] = explode("＿", $swop->rows[$i]['district']);
                    $swop->rows[$i]['street'] = explode("＿", $swop->rows[$i]['street']);
                    $content = $content.'<div class="td" style="padding:5px;">'.$swop->rows[$i]['postal_code'].' '.$swop->rows[$i]['province'][1].' '.$swop->rows[$i]['city'][1].' '.$swop->rows[$i]['district'][1].' '.($swop->rows[$i]['street'][0]==""?"":$swop->rows[$i]['street'][1]).' '.$swop->rows[$i]['address'].'</div>';
                    $content = $content.'<div class="td" style="padding:5px;">'.$swop->rows[$i]['contact_phone']."<br/>".$swop->rows[$i]['contact_mobile'].'</div>';
                    $swop->rows[$i]['province'] = implode("＿", $swop->rows[$i]['province']);
                    $swop->rows[$i]['city'] = implode("＿", $swop->rows[$i]['city']);
                    $swop->rows[$i]['district'] = implode("＿", $swop->rows[$i]['district']);
                    $swop->rows[$i]['street'] = implode("＿", $swop->rows[$i]['street']);
                    $usual_display = $swop->rows[$i]['preset']?'<img id="address_preset" src='.$this->base["tpl"]."click_icon.jpg".' style="position:relative;top:3px;">目前常用地址':'<input class="address_usual" type="button" value="設成常用地址" style="cursor:pointer;" data-fi_no="'.$swop->rows[$i]['fi_no'].'">';
                    $content = $content.'<div class="td" style="padding:5px;"><input class="address_modify" type="button" value="修改" style="cursor:pointer;" data-fi_no="'.$swop->rows[$i]['fi_no'].'" data-consignee="'.$swop->rows[$i]['consignee'].'" data-postal_code="'.$swop->rows[$i]['postal_code'].'" data-province="'.$swop->rows[$i]['province'].'" data-city="'.$swop->rows[$i]['city'].'" data-district="'.$swop->rows[$i]['district'].'" data-street="'.$swop->rows[$i]['street'].'" data-address="'.$swop->rows[$i]['address'].'" data-contact_phone="'.$swop->rows[$i]['contact_phone'].'" data-contact_mobile="'.$swop->rows[$i]['contact_mobile'].'">　<input class="address_delete" type="button" value="刪除" style="cursor:pointer;" data-fi_no="'.$swop->rows[$i]['fi_no'].'">　<span class="preset_status">'.$usual_display.'</span>&nbsp;</div>';
                    $content = $content.'</div>';
            }
            ?>
            <div id="address_table" class="table" style="line-height: 40px;border:1px solid #E5E5E5;margin-top: 40px;">
                    <div id="trHeader" class="tr" style="background:#FAFEFF;">
                            <div class="td" style="padding:5px;">收件人姓名</div>
                            <div class="td" style="padding:5px;">地址</div>
                            <div class="td" style="padding:5px;">電話/手機</div>
                            <div class="td" style="padding:5px;">操作</div>
                    </div>
                    <?php echo $content; ?>
            </div>
        </div>
    </div>
</div>
