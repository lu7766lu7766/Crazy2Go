
		<div class="template_center">
                        <div style='padding:30px 0px;'><?php echo $this->cPath;?></div>
			<div style="">
				<div style="float:left; width:979px; margin-right:24px;">
					<div style='width:898px;padding:40px;border-top:1px solid #F2F2F2;border-left:1px solid #F2F2F2;border-right:1px solid #E2E2E2;border-bottom:1px solid #E2E2E2;margin-bottom: 20px;'>
						<div style="float:left; width:50%;">
							<div style="position:relative; width:445px; height:445px;">
								<?php
								$i = count($images->images_array);
								foreach($images->images_array as $k => $v) {
								?>
								<div style="position:absolute; z-index:<?php echo $i; ?>;"><img id='product_big_image' src="<?php
									if(substr($v, 0, 4) == "http") {
										echo $v;
									}
									else {
										echo $this->base['god'].$v;
									}
								?>" style='border:1px solid #C3C3C3;width:445px; height:445px;'><?php echo "<img src='".$this->base["god"]."magnify.png"."' style='position:absolute;right:12px;bottom:12px;'>";?></div>
								<?php
									$i--;
                                                                        break;//只要一張
								}
								?>
							</div>
							<div style='text-align: center;padding:20px 0px; width:445px;'>
								<?php
								$i = count($images->images_array);
								foreach($images->images_array as $k => $v) {
								?>
								<div style="border:1px solid #D2D2D2; display: inline-block;padding:7px;cursor:pointer;"><img class='product_images' src="<?php
									if(substr($v, 0, 4) == "http") {
										echo $v;
									}
									else {
										echo $this->base['min'].$v;
									}
								?>" style="width:25px;"></div>
								<?php
									$i--;
								}
								?>
								<div style="clear:both;"></div>
							</div>
							<div style='margin-top:30px;'>
                                                            <div id='add_goods_collect' data-fi_no="<?php echo $swop->goods[0]['fi_no'];?>" style="cursor:pointer;display:inline-block;border:1px solid #B7B6B9;height:25px;margin-right: 5px;">
                                                                    <?php echo "<img src='".$this->base["god"]."favorite_icon.jpg"."'>";?>
                                                                    <span style='padding:8px;position:relative;bottom:8px;'>加入收藏</span>
                                                                </div>
								<!--<div style="display:inline-block;border:1px solid #B7B6B9;height:25px;margin-right: 5px;">
                                                                    <?php 
                                                                        echo "<img src='".$this->base["god"]."share_icon1.jpg"."'>";
                                                                        echo "<img src='".$this->base["god"]."share_icon2.jpg"."'>";
                                                                        echo "<img src='".$this->base["god"]."share_icon3.jpg"."'>";
                                                                        echo "<img src='".$this->base["god"]."share_icon4.jpg"."'>";
                                                                        echo "<img src='".$this->base["god"]."share_icon5.jpg"."'>";
                                                                        echo "<img src='".$this->base["god"]."share_icon6.jpg"."'>";
                                                                    ?>
                                                                    <span style='padding:8px;position:relative;bottom:8px;cursor:pointer;'>分享連結</span>
                                                                </div>-->
								<!--<div style="float:left;">人氣 <?php echo $swop->goods[0]['click']; ?></div>-->
                                                                <div onclick='win=window.open("","_blank");$.post("<?php echo $this->base["url"];?>"+"service/ajax_select_service/",{type:1,store:<?php echo $swop->goods[0]['store'];?>},function(qq){openWin($.trim(qq));});' style="cursor:pointer;display:inline-block;border:1px solid #B7B6B9;height:25px;">
                                                                    <?php echo "<img src='".$this->base["god"]."service_icon_1.jpg"."'>";?>
                                                                    <span style='padding:8px;position:relative;bottom:8px;cursor:pointer;'>線上諮詢</span>
                                                                </div>
							</div>
						</div>
						<div style="float:left; width:50%;">
							<div style='width:400px;margin-left: 42px;'>
								<div style='background:#F5F5F5;padding:10px 10px 20px 10px;'><span style='font-size:16pt;font-weight: bold;'><?php echo $swop->goods[0]['name']; ?></span></div>
								<div style='background:#F5F5F5;padding:10px 10px 10px 10px;border-bottom:2px solid #E8E6E7;'><span style='color:#969B9C'><b><?php echo $swop->goods[0]['depiction']; ?></b></span></div>
								<div class="table">
									<div class="tr" style='background:#F5F5F5;'>
										<div class="td" style="width:80px;padding:10px 10px 10px 10px;color:#969B9C;font-size:8pt;">商品市價</div>
										<div class="td" style="padding:10px 10px 10px 10px;"><?php echo "<span style='font-family:Arial;'>¥</span>".$swop->goods[0]['price']." (約 NT$ ".(ceil($swop->goods[0]['price']*$swop->exchange_rate*100)/100)." )"; ?></div>
									</div>
									<div class="tr" style='background:#F5F5F5;padding:10px 10px 10px 10px;'>
										<div class="td" style="padding:10px 10px 10px 10px;color:#969B9C;font-size:8pt;">商品售價</div>
										<div class="td" style="padding:10px 10px 10px 10px;"><?php $price = $swop->goods[0]['discount']==0?$swop->goods[0]['promotions']:$swop->goods[0]['discount']; echo  "<span style='font-size:16pt;position:relative;bottom:4px;color:#FB0000;'><span style='font-family:Arial;'>¥</span>".$price."</span> (約 NT$ ".(ceil($price*$swop->exchange_rate*100)/100)." )"?></div>
									</div>
									<div class="tr" style='background:#F5F5F5;padding:10px 10px 10px 10px;'>
										<div class="td" style="padding:10px 10px 10px 10px;color:#969B9C;font-size:8pt;">商品評價</div>
										<div id="rank_bar" class="td" style="position:relative;padding:10px 10px 30px 10px;">
											<div style="position:absolute; z-index:1; color:#d0d0d0;">
												<span style="overflow:hidden; display:inline-block;"><?php echo "<img src='".$this->base["god"]."star_gray.png"."'>";?></span>
												<span style="overflow:hidden; display:inline-block;"><?php echo "<img src='".$this->base["god"]."star_gray.png"."'>";?></span>
												<span style="overflow:hidden; display:inline-block;"><?php echo "<img src='".$this->base["god"]."star_gray.png"."'>";?></span>
												<span style="overflow:hidden; display:inline-block;"><?php echo "<img src='".$this->base["god"]."star_gray.png"."'>";?></span>
												<span style="overflow:hidden; display:inline-block;"><?php echo "<img src='".$this->base["god"]."star_gray.png"."'>";?></span>
												<span style="overflow:hidden; display:inline-block;"><?php echo "<img src='".$this->base["god"]."star_gray.png"."'>";?></span>
											</div>
											<div style="position:absolute; z-index:2;">
												<?php
												if($swop->goods[0]['evaluation_number'] != 0) {
													$evaluation = number_format( ($swop->goods[0]['evaluation_score']/$swop->goods[0]['evaluation_number']), 1);
												}
												else {
													$evaluation = "0.0";
												}
												$evaluation_num = explode(".", $evaluation);
												for($j=0; $j<$evaluation_num[0]; $j++) {
												?>
												<span style="overflow:hidden; display:inline-block;"><?php echo "<img src='".$this->base["god"]."star_red.png"."'>";?></span>
												<?php } ?>
												<span style="overflow:hidden; display:inline-block; width:<?php echo 12/10*substr($evaluation_num[1], 0, 1); ?>px"><?php echo "<img src='".$this->base["god"]."star_red.png"."'>";?></span>
											</div>
                                                                                        <div style='color:#969B9C;margin-left: 120px;'>平均 <span id='goods_rank'><?php echo $evaluation;?></span> 分</div>
										</div>
									</div>
									
									<?php echo $style->style_content; ?>
									
									<div class="tr">
										<div class="td" style="padding:10px 10px 10px 10px;color:#969B9C;font-size:8pt;">付款方式</div>
                                                                                <div class="td" style="padding:10px 10px 10px 10px;">
                                                                                    <?php 
                                                                                        echo "<img src='".$this->base["god"]."pay_icon1.jpg"."' > ATM轉帳 ";
                                                                                        echo "<img src='".$this->base["god"]."pay_icon2.jpg"."' > 支付寶支付 ";
                                                                                        echo "<img src='".$this->base["god"]."pay_icon3.jpg"."' > 國際信用卡 ";
                                                                                    ?>
                                                                                </div>
									</div>
									<div class="tr">
										<div class="td" style="padding:10px 10px 10px 10px;"><?php echo "<img src='".$this->base["god"]."sale_icon_flower.png"."' >";?></div>
										<div class="td" style="padding:10px 10px 10px 10px;color:#FC491F;font-size: 8pt;font-weight: bold;"><?php echo $swop->goods[0]['promotions_message']; ?></div>
									</div>
								</div>
								<div>
									<div id="nowed" style="margin:10px 10px 10px 10px;float:left; line-height:40px; width:180px; text-align:center; background-color:#A33124; color:red;cursor:pointer;padding-top:12px;  height:40px;border:0px;font-size:14pt;color:white; line-height:normal;-webkit-border-radius: 6px;-moz-border-radius: 6px;border-radius: 6px;-webkit-box-shadow: rgb(102, 102, 102) 1px 1px 1px;-moz-box-shadow: rgb(102, 102, 102) 1px 1px 1px;" data-fi_no="<?php echo $swop->goods[0]['fi_no']; ?>" data-lock="1">立刻購買</div>
									<div id="added" style="margin:10px 10px 10px 10px;float:left; line-height:40px; width:180px; text-align:center; background-color:#FB461F; color:red;cursor:pointer;padding-top:12px; height:40px;border:0px;font-size:14pt;color:white; line-height:normal;-webkit-border-radius: 6px;-moz-border-radius: 6px;border-radius: 6px;-webkit-box-shadow: rgb(102, 102, 102) 1px 1px 1px;-moz-box-shadow: rgb(102, 102, 102) 1px 1px 1px;" data-fi_no="<?php echo $swop->goods[0]['fi_no']; ?>" data-lock="1"><?php echo "<img src='".$this->base["god"]."shopcart_icon.png"."' style='position:relative;top:3px;' >"; ?> 加入購物車</div>
									<div style="clear:both;"></div>
								</div>
								
							</div>
						</div>
						<div style="clear:both;"></div>
					</div>
					<div style="border-top:1px solid #F2F2F2;border-left:1px solid #F2F2F2;border-right:1px solid #E2E2E2;border-bottom:1px solid #E2E2E2;">
						<div style="text-align:center;border-bottom:2px solid #ECECEC;">
							<div class="goods_button" data-info="introduction" style="cursor:pointer;border-right:1px solid #ECECEC;float:left; width:200px; line-height:45px; background-color:#F3F3F3;">商品詳情</div>
							<div class="goods_button" data-info="evaluate" style="cursor:pointer;border-right:1px solid #ECECEC;float:left; width:200px; line-height:45px; background-color:#FFFFFF;">商品評論</div>
							<div class="goods_button" data-info="related" style="cursor:pointer;border-right:1px solid #ECECEC;float:left; width:200px; line-height:45px; background-color:#FFFFFF;">相關商品</div>
							<div class="goods_button" data-info="instructions" style="cursor:pointer;border-right:1px solid #ECECEC;float:left; width:200px; line-height:45px; background-color:#FFFFFF;">注意事項</div>
							<div style="clear:both;"></div>
						</div>
						<div class="goods_context" id="introduction" style="display:block;padding:40px;">
							<?php
                                                        if($swop->attribute[0])
                                                        {
                                                            echo '<div class="table" style="border-bottom: 2px solid #ECECEC;">';

                                                            //print_r($swop->attribute);
                                                            //print_r($swop->attribute_item);
                                                            for($i=0; $i<count($swop->attribute); $i++) {
                                                                    //echo "[".($i/3)."｜".($i%3)."]";
                                                                    if(($i%3) == 0) {
                                                                            echo '<div class="tr">';
                                                                    }
                                                                    echo '<div class="td" style="width:33%;padding:0px 0px 20px 0px;"><span style="color:#969B9C;">'.$swop->attribute[$i]['name'].'：</span>';
                                                                    $item = $swop->attribute_item[$swop->attribute[$i]['fi_no']];
                                                                    for($j=0; $j<count($item); $j++) {
                                                                            if($j!=0) {
                                                                                    echo "、";
                                                                            }
                                                                            echo $item[$j];
                                                                    }
                                                                    echo '</div>';
                                                                    if(($i%3) == 2) {
                                                                            echo '</div>';
                                                                    }

                                                                    $attr_temp = $i/2;
                                                            }
                                                            if($attr_temp != 2) {
                                                                    for($i=0; $i<count(floor($attr_temp)); $i++) {
                                                                            echo '<div class="td"></div>';
                                                                    }
                                                                    echo '</div>';
                                                            }

                                                            echo '</div>';
                                                        }
							?>
                                                    <div style='margin-top:20px;overflow: hidden;'><?php echo str_replace("../", $this->base["url"]."/", $swop->goods[0]['introduction']); echo "<br/>";if($swop->goods[0]['direct']==3 || $swop->goods[0]['direct']==4)echo "<img src='".$this->base["tpl"]."direct_delivery.jpg"."' style='margin-top:20px;'>";?></div>
						</div>
						<div class="goods_context" id="evaluate" style="display:none;padding:40px;" data-fi_no="<?php echo $swop->goods[0]['fi_no']; ?>" data-check="0"></div>
						<div class="goods_context" id="related" style="display:none;padding:40px;" data-fi_no_news="<?php echo implode(",",$swop->goods[0]['related']["news"]); ?>"  data-fi_no_hots="<?php echo implode(",",$swop->goods[0]['related']["hots"]); ?>" data-check="0"></div>
						<div class="goods_context" id="instructions" style="display:none;padding:40px;"><?php echo $swop->goods[0]['instructions']; ?></div>
					</div>
				</div>
				<div style="float:left; width:221px;">
					<!--<div style="width:221px;border-top:1px solid #F2F2F2;border-left:1px solid #F2F2F2;border-right:1px solid #E2E2E2;border-bottom:1px solid #E2E2E2;">
                                            <?php
                                                echo "<div id='store_image' style='border-bottom:1px solid #D2D2D2;'>"."<img src='".$this->base["sto"].$swop->store[0]['images']."' style='width:220px;height:168px;'>"."</div>";
                                                echo "<div style='text-align:center;height:25px;'>"."<div style='position:relative;top:-30px;display:inline-block;background:#F5F3F4;border:6px solid rgba(0,0,0,0.2);-webkit-background-clip: padding-box;background-clip: padding-box;padding:10px;-webkit-border-radius: 30px;-moz-border-radius: 30px;border-radius: 30px;'><img src='".$this->base["god"]."shop_icon.png"."'></div>"."</div>";
                                                echo "<div id='store_name' style='font-size:18pt;padding:20px 10px;'>".$swop->store[0]['name']."<span id='add_store_collect' data-fi_no='".$swop->store[0]['fi_no']."' style='cursor:pointer;float:right;font-size:6pt;'><img src='".$this->base["god"]."favorite_icon2.jpg"."'>收藏</span></div>";
						
						if($swop->store[0]['service_score'] != 0 && $swop->store[0]['service_number'] != 0) {
							echo "<div style='display:inline-block;width:90px;padding:10px;background:#F5F5F5;border-right:1px solid #F5F5F5;color:#969B9C;'>服務評分 <span style='color:#FC4920;font-size:12pt'>".number_format($swop->store[0]['service_score'] / $swop->store[0]['service_number'], 1)."</span></div>";
						} else {
							echo "<div style='display:inline-block;width:90px;padding:10px;background:#F5F5F5;border-right:1px solid #F5F5F5;color:#969B9C;'>服務評分 <span style='color:#FC4920;font-size:12pt'>"."0.0"."</span></div>";
						}
						
						if($swop->store[0]['quality_score'] != 0 && $swop->store[0]['quality_number'] != 0) {
							echo "<div style='display:inline-block;width:90px;padding:10px;background:#F5F5F5;color:#969B9C;'>品質評分 <span style='color:#FC4920;font-size:12pt'>".number_format($swop->store[0]['quality_score'] / $swop->store[0]['quality_number'], 1)."</span></div>";
						} else {
							echo "<div style='display:inline-block;width:90px;padding:10px;background:#F5F5F5;color:#969B9C;'>品質評分 <span style='color:#FC4920;font-size:12pt'>"."0.0"."</span></div>";
						}
                                                echo "<div onclick='location.href=\"".$this->base["url"]."brandstore?store=".$swop->goods[0]['store']."\"' style='cursor:pointer;display:inline-block;width:90px;padding:10px;background:#F5F5F5;border-top:1px solid #E9E9E9;border-right:1px solid #D2D2D2;color:#969B9C;'>進入店舖 <span style='display:inline-block;position:relative;left:3px;top:-18px;'><img src='".$this->base["god"]."home_icon.png"."' style='position:absolute;'></span></div>";
                                                echo "<div onclick='win=window.open(\"\",\"_blank\");$.post(\"".$this->base["url"]."service/ajax_select_service/\",{type:1,store:".$swop->goods[0]['store']."},function(qq){openWin($.trim(qq));});'style='cursor:pointer;display:inline-block;width:90px;padding:10px;background:#F5F5F5;border-top:1px solid #E9E9E9;color:#969B9C;'>連絡廠商 <span style='display:inline-block;position:relative;left:3px;top:-18px;'><img src='".$this->base["god"]."service_icon_2.jpg"."' style='position:absolute;'></span></div>";
						?></div>-->
					<div style="width:221px;margin-top:0px/*20px*/;border-top:1px solid #F2F2F2;border-left:1px solid #F2F2F2;border-right:1px solid #E2E2E2;border-bottom:1px solid #E2E2E2;"><?php
                                                echo "<div style='font-size:18pt;padding:15px 10px;text-align:center;'><!--賣家-->排行榜</div>";
                                                echo "<div id='order_by_transaction' style='display:inline-block;width:90px;padding:15px 10px;background:#F5F5F5;cursor:pointer;text-align:center;font-szie:8pt;border-right:1px solid #D2D2D2;'>按銷量</div>";
                                                echo "<div id='order_by_addedday' style='display:inline-block;width:90px;padding:15px 10px;background:#F5F5F5;cursor:pointer;text-align:center;font-szie:8pt;'>按新品</div>";
						echo "<div id='order_product_list' data-page='1' data-type='goods_transaction_times'></div>";
                                                echo "<div id='goods_transaction_times' style='display:none;'>";
                                                for($i=0; $i<count($swop->goods_transaction_times); $i++) {
                                                        $swop->goods_transaction_times[$i]["images"] = json_decode($swop->goods_transaction_times[$i]["images"]);
                                                        $swop->goods_transaction_times[$i]["images"] = $swop->goods_transaction_times[$i]["images"][0];
							echo "<div style='position:relative;padding:10px;border-bottom:1px solid #D2D2D2;'>";
                                                            echo "<a href='".$this->base['url']."goods?no=".$swop->goods_transaction_times[$i]["fi_no"]."' style='color:#969B9C;'><img src='";
                                                            echo $this->base['thu'].$swop->goods_transaction_times[$i]["images"];
                                                            echo "' style='width:80px; height:80px; vertical-align:top;margin-right:10px;'>";
                                                            echo "<div style='position:absolute;left:2px;top:2px;padding-left:1px;padding-top:1px;display:inline-block;text-align:center;background:#FB491F;width:20px;height:20px;color:white;-webkit-border-radius: 10px;-moz-border-radius: 10px;border-radius: 10px;'>".($i+1)."</div>";
                                                                echo "<div style='display:inline-block;width:110px;'>";
                                                                echo "<span>".$swop->goods_transaction_times[$i]["name"]."</span>";
                                                                if($swop->goods_transaction_times[$i]["discount"] == "0") {
                                                                        $tmp_value = $swop->goods_transaction_times[$i]["promotions"];
                                                                }
                                                                else {
                                                                        $tmp_value = $swop->goods_transaction_times[$i]["discount"];
                                                                }
                                                                echo "<br/>";
                                                                echo "<span style='color:red;font-size:12pt;'><span style='font-family:Arial;'>¥</span>".$tmp_value."</span> ";
                                                                echo "<span style='text-decoration:line-through;'><span style='font-family:Arial;'>¥</span>".$swop->goods_transaction_times[$i]["price"]."</span>";
                                                                echo "</div>";
                                                            echo "</a>";
                                                        echo "</div>";
                                                }
                                                echo "</div>";
                                                echo "<div id='goods_latest' style='display:none;'>";
                                                for($i=0; $i<count($swop->goods_latest); $i++) {
                                                        $swop->goods_latest[$i]["images"] = json_decode($swop->goods_latest[$i]["images"]);
                                                        $swop->goods_latest[$i]["images"] = $swop->goods_latest[$i]["images"][0];
							echo "<div style='position:relative;padding:10px;border-bottom:1px solid #D2D2D2;'>";
                                                            echo "<a href='".$this->base['url']."goods?no=".$swop->goods_latest[$i]["fi_no"]."' style='color:#969B9C;'><img src='";
                                                            echo $this->base['thu'].$swop->goods_latest[$i]["images"];
                                                            echo "' style='width:80px; height:80px; vertical-align:top;margin-right:10px;'>";
                                                            echo "<div style='position:absolute;left:2px;top:2px;padding-left:1px;padding-top:1px;display:inline-block;text-align:center;background:#FB491F;width:20px;height:20px;color:white;-webkit-border-radius: 10px;-moz-border-radius: 10px;border-radius: 10px;'>".($i+1)."</div>";
                                                                echo "<div style='display:inline-block;width:110px;'>";
                                                                echo "<span>".$swop->goods_latest[$i]["name"]."</span>";
                                                                if($swop->goods_latest[$i]["discount"] == "0") {
                                                                        $tmp_value = $swop->goods_latest[$i]["promotions"];
                                                                }
                                                                else {
                                                                        $tmp_value = $swop->goods_latest[$i]["discount"];
                                                                }
                                                                echo "<br/>";
                                                                echo "<span style='color:red;font-size:12pt;'><span style='font-family:Arial;'>¥</span>".$tmp_value."</span> ";
                                                                echo "<span style='text-decoration:line-through;'><span style='font-family:Arial;'>¥</span>".$swop->goods_latest[$i]["price"]."</span>";
                                                                echo "</div>";
                                                            echo "</a>";
                                                        echo "</div>";
                                                }
                                                echo "</div>";
                                                echo "<div style='display:inline-block;width:100px;padding:15px 10px;text-align:center;font-szie:8pt;border-right:1px solid #D2D2D2;'>看所有商品</div>";
                                                echo "<div style='display:inline-block;width:80px;padding:15px 10px;cursor:pointer;text-align:center;font-size:8pt;'>";
                                                    echo "<img id='order_up' src='".$this->base["tpl"]."arrow_left.png"."' style='margin-right:20px;-ms-transform:rotate(90deg); -moz-transform:rotate(90deg); -webkit-transform:rotate(90deg); -o-transform:rotate(90deg); transform:rotate(90deg);'>";
                                                    echo "<img id='order_down' src='".$this->base["tpl"]."arrow_left.png"."' style='-ms-transform:rotate(270deg); -moz-transform:rotate(270deg); -webkit-transform:rotate(270deg); -o-transform:rotate(270deg); transform:rotate(270deg);'>";
                                                echo "</div>";
						?></div>
					<!--<div id="store_activity" style="width:221px;margin-top:20px;text-align:center;padding-bottom: 20px;border-top:1px solid #F2F2F2;border-left:1px solid #F2F2F2;border-right:1px solid #E2E2E2;border-bottom:1px solid #E2E2E2;"><?php
                                                echo "<div style='font-size:18pt;padding:15px 10px;text-align:center;border-bottom:1px solid #D2D2D2;margin-bottom:15px;'>商店活動</div>";
						for($i=0; $i<count($swop->store_advertisement); $i++) {
							switch($swop->store_advertisement[$i]['type']){
							case "3":
								$tmp_url = $this->base['url']."goods?no=".$swop->store_advertisement[$i]['item'];
								break;
							case "4":
								$tmp_url = $this->base['url']."brandstore?store=".$swop->store[0]['fi_no']."&bskeyword=".$swop->store_advertisement[$i]['item'];
								break;
							}
							echo "<a href='".$tmp_url."'><div><img src='".$this->base['sto'].$swop->store_advertisement[$i]['images']."' style='width:188px; height:58px;'></div></a>";
						}
						?></div>-->
					<div style="width:221px;margin-top:20px;text-align:center;border-top:1px solid #F2F2F2;border-left:1px solid #F2F2F2;border-right:1px solid #E2E2E2;border-bottom:1px solid #E2E2E2;"><?php
                                                echo "<div style='font-size:18pt;padding:15px 10px;text-align:center;border-bottom:1px solid #D2D2D2;margin-bottom:15px;'>瀏覽紀錄</div>";
                                                echo "<div id='product_navi_record'></div>";
                                                ?></div>
				</div>
				<div style="clear:both;"></div>
				
			</div>
		</div>

