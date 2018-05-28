<div class="template_center">
    <div style="margin-top: 40px;padding-bottom:18px;border-bottom:1px solid #D4D4D4;">
        <div style="display:inline-block;width:208px;margin-right: 28px;"><span id="member_center" style="font-size:23pt;font-weight:bold">我的跨域瘋</span></div>
        <div style="display:inline-block;font-weight: bold;">當前的位置 > <?php echo "<a href='".$this->base["url"]."member/center'>我的跨域瘋</a>";?> > 瀏覽紀錄</div>
    </div>
    <div style="margin-top: 15px;">
        <div id="member_menu" style="display:inline-block;width:208px;border:1px solid #D4D4D4;margin-right: 28px;font-size:11pt;font-weight: bold;">
                            
        </div>
        <div style="display:inline-block;width:986px;float:right;margin-bottom:30px;line-height: 50px;">
            <div style="background:#F5F5F5;border:1px solid #D4D4D4;height:35px;padding:0px 5px;">
                <?php
                    echo "<div style='display:inline-block;line-height:35px;'>";
                    echo '<input id="select_all" name="select_all" type="checkbox" class="icheckbox"><label for="select_all" style="line-height:17px;" > 全選 </label>&nbsp;';
                    echo '<input id="historylog_delete" type="button" value="刪除" style="cursor:pointer;height:18px;border:1px solid #DDD9DA;font-size:8pt;background:white;color:black;text-align: center;-webkit-border-radius: 3px;-moz-border-radius: 3px;border-radius: 3px;">&nbsp;';
                    echo '<input id="add_favorite" type="button" value="加入收藏夾" style="cursor:pointer;height:18px;border:1px solid #DDD9DA;font-size:8pt;background:white;color:black;text-align: center;-webkit-border-radius: 3px;-moz-border-radius: 3px;border-radius: 3px;">&nbsp;';
                    echo "&nbsp;</div>";
                    
                    echo "<div style='float:right; display:inline-block;line-height:35px;height:35px;'>";
                    
                    echo '<div style="display:inline-block;border:1px solid #DCD8D9;"><input id="search_text" type="text" placeholder="請輸入欲搜尋商品名稱" style="width:160px;height:23px;background:white;border:0px;"><input id="search_btn" type="button" value="搜尋" style="width:40px;height:26px;background:#EB3339;border:0px;cursor:pointer;color:white;"></div>&nbsp;';
                    echo '<div style="float:right;border:1px solid #DCD8D9;height:26px;line-height:26px;margin-top:4px;width:95px;background:white;">'
                            . '<div id="page_display" style="float:left;text-align:center;height:26px;width:53px;">1/25</div>'
                            . '<div id="page_goback" style="float:left;cursor:pointer;text-align:center;height:26px;width:20px;border-left:1px solid #DCD8D9;"><img src="'.$this->base["god"].'arrow_deepgray.png" style="margin-top:9px;-ms-transform:rotate(270deg); -moz-transform:rotate(270deg); -webkit-transform:rotate(270deg); -o-transform:rotate(270deg); transform:rotate(270deg);"></div>'
                            . '<div id="page_goforward" style="float:left;cursor:pointer;text-align:center;height:26px;width:20px;border-left:1px solid #DCD8D9;"><img src="'.$this->base["god"].'arrow_deepgray.png" style="margin-top:9px;-ms-transform:rotate(90deg); -moz-transform:rotate(90deg); -webkit-transform:rotate(90deg); -o-transform:rotate(90deg); transform:rotate(90deg);"></div>'
                       . '</div>&nbsp;';
                    echo "</div>"
                ?>
            </div>
            <?php 
                $items = $swop->history_goods;
                $len = count($items);
                $trs = ceil($len/5);
                $k = 0;
                if($len>0)
                {
                    echo "<div id=wrapper style='text-align:center;'>";
                    echo "<div class='table' style='line-height:20px;'>";
                    for($i = 0; $i < $trs; $i++)
                    {
                        echo $i==0?"<div class='tr'>":"<div class='tr' style='border-top:1px solid #D4D4D4;'>";
                        $kLen = $k+5;
                        for($j = $k; $j < $kLen; $j++)
                        {
                            echo $j == $k?"<div class='td' style='width:175px;'>":"<div class='td' style='width:225px;text-align:right;'>";
                            if($items[$j])
                            {
                                $items[$j]["images"] = json_decode($items[$j]["images"]);
                                $items[$j]["images"] = $items[$j]["images"][0];
                                echo "<div class='show_item' fi_no='".$items[$j]["fi_no"]."' historylog_id='".$items[$j]["historylog_id"]."' isChecked='0' style='display:inline-block;border:1px solid white;margin:20px 0px;text-align:left;'>";
                                
                                echo "<img src='".$this->base["thu"].$items[$j]["images"]."' style='width:173px;height:173px;border:1px solid #D4D4D4;cursor:pointer;'>";
                                echo "<div><a href='".$this->base['url']."goods?no=".$items[$j]["fi_no"]."' target='_blank'>".mb_substr($items[$j]["name"], 0, 10,"utf-8")."...<br/></a></div>";
                                echo "<div style='color:red;'><span style='font-family:Arial;'>¥</span>".$items[$j]["promotions"]."<input class='add_cart' fi_no='".$items[$j]["fi_no"]."' type='button' value='加入購物車' style='cursor:pointer;float:right;background:#EC343A;color:white;border:0px;-webkit-border-radius: 3px;-moz-border-radius: 3px;border-radius: 3px;'></div>";
                                
                                echo "</div>";
                            }
                            echo "</div>";
                        }
                        echo "</div>";
                        if($kLen > $len)break;
                        $k+=5;
                    }
                    echo "</div>";
                }
                echo "<div>".$page->page_content."</div>";
                echo "</div>";//id=wrapper
            ?>
        </div>
        <!--delete-->
        <div style='float:left;'>
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
                                                                                <?php 
                                                                                    $swop->love[$i-1]['images'] = json_decode($swop->love[$i-1]['images']);
                                                                                    $swop->love[$i-1]['images'] = $swop->love[$i-1]['images'][0];
                                                                                ?>
										<img src="<?php echo $this->base["thu"].$swop->love[$i-1]['images'];?>" style="width:115px; height:115px; border-radius:5px;">
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
        </div>    
        <!--delete-->
    </div>
</div>
