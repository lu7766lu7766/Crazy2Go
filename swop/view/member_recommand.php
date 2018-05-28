
<div class="template_center">
    <div style="margin-top: 40px;padding-bottom:18px;border-bottom:1px solid #D4D4D4;">
        <div style="display:inline-block;width:208px;margin-right: 28px;"><span id="member_center" style="font-size:23pt;font-weight:bold">我的跨域瘋</span></div>
        <div style="display:inline-block;font-weight: bold;">當前的位置 > <?php echo "<a href='".$this->base["url"]."member/center'>我的跨域瘋</a>";?> > 為我推薦</div>
    </div>
    <div style="margin-top: 15px;">
        <div id="member_menu" style="display:inline-block;width:208px;border:1px solid #D4D4D4;margin-right: 28px;font-size:11pt;font-weight: bold;">
                            
        </div>
        <div style="display:inline-block;width:986px;float:right;margin-bottom:30px;">
            <?php
                $style1 = "cursor:pointer;display:inline-block;padding:5px 15px 10px 15px;margin:1px 4px 1px 0px;font-weight: bold;background:#FFFFFF;color:#000000;border-left:2px solid #D92F19;border-top:2px solid #D92F19;border-right:2px solid #D92F19;position:relative;top:3px;";
                $style2 = "cursor:pointer;display:inline-block;padding:5px 15px 10px 15px;margin:1px 4px 1px 0px;font-weight: bold;background:#F5F5F5;color:#838383;position:relative;top:1px;";
            ?>
            <div id="cate_select" style1="<?php echo $style1;?>" style2="<?php echo $style2;?>">
                <?php
                    $len = count($swop->tab);
                    for($i = 0;$i < $len;$i++)
                    {
                        echo "<div class='cate_select' cat='".$swop->tab[$i]["fi_no"]."' style='".$style2."'><img src='".$this->base["tpl"].$swop->tab[$i]["icon"]."' style='position:relative;top:5px;left:-2px;'>".$swop->tab[$i]["name"]."</div>";
                    }
                ?>
            </div>
            <div style="border-top:2px solid #D92F19;">
                <?php 
                $items = $swop->recommand;
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
        </div>
    </div>
</div>
