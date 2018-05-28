
		<div class="template_center">
		<?php
		if(count($this->error) < 1) {
		?>
			<?php
			if(count($swop->goods_index) > 0) {
			?>
				<div id="navigation" style="margin:28px 0 14px 0; font-size:8pt; font-weight:bold;">
					<div id="navigation_address" style="float:left; width:80%; height:18px; line-height:18px;"></div>
					<div id="navigation_total" style="float:left; width:20%; height:18px; line-height:18px; height:18px;text-align:right;">共 <?php echo $swop->num; ?> 件相關商品</div>
					<div style="clear:both;"></div>
				</div>
				<div id="append" class="table" style="border-top:#dddddd 2px solid; font-weight:bold;" data-attr='<?php echo $swop->string_aai; ?>'>
					
				</div>
				
				<div<?php /* style="float:left; width:977px;"*/ ?>>
					<div style="background-color:#f2f2f2; border:#d4cfd0 1px solid; width:1217px; <?php /*width:969px; */?>padding:4px 3px 4px 3px; margin:17px 0 7px 0;">
						<div style="float:left; width:505px; margin-right:7px; font-size:9pt;"><?php echo $sort->sort_content; ?></div>
						<div style="float:left; width:206px;">
							<?php
							if( $_GET['price_start']!="" && is_numeric($_GET['price_start']) && $_GET['price_end']!="" && is_numeric($_GET['price_end']) && $_GET['price_end'] > $_GET['price_start']) {
								$price_start = floor($_GET['price_start']);
								$price_end = floor($_GET['price_end']);
							}
							?>
							<div style="float:left;"><input type="text" id="price_start" name="price_start" value="<?php echo $price_start; ?>" style="width:55px; height:13px; border:#DDD9DA 1px solid; border-radius:0; padding:5px; text-align:center;" placeholder="$ 最低價"></div>
							<div style="float:left; width:5px; height:11px; border-bottom:#DDD9DA 1px solid;">&nbsp;&nbsp;</div>
							<div style="float:left; margin-right:9px;"><input type="text" id="price_end" name="price_end" value="<?php echo $price_end; ?>" style="width:55px; height:13px; border:#DDD9DA 1px solid; border-radius:0; padding:5px; text-align:center;" placeholder="$ 最高價"></div>
							<div style="float:left;"><input id="interval" type="submit" value="搜索" style="cursor:pointer; width:42px; height:23px; margin-top:1px; font-size:9pt; color:#fff; background:#F66320; border:0; border-radius:0; -moz-appearance:none; -webkit-appearance:none;"></div>
							<div style="clear:both;"></div>
						</div>
						<div style="float:left; width:499px; <?php /*width:251px; */ ?>">
							<div style="width:91px; height:23px; margin-left:406px; <?php /*margin-left:158px; */?>border:#d4cfd0 1px solid; background-color:#FFFFFF;">
								<div style="float:left; width:57px; line-height:23px; text-align:center; border-right:#d4cfd0 1px solid; font-size:10pt; color:#838383;">
									<?php
									if($_GET['page']=='' || $_GET['page']<=0 || !is_numeric($_GET['page'])) {
										$page_now = "1";
									}
									elseif($_GET['page'] > ceil($swop->num / 20)) {
										$page_now = ceil($swop->num / 20);
									}
									else {
										$page_now = $_GET['page'];
									}
									$page_all = ceil($swop->num / 20);
									?>
									<span style="color:#EC3336;"><?php echo $page_now; ?></span>/<?php echo $page_all; ?>
								</div>
								<div id="page_up" style="float:left; width:17px; height:15px; padding-top:8px; text-align:right;"><img src="<?php echo $this->base['god']; ?>arrow_deepgray.png" style="-ms-transform:rotate(270deg); -moz-transform:rotate(270deg); -webkit-transform:rotate(270deg); -o-transform:rotate(270deg); transform:rotate(270deg);"></div>
								<div id="page_down" style="float:left; width:16px; height:15px; padding-top:8px;"><img src="<?php echo $this->base['god']; ?>arrow_deepgray.png" style="-ms-transform:rotate(90deg); -moz-transform:rotate(90deg); -webkit-transform:rotate(90deg); -o-transform:rotate(90deg); transform:rotate(90deg);"></div>
								<div style="clear:both;"></div>
							</div>
						</div>
						<div style="clear:both;"></div>
					</div>
					
					<div style="width:1225px; <?php /*width:977px; */ ?>display:inline-block;">
			<?php
			$ic = 1;
			for($i=0; $i<count($swop->goods_index); $i++) {
			?>
						<?php
						switch($ic) {
							case 4:
								$tmp_padding_right = "18";
								break;
							case 5:
								$tmp_padding_right = "0";
								$ic = 0;
								break;
							default:
								$tmp_padding_right = "19";
						}
						$ic++;
						?>
							<div style="float:left; width:230px; height:373px; padding:12px <?php echo $tmp_padding_right; ?>px 7px 0; font-size:8pt;<?php if($i>=5){echo " border-top:#e2e2e2 1px solid;";} ?>">
								<?php if($swop->goods_index[$i]['discount']==1) { ?><div style="position:absolute; width:228px; height:371px; border:#e61726 1px solid; z-index:5; pointer-events:none;"></div><?php } ?>
								<?php
								$v = $images->images_array[$i][0];
								if(substr($v, 0, 4) == "http") {
									$images_url = $v;
								}
								else {
									$images_url = $this->base['god'].$v;
								}
								
								
								
								$tmp_name[$i] = $swop->goods_index[$i]['name'];
								$tmp_promotions[$i] = $swop->goods_index[$i]['promotions'];
								$tmp_price[$i] = $swop->goods_index[$i]['price'];
								$tmp_images[$i] = $images_url;
								$tmp_discount[$i] = $swop->goods_index[$i]['discount'];
								$tmp_evaluation[$i] = $swop->goods_index[$i]['evaluation'];
								$tmp_store_name[$i] = $swop->goods_index[$i]['store_name'];
								$tmp_evaluation_number[$i] = $swop->goods_index[$i]['evaluation_number'];
								$tmp_transaction_times[$i] = $swop->goods_index[$i]['transaction_times'];
								$tmp_free_shipping[$i] = $swop->goods_index[$i]['free_shipping'];
								$tmp_free_gifts[$i] = $swop->goods_index[$i]['free_gifts'];
								
								
								
								?>
								<a href="goods?no=<?php echo $swop->goods_index[$i]['fi_no']; ?>">
									<div style="position:relative; width:230px; height:266px;">
										<img src='<?php echo $images_url; ?>' style="position:absolute; width:230px; height:230px;">
										<div style="position:absolute; top:230px; height:30px; line-height:15px; display:block; overflow:hidden; margin:3px 0 3px 0;"><?php echo $swop->goods_index[$i]['name']; ?></div>
										<?php if($swop->goods_index[$i]['discount']==1) { ?><img src='<?php echo $this->base['god']; ?>sale_icon.png' style="position:absolute; z-index:6; top:-1px; right:-1px;"><?php } ?>
									</div>
								</a>
								<div style="margin:0 3px 0 3px;">
									<div style="margin:11px 0 27px 0;"><span style="color:red; font-weight:bold; font-size:9pt;">$<?php echo $swop->goods_index[$i]['promotions']; ?></span> <span style="text-decoration:line-through;">$<?php echo $swop->goods_index[$i]['price']; ?></span></div>
									<div>
										<div style="float:left; position:relative; width:104px; height:15px;">
											<div style="position:absolute; -webkit-filter:grayscale(1) opacity(0.2);">
												<?php for($k=0; $k<6; $k++) { ?>
												<div style="float:left; display:inline-block; margin-left:1px;"><img src="<?php echo $this->base['god']; ?>red_star.png"></div>
												<?php } ?>
												<div style="clear:both;"></div>
											</div>
											<div style="position:absolute;">
												<?php
												$evaluation_num = explode(".", $swop->goods_index[$i]['evaluation']);
												for($j=0; $j<$evaluation_num[0]; $j++) {
												?>
												<div style="float:left; display:inline-block; margin-left:1px;"><img src="<?php echo $this->base['god']; ?>red_star.png"></div>
												<?php } ?>
												<div style="float:left; display:inline-block; margin-left:1px; overflow:hidden; width:<?php echo 12/10*substr($evaluation_num[1], 0, 1); ?>px;"><img src="<?php echo $this->base['god']; ?>red_star.png"></div>
												<div style="clear:both;"></div>
											</div>
										</div>
										<div style="float:left; line-height:15px;">評價 <span style="color:#334b92; font-weight:bold;"><?php echo $swop->goods_index[$i]['evaluation_number']; ?></span>｜購買 <span style="color:#334b92; font-weight:bold;"><?php echo $swop->goods_index[$i]['transaction_times']; ?></span></div>
										<div style="clear:both;"></div>
									</div>
									<div style="margin-top:20px;">
										<a href="<?php echo $this->base['url']; ?>brandstore?store=<?php echo $swop->goods_index[$i]['store']; ?>"><div style="float:left; margin-right:7px; display:block; overflow:hidden; width:57px; height:13px; text-decoration:underline;"><?php echo $swop->goods_index[$i]['store_name']; ?></div></a>
										<div onclick="win=window.open('','_blank');$.post('<?php echo $this->base['url']; ?>service/ajax_select_service/',{type:1,store:<?php echo $swop->goods_index[$i]['store']; ?>},function(qq){openWin($.trim(qq));});" style="float:left; margin-right:7px; cursor:pointer;"><div style="float:left; position:relative; top:-3px; margin-right:3px; background:url(<?php echo $this->base['god']; ?>service_icon.png); width:20px; height:20px;"></div><div style="float:left;">客服</div><div style="clear:both;"></div></div>
										<div style="float:left; margin-right:7px;"><div style="float:left; position:relative; top:-1px; margin-right:3px; background:url(<?php echo $this->base['god']; ?>check_icon.png) no-repeat center; width:13px; height:13px; border:#f44b09 1px solid; <?php if($swop->goods_index[$i]['free_shipping']==0){echo ' -webkit-filter:grayscale(1) opacity(0.2);';} ?>"></div><div style="float:left; color:<?php echo ($swop->goods_index[$i]['free_shipping']==0)?"gray":"#f44b09"; ?>;;">送贈品</div><div style="clear:both;"></div></div>
										<div style="float:left;"><div style="float:left; position:relative; top:-1px; margin-right:3px; background:url(<?php echo $this->base['god']; ?>check_icon.png) no-repeat center; width:13px; height:13px; border:#f44b09 1px solid; <?php if($swop->goods_index[$i]['free_gifts']==0){echo ' -webkit-filter:grayscale(1) opacity(0.2);';} ?>"></div><div style="float:left; color:<?php echo ($swop->goods_index[$i]['free_gifts']==0)?"gray":"#f44b09"; ?>;">免運</div><div style="clear:both;"></div></div>
										<div style="clear:both;"></div>
									</div>
								</div>
							</div>
			<?php
			}
			?>
					</div>
				</div>
				<?php /*<div style="float:left; width:230px; margin-left:18px;">
					<div style="font-size:11pt; line-height:25px; background-color:#f2f2f2; border:#d4cfd0 1px solid; padding:4px 18px 4px 18px; margin:17px 0 7px 0;">
						廠商熱賣
					</div>
					<div>
						
						
						
						
						<?php for($u=0; $u<3; $u++) { ?>
						<a href="goods?no=1">
							<div style="float:left; width:230px; height:373px; padding:12px 19px 7px 0; font-size:8pt; <?php if($u!=0) {echo "border-top:#e2e2e2 1px solid;";} ?>">
								<?php if($tmp_discount[0]==1) { ?><div style="position:absolute; width:228px; height:371px; border:#e61726 1px solid; z-index:5;"></div><?php } ?>
								<div style="position:relative; width:230px; height:230px;">
									<img src="<?php echo $tmp_images[0]; ?>" style="position:absolute; width:230px; height:230px;">
									<?php if($_discount[0]==1) { ?><img src='<?php echo $this->base['god']; ?>sale_icon.png' style="position:absolute; z-index:6; top:-1px; right:-1px;"><?php } ?>
								</div>
								<div style="margin:0 3px 0 3px;">
									<div style="height:30px; line-height:15px; display:block; overflow:hidden; margin:3px 0 3px 0;"><?php echo $tmp_name[0]; ?></div>
									<div style="margin:11px 0 27px 0;"><span style="color:red; font-weight:bold; font-size:9pt;">$<?php echo $tmp_promotions[0]; ?></span> <span style="text-decoration:line-through;">$<?php echo $tmp_price[0]; ?></span></div>
									<div>
										<div style="float:left; position:relative; width:104px; height:15px;">
											<div style="position:absolute; -webkit-filter:grayscale(1) opacity(0.2);">
												<?php for($k=0; $k<6; $k++) { ?>
												<div style="float:left; display:inline-block; margin-left:1px;"><img src="<?php echo $this->base['god']; ?>red_star.png"></div>
												<?php } ?>
												<div style="clear:both;"></div>
											</div>
											<div style="position:absolute;">
												<?php
												$evaluation_num = explode(".", $tmp_evaluation[0]);
												for($j=0; $j<$evaluation_num[0]; $j++) {
												?>
												<div style="float:left; display:inline-block; margin-left:1px;"><img src="<?php echo $this->base['god']; ?>red_star.png"></div>
												<?php } ?>
												<div style="float:left; display:inline-block; margin-left:1px; overflow:hidden; width:<?php echo 12/10*substr($evaluation_num[1], 0, 1); ?>px;"><img src="<?php echo $this->base['god']; ?>red_star.png"></div>
												<div style="clear:both;"></div>
											</div>
										</div>
										<div style="float:left; line-height:15px;">評價 <span style="color:#334b92; font-weight:bold;"><?php echo $tmp_evaluation_number[0]; ?></span>｜購買 <span style="color:#334b92; font-weight:bold;"><?php echo $tmp_transaction_times[0]; ?></span></div>
										<div style="clear:both;"></div>
									</div>
									<div style="margin-top:20px;">
										<div style="float:left; margin-right:7px; display:block; overflow:hidden; width:57px; height:13px; text-decoration:underline;"><?php echo $tmp_store_name[0]; ?></div>
										<div style="float:left; margin-right:7px;"><div style="float:left; position:relative; top:-3px; margin-right:3px; background:url(<?php echo $this->base['god']; ?>service_icon.png); width:20px; height:20px;"></div><div style="float:left;">客服</div><div style="clear:both;"></div></div>
										<div style="float:left; margin-right:7px;"><div style="float:left; position:relative; top:-1px; margin-right:3px; background:url(<?php echo $this->base['god']; ?>check_icon.png) no-repeat center; width:13px; height:13px; border:#f44b09 1px solid; <?php if($tmp_free_shipping[0]==1){echo ' style="-webkit-filter:grayscale(1) opacity(0.2);"';} ?>"></div><div style="float:left; color:#f44b09;">送贈品</div><div style="clear:both;"></div></div>
										<div style="float:left;"><div style="float:left; position:relative; top:-1px; margin-right:3px; background:url(<?php echo $this->base['god']; ?>check_icon.png) no-repeat center; width:13px; height:13px; border:#f44b09 1px solid; <?php if($tmp_free_gifts[0]==1){echo ' style="-webkit-filter:grayscale(1) opacity(0.2);"';} ?>"></div><div style="float:left; color:#f44b09;">免運</div><div style="clear:both;"></div></div>
										<div style="clear:both;"></div>
									</div>
								</div>
							</div>
						</a>
						<?php } ?>
						
						
						
						
					</div>
				</div>
				<div style="clear:both;"></div>*/ ?>
				<div style="padding-top:21px; margin:32px 0 33px 0; border-top:#e2e2e2 1px solid;">
					<?php echo $page->page_content; ?>
				</div>
				<div style="height:28px; font-size:9pt; border-bottom:#D92F19 2px solid;">
					<div style="float:left; width:50%; color:#EC343A; font-weight:bold;">相關推薦</div>
					<div style="float:left; width:50%;">
						<div id="change_related" style="float:right;" data-select="0">
							<div style="float:right;">換一批</div>
							<div style="float:right;"><img src="<?php echo $this->base['god']; ?>change_icon.png" style="position:relative; top:-3px; right:3px;"></div>
							<div style="clear:both;"></div>
						</div>
						<div style="clear:both;"></div>
					</div>
					<div style="clear:both;"></div>
				</div>
				<div style="margin-bottom:29px; height:304px; font-size:8pt; color:#848484; border-bottom:#BDBDBD 1px solid;">
					
					
					
					
						<?php $a = 0; $b = 0; for($i=1; $i<=count($swop->recommend); $i++) { ?>
						<?php if(($i % 5) == 1) { ?>
						<div class="related_context" id="related_<?php echo $a; ?>"<?php if($a != 0) {?> style="display:none;"<?php } $a++; ?>>
						<?php } ?>
						
						<a href="<?php echo $this->base['url']; ?>goods?no=<?php echo $swop->recommend[$i-1]['fi_no']; ?>">
							<div style="float:left; width:230px; height:304px; margin:0 <?php if($b<3){echo "19";}elseif($b==3){echo "18";}else{echo "0";}  ?>px 0 0;">
								<div style="position:relative; width:230px; height:230px;">
									<img src="<?php
										$recommend_images = json_decode($swop->recommend[$i-1]['images']);
										echo $this->base['god'].$recommend_images[0];
										?>" style="position:absolute; width:230px; height:230px;">
									<?php if($swop->recommend[$i-1]['transaction_times'] > 100) { ?>
									<img src='<?php echo $this->base['god']; ?>hotsale_icon.png' style="position:absolute; z-index:6; top:9px; right:-9px;">
									<?php } ?>
								</div>
								<div style="margin:0 3px 0 3px;">
									<div style="margin:16px 0 9px 0; color:red; font-weight:bold; font-size:9pt">$<?php echo ($swop->recommend[$i-1]['discount']!=0)?$swop->recommend[$i-1]['discount']:$swop->recommend[$i-1]['promotions']; ?></div>
									<div style="height:30px; line-height:15px; display:block; overflow:hidden; margin:3px 0 3px 0;"><?php echo $swop->recommend[$i-1]['name']; ?></div>
								</div>
							</div>
						</a>
						
						<?php $b++; if(is_int($i/5) || $i == count($swop->recommend)) { $b = 0; ?>
						<div style="clear:both;"></div>
						</div>
						<?php } ?>
						
						<?php } ?>
						
						
						
				</div>
				<div style="height:284px; background-color:#F9F9F9; border:#BDBDBD 1px solid; margin-bottom:33px;">
					<div style="margin:23px 18px 30px 18px; color:#EB494C; font-weight:bold;">猜你喜歡</div>
					<div id="change_love" data-select="0">
						<div id="love_left" style="float:left; width:20px; height:115px; background:url(<?php echo $this->base['god']; ?>arrow_icon.png) no-repeat center; -moz-transform: scaleX(-1); -o-transform: scaleX(-1); -webkit-transform: scaleX(-1); transform: scaleX(-1); filter: FlipH; -ms-filter: "FlipH";"></div>
						<div style="float:left; width:1173px; margin:0 5px 0 5px; font-size:8pt;">
					
					
					
							<?php $a = 0; for($i=1; $i<=count($swop->love); $i++) { ?>
							<?php if(($i % 9) == 1) { ?>
							<div class="love_context" id="love_<?php echo $a; ?>"<?php if($a != 0) {?> style="display:none;"<?php } $a++; ?>>
							<?php } ?>
							
							<a href="<?php echo $this->base['url']; ?>goods?no=<?php echo $swop->love[$i-1]['fi_no']; ?>">
								<div style="float:left; width:115px; margin:0 8px 0 7px;">
									<div style="width:115px; height:115px;">
										<img src="<?php
											$love_images = json_decode($swop->love[$i-1]['images']);
											echo $this->base['god'].$love_images[0];
											?>" style="width:115px; height:115px; border-radius:5px;">
									</div>
									<div style="margin:20px 0 10px 0; color:red; font-weight:bold; font-size:9pt;">$<?php echo ($swop->love[$i-1]['discount']!=0)?$swop->love[$i-1]['discount']:$swop->love[$i-1]['promotions']; ?></div>
									<div style="height:45px; line-height:15px; display:block; overflow:hidden;"><?php echo $swop->love[$i-1]['name']; ?></div>
								</div>
							</a>
					
							<?php $b++; if(is_int($i/9) || $i == count($swop->love)) { ?>
							<div style="clear:both;"></div>
							</div>
							<?php } ?>
							
							<?php } ?>

					
					
						</div>
						<div id="love_right" style="float:left; width:20px; height:115px; background:url(<?php echo $this->base['god']; ?>arrow_icon.png) no-repeat center;"></div>
						<div style="clear:both;"></div>
					</div>
				</div>
			<?php
			} else {
			?>
			<div style="text-align:center; line-height:250px;">未有任何結果</div>
			<?php
			}
			?>
		<?php
		} else {
		?>
			<?php
			for($i=0; $i<count($this->error); $i++) {
			?>
			<div><?php echo $this->error[$i]; ?></div>
			<?php
			}
			?>
		<?php
		}
		?>
		</div>
