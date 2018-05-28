
		<div class="template_center">
                    <div style="margin-top: 40px;padding-bottom:18px;border-bottom:1px solid #D4D4D4;">
                        <div style="display:inline-block;width:208px;margin-right: 28px;"><span id="member_center" style="font-size:23pt;font-weight:bold">我的跨域瘋</span></div>
                        <div style="display:inline-block;font-weight: bold;">當前的位置 > <?php echo "<a href='".$this->base["url"]."member/center'>我的跨域瘋</a>";?></div>
                    </div>
                    <div style="margin-top: 15px;">
                        <div id="member_menu" style="display:inline-block;width:208px;border:1px solid #D4D4D4;margin-right: 28px;font-size:11pt;font-weight: bold;">
                            
                        </div>
                        <div style="display:inline-block;width:986px;float:right;margin-bottom:30px;">
                            <div style="display: inline-block;width:764px;height:177px;background: #FEF4F5;margin-right: 11px;float:left;border:1px solid #DCD8D7;">
                                <div style='display: inline-block;margin:40px 0px 40px 30px;'>
                                    <div style="display:inline-block;position:relative;">
                                        <?php echo $_SESSION['info']["picture"]==""?'<div id="member_picture_back" style="display:inline-block;border:5px solid #4D494A;position:absolute;left:0px;top:0px;width:80px;height:80px;line-height: 80px;text-align:center;-webkit-border-radius: 50px;-moz-border-radius: 50px;border-radius: 50px;">設置大頭照</div>':'';?>
                                        <img id="member_picture" src="<?php echo $this->base["mbr"].$_SESSION['info']["picture"]; ?>" style="border:5px solid #4D494A;width:80px;height:80px;-webkit-border-radius: 50px;-moz-border-radius: 50px;border-radius: 50px;">
                                        <div id="member_picture_cover" style="display:inline-block;position:absolute;left:0px;top:0px;width:90px;height:90px;line-height: 90px;background:rgba(0,0,0,0.5);color:white;text-align:center;-webkit-border-radius: 50px;-moz-border-radius: 50px;border-radius: 50px;">點擊換圖</div>
                                        <input id="member_picture_pick" type="file" style="position:absolute;left:0px;top:0px;width:90px;height:90px;opacity:0;cursor: pointer;">
                                    </div>
                                    <div style='display: inline-block;float:right;margin-left: 10px;'>
                                        <div style='font-weight: bold; font-size: 11pt;margin-top: 10px;margin-bottom: 10px;'><?php echo $_SESSION['info']["id"]; ?></div>
                                        <div style="position:relative;display:block;width:150px;">
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
                                                if($swop->mem_info[0]['evaluation_number'] != 0) {
                                                        $evaluation = number_format( ($swop->mem_info[0]['evaluation_score']/$swop->mem_info[0]['evaluation_number']), 1);
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
                                        </div>
                                        <br/>
                                        <div style='color:#969B9C;'>平均 <span><?php echo $evaluation;?></span> 分 / 總共 <?php echo $swop->mem_info[0]['evaluation_number'];?> 個評價</div>
                                    </div>
                                </div>
                                <div style='display:inline-block;float:right;width:330px;height:179px;'>
                                    <div class='table'style='margin-top: 35px;'>
                                        <!--<div class='tr'><div class='td' style='height:30px;font-weight:bold;'><img src="<?php echo $this->base["tpl"]."icon1.jpg";?>">　紅利積分</div><div class='td' style='color:#A9ABAA;'>我的積分：<?php echo $swop->sum_bonus;?>點｜<a href='<?php echo $this->base["url"]."member/bonus";?>' style="color:#A9ABAA;">兌換成跨寶通幣</a></div></div>-->
                                        <div class='tr'><div class='td' style='height:40px;font-weight:bold;'><img src="<?php echo $this->base["tpl"]."icon2.jpg";?>">　我的收藏</div><div class='td' style='color:#A9ABAA;'><a href='<?php echo $this->base["url"]."member/collect?type=goods&order=date&by=desc";?>' style="color:#A9ABAA;">收藏商品</a>｜<a href='<?php echo $this->base["url"]."member/collect?type=store&order=date&by=desc";?>' style="color:#A9ABAA;">收藏店家</a>｜<a href='<?php echo $this->base["url"]."member/collect?type=goods&order=date&by=desc";?>' style="color:#A9ABAA;">更多 <img src="<?php echo $this->base["tpl"]."gray.png";?>"></a></div></div>
                                        <div class='tr'><div class='td' style='height:40px;font-weight:bold;'><img src="<?php echo $this->base["tpl"]."icon3.jpg";?>">　帳號設置</div><div class='td' style='color:#A9ABAA;'><a href='<?php echo $this->base["url"]."member/address";?>' style="color:#A9ABAA;">修改地址</a>｜<a href='<?php echo $this->base["url"]."member/account";?>' style="color:#A9ABAA;">修改帳號</a>｜<a href='<?php echo $this->base["url"]."member/address";?>' style="color:#A9ABAA;">更多 <img src="<?php echo $this->base["tpl"]."gray.png";?>"></a></div></div>
                                        <div class='tr'><div class='td' style='height:40px;font-weight:bold;'><img src="<?php echo $this->base["tpl"]."icon4.jpg";?>">　我要申訴</div><div class='td' style='color:#A9ABAA;'><a href='<?php echo $this->base["url"]."member/appeal";?>' style="color:#A9ABAA;">聯繫客服</a></div></div>
                                    </div>
                                </div>
                            </div>
                            <div class="ad_tab" style="display: inline-block;width:208px;height:179px;background: #EEEEEE;">
                                <div style="background: white;padding:5px 0px 5px 0px;"><span style="font-weight:bold;">最新活動</span><span style="float:right"><img id="ad_left" src="<?php echo $this->base["tpl"]."myaccount_1-3.png";?>" style="cursor:pointer;position:relative;top:1px;-ms-transform:rotate(180deg); -moz-transform:rotate(180deg); -webkit-transform:rotate(180deg); -o-transform:rotate(180deg); transform:rotate(180deg);">　<img id="ad_right" src="<?php echo $this->base["tpl"]."myaccount_1-3.png";?>" style="cursor:pointer;"></span></div>
                                <div id="center_ad" style='position:relative;'>
                                    <?php
                                        $len = count($swop->ad);
                                        for($i = 0;$i<$len;$i++)
                                        {
                                            echo "<a href='".$swop->ad[$i]["url"]."' style='display:inline-block;position:absolute;'><img src='".$this->base["adm"].$swop->ad[$i]["images"]."' title='".$i."' style='width:208px;height:76px;'></a>";
                                        }
                                    ?>
                                </div>
                            </div>
                            <div style="display: inline-block;position:relative;width:983px;height:542px;background: #FCFCFC;border:1px solid #D4D4D4;margin-top: 30px;">
                                <div class="mem_panel" style="padding:20px 30px 20px 30px;width:431px;height:231px;border:1px solid #D4D4D4;position:absolute;top:-1px;left:-1px;">
                                    <div style="height:135px;border-bottom: 1px solid #D4D4D4;">
                                        <div><span style="font-weight: bold;"><img src="<?php echo $this->base["tpl"]."icon1.png";?>" style="position:relative;top:3px;"> 我的物流</span><a href="<?php echo $this->base["mem"]."order_sendout"?>"><span style="float: right;color:#A9ABAA;">更多商品 <img src="<?php echo $this->base["tpl"]."gray.png";?>"></span></a></div>
                                        <?php if($swop->logistic[0]){ ?> 
                                        <div style="height:70px;margin-top: 25px;">
                                            <a href="<?php echo $this->base["mem"].($swop->logistic[0]["status_receiving"]==1?"order_sendout":"order_confirm")."?sn=".$swop->logistic[0]["sn"]?>"><div style="float: left;padding-right: 10px;"><img src="<?php echo $this->base["sto"].$swop->logistic[0]["store_image"];?>" style="float:left;width:70px;height:70px;border:1px solid #D4D4D4;"></div></a>
                                            <span style="display:block;color:#A9ABAA;">
                                                <span style="color:#A9ABAA;">賣家：<?php echo $swop->logistic[0]["store_name"]?></span>｜<span>訂單編號：<?php echo $swop->logistic[0]["sn"];?></span><br/><br/>
                                                <span style="color:#A9ABAA;font-weight: bold;"><?php echo $swop->logistic[0]["status_receiving_name"]?></span><br/>
                                                <span style='color:#E0E0E0;font-size: 8pt;'><?php echo str_replace("/",".",substr($swop->logistic[0]["trace"],0,10));?> (最新更新)</span><br/>
                                                <div style="display:inline-block;cursor: pointer;float:right;width:55px;height:18px;border:1px solid #A9ABAA;font-size:8pt;font-weight: bold;color:black;text-align: center;-webkit-border-radius: 3px;-moz-border-radius: 3px;border-radius: 3px;"><a href="<?php echo $this->base["mem"].($swop->logistic[0]["status_receiving"]==1?"order_sendout":"order_confirm")."?sn=".$swop->logistic[0]["sn"]?>">查看明細</a></div>
                                            </span>
                                        </div>
                                        <?php }?>
                                    </div>
                                    <div style="height:95px;">
                                        <?php if($swop->logistic[1]){ ?> 
                                        <div style="height:70px;margin-top: 20px;">
                                            <a href="<?php echo $this->base["mem"].($swop->logistic[1]["status_receiving"]==1?"order_sendout":"order_confirm")."?sn=".$swop->logistic[1]["sn"]?>"><div style="float: left;padding-right: 10px;"><img src="<?php echo $this->base["sto"].$swop->logistic[1]["store_image"];?>" style="float:left;width:70px;height:70px;border:1px solid #D4D4D4;"></div></a>
                                            <span style="display:block;color:#A9ABAA;">
                                                <span style="color:#A9ABAA;">賣家：<?php echo $swop->logistic[1]["store_name"]?></span>｜<span>訂單編號：<?php echo $swop->logistic[1]["sn"];?></span><br/><br/>
                                                <span style="color:#A9ABAA;font-weight: bold;"><?php echo $swop->logistic[1]["status_receiving_name"]?></span><br/>
                                                <span style='color:#E0E0E0;font-size: 8pt;'><?php echo str_replace("/",".",substr($swop->logistic[1]["trace"],0,10));?> (最新更新)</span><br/>
                                                <div style="display:inline-block;cursor: pointer;float:right;width:55px;height:18px;border:1px solid #A9ABAA;font-size:8pt;font-weight: bold;color:black;text-align: center;-webkit-border-radius: 3px;-moz-border-radius: 3px;border-radius: 3px;"><a href="<?php echo $this->base["mem"].($swop->logistic[1]["status_receiving"]==1?"order_sendout":"order_confirm")."?sn=".$swop->logistic[1]["sn"]?>">查看明細</a></div>
                                            </span>
                                        </div>
                                        <?php }?>
                                    </div>
                                </div>
                                <div class="mem_panel" style="padding:20px 30px 20px 30px;width:431px;height:231px;border:1px solid #D4D4D4;position:absolute;top:-1px;left:491px;">
                                    <div style="height:135px;border-bottom: 1px solid #D4D4D4;">
                                        <div><span style="font-weight: bold;"><img src="<?php echo $this->base["tpl"]."icon4.png";?>" style="position:relative;top:3px;"> 我的購物車</span><span style="float: right;color:#A9ABAA;">近期優惠 <span style="color:red;font-weight: bold;"><?php echo $swop->discount_count;?></span>｜庫存緊張 <span style="color:red; font-weight: bold;"><?php echo $swop->inventory_low_count;?></span>｜所有商品</span></div>
                                        <?php if($swop->cart[0]){ ?> 
                                        <?php 
                                            $swop->cart[0]["images"] = json_decode($swop->cart[0]["images"],true);
                                            $swop->cart[0]["images"] = $swop->cart[0]["images"][0];
                                        ?> 
                                        <div style="height:70px;margin-top: 25px;">
                                            <a href="<?php echo $this->base["url"]."goods?no=".$swop->cart[0]["fi_no"];?>"><div style="float: left;padding-right: 10px;"><img src="<?php echo $this->base["thu"].$swop->cart[0]["images"]?>" style="float:left;width:70px;height:70px;border:1px solid #D4D4D4;"></div></a>
                                            <span style="color:#A9ABAA;"><?php echo $swop->cart[0]["name"]?></span>
                                            <span style="display:block;color:#A9ABAA;position:relative;top:20px;">
                                                <span style="color:red;font-weight: bold;"><?php echo "<span style='font-family:Arial;'>¥</span>".$swop->cart[0]["current_buy_price"];?></span> 
                                                <span style='color:#E0E0E0;font-size: 8pt;'><?php if(!$swop->cart[0]["inventory_low"])echo "原價 <span style='font-family:Arial;'>¥</span>".$swop->cart[0]["price"];?></span>
                                                <div style="display:inline-block;color:white;padding:0px;margin-left:10px;background:<?php echo $swop->cart[0]["inventory_low"]?"#DC393E url(".$this->base["tpl"]."red.jpg)":($swop->cart[0]["price_diff"]>0?"#B9CA9B url(".$this->base["tpl"]."green.jpg)":"#FFFFFF");?>"><?php echo $swop->cart[0]["inventory_low"]?"庫存緊張":($swop->cart[0]["price_diff"]>0?"節省".$swop->cart[0]["price_diff"]:"");?></div>
                                                <div style="display:inline-block;cursor: pointer;float:right;width:55px;height:18px;border:1px solid #A9ABAA;font-size:8pt;font-weight: bold;color:black;text-align: center;-webkit-border-radius: 3px;-moz-border-radius: 3px;border-radius: 3px;">前往結帳</div>
                                            </span>
                                        </div>
                                        <?php }?>
                                    </div>
                                    <div style="height:95px;">
                                        <?php if($swop->cart[1]){ ?> 
                                        <?php 
                                            $swop->cart[1]["images"] = json_decode($swop->cart[1]["images"],true);
                                            $swop->cart[1]["images"] = $swop->cart[1]["images"][0];
                                        ?> 
                                        <div style="height:70px;margin-top: 20px;">
                                            <a href="<?php echo $this->base["url"]."goods?no=".$swop->cart[1]["fi_no"];?>"><div style="float: left;padding-right: 10px;"><img src="<?php echo $this->base["thu"].$swop->cart[1]["images"]?>" style="float:left;width:70px;height:70px;border:1px solid #D4D4D4"></div></a>
                                            <span style="color:#A9ABAA;"><?php echo $swop->cart[1]["name"]?></span>
                                            <span style="display:block;color:#A9ABAA;position:relative;top:20px;">
                                                <span style="color:red;font-weight: bold;"><?php echo "<span style='font-family:Arial;'>¥</span>".$swop->cart[1]["current_buy_price"];?></span> 
                                                <span style='color:#E0E0E0;font-size: 8pt;'><?php if(!$swop->cart[1]["inventory_low"])echo "原價 <span style='font-family:Arial;'>¥</span>".$swop->cart[1]["price"];?></span>
                                                <div style="display:inline-block;color:white;padding:0px;margin-left:10px;background:<?php echo $swop->cart[1]["inventory_low"]?"#DC393E url(".$this->base["tpl"]."red.jpg)":($swop->cart[0]["price_diff"]>0?"#B9CA9B url(".$this->base["tpl"]."green.jpg)":"#FFFFFF");?>"><?php echo $swop->cart[1]["inventory_low"]?"庫存緊張":($swop->cart[1]["price_diff"]>0?"節省".$swop->cart[1]["price_diff"]:"");?></div>
                                                <div style="display:inline-block;cursor: pointer;float:right;width:55px;height:18px;border:1px solid #A9ABAA;font-size:8pt;font-weight: bold;color:black;text-align: center;-webkit-border-radius: 3px;-moz-border-radius: 3px;border-radius: 3px;">前往結帳</div>
                                            </span>
                                        </div>
                                        <?php }?>
                                    </div>
                                </div>
                                <div class="mem_panel" style="padding:20px 30px 20px 30px;width:431px;height:230px;border:1px solid #D4D4D4;position:absolute;top:271px;left:-1px;">
                                    <div style="height:135px;border-bottom: 1px solid #D4D4D4;">
                                        <div><span style="font-weight: bold;"><img src="<?php echo $this->base["tpl"]."icon2.png";?>" style="position:relative;top:3px;"> 我的收藏夾</span></div>
                                        <div style="height:70px;margin-top: 25px;">
                                            <!--<div style="float:left;width:96px;height:73px;padding-right: 10px;margin-top: 10px;">
                                                　<img src="<?php echo $this->base["tpl"]."shop_icon.png";?>"> 
                                                <span style="font-size: 8pt;font-weight:bold;">店鋪收藏</span>
                                                <div style='display:inline-block;cursor: pointer;padding:2px 0px 0px 5px;margin-top:5px;color:#A9ABAA;width:90px;height:18px;border:1px solid #A9ABAA;font-size:8pt;color:A9ABAA;-webkit-border-radius: 3px;-moz-border-radius: 3px;border-radius: 3px;'>查看所有店舖 <span style="color:red; font-weight: bold;"><?php echo $swop->collect_store_count;?></span></div>
                                            </div>
                                            <?php 
                                                if($swop->collect_store[0])
                                                {
                                                    $len = count($swop->collect_store);
                                                    for($i = 0;$i<$len;$i++)
                                                    {
                                                        echo '<a href="'.$this->base["url"].'brandstore?store='.$swop->collect_store[$i]["fi_no"].'">';
                                                        echo "<div data-fi_no='".$swop->collect_store[$i]["fi_no"]."' style='display:inline-block;padding-left:10px;width:70px;height:70px;position:relative;'>";
                                                        echo "<div style='position:absolute;bottom:0px;background:rgba(0,0,0,0.5);color:white;width:72px;text-align:center;font-size:8pt;padding:3px 0px 3px 0px;'>".mb_substr($swop->collect_store[$i]["name"], 0, 4, "utf-8")."</div>";
                                                        echo '<div style="float: left;padding-right: 10px;"><img src="'.$this->base["sto"].$swop->collect_store[$i]["images"].'" style="width:70px;height:70px;border:1px solid #D4D4D4;"></div>';
                                                        echo "</div>";
                                                        echo '</a>';
                                                    }
                                                }
                                            ?>-->
                                            <!--可刪-->
                                            <div style="float:left;width:96px;height:73px;padding-right: 10px;margin-top: 10px;">
                                                　<img src="<?php echo $this->base["tpl"]."product_icon.png";?>"> 
                                                <span style="font-size: 8pt;font-weight:bold;">商品收藏</span>
                                                <div style='display:inline-block;cursor: pointer;padding:2px 0px 0px 5px;margin-top:5px;color:#A9ABAA;width:100px;height:18px;border:1px solid #A9ABAA;font-size:8pt;color:A9ABAA;-webkit-border-radius: 3px;-moz-border-radius: 3px;border-radius: 3px;'>查看所有商品 <span style="color:red; font-weight: bold;"><?php echo $swop->collect_goods_count;?></span></div>
                                            </div>
                                            <?php 
                                                if($swop->collect_goods[0])
                                                {
                                                    $len = count($swop->collect_goods);
                                                    for($i = 0;$i<4;$i++)
                                                    {
                                                        if(!$swop->collect_goods[$i])continue;
                                                        $swop->collect_goods[$i]["images"] = json_decode($swop->collect_goods[$i]["images"],true);
                                                        $swop->collect_goods[$i]["images"] = $swop->collect_goods[$i]["images"][0];
                                                        echo '<a href="'.$this->base["url"].'goods?no='.$swop->collect_goods[$i]["fi_no"].'">';
                                                        echo "<div data-fi_no='".$swop->collect_goods[$i]["fi_no"]."' style='display:inline-block;padding-left:10px;width:70px;height:70px;position:relative;'>";
                                                        echo "<div style='position:absolute;bottom:0px;background:rgba(0,0,0,0.5);color:white;width:72px;text-align:center;font-size:8pt;padding:3px 0px 3px 0px;'>".mb_substr($swop->collect_goods[$i]["name"], 0, 4, "utf-8")."</div>";
                                                        echo '<div style="float: left;padding-right: 10px;"><img src="'.$this->base["thu"].$swop->collect_goods[$i]["images"].'" style="width:70px;height:70px;border:1px solid #D4D4D4;"></div>';
                                                        echo "</div>";
                                                        echo '</a>';
                                                    }
                                                }
                                            ?>
                                            <!--可刪-->
                                        </div>
                                    </div>
                                    <div style="height:94px;">
                                        <div style="height:70px;margin-top: 20px;">
                                            <!--<div style="float:left;width:96px;height:73px;padding-right: 10px;margin-top: 10px;">
                                                　<img src="<?php echo $this->base["tpl"]."product_icon.png";?>"> 
                                                <span style="font-size: 8pt;font-weight:bold;">商品收藏</span>
                                                <div style='display:inline-block;cursor: pointer;padding:2px 0px 0px 5px;margin-top:5px;color:#A9ABAA;width:90px;height:18px;border:1px solid #A9ABAA;font-size:8pt;color:A9ABAA;-webkit-border-radius: 3px;-moz-border-radius: 3px;border-radius: 3px;'>查看所有商品 <span style="color:red; font-weight: bold;"><?php echo $swop->collect_goods_count;?></span></div>
                                            </div>
                                            <?php 
                                                if($swop->collect_goods[0])
                                                {
                                                    $len = count($swop->collect_goods);
                                                    for($i = 0;$i<$len;$i++)
                                                    {
                                                        $swop->collect_goods[$i]["images"] = json_decode($swop->collect_goods[$i]["images"],true);
                                                        $swop->collect_goods[$i]["images"] = $swop->collect_goods[$i]["images"][0];
                                                        echo '<a href="'.$this->base["url"].'goods?no='.$swop->collect_goods[$i]["fi_no"].'">';
                                                        echo "<div data-fi_no='".$swop->collect_goods[$i]["fi_no"]."' style='display:inline-block;padding-left:10px;width:70px;height:70px;position:relative;'>";
                                                        echo "<div style='position:absolute;bottom:0px;background:rgba(0,0,0,0.5);color:white;width:72px;text-align:center;font-size:8pt;padding:3px 0px 3px 0px;'>".mb_substr($swop->collect_goods[$i]["name"], 0, 4, "utf-8")."</div>";
                                                        echo '<div style="float: left;padding-right: 10px;"><img src="'.$this->base["thu"].$swop->collect_goods[$i]["images"].'" style="width:70px;height:70px;border:1px solid #D4D4D4;"></div>';
                                                        echo "</div>";
                                                        echo '</a>';
                                                    }
                                                }
                                            ?>-->
                                            <!--可刪-->
                                            <div style="float:left;width:96px;height:73px;padding-right: 10px;margin-top: 10px;">
                                            </div>
                                            <?php 
                                                if($swop->collect_goods[4])
                                                {
                                                    $len = count($swop->collect_goods);
                                                    for($i = 4;$i<8;$i++)
                                                    {
                                                        if(!$swop->collect_goods[$i])continue;
                                                        //$swop->collect_goods[$i]["images"] = json_decode($swop->collect_goods[$i]["images"],true);
                                                        //$swop->collect_goods[$i]["images"] = $swop->collect_goods[$i]["images"][0];
                                                        echo '<a href="'.$this->base["url"].'goods?no='.$swop->collect_goods[$i]["fi_no"].'">';
                                                        echo "<div data-fi_no='".$swop->collect_goods[$i]["fi_no"]."' style='display:inline-block;padding-left:10px;width:70px;height:70px;position:relative;'>";
                                                        echo "<div style='position:absolute;bottom:0px;background:rgba(0,0,0,0.5);color:white;width:72px;text-align:center;font-size:8pt;padding:3px 0px 3px 0px;'>".mb_substr($swop->collect_goods[$i]["name"], 0, 4, "utf-8")."</div>";
                                                        echo '<div style="float: left;padding-right: 10px;"><img src="'.$this->base["thu"].$swop->collect_goods[$i]["images"].'" style="width:70px;height:70px;border:1px solid #D4D4D4;"></div>';
                                                        echo "</div>";
                                                        echo '</a>';
                                                    }
                                                }
                                            ?>
                                            <!--可刪-->
                                        </div>
                                    </div>
                                </div>
                                <div class="mem_panel" style="padding:20px 30px 20px 30px;width:431px;height:230px;border:1px solid #D4D4D4;position:absolute;top:271px;left:491px;">
                                    <div style="height:135px;border-bottom: 1px solid #D4D4D4;">
                                        <div><span style="font-weight: bold;"><img src="<?php echo $this->base["tpl"]."icon3.png";?>" style="position:relative;top:3px;"> 瀏覽記錄</span><span style="float: right;color:#A9ABAA;">看更多 <img src="<?php echo $this->base["tpl"]."gray.png";?>"></span></div>
                                        <div style="height:70px;margin-top: 25px;">
                                            <?php if($swop->cookie[0]){ ?>
                                            <?php
                                                $swop->cookie[0]["images"] = json_decode($swop->cookie[0]["images"],true);
                                                $swop->cookie[0]["images"] = $swop->cookie[0]["images"][0];
                                            ?>
                                            <a href="<?php echo $this->base["url"]."goods?no=".$swop->cookie[0]["fi_no"];?>"><div style="float: left;padding-right: 10px;"><img src="<?php echo $this->base["thu"].$swop->cookie[0]["images"];?>" style="float:left;width:70px;height:70px;border:1px solid #D4D4D4;"></div></a>
                                            <span style='color:#A9ABAA;'>賣家：<?php echo $swop->cookie[0]["store_name"];?></span>
                                            <div style="position:relative;display:inline-block;margin-bottom: 20px;">
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
                                                    if($swop->cookie[0]['evaluation_number'] != 0) {
                                                            $evaluation = number_format( ($swop->cookie[0]['evaluation_score']/$swop->cookie[0]['evaluation_number']), 1);
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
                                                <div style='color:#A9ABAA;margin-left: 120px;'>平均 <span><?php echo $evaluation;?></span> 分 / 總共 <?php echo $swop->cookie[0]['evaluation_number'];?> 個評價</div>
                                            </div>
                                            <span style='display:block;color:#A9ABAA;'><?php echo mb_substr($swop->cookie[0]["name"], 0, 20,"utf-8")."...";?><div class="add_goods_collect" fi_no="<?php echo$swop->cookie[0]["fi_no"];?>" style='display:inline-block;cursor: pointer;float:right;width:55px;height:18px;border:1px solid #A9ABAA;font-size:8pt;font-weight: bold;color:black;text-align: center;-webkit-border-radius: 3px;-moz-border-radius: 3px;border-radius: 3px;'>加入收藏</div></span>
                                            <?php } ?>
                                        </div>
                                    </div>
                                    <div style="height:94px;">
                                        <div style="height:70px;margin-top: 20px;">
                                            <?php if($swop->cookie[1]){ ?>
                                            <?php
                                                $swop->cookie[1]["images"] = json_decode($swop->cookie[1]["images"],true);
                                                $swop->cookie[1]["images"] = $swop->cookie[1]["images"][0];
                                            ?>
                                            <a href="<?php echo $this->base["url"]."goods?no=".$swop->cookie[1]["fi_no"];?>"><div style="float: left;padding-right: 10px;"><img src="<?php echo $this->base["thu"].$swop->cookie[1]["images"];?>" style="float:left;width:70px;height:70px;border:1px solid #D4D4D4;"></div></a>
                                            <span style='color:#A9ABAA;'>賣家：<?php echo $swop->cookie[1]["store_name"];?></span>
                                            <div style="position:relative;display:inline-block;margin-bottom: 20px;">
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
                                                    if($swop->cookie[1]['evaluation_number'] != 0) {
                                                            $evaluation = number_format( ($swop->cookie[1]['evaluation_score']/$swop->cookie[1]['evaluation_number']), 1);
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
                                                <div style='color:#A9ABAA;margin-left: 120px;'>平均 <span><?php echo $evaluation;?></span> 分 / 總共 <?php echo $swop->cookie[1]['evaluation_number'];?> 個評價</div>
                                            </div>
                                            <span style='display:block;color:#A9ABAA;'><?php echo mb_substr($swop->cookie[1]["name"], 0, 20,"utf-8")."...";?><div class="add_goods_collect" fi_no="<?php echo$swop->cookie[1]["fi_no"];?>" style='display:inline-block;cursor: pointer;float:right;width:55px;height:18px;border:1px solid #A9ABAA;font-size:8pt;font-weight: bold;color:black;text-align: center;-webkit-border-radius: 3px;-moz-border-radius: 3px;border-radius: 3px;'>加入收藏</div></span>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div style="position:relative; display: inline-block;width:983px;height:257px;background: #F7F7F7;border:1px solid #D4D4D4;margin-top: 13px;">
                                <div id='product_hot' style="padding:20px 30px 20px 30px;width:431px;height:230px;position:absolute;top:-1px;left:-1px;">
                                    <div><span style="font-weight: bold;">為我推薦</span><span style="float: right;color:#A9ABAA;"><img class='product_left' src="<?php echo $this->base["tpl"]."myaccount_1-3.png";?>" style="cursor:pointer;position:relative;top:1px;-ms-transform:rotate(180deg); -moz-transform:rotate(180deg); -webkit-transform:rotate(180deg); -o-transform:rotate(180deg); transform:rotate(180deg);">　<img class='product_right' src="<?php echo $this->base["tpl"]."myaccount_1-3.png";?>" style="cursor:pointer;"></span></div>
                                    <div style="float: left; display: inline-block;width:80px;height:70px;margin-top: 10px;"><div style="display: inline-block;width:60px;"><img src="<?php echo $this->base["tpl"]."type_hot.png";?>"></div></div>
                                    <div style="float: right; display: inline-block;margin-top: 20px;width:340px;height:180px">
                                        <div class='product_list_up' style="height:90px;border-bottom: 1px solid #D4D4D4;"></div>
                                        <div class='product_list_down' style="height:90px;padding-top: 20px;"></div>
                                        <div class='product_show' style='display: none;'>
                                            <?php 
                                                if($swop->hot[0])
                                                {
                                                    $len = count($swop->hot);
                                                    for($i = 0;$i<$len;$i++)
                                                    {
                                                        $swop->hot[$i]["images"] = json_decode($swop->hot[$i]["images"]);
                                                        $swop->hot[$i]["images"] = $swop->hot[$i]["images"][0];
                                                        echo '<a href="'.$this->base["url"].'goods?no='.$swop->hot[$i]["fi_no"].'">';
                                                        echo "<div data-fi_no='".$swop->hot[$i]["fi_no"]."' style='display:inline-block;padding-left:15px;width:70px;height:70px;position:relative;'>";
                                                        echo "<div style='position:absolute;bottom:0px;background:rgba(0,0,0,0.5);color:white;width:72px;text-align:center;font-size:8pt;padding:3px 0px 3px 0px;'>".mb_substr($swop->hot[$i]["name"], 0, 4, "utf-8")."</div>";
                                                        echo '<div style="float: left;padding-right: 10px;"><img src="'.$this->base["thu"].$swop->hot[$i]["images"].'" style="width:70px;height:70px;border:1px solid #D4D4D4;"></div>';
                                                        echo "</div>";
                                                        echo '</a>';
                                                    }
                                                }
                                            ?>
                                        </div>
                                    </div>
                                </div>
                                <div style="width:0px;height:192px;position:absolute;top:36px;left:491px;border-right:1px solid #D4D4D4;"></div>
                                <div id='product_guess' style="padding:20px 30px 20px 30px;width:431px;height:230px;position:absolute;top:-1px;left:491px;">
                                    <div><span style="font-weight: bold;opacity: 0;">.</span><span style="float: right;color:#A9ABAA;"><img class='product_left' src="<?php echo $this->base["tpl"]."myaccount_1-3.png";?>" style="cursor:pointer;position:relative;top:1px;-ms-transform:rotate(180deg); -moz-transform:rotate(180deg); -webkit-transform:rotate(180deg); -o-transform:rotate(180deg); transform:rotate(180deg);">　<img class='product_right' src="<?php echo $this->base["tpl"]."myaccount_1-3.png";?>" style="cursor:pointer;"></span></div>
                                    <div style="float: left; display: inline-block;width:80px;height:70px;margin-top: 10px;"><div style="display: inline-block;width:60px;"><img src="<?php echo $this->base["tpl"]."type_youlike.png";?>"></div></div>
                                    <div style="float: right; display: inline-block;margin-top: 20px;width:340px;height:180px">
                                        <div class='product_list_up' style="height:90px;border-bottom: 1px solid #D4D4D4;"></div>
                                        <div class='product_list_down' style="height:90px;padding-top: 20px;"></div>
                                        <div class='product_show' style='display: none;'>
                                            <?php 
                                                if($swop->guess[0])
                                                {
                                                    $len = count($swop->guess);
                                                    
                                                    for($i = 0;$i<$len;$i++)
                                                    {
                                                        $swop->guess[$i]["images"] = json_decode($swop->guess[$i]["images"]);
                                                        $swop->guess[$i]["images"] = $swop->guess[$i]["images"][0];
                                                        echo '<a href="'.$this->base["url"].'goods?no='.$swop->guess[$i]["fi_no"].'">';
                                                        echo "<div data-fi_no='".$swop->guess[$i]["fi_no"]."' style='display:inline-block;padding-left:15px;width:70px;height:70px;position:relative;'>";
                                                        echo "<div style='position:absolute;bottom:0px;background:rgba(0,0,0,0.5);color:white;width:72px;text-align:center;font-size:8pt;padding:3px 0px 3px 0px;'>".mb_substr($swop->guess[$i]["name"], 0, 4, "utf-8")."</div>";
                                                        echo '<div style="float: left;padding-right: 10px;"><img src="'.$this->base["thu"].$swop->guess[$i]["images"].'" style="width:70px;height:70px;border:1px solid #D4D4D4;"></div>';
                                                        echo "</div>";
                                                        echo '</a>';
                                                    }
                                                }
                                            ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
		</div>
