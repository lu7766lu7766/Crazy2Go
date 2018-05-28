
		<div class="template_center">
			<form id="checkout">
				<div style="height:59px; margin:10px 0 45px 0; font-size:21pt; border-bottom:2px solid #D4D4D4; ">
					<div style="float:left;"><img src="<?php echo $this->base['god']; ?>order_icon.png"></div>
					<div style="float:left; margin-top:14px;">結算中心</div>
					<div style="clear:both;"></div>
				</div>
				<div style="height:57px; margin-bottom:73px; text-align:center; font-size:12pt;">
					<div style="width:850px; height:57px; text-align:center; display:inline-block; z-index:2; position:relative;">
						<div style="float:left; width:100px; height:57px;">
							<img src="<?php echo $this->base['god']; ?>step1.png"><br />物流方法確認
						</div>
						<div style="display:inline-block; width:100px; height:57px;">
							<img src="<?php echo $this->base['god']; ?>step2.png"><br />訂單明細確認
						</div>
						<div style="float:right; width:100px; height:57px;">
							<img src="<?php echo $this->base['god']; ?>step3.png"><br />完成訂購
						</div>
						
					</div>
					<div style="display:inline-block; z-index:1; position:relative; top:-44px; width:744px; height:9px; background-image:url(<?php echo $this->base['god']; ?>line.png);"></div>
				</div>
				<div style="height:30px; border-bottom:2px solid #E4E4E4; margin-bottom:7px; font-size:12pt; color:#373737;">步驟1：物流方式確認</div>
				<div class="table" style="line-height:45px;">
					<?php
					for($i=0; $i<count($swop->member_address); $i++) {
						$swop->member_address[$i]['province'] = explode("＿", $swop->member_address[$i]['province']);
						$swop->member_address[$i]['city'] = explode("＿", $swop->member_address[$i]['city']);
						$swop->member_address[$i]['district'] = explode("＿", $swop->member_address[$i]['district']);
						$swop->member_address[$i]['street'] = explode("＿", $swop->member_address[$i]['street']);
						
						$str = $swop->member_address[$i]['consignee']."；".$swop->member_address[$i]['postal_code']."；";
						$str .= $swop->member_address[$i]['province'][0]."；".$swop->member_address[$i]['city'][0]."；".$swop->member_address[$i]['district'][0]."；".$swop->member_address[$i]['street'][0]."；";
						$str .= $swop->member_address[$i]['province'][1]."；".$swop->member_address[$i]['city'][1]."；".$swop->member_address[$i]['district'][1]."；".$swop->member_address[$i]['street'][1]."；";
						$str .= $swop->member_address[$i]['address']."；".$swop->member_address[$i]['contact_phone']."；".$swop->member_address[$i]['contact_mobile'];
						
						if($swop->member_address[$i]['preset']){
							$common['consignee'] = $swop->member_address[$i]['consignee'];
							$common['postal_code'] = $swop->member_address[$i]['postal_code'];
							$common['province'] = $swop->member_address[$i]['province'];
							$common['city'] = $swop->member_address[$i]['city'];
							$common['district'] = $swop->member_address[$i]['district'];
							$common['street'] = $swop->member_address[$i]['street'];
							$common['address'] = $swop->member_address[$i]['address'];
							$common['contact_phone'] = explode("-", $swop->member_address[$i]['contact_phone']);
							$common['contact_mobile'] = explode("-", $swop->member_address[$i]['contact_mobile']);
						}
					}
					?>
					<div class="tr">
						<div class="td" style="width:244px; font-size:10.5pt; color:#838383;">送貨地址<span style="color:#DE4030; position:relative; top:-2px; margin:0 3px 0 3px;">*</span></div>
						<div class="td" style="vertical-align:middle;">
							<div style="position:relative; float:left;">
								<div style="position:absolute; width:95px; line-height:16px; padding:6px; border:1px solid #E5E5E5; font-size:9pt; color:#B0B0B0;">
									<div id="nprovince" style="width:80px; white-space:nowrap; text-overflow:ellipsis; overflow:hidden;"><?php echo ($common['province'][0]!="")?$common['province'][1]:"省份"; ?></div>
									<img src="http://www.crazy2go.com/public/img/template/sharp_arrow.jpg" style="position:absolute; top:4px; right:9px; -ms-transform:rotate(180deg); -moz-transform:rotate(180deg); -webkit-transform:rotate(180deg); -o-transform:rotate(180deg); transform:rotate(180deg);">
									<img src="http://www.crazy2go.com/public/img/template/sharp_arrow.jpg" style="position:absolute; bottom:4px; right:9px;">
								</div>
								<select id="province" name="province" style="cursor:pointer; width:109px; height:30px; display:inline-block; background:transparent; opacity:0.0;">
									<option value="">省份</option>
								</select>
								<input type="hidden" id="aprovince" name="aprovince" value="<?php echo $common['province'][0]; ?>">
							</div>
							<div style="position:relative; float:left; margin-left:21px;">
								<div class="select_type" data-select="city" style="position:absolute; width:95px; line-height:16px; padding:6px; border:1px solid #E5E5E5; font-size:9pt; color:#B0B0B0;">
									<div id="ncity" style="width:80px; white-space:nowrap; text-overflow:ellipsis; overflow:hidden;"><?php echo ($common['city'][0]!="")?$common['city'][1]:"城市"; ?></div>
									<img src="http://www.crazy2go.com/public/img/template/sharp_arrow.jpg" style="position:absolute; top:4px; right:9px; -ms-transform:rotate(180deg); -moz-transform:rotate(180deg); -webkit-transform:rotate(180deg); -o-transform:rotate(180deg); transform:rotate(180deg);">
									<img src="http://www.crazy2go.com/public/img/template/sharp_arrow.jpg" style="position:absolute; bottom:4px; right:9px;">
								</div>
								<select id="city" name="city" style="cursor:pointer; width:109px; height:30px; display:inline-block; background:transparent; opacity:0.0;">
									<option value="">城市</option>
								</select>
								<input type="hidden" id="acity" name="acity" value="<?php echo $common['city'][0]; ?>">
							</div>
							<div style="position:relative; float:left; margin-left:21px;">
								<div class="select_type" data-select="district" style="position:absolute; width:95px; line-height:16px; padding:6px; border:1px solid #E5E5E5; font-size:9pt; color:#B0B0B0;">
									<div id="ndistrict" style="width:80px; white-space:nowrap; text-overflow:ellipsis; overflow:hidden;"><?php echo ($common['district'][0]!="")?$common['district'][1]:"縣區"; ?></div>
									<img src="http://www.crazy2go.com/public/img/template/sharp_arrow.jpg" style="position:absolute; top:4px; right:9px; -ms-transform:rotate(180deg); -moz-transform:rotate(180deg); -webkit-transform:rotate(180deg); -o-transform:rotate(180deg); transform:rotate(180deg);">
									<img src="http://www.crazy2go.com/public/img/template/sharp_arrow.jpg" style="position:absolute; bottom:4px; right:9px;">
								</div>
								<select id="district" name="district" style="cursor:pointer; width:109px; height:30px; display:inline-block; background:transparent; opacity:0.0;">
									<option value="">縣區</option>
								</select>
								<input type="hidden" id="adistrict" name="adistrict" value="<?php echo $common['district'][0]; ?>">
							</div>
							<div style="position:relative; float:left; margin-left:21px;">
								<div class="select_type" data-select="street" style="position:absolute; width:120px; line-height:16px; padding:6px; border:1px solid #E5E5E5; font-size:9pt; color:#B0B0B0;">
									<div id="nstreet" style="width:105px; white-space:nowrap; text-overflow:ellipsis; overflow:hidden;"><?php echo ($common['street'][0]!="")?$common['street'][1]:"街道"; ?></div>
									<img src="http://www.crazy2go.com/public/img/template/sharp_arrow.jpg" style="position:absolute; top:4px; right:9px; -ms-transform:rotate(180deg); -moz-transform:rotate(180deg); -webkit-transform:rotate(180deg); -o-transform:rotate(180deg); transform:rotate(180deg);">
									<img src="http://www.crazy2go.com/public/img/template/sharp_arrow.jpg" style="position:absolute; bottom:4px; right:9px;">
								</div>
								<select id="street" name="street" style="cursor:pointer; width:134px; height:30px; display:inline-block; background:transparent; opacity:0.0;">
									<option value="">街道</option>
								</select>
								<input type="hidden" id="astreet" name="astreet" value="<?php echo $common['street'][0]; ?>">
							</div>
							<div style="position:relative; float:left; margin-left:21px;">
								<input type="text" id="address" name="address" value="<?php echo $common['address']; ?>" style="width:419px; line-height:16px; padding:6px; border:1px solid #E5E5E5; font-size:9pt; color:#B0B0B0; margin:0px;">
							</div>
							<div style="clear:both;"></div>
						</div>
					</div>
					<?php
					if(count($swop->member_address)>0) {
					?>
					<div class="tr">
						<div class="td" style="width:244px; font-size:10.5pt; color:#838383;">常用收貨地址</div>
						<div class="td" style="vertical-align:middle;">
							<div style="position:relative;">
								<select id="common" name="common" style="z-index:2; position:relative; cursor:pointer; width:430px; height:30px; display:inline-block; background:transparent; opacity:0.0;">
									<?php
									for($i=0; $i<count($swop->member_address); $i++) {
									?>
									<option <?php if($swop->member_address[$i]['preset']){echo " selected";} ?> data-common="<?php echo $str; ?>"><?php echo $swop->member_address[$i]['consignee']." - ".$swop->member_address[$i]['province'][1].$swop->member_address[$i]['city'][1].$swop->member_address[$i]['district'][1].$swop->member_address[$i]['street'][1].$swop->member_address[$i]['address']; ?></option>
									<?php
									}
									?>
								</select>
								<div style="z-index:1; position:absolute; top:0px; width:416px; line-height:16px; padding:6px; border:1px solid #E5E5E5; font-size:9pt; color:#B0B0B0;">
									<div id="ncommon" style="width:401px; white-space:nowrap; text-overflow:ellipsis; overflow:hidden;"><?php echo $common['consignee']." - ".$common['province'][1].$common['city'][1].$common['district'][1].$common['street'][1].$common['address']; ?></div>
									<img src="http://www.crazy2go.com/public/img/template/sharp_arrow.jpg" style="position:absolute; top:4px; right:9px; -ms-transform:rotate(180deg); -moz-transform:rotate(180deg); -webkit-transform:rotate(180deg); -o-transform:rotate(180deg); transform:rotate(180deg);">
									<img src="http://www.crazy2go.com/public/img/template/sharp_arrow.jpg" style="position:absolute; bottom:4px; right:9px;">
								</div>
								<input type="hidden" id="acommon" name="acommon">
							</div>
						</div>
					</div>
					<?php
					}
					?>
					<div class="tr">
						<div class="td" style="font-size:10.5pt; color:#838383;">郵遞區號<span style="color:#DE4030; position:relative; top:-2px; margin:0 3px 0 3px;">*</span></div>
						<div class="td" style="vertical-align:middle;"><input type="text" id="postal_code" name="postal_code" value="<?php echo $common['postal_code']; ?>" style="width:172px; line-height:16px; padding:6px; border:1px solid #E5E5E5; font-size:9pt; color:#B0B0B0; margin:0px;" placeholder="郵政編號僅能由數字組成"></div>
					</div>
					<div class="tr">
						<div class="td" style="font-size:10.5pt; color:#838383;">姓名<span style="color:#DE4030; position:relative; top:-2px; margin:0 3px 0 3px;">*</span></div>
						<div class="td" style="vertical-align:middle;"><input type="text" id="consignee" name="consignee" value="<?php echo $common['consignee']; ?>" style="width:172px; line-height:16px; padding:6px; border:1px solid #E5E5E5; font-size:9pt; color:#B0B0B0; margin:0px;" placeholder="請輸入收件人姓名"></div>
					</div>
					<div class="tr">
						<div class="td" style="font-size:10.5pt; color:#838383;">
							<div style="float:left;">聯繫電話</div>
							<div style="float:left; color:#DE4030; position:relative; top:-2px; margin:0 3px 0 3px;">*</div>
							<div style="float:left; font-size:8pt;">(手機與電話號碼請至少填寫一項)</div>
							<div style="clear:both;"></div>
						</div>
						<div class="td" style="font-size:9pt; color:#B0B0B0; vertical-align:middle;">
							<div style="float:left;"><input type="text" id="contact_mobile_international" name="contact_mobile_international" value="<?php echo $common['contact_mobile'][0]; ?>" style="width:40px; line-height:16px; padding:6px; border:1px solid #E5E5E5; font-size:9pt; color:#B0B0B0; margin:0px;" placeholder="國際號"></div>
							<div style="float:left;"><div style="width:9px; height:14px; border-bottom:1px solid #E5E5E5; margin:0 4px 0 4px;"></div></div>
							<div style="float:left;"><input type="text" class="group_number" id="contact_mobile_number" name="contact_mobile_number" value="<?php echo $common['contact_mobile'][1]; ?>" style="width:166px; line-height:16px; padding:6px; border:1px solid #E5E5E5; font-size:9pt; color:#B0B0B0; margin:0px;" placeholder="手機號碼"></div>
							<div style="float:left; line-height:30px; margin-left:18px;">範例：86-91234567899</div>
							<div style="clear:both;"></div>
						</div>
					</div>
					<div class="tr">
						<div class="td">　</div>
						<div class="td" style="font-size:9pt; color:#B0B0B0; vertical-align:middle;">
							<div style="float:left;"><input type="text" id="contact_phone_international" name="contact_phone_international" value="<?php echo $common['contact_phone'][0]; ?>" style="width:40px; line-height:16px; padding:6px; border:1px solid #E5E5E5; font-size:9pt; color:#B0B0B0; margin:0px;" placeholder="國際號"></div>
							<div style="float:left;"><div style="width:9px; height:14px; border-bottom:1px solid #E5E5E5; margin:0 4px 0 4px;"></div></div>
							<div style="float:left;"><input type="text" id="contact_phone_area" name="contact_phone_area" value="<?php echo $common['contact_phone'][1]; ?>" style="width:40px; line-height:16px; padding:6px; border:1px solid #E5E5E5; font-size:9pt; color:#B0B0B0; margin:0px;" placeholder="區號"></div>
							<div style="float:left;"><div style="width:9px; height:14px; border-bottom:1px solid #E5E5E5; margin:0 4px 0 4px;"></div></div>
							<div style="float:left;"><input type="text" class="group_number" id="contact_phone_number" name="contact_phone_number" value="<?php echo $common['contact_phone'][2]; ?>" style="width:166px; line-height:16px; padding:6px; border:1px solid #E5E5E5; font-size:9pt; color:#B0B0B0; margin:0px;" placeholder="電話號碼"></div>
							<div style="float:left; line-height:30px; margin-left:18px;">區碼的0請去掉，範例：86-0-912345678</div>
							<div style="clear:both;"></div>
						</div>
					</div>
					<div class="tr">
						<div class="td" style="font-size:10.5pt; color:#838383;">儲存地址</div>
						<div class="td" style="font-size:9pt; color:#B0B0B0; vertical-align:middle;">
							<div id="save_address" style="cursor:pointer; width:130px;"><img id="isave_address" src="<?php echo $this->base['god']; ?>uncheck_button.png" style="position:relative; top:2px; margin-right:7px;">儲存至我的常用地址</div>
							<input type="hidden" id="asave_address" name="asave_address" value="0">
							<input type="hidden" id="ashipping_select" name="ashipping_select" value="2">
							<input type="hidden" id="aidentity" name="aidentity" value="<?php echo $swop->direct; ?>">
						</div>
					</div>
					<?php
					if($swop->direct == 1) {
					?>
					<div class="tr">
						<div class="td" style="font-size:10.5pt; color:#838383;">身分證號<span style="color:#DE4030; position:relative; top:-2px; margin:0 3px 0 3px;">*</span></div>
						<div class="td" style="font-size:9pt; color:#B0B0B0; vertical-align:middle;">
							<div style="margin-bottom:6px;"><img src="<?php echo $this->base['god']; ?>caveat.png" style="position:relative; top:2px; margin:0 7px 0 3px;">請上傳彩色身份證（支持jpg、jpeg、bmp、gif、png，檔案請勿超過2mb）在下單中國海關規定，直郵包裹需上傳收件人身份證，否則海關不予清關。</div>
							<div style="float:left; width:348px; color:#fff; text-align:center;">
								<div id="identity_front" style="position:relative; width:167px; line-height:28px; border:1px solid #E5E5E5; background-color:#B7B7B7; margin-bottom:14px;">
									上傳身份證（個人訊息面）
									<input type="file" id="aidentity_front" name="aidentity_front" accept="image/*" style="position:absolute; top:0px; left:0px; width:169px; height:30px; opacity:0.0;">
								</div>
								<div id="identity_back" style="position:relative; width:167px; line-height:28px; border:1px solid #E5E5E5; background-color:#B7B7B7;">
									上傳身份證（國徽圖徽面）
									<input type="file" id="aidentity_back" name="aidentity_back" accept="image/*" style="position:absolute; top:0px; left:0px; width:169px; height:30px; opacity:0.0;">
								</div>
							</div>
							<div style="float:left; width:428px; height:163px; border:1px solid #E5E5E5; padding:0 20px; margin-bottom:15px;">
								<div style="float:left; width:232px;">
									<div style="line-height: 29px;">個人訊息面</div>
									<img id="nidentity_front" src="<?php echo $this->base['god']; ?>identity_card.jpg" style="width:192px; height:123px;">
								</div>
								<div style="float:left;">
									<div style="line-height: 29px;">國徽圖徽面</div>
									<img id="nidentity_back" src="<?php echo $this->base['god']; ?>identity_card2.jpg" style="width:192px; height:123px;">
								</div>
								<div style="clear:both;"></div>
							</div>
							<div style="clear:both;"></div>
						</div>
					</div>
					<?php
					}
					if($swop->undirect > 0) {
					?>
					<div class="tr">
						<div class="td" style="font-size:10.5pt; color:#838383;">送貨方式<span style="color:#DE4030; position:relative; top:-2px; margin:0 3px 0 3px;">*</span></div>
						<div class="td" style="font-size:9pt; color:#B0B0B0; vertical-align:middle;">
							<div class="shipping_select" data-shipping="2" style="float:left; cursor:pointer; width:130px;"><img class="ishipping_select" data-shipping="2" src="<?php echo $this->base['god']; ?>check_button.png" style="position:relative; top:2px; margin-right:7px;"><span id="shipping_select2">順豐快遞</span></div>
							<div class="shipping_select" data-shipping="3" style="float:left; cursor:pointer; width:130px;"><img class="ishipping_select" data-shipping="3" src="<?php echo $this->base['god']; ?>uncheck_button.png" style="position:relative; top:2px; margin-right:7px;"><span id="shipping_select3">申通快遞</span></div>
							<div class="shipping_select" data-shipping="4" style="float:left; cursor:pointer; width:130px;"><img class="ishipping_select" data-shipping="4" src="<?php echo $this->base['god']; ?>uncheck_button.png" style="position:relative; top:2px; margin-right:7px;"><span id="shipping_select4">韻達快遞</span></div>
							<div class="shipping_select" data-shipping="5" style="float:left; cursor:pointer; width:130px;"><img class="ishipping_select" data-shipping="5" src="<?php echo $this->base['god']; ?>uncheck_button.png" style="position:relative; top:2px; margin-right:7px;"><span id="shipping_select5">郵政EMS(特快)</span></div>
							<div style="clear:both;"></div>
						</div>
					</div>
					<?php
					}
					?>
				</div>
				<div style="padding:41px 0 12px 0; border-top:1px solid #E4E4E4; font-size:12pt; color:#373737;">步驟2：訂單明細確定</div>
				<div class="table">
					<div class="tr" style="height:26px; border-bottom:0px; font-size:9pt;">
						<div class="td" style="width:250px;">商品圖片</div>
						<div class="td" style="width:250px;">商店名稱</div>
						<div class="td" style="width:235px;">商品名稱</div>
						<div class="td" style="width:125px;">規格/尺寸</div>
						<div class="td" style="width:76px;">數量</div>
						<div class="td" style="width:88px;">重量</div>
						<div class="td" style="width:97px;">價格</div>
						<div class="td" style="width:103px;">小計</div>
					</div>
					<?php
					$store_no = '';
					$store_no_tmp = 0;
					$store_lock = 0;
					$total = 0;
					$total_fee = 0;
					$store_weight = array();
					$store_subtotal = 0;
					$store_change = 0;
					$table_border_top = 1;
					$direct_check = 0;
					
					$all_cart_check = 0;
					for($i=0; $i<count($swop->goods); $i++) {
						if($store_lock != $swop->goods[$i]['store']) {
							for($j=0; $j<count($swop->store); $j++) {
								if($swop->store[$j]['fi_no'] == $swop->goods[$i]['store']) {
									$store_name = $swop->store[$j]['name'];
									$store_name_one = 0;
									$store_no .= $swop->store[$j]['fi_no']."｜";
									$store_lock = $swop->goods[$i]['store'];
									$store_change = 1;
								}
							}
					?>
					<?php
							if($i!=0 && $store_change==1) { //小計 重量 金額
								$store_change = 0;
								
								/*
								for($j=0; $j<count($swop->shipping_fee); $j++) {
									if($swop->shipping_fee[$j]['type'] == 1) {
										foreach($store_weight as $key => $val) {
											if($val > $swop->shipping_fee[$j]['range_a'] && $val <= $swop->shipping_fee[$j]['range_b']) {
												if($swop->shipping_fee[$j]['mod'] == 0) {
													$shipping_fee[$key] = $swop->shipping_fee[$j]['amount'];
												}
												else if($swop->shipping_fee[$j]['mod'] == 1) {
													$shipping_fee[$key] = $val * $swop->shipping_fee[$j]['amount'];
												}
											}
										}
										if(count($shipping_fee)>0) {
											$shipping_fee_all = array_sum($shipping_fee) / $swop->exchange_rate[0]['rate'];;
										}
									}
								}
								*/
					?>
					
					<div class="tr" style="background-color:#F5F5F5; height:43px; font-size: 9pt;">
						<div class="td" style="padding-left:28px; position:relative; vertical-align:middle; border-top:1px solid #CBCBCD; border-bottom:1px solid #CBCBCD; border-left:1px solid #CBCBCD;">
							留言給賣家<input type="text" class="message" name="message" style="position:absolute; top:5px; left:97px; width:371px; line-height:28px; border:1px solid #E5E5E5; font-size:9pt; color:#B0B0B0; margin:0px;">
						</div>
						<div class="td" style="vertical-align:middle; border-top:1px solid #CBCBCD; border-bottom:1px solid #CBCBCD;"></div>
						<div class="td" style="vertical-align:middle; border-top:1px solid #CBCBCD; border-bottom:1px solid #CBCBCD;">物流方式：<span class="transport">順豐快遞</span><?php if($direct_check == 1) {echo "+台灣直郵"; $direct_check = 0;}; ?></div>
						<div class="td" style="vertical-align:middle; border-top:1px solid #CBCBCD; border-bottom:1px solid #CBCBCD;"></div>
						<div class="td" style="vertical-align:middle; border-top:1px solid #CBCBCD; border-bottom:1px solid #CBCBCD;"></div>
						<div class="td" style="vertical-align:middle; border-top:1px solid #CBCBCD; border-bottom:1px solid #CBCBCD;"><?php echo sprintf("%01.2f", array_sum($store_weight)); $store_weight = array(); ?>KG</div>
						<div class="td" style="vertical-align:middle; border-top:1px solid #CBCBCD; border-bottom:1px solid #CBCBCD;">
							<span style="font-weight:bold; color:#ff9854;">
								<span style="font-family:Arial;">¥</span><span class="store_weight" id="store_weight<?php echo $store_no_tmp; ?>">0</span><?php //echo sprintf("%01.2f", $shipping_fee_all); $total_fee = $total_fee + $shipping_fee_all; ?>
							</span>
							<span style="display:inline-block;">(運費)</span></div>
						<div class="td" style="vertical-align:middle; border-top:1px solid #CBCBCD; border-bottom:1px solid #CBCBCD; border-right:1px solid #CBCBCD;">
							<span style="font-weight:bold; color:#ff9854;">
								<span style="font-family:Arial;">¥</span><span class="store_subtotal" id="store_subtotal<?php echo $store_no_tmp; $store_no_tmp = 0; ?>" data-subtotal="<?php echo sprintf("%01.2f", $shipping_fee_all + $store_subtotal); ?>" ><?php echo sprintf("%01.2f", $shipping_fee_all + $store_subtotal); $store_subtotal = 0; ?></span>
							</span>
							<span style="display:inline-block;">(合計)</span>
						</div>
					</div>
					<div class="tr" style="height:30px;">
						
					</div>
					<?php
								$table_border_top = 1;
							}
					?>
					<?php
						}
					?>
					<?php
						$cart_type = explode("∵", $this->cart_type[$swop->goods[$i]['fi_no']]);
						$cart_item = explode("∵", $this->cart_item[$swop->goods[$i]['fi_no']]);
						$cart_num = explode("∵", $this->cart_num[$swop->goods[$i]['fi_no']]);
						$cart_stock = explode("∵", $this->cart_stock[$swop->goods[$i]['fi_no']]);
						$cart_array = explode("∵", $this->cart_array[$swop->goods[$i]['fi_no']]);
						$cart_check = explode("∵", $this->cart_check[$swop->goods[$i]['fi_no']]);
						//print_r($cart_check);
						for($j=0; $j<count($cart_item)-1; $j++) {
							$rtype = explode("｜", $cart_type[$j]);
							$ritem = explode("｜", $cart_item[$j]);
							
							$type_verification = 0;
							$status_verification = 0;
							if($swop->goods[$i]['combination']=="") {
								for($k=0; $k<count($rtype); $k++) {
									$select_option = explode("；", $cart_array[$j]);
									$select_option_number = explode("｜", $select_option[1]);
									$select_option_style = explode("｜", $select_option[3]);
									//驗證規格位置是否正確存在
									if($this->style_content[$swop->goods[$i]["fi_no"]][$rtype[$k]][$select_option_number[$k]] != $select_option_style[$k]) {
										$type_verification++;
									}
								}
							}
							else {
								$combination_arr = $this->json_url_arr($swop->goods[$i]['combination']);
								$combination_inv = array();
								for($k=0; $k<count($combination_arr[0]["fi_no"]); $k++) {
									$combination_exp = explode("；", $combination_arr[0]["specifications"][$k]);
									$combination_num = 0;
									$combination_fit = array();
									foreach($combination_arr[($k+1)] as $ke => $va) {
										$combination_fit[] = array("ke"=>$ke,"va"=>$va);
									}
									$specifications_arr = $this->json_url_arr($swop->goods_combination[$combination_arr[0]["fi_no"][$k]]["specifications"]);
									foreach($specifications_arr as $ke => $va) {
										//echo "資料庫規格：".$ke." / ".$va[$combination_exp[$combination_num]];
										//echo "組合商品規格：".$combination_fit[$combination_num]["ke"]." / ".$combination_fit[$combination_num]["va"][0];
										if($ke != $combination_fit[$combination_num]["ke"] || $va[$combination_exp[$combination_num]] != $combination_fit[$combination_num]["va"][0]) {
											$type_verification++;
										}
										$combination_num++;
									}
								}
							}
							
							if($swop->goods[$i]['status_audit'] == 0 || $swop->goods[$i]['status_shelves'] == 0 || $swop->goods[$i]['delete'] == 1) {
								$status_verification++;
							}
							
							if($type_verification>0 || $status_verification>0) {
								$error_verification = 1;
							}
							else {
								$error_verification = 0;
							}
					?>
					<div class="tr" style="background-color:#F1FCFE; font-size:9pt;">
						<div class="td" style="<?php echo ($table_border_top==1)?"border-top:1px solid #CBCBCD; ":""; ?>border-left:1px solid #CBCBCD; padding:16px 20px 16px 26px;">
							<img src="<?php echo $this->base['thu'].$swop->goods[$i]['images'][0]; ?>" style="width:124px; height:124px; border:1px solid #A6A7A9;"></div>
						<div class="td" style="<?php echo ($table_border_top==1)?"border-top:1px solid #CBCBCD; ":""; ?>padding:16px 20px 0 0; font-size:10.5pt;"><?php if($store_name_one==0){echo "<div>".$store_name."</div>"; $store_name_one=1;} ?><?php if($error_verification==1){echo '<div style="color:#DE4030;">商品失效</div>'; $all_cart_check++;} ?></div>
						<div class="td" style="<?php echo ($table_border_top==1)?"border-top:1px solid #CBCBCD; ":""; ?>padding:16px 20px 0 0;"><a href="<?php echo $this->base['url']; ?>goods?no=<?php echo $swop->goods[$i]['fi_no']; ?>"><?php echo $swop->goods[$i]['name']; ?></a></div>
						<?php
							$explode_array = explode("；", $cart_array[$j]);
							$explode_style = explode("｜", $explode_array[2]);
							$explode_item = explode("｜", $explode_array[3]);
							$style_item = "";
							for($e=0; $e<count($explode_style); $e++) {
								$style_item .= $explode_style[$e]."：".$explode_item[$e]."<br />";
							}
						?>
						<div class="td" style="<?php echo ($table_border_top==1)?"border-top:1px solid #CBCBCD; ":""; ?>padding:16px 20px 0 0;"><?php if($explode_style[0]=="default" && $explode_item[0]=="default") {echo "依廠商描述";} else {echo $style_item;} ?></div>
						<div class="td" style="<?php echo ($table_border_top==1)?"border-top:1px solid #CBCBCD; ":""; ?>padding:16px 20px 0 0;"><div><?php echo $cart_num[$j]; ?></div><?php if($cart_check[$j]=='out'){echo '<div style="color:#DE4030;">庫存不足</div>'; $all_cart_check++;} ?></div>
						<?php
							$lwh_a = sprintf("%01.2f", $swop->goods[$i]['volumetric_weight']['長']*$swop->goods[$i]['volumetric_weight']['寬']*$swop->goods[$i]['volumetric_weight']['高']/6000);
							$lwh_b = sprintf("%01.2f", $swop->goods[$i]['volumetric_weight']['重量']);
							if($lwh_a > $lwh_b) { $lwh = $lwh_a; } else { $lwh = $lwh_b;}
							
							$store_weight[$swop->goods[$i]['direct']] = $store_weight[$swop->goods[$i]['direct']] + ($lwh * $cart_num[$j]);
							if($swop->goods[$i]['direct'] == 1) {
								$direct_check = 1;
							}
						?>
						<div class="td goods_weight" data-fi_no="<?php echo $swop->goods[$i]['fi_no']; ?>" data-quantity="<?php echo $cart_num[$j]; ?>" style="<?php echo ($table_border_top==1)?"border-top:1px solid #CBCBCD; ":""; ?>padding:16px 20px 0 0;"><?php echo sprintf("%01.2f", $lwh * $cart_num[$j]); ?>KG</div>
						<div class="td" style="<?php echo ($table_border_top==1)?"border-top:1px solid #CBCBCD; ":""; ?>padding:16px 20px 0 0; font-weight:bold;"><span style="font-family:Arial;">¥</span><?php echo $swop->goods[$i]['promotions']; ?></div>
						<?php
							$num_mon = $cart_num[$j] * $swop->goods[$i]['promotions'];
						?>
						<div class="td" style="<?php echo ($table_border_top==1)?"border-top:1px solid #CBCBCD; ":""; $table_border_top = 0; ?>padding:16px 20px 0 0; border-right:1px solid #CBCBCD; font-weight:bold; color:#ff9854;"><span style="font-family:Arial;">¥</span><?php echo sprintf("%01.2f", $num_mon); $store_subtotal = $store_subtotal + $num_mon; ?></div>
						<?php
							$total = $total + $num_mon;
							$store_no_tmp = $swop->goods[$i]['store'];
						?>
					</div>
					<?php
						}
					?>
					<?php
						if($i==(count($swop->goods)-1) ) { //小計 重量 金額 最後一次
							$store_change = 0;
							
							/*
							for($j=0; $j<count($swop->shipping_fee); $j++) {
								if($swop->shipping_fee[$j]['type'] == 1) {
									foreach($store_weight as $key => $val) {
										if($val > $swop->shipping_fee[$j]['range_a'] && $val <= $swop->shipping_fee[$j]['range_b']) {
											if($swop->shipping_fee[$j]['mod'] == 0) {
												$shipping_fee[$key] = $swop->shipping_fee[$j]['amount'];
											}
											else if($swop->shipping_fee[$j]['mod'] == 1) {
												$shipping_fee[$key] = $val * $swop->shipping_fee[$j]['amount'];
											}
										}
									}
									if(count($shipping_fee)>0) {
										$shipping_fee_all = array_sum($shipping_fee) / $swop->exchange_rate[0]['rate'];
									}
								}
							}
							*/
					?>
					<div class="tr" style="background-color:#F5F5F5; height:43px; font-size: 9pt;">
						<div class="td" style="padding-left:28px; position:relative; vertical-align:middle; border-top:1px solid #CBCBCD; border-bottom:1px solid #CBCBCD; border-left:1px solid #CBCBCD;">
							留言給賣家<input type="text" class="message" name="message" style="position:absolute; top:5px; left:97px; width:371px; line-height:28px; border:1px solid #E5E5E5; font-size:9pt; color:#B0B0B0; margin:0px;">
						</div>
						<div class="td" style="vertical-align:middle; border-top:1px solid #CBCBCD; border-bottom:1px solid #CBCBCD;"></div>
						<div class="td" style="vertical-align:middle; border-top:1px solid #CBCBCD; border-bottom:1px solid #CBCBCD;">物流方式：<span class="transport"><?php echo (($swop->undirect > 0)?"順豐快遞":""); ?></span><?php if($direct_check == 1) { echo (($swop->undirect > 0)?"+":"")."台灣直郵"; $direct_check = 0;}; ?></div>
						<div class="td" style="vertical-align:middle; border-top:1px solid #CBCBCD; border-bottom:1px solid #CBCBCD;"></div>
						<div class="td" style="vertical-align:middle; border-top:1px solid #CBCBCD; border-bottom:1px solid #CBCBCD;"></div>
						<div class="td" style="vertical-align:middle; border-top:1px solid #CBCBCD; border-bottom:1px solid #CBCBCD;"><?php echo sprintf("%01.2f", array_sum($store_weight)); $store_weight = array(); ?>KG</div>
						<div class="td" style="vertical-align:middle; border-top:1px solid #CBCBCD; border-bottom:1px solid #CBCBCD;">
							<span style="font-weight:bold; color:#ff9854;">
								<span style="font-family:Arial;">¥</span><span class="store_weight" id="store_weight<?php echo $store_no_tmp; ?>">0</span><?php //echo sprintf("%01.2f", $shipping_fee_all); $total_fee = $total_fee + $shipping_fee_all; ?>
							</span>
							<span style="display:inline-block;">(運費)</span></div>
						<div class="td" style="vertical-align:middle; border-top:1px solid #CBCBCD; border-bottom:1px solid #CBCBCD; border-right:1px solid #CBCBCD;">
							<span style="font-weight:bold; color:#ff9854;">
								<span style="font-family:Arial;">¥</span><span class="store_subtotal" id="store_subtotal<?php echo $store_no_tmp; $store_no_tmp = 0; ?>" data-subtotal="<?php echo sprintf("%01.2f", $shipping_fee_all + $store_subtotal); ?>" ><?php echo sprintf("%01.2f", $shipping_fee_all + $store_subtotal); $store_subtotal = 0; ?></span>
							</span>
							<span style="display:inline-block;">(合計)</span>
						</div>
					</div>
					<?php
						}
					?>
					<?php
					}
					?>
				</div>
				<div style="margin-top:14px;">
					<div style="float:right; height:158px;">
						<div class="table" style="font-size:9pt;">
							<!--div class="tr" style="line-height:38px; border:1px solid #E5E5E5;">
								<div class="td" style="min-width:185px; padding-left:22px; color:#848484;">訂單合計(含運費)</div>
								<div class="td" style="font-weight:bold; color:#ff9854;"><span style="font-family:Arial;">¥</span><span id="total"><?php /*echo sprintf("%01.2f", ($total+$total_fee));*/ ?></span></div>
							</div>
							<div class="tr">
								<div class="td">今日人民幣匯率參考<span style="color:#ff9854;"><?php /* echo number_format($swop->exchange_rate[0]['rate'], 1); */ ?></span></div>
								<div class="td" style="font-weight:bold; color:#ff9854;">¥<?php /* echo ($total+$total_fee)*number_format($swop->exchange_rate[0]['rate'], 1); */ ?></div>
							</div>
							<div class="tr">
								<div class="td" style="height:13px;"></div>
								<div class="td"></div>
							</div>
							<div class="tr" style="line-height:38px; border:1px solid #E5E5E5;">
								<div class="td" style="padding:0 19px 0 22px;"><img src='<?php /*echo $this->base['god']; */?>order_icon2.png' style="position:relative; top:8px;">您的跨寶通消費金共<span style="font-weight:bold; color:#ff9854;"><span style="font-family:Arial;">¥</span><span id="currency"><?php /*$currency['increase'] = 0; $currency['reduce'] = 0; for($i=0; $i<count($swop->member_currency); $i++) { $currency['increase'] = $currency['increase'] + $swop->member_currency[$i]['increase']; $currency['reduce'] = $currency['reduce'] + $swop->member_currency[$i]['reduce']; } echo ($currency['increase'] - $currency['reduce']);*/ ?></span></span>元</div>
								<div class="td" style="vertical-align:middle;"><input id="offsetting" type="text" style="width:86px; height:28px; border:1px solid #E5E5E5; text-align:center;" placeholder="欲抵扣消費金"></div>
							</div>
							<div class="tr">
								<div class="td" style="height:20px;"></div>
								<div class="td"></div>
							</div-->
							<div class="tr" style="line-height:38px; border:2px solid #FC5626;">
								<div class="td" style="padding-left:22px;">訂單合計(含運費)</div>
								<div class="td" style="padding-right:20px; font-size:16pt; font-weight:bold; color:#ff9854;"><span style="font-family:Arial;">¥</span><span id="actual_payment"><?php echo sprintf("%01.2f", ($total+$total_fee)); /* *number_format($swop->exchange_rate[0]['rate'], 1); */ ?></span></div>
							</div>
						</div>
					</div>
					<div style="clear:both;"></div>
				</div>
				
				<div style="margin:28px 0 33px 0; text-align:right;"><input type="hidden" id="allstore" value="<?php echo $store_no;?>"><a href="<?php echo $this->base['url']."cart"; ?>"><img src="<?php echo $this->base['god']."checkout_button.png"; ?>"></a><input type="submit" value="" style="width:156px; height:51px; margin-left: 24px; border:0px; padding:0px; background-color:transparent; background-image:url(<?php echo $this->base['god']; ?>checkout_button2.png); "<?php if($all_cart_check>0){echo ' disabled="disabled"';} ?> /></div>
			</form>
		</div>
