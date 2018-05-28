
		<div class="template_center">
			<form id="order">
				
				<div style="font-weight:bold; padding-bottom:34px;">
					<?php if(count($this->cart_change) > 0) { ?>
					<div style="font-size:11pt; color:#373737; padding:11px 0 11px 0; border-bottom:1px #7B7B7B solid;">您的購物車中有 <span style='color:#DB2718;'><?php echo count($this->cart_change); ?></span> 個商品價格已經發生變化了！</div>
					<div style="font-size:10pt; color:#848484;">
						<?php for($i=0; $i<count($this->cart_change); $i++) { ?>
						<div style="margin:28px 0 28px 31px; position:relative;"><img src="<?php echo $this->base['tpl']; ?>shopping_bag.png" style="position:absolute; top:-5px; left:-31px;"><?php echo $this->cart_change[$i]; ?></div>
						<?php } ?>
					</div>
					<?php } ?>
				</div>
				
				<div id="shopping_cart" class="table">
					<div class="tr" style="font-size:10pt; font-weight:bold; color:#333333;">
						<div class="td" style="font-size:12pt; height:27px;">購物明細</div>
						<div class="td">商店名稱</div>
						<div class="td">商品名稱	</div>
						<div class="td">規格/尺寸</div>
						<div class="td">價錢</div>
						<div class="td">數量</div>
						<div class="td">操作</div>
					</div>
					<?php
					$store_no = 0;
					$store_no_tmp = 0;
					$store_lock = 0;
					$store_subtotal = 0;
					$store_background = '';
					
					for($i=0; $i<count($swop->goods); $i++) {
						if($store_lock != $swop->goods[$i]['store']) {
							for($j=0; $j<count($swop->store); $j++) {
								if($swop->store[$j]['fi_no'] == $swop->goods[$i]['store']) {
									$store_name = $swop->store[$j]['name'];
									$store_no = $swop->store[$j]['fi_no'];
									$store_lock = $swop->goods[$i]['store'];
									if($store_background == '' || $store_background == 'FAFEFF') {
										$store_background = 'F9F9F9';
									}
									else {
										$store_background = 'FAFEFF';
									}
								}
							}
						}
					?>
					<?php
						$cart_type = explode("∵", $this->cart_type[$swop->goods[$i]['fi_no']]);
						$cart_item = explode("∵", $this->cart_item[$swop->goods[$i]['fi_no']]);
						$cart_num = explode("∵", $this->cart_num[$swop->goods[$i]['fi_no']]);
						$cart_stock = explode("∵", $this->cart_stock[$swop->goods[$i]['fi_no']]);
						$cart_array = explode("∵", $this->cart_array[$swop->goods[$i]['fi_no']]);
						$cart_check = explode("∵", $this->cart_check[$swop->goods[$i]['fi_no']]);
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
									foreach((array)$specifications_arr as $ke => $va) {
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
					<div class="tr" id="tr<?php echo $swop->goods[$i]['fi_no']."_".$cart_stock[$j]; ?>" style="border-top:1px #<?php
																																if($i == 0 && $j == 0) {
																																	echo "7B7B7B";
																																}
																																else if($nextmp_verification == 1) {
																																	echo "B7B7B7";
																																	$nextmp_verification = 0;
																																}
																																else if($error_verification == 1) {
																																	echo "B7B7B7";
																																	$nextmp_verification = 1;
																																}
																																else {
																																	echo "E9E9E9";
																																}
																																?> solid; font-size:10.5pt; color:#030303;<?php
																																if($i == (count($swop->goods)-1) && $j == (count($cart_item)-2)) {
																																	echo ' border-bottom:1px #BFC0C2 solid;';
																																}
																																
																																if($error_verification == 1) {
																																	echo ' background-color:#EAEAEA; color:#848484;';
																																}
																																else {
																																	echo ' background-color:#'.$store_background.";";
																																}
																																?>">
						<div class="td" style="padding:17px 0 16px 18px; width:211px;">
							<img src="<?php echo $this->base['thu'].$swop->goods[$i]['images'][0]; ?>" style="width:124px; border:1px #A7A7A7 solid;">
						</div>
						<div class="td store" id="store<?php echo $swop->goods[$i]['fi_no']."_".$cart_stock[$j]; ?>" data-store="<?php echo $store_no; ?>" style="padding: 17px 0 16px 0; width:269px;">
							<?php
								if($store_no != $store_no_tmp) {
									$store_no_tmp = $store_no;
							?>
							<div style="margin-bottom:8px;">
								<input type="checkbox" id="storese<?php echo $store_no; ?>" class="storese icheckbox2" name="storese" value="<?php echo $store_no; ?>">
								<label for="storese<?php echo $store_no; ?>"><span style="position:relative; left:7px; top:-2px;"><?php echo $store_name; ?></span></label>
								<img src="<?php echo $this->base['tpl']; ?>service.png" style="margin:0 5px 0 17px; position:relative; top:2px;">
								<span style="color:#848484; position:relative; top:-2px;">客服</span>
							</div>
							<?php  } ?>
							<?php if($error_verification == 1) {echo '<div style="width:64px; line-height:25px; margin-left:21px; border-radius:2px; background-color:#848484; color:#FFF; font-size:10.5pt; text-align:center;">商品失效</div>';}?>
						</div>
						<div class="td" style="padding: 17px 15px 16px 0; width:232px; line-height:20px;"><a href="<?php echo $this->base['url']; ?>goods?no=<?php echo $swop->goods[$i]['fi_no']; ?>"><?php echo $swop->goods[$i]['name']; ?></a></div>
						<div class="td" style="padding: 17px 0 16px 0; width:147px;">
							<?php
							if($rtype[0] == "default" && $ritem[0] == "default") {
							?>
							■ 依廠商描述
							<?php 
							}
							else {
								for($k=0; $k<count($rtype); $k++) {
							?>
								<div style="height:24px; color:#848484;"><?php echo $rtype[$k]; ?></div>
								<div style="margin-bottom:19px;">
									<img src="<?php echo $this->base['tpl']; ?>check2.png" style="position:relative; top:2px; margin-right:7px;">
									<div id="<?php echo $swop->goods[$i]["fi_no"]."_".$k; ?>" style="display:inline-block;"><?php echo $ritem[$k]; ?></div>
									<div style="position:relative; top:2px; display:inline-block; width:50px; height:16px; border:1px #848484 solid; background-color:#fff; border-radius:1px; font-size:9pt; color:#848484;">
										<img src="<?php echo $this->base['tpl']; ?>modify.png" style="margin:0 3px 0 3px;">
										<div style="display:inline-block; position:relative; top:-3px;">修改</div>
										<div <?php if($error_verification != 1) { ?> class="type_change" data-type="<?php echo $rtype[$k]; ?>" data-count="<?php echo count($this->style_content[$swop->goods[$i]["fi_no"]][$rtype[$k]]); ?>" data-item="<?php echo $ritem[$k]; ?>" data-fi_no="<?php echo $swop->goods[$i]["fi_no"]."_".$cart_stock[$j]; ?>" data-index="<?php echo $k; ?>" data-val="<?php echo implode("｜", $this->style_content[$swop->goods[$i]["fi_no"]][$rtype[$k]]); ?>" <?php } ?> style="position: absolute; top:0px; left:0px; width:50px; height:16px; "></div>
									</div>
								</div>
							<?php
								}
							}
							?>
						</div>
						<div class="td single_price" id="single_price<?php echo $swop->goods[$i]['fi_no']."_".$cart_stock[$j]; ?>" data-single_price="<?php echo $cart_value = ($swop->goods[$i]['discount']!=0)?$swop->goods[$i]['discount']:$swop->goods[$i]['promotions']; ?>" style="padding: 17px 0 16px 0; width:117px;">
							<div style="font-weight:bold; margin-bottom:11px;"><span style="font-family:Arial;">¥</span><?php echo $cart_value; ?></div>
							<?php
							if($swop->goods[$i]['discount']!=0) {
								echo "<div style='text-decoration:line-through; color:#848381;'><span style='font-family:Arial;'>¥</span>".$swop->goods[$i]['promotions']."</div>";
								echo "<div style='margin-top:16px;'><img src='".$this->base['tpl']."sale.png'></div>";//(ceil($swop->goods[$i]['discount']*100/$swop->goods[$i]['promotions'])/10) //無條件捨去在轉折數
							}
							?>
						</div>
						<div class="td" style="padding: 17px 0 16px 0; width:98px;">
							<div style="float:left;"><input type="text" id="number<?php echo $swop->goods[$i]['fi_no']."_".$cart_stock[$j]; ?>" class="number" data-select="<?php echo $cart_array[$j]; ?>"  value="<?php echo $cart_num[$j]; ?>" <?php if($error_verification != 1) { ?> data-log="<?php echo $cart_num[$j]; ?>" data-fi_no="<?php echo $swop->goods[$i]['fi_no']."_".$cart_stock[$j];; ?>" data-stock="<?php echo implode(",",$swop->goods[$i]['inventory']); ?>" <?php } else { ?> disabled="disabled" <?php } ?> style="width:33px; line-height:25px; text-align:center; border:1px #EFEFEF solid;"></div>
							<div style="float:left; margin-left:1px;">
								<div class="number_plus" <?php if($error_verification != 1) { ?> data-fi_no="<?php echo $swop->goods[$i]["fi_no"]."_".$cart_stock[$j]; ?>" <?php } ?> style="width:18px; height:11px; background-color:#fff; margin-top:4px;"><img src="<?php echo $this->base['tpl']; ?>top_arrow.jpg"></div>
								<div class="number_minus" <?php if($error_verification != 1) { ?> data-fi_no="<?php echo $swop->goods[$i]["fi_no"]."_".$cart_stock[$j]; ?>" <?php } ?> style="width:18px; height:11px; background-color:#fff; margin-top:3px;"><img src="<?php echo $this->base['tpl']; ?>top_arrow.jpg" style="-ms-transform: rotate(180deg);-moz-transform: rotate(180deg);-webkit-transform: rotate(180deg);-o-transform: rotate(180deg);transform: rotate(180deg);"></div>
							</div>
							<div style="clear:both;"></div>
							<span class="message" id="message<?php echo $swop->goods[$i]['fi_no']."_".$cart_stock[$j]; ?>" style="color:red;"><?php if($cart_check[$j]=='out'){echo '庫存不足';} ?></span>
						</div>
						<div class="td" style="padding: 17px 31px 16px 0; width:88px; font-size:9pt; color:#848484;">
							<div class="delete" data-fi_no="<?php echo $swop->goods[$i]['fi_no']."_".$cart_stock[$j]; ?>" style="margin-bottom:15px;">
								<img src="<?php echo $this->base['tpl']; ?>delete.png" style="margin-right:5px;">
								<span style="position:relative; top:-3px;">刪除</span>
							</div>
							<?php if($error_verification != 1) { ?>
								<div style="margin-bottom:17px;">
									<input type="checkbox" id="choose<?php echo $swop->goods[$i]['fi_no']."_".$cart_stock[$j]; ?>" class="icheckbox2 choose choose<?php echo $store_no; ?>" name="choose[]" value="<?php echo $swop->goods[$i]['fi_no']."_".$cart_stock[$j]; ?>" >
									<label for="choose<?php echo $swop->goods[$i]['fi_no']."_".$cart_stock[$j]; ?>" style="line-height:14px; width:30px; text-align:right;">選取</label>
									</div>
								<div class="collect" data-fi_no="<?php echo $swop->goods[$i]['fi_no']."_".$cart_stock[$j]; ?>">
									<img src="<?php echo $this->base['tpl']; ?>favorite.png" style="margin-right:5px;">
									<span style="position:relative; top:-3px;">移到收藏夾</span>
								</div>
							<?php }else { ?>
								<div style="margin-bottom:15px;">
									<img src="<?php echo $this->base['tpl']; ?>check.png" style="margin-right:5px;">
									<span style="position:relative; top:-3px;">選取</span>
								</div>
								<div>
									<img src="<?php echo $this->base['tpl']; ?>favorite.png" style="margin-right:5px;">
									<span style="position:relative; top:-3px;">移到收藏夾</span>
								</div>
							<?php } ?>
						</div>
					</div>
					<?php
						}
					?>
					<?php
					}
					?>
				</div>
				<div style="padding:11px 13px 11px 18px; font-size:9pt; margin-top:26px; border:1px #DCD8D9 solid; background-color:#F5F5F5;">
					<div style="float:left; width:50%; color:#848484;">
						<div style="float:left;">
							<input type="checkbox" id="batch_select" class="icheckbox2" name="batch_select"><label for="batch_select" style="line-height:14px; width:30px; text-align:right;">全選</label>
						</div>
						<div id="batch_delete" style="float:left; margin-left:24px;">
							<img src="<?php echo $this->base['tpl']; ?>delete.png" style="margin-right:5px;"> <span style="position:relative; top:-3px;">刪除</span>
						</div>
						<div id="batch_collect" style="float:left; margin-left:24px;">
							<img src="<?php echo $this->base['tpl']; ?>favorite.png" style="margin-right:5px;"> <span style="position:relative; top:-3px;">移到收藏夾</span>
						</div>
						<div style="clear:both;"></div>
					</div>
					<div style="float:left; text-align:right; width:50%; color:#242424;">
						<span style="margin-right:10px;">購物車合計<span id="cart_number" style="font-size:12pt; font-weight:bold; color:#F66420; margin:0 3px 0 3px;">0</span>件商品</span>
						<span style="margin-right:10px;">合計<span id="cart_total" style="font-size:12pt; font-weight:bold; color:#F66420; margin:0 3px 0 3px;"><span style="font-family:Arial;">¥</span>0</span>(未含運費)</span>
						<!--span><span id="cart_renminbi" style="font-size:12pt; font-weight:bold; color:#F66420; margin-right:3px;">¥0.0</span>(今日人民幣匯率參考<span id="cart_rate" data-rate="<?php /* echo number_format($swop->exchange_rate[0]['rate'], 1); */ ?>"><?php /* echo number_format($swop->exchange_rate[0]['rate'], 2); */ ?></span>)</span-->
					</div>
					<div style="clear:both;"></div>
				</div>
				<div style="margin:22px 0 65px 0; text-align:right;">
					<input id="oncontinue" type="button" value="繼續購物" style="width:153px; height:48px; border:0px; background-color:#A12F22; box-shadow:1px 1px 1px #000; color:#fff; font-size:13.5pt; border-radius:4px; padding:0px; line-height:normal; *overflow:visible;" />
					<input id="onsubmit" type="submit" value="立刻結帳" style="width:153px; height:48px; border:0px; background-color:#FB5123; box-shadow:1px 1px 1px #000; color:#fff; font-size:13.5pt; border-radius:4px; padding:0px; margin-left: 22px; line-height:normal; *overflow:visible;" disabled="disabled" />
				</div>
				
				<div style="position:relative; height:36px; text-align:center; font-size:12pt; font-weight:bold;">
					<div class="other_tab" data-tab="related" style="float:left; z-index:5; position:relative; background-color:#fff; width:80px; line-height:32px; border-top:#D92F19 2px solid; border-left:#D92F19 2px solid; border-right:#D92F19 2px solid; padding-bottom:2px; color:#EC343A;">相關推薦</div>
					<div class="other_tab" data-tab="love" style="float:left; z-index:5; position:relative; background-color:#F5F5F5; width:80px; line-height:32px; border-top:#F5F5F5 2px solid; border-left:#F5F5F5 2px solid; border-right:#F5F5F5 2px solid;  margin-left:2px;">猜你喜歡</div>
					<div class="other_tab" data-tab="collect" style="float:left; z-index:5; position:relative; background-color:#F5F5F5; width:80px; line-height:32px; border-top:#F5F5F5 2px solid; border-left:#F5F5F5 2px solid; border-right:#F5F5F5 2px solid;  margin-left:2px;">我的收藏</div>
					<div style="clear:both; z-index:4; position:absolute; bottom:0px; width:100%; height:2px; background-color:#D92F19;"></div>
				</div>
				
				<div style="position:relative; height:299px; margin:15px 0 56px 0; font-size:8pt;">
					
					
					<div class='other_context' id="related" data-select="0">
						<?php $a = 0; for($i=1; $i<=count($swop->recommend); $i++) { ?>
						<?php if(($i % 5) == 1) { ?>
						<div class="related_context" id="related_<?php echo $a; ?>"<?php if($a != 0) {?> style="display:none;"<?php } $a++; ?>>
						<?php } ?>
						
						<a href="<?php echo $this->base['url']; ?>goods?no=<?php echo $swop->recommend[$i-1]['fi_no']; ?>">
							<div style="float:left; width:203px; height:275px; border:#DCD8D9 1px solid; border-radius:1px; padding:12px 12px 10px 12px;<?php if(!is_int($i/5)){echo " margin-right:20px;";} ?>">
								<div><img src="<?php
									$recommend_images = json_decode($swop->recommend[$i-1]['images']);
									echo $this->base['thu'].$recommend_images[0];
									?>" style="width:208px; height:208px;"></div>
								<div style="margin:19px 0 7px 0; color:#EB3339; font-weight:bold;"><span style="font-family:Arial;">¥</span><?php echo ($swop->recommend[0]['discount']!=0)?$swop->recommend[0]['discount']:$swop->recommend[0]['promotions']; ?></div>
								<div style="width:203px; height:28px; line-height:14px; color:#8D8D8D"><?php echo $swop->recommend[$i-1]['name']; ?></div>
							</div>
						</a>
						
						<?php if(is_int($i/5) || $i == count($swop->recommend)) { ?>
						<div style="clear:both;"></div>
						</div>
						<?php } ?>
						
						<?php } ?>
					</div>
					<div class='other_context' id="love" data-select="0" data-check="0" data-fi_no="<?php if(count($swop->cart_category) > 0) { echo implode(",", array_unique($swop->cart_category)); } ?>" style="display:none;"></div>
					<div class='other_context' id="collect" data-select="0" data-check="0" style="display:none;"></div>
					
					
					<div id="select_left" data-tab="related" style="position:absolute; top:162px; left:0px; width:20px; height:295px; line-height:295px;"><img src="<?php echo $this->base['tpl']; ?>left_arrow.jpg"></div>
					<div id="select_right" data-tab="related" style="position:absolute; top:162px; right:0px; width:20px; height:295px; line-height:295px;"><img src="<?php echo $this->base['tpl']; ?>left_arrow.jpg" style="-ms-transform:rotate(180deg); -moz-transform:rotate(180deg); -webkit-transform:rotate(180deg); -o-transform:rotate(180deg); transform:rotate(180deg);"></div>
				</div>
				
				<div id="type_edit" style="position:absolute; padding:15px 0px 15px 17px; display:none; border:#D6D8D9 solid 1px; background-color:#fff; z-index:5; border-radius:7px; line-height:31px; font-size:10.5pt;"></div>
			</form>
			<div id="select_box" style="position:absolute; top:0px; left:0px; pointer-events:none;"></div>
		</div>
