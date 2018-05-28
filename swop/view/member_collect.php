<div class="template_center">
    <div style="margin-top: 40px;padding-bottom:18px;border-bottom:1px solid #D4D4D4;position:relative;">
        <div style="display:inline-block;width:208px;margin-right: 28px;"><span id="member_center" style="font-size:23pt;font-weight:bold">我的跨域瘋</span></div>
        <div style="display:inline-block;font-weight: bold;">當前的位置 > <?php echo "<a href='".$this->base["url"]."member/center'>我的跨域瘋</a>";?> > 我的收藏</div>
        <div style="display:inline-block;position:absolute;bottom:18px;right:0px;">
            <?php 
                if($_GET["type"]=="goods")
                {
                    //echo '<a href="'.$this->base["url"].'member/collect?type=store&order=date&by=desc"><img src="'.$this->base["tpl"].'shop_icon.png" style="position:relative;top:6px;"> <div style="display:inline-block;cursor: pointer;padding:0px 3px;height:18px;border:1px solid #A9ABAA;font-size:8pt;font-weight: bold;color:black;text-align: center;-webkit-border-radius: 3px;-moz-border-radius: 3px;border-radius: 3px;">店鋪收藏 '.$swop->store_num.'</div></a>';
                    echo '　<img src="'.$this->base["tpl"].'product_icon2.png" style="position:relative;top:6px;"> <div style="display:inline-block;cursor: pointer;padding:0px 3px;height:18px;border:1px solid #FFFFFF;font-size:8pt;font-weight: bold;color:white;background:#E93439;text-align: center;-webkit-border-radius: 3px;-moz-border-radius: 3px;border-radius: 3px;">商品收藏 '.$swop->goods_num.'</div>';
                }
                /*else if($_GET["type"]=="store")
                {
                    echo '<img src="'.$this->base["tpl"].'shop_icon2.png" style="position:relative;top:6px;"> <div style="display:inline-block;cursor: pointer;padding:0px 3px;height:18px;border:1px solid #FFFFFF;font-size:8pt;font-weight: bold;color:white;background:#E93439;text-align: center;-webkit-border-radius: 3px;-moz-border-radius: 3px;border-radius: 3px;">店鋪收藏 '.$swop->store_num.'</div>';
                    echo '　<a href="'.$this->base["url"].'member/collect?type=goods&order=date&by=desc"><img src="'.$this->base["tpl"].'product_icon.png" style="position:relative;top:6px;"> <div style="display:inline-block;cursor: pointer;padding:0px 3px;height:18px;border:1px solid #A9ABAA;font-size:8pt;font-weight: bold;color:black;text-align: center;-webkit-border-radius: 3px;-moz-border-radius: 3px;border-radius: 3px;">商品收藏 '.$swop->goods_num.'</div></a>';
                }*/
            ?>
        </div>
    </div>
    <div style="margin-top: 15px;">
        <div id="member_menu" style="display:inline-block;width:208px;border:1px solid #D4D4D4;margin-right: 28px;font-size:11pt;font-weight: bold;">
                            
        </div>
        <div style="display:inline-block;width:986px;float:right;margin-bottom:30px;line-height: 50px;">
            <div style="background:#F5F5F5;border:1px solid #D4D4D4;height:35px;padding:0px 5px;">
                <?php
                    
                    echo "<div style='display:inline-block;line-height:35px;'>";
                    if($_GET["order"]=="date")
                    {
                        echo    '<div style="text-align:center;display:inline-block; width:62px; line-height:23px; background:#ffffff; border:1px solid #d1cfd0;">'
                            . '<a href="" style="text-decoration:blink; color:#EB313A;">時間<img src="'.$this->base["god"].'arrow_red.png" style="margin-left:3px; position:relative; top:1px;"></a>'
                            . '</div>';
                        echo    '<div style="text-align:center;display:inline-block; width:62px; line-height:23px; background:#ffffff; border:1px solid #d1cfd0;">'
                            . '<a href="'.$this->base["url"].'member/collect?type='.$_GET["type"].'&order=price&by=desc&page=1" style="text-decoration:blink; color:#848484;">價錢<img src="'.$this->base["god"].'arrow_gray2.png" style="margin-left:3px; position:relative; top:1px;"></a>'
                            . '</div>';
                    }
                    else if($_GET["order"]=="price")
                    {
                        echo    '<div style="text-align:center;display:inline-block; width:62px; line-height:23px; background:#ffffff; border:1px solid #d1cfd0;">'
                            . '<a href="'.$this->base["url"].'member/collect?type='.$_GET["type"].'&order=date&by=desc&page=1" style="text-decoration:blink; color:#848484;">時間<img src="'.$this->base["god"].'arrow_gray2.png" style="margin-left:3px; position:relative; top:1px;"></a>'
                            . '</div>';
                        echo    '<div style="text-align:center;display:inline-block; width:62px; line-height:23px; background:#ffffff; border:1px solid #d1cfd0;">'
                            . '<a href="" style="text-decoration:blink; color:#EB313A;">價錢<img src="'.$this->base["god"].'arrow_red.png" style="margin-left:3px; position:relative; top:1px;"></a>'
                            . '</div>';
                    }
                    echo "&nbsp;</div>";
                    
                    echo "<div style='float:right; display:inline-block;line-height:35px;height:35px;'>";
                    echo '<input id="select_all" name="select_all" type="checkbox" class="icheckbox"><label for="select_all" style="line-height:17px;" > 全選 </label>&nbsp;';
                    echo '<input id="collect_delete" type="button" value="刪除" style="cursor:pointer;height:18px;border:1px solid #DDD9DA;font-size:8pt;background:white;color:black;text-align: center;-webkit-border-radius: 3px;-moz-border-radius: 3px;border-radius: 3px;">&nbsp;';
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
                if($_GET["type"]=="goods")
                {
                    $items = $swop->collect_goods;
                }
                else if($_GET["type"]=="store")
                {
                    $items = $swop->collect_store;
                }
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
                                echo "<div class='show_item' fi_no='".$items[$j]["fi_no"]."' ctype='".$_GET["type"]."' collect_id='".$items[$j]["collect_id"]."' isChecked='0' style='display:inline-block;border:1px solid white;margin:20px 0px;text-align:left;'>";
                                switch($_GET["type"])
                                {
                                    case "goods":
                                        echo "<img src='".$this->base["thu"].$items[$j]["images"]."' style='width:173px;height:173px;border:1px solid #D4D4D4;cursor:pointer;'>";
                                        echo "<div><a href='".$this->base['url']."goods?no=".$items[$j]["fi_no"]."' target='_blank'>".mb_substr($items[$j]["name"], 0, 10,"utf-8")."...<br/></a></div>";
                                        echo "<div style='color:red;'><span style='font-family:Arial;'>¥</span>".$items[$j]["promotions"]."<input class='add_cart' fi_no='".$items[$j]["fi_no"]."' type='button' value='加入購物車' style='cursor:pointer;float:right;background:#EC343A;color:white;border:0px;-webkit-border-radius: 3px;-moz-border-radius: 3px;border-radius: 3px;'></div>";
                                        break;
                                    case "store":
                                        echo "<img src='".$this->base["thu"].$items[$j]["images"]."' style='width:173px;height:173px;border:1px solid #D4D4D4;cursor:pointer;'>";
                                        echo "<div><a href='".$this->base['url']."brandstore?store=".$items[$j]["fi_no"]."' target='_blank'>".mb_substr($items[$j]["name"], 0, 10,"utf-8")."...<br/></a></div>";
                                        echo "<div><input class='add_cart' fi_no='".$items[$j]["fi_no"]."' type='button' value='加入購物車' style='cursor:pointer;float:right;background:#EC343A;color:white;border:0px;-webkit-border-radius: 3px;-moz-border-radius: 3px;border-radius: 3px;'></div>";
                                        break;
                                }
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
    </div>
</div>
