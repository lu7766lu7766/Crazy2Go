
<div class="template_center">
    <div style="margin-top: 40px;padding-bottom:18px;border-bottom:1px solid #D4D4D4;">
        <div style="display:inline-block;width:208px;margin-right: 28px;"><span id="member_center" style="font-size:23pt;font-weight:bold">我的跨域瘋</span></div>
        <div style="display:inline-block;font-weight: bold;">當前的位置 > <?php echo "<a href='".$this->base["url"]."member/center'>我的跨域瘋</a>";?> > 紅利積分兌換</div>
    </div>
    <div style="margin-top: 15px;">
        <div id="member_menu" style="display:inline-block;width:208px;border:1px solid #D4D4D4;margin-right: 28px;font-size:11pt;font-weight: bold;">
                            
        </div>
        <div style="display:inline-block;width:986px;float:right;margin-bottom:30px;">
            <div style="height:177px;background: #FEF4F5;border:1px solid #DCD8D7;">
                <div style='display: inline-block;margin:40px 0px 40px 30px;'>
                    <img src="<?php echo $this->base["mbr"].$_SESSION['info']["picture"]; ?>" style="border:5px solid #4D494A;width:80px;height:80px;-webkit-border-radius: 50px;-moz-border-radius: 50px;border-radius: 50px;">
                    <div style='display: inline-block;float:right;margin-left: 10px;'>
                        <div style='font-weight: bold; font-size: 11pt;margin-top: 10px;margin-bottom: 10px;'><?php echo $_SESSION['info']["name"].$_SESSION['info']["id"]; ?></div>
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
                <div style="display:inline-block;margin-top:80px;margin-left:45px;width:590px;border-left:1px solid #DCD8D7;text-align: center;">
                    <div style="display:inline-block;width:140px;"><span style="color:#858384">累積積分</span><br/><span id="total_bonus" style="color:#EB3339;font-weight: bold;font-size:15pt;"><?php echo $swop->sum_bonus;?></span>　點</div>
                    <div style="display:inline-block;position:relative;bottom:10px;">
                        <div style="display:inline-block;">
                            <input id="currency2bonus_value" type="text" placeholder="請輸入欲兌換紅利積分" style="border:1px solid #DCD8D9;width:160px;height:26px;background:white;">
                            <input id="currency2bonus" type="button" value="" style="position:relative;left:-10px;vertical-align:bottom;width:123px;height:31px;background:url(<?php echo $this->base['tpl']."button_change.png";?>);border:0px;cursor:pointer;color:white;">
                        </div>
                    </div>
                    <div style="display:inline-block;width:150px;"><span style="color:#858384">已兌換快寶通幣</span><br/><span id="total_currency" style="color:#EB3339;font-weight: bold;font-size:15pt;"><?php echo $swop->sum_currency;?></span>　
                        元</div>
                </div>
            </div>
            <div style="padding:20px 0px;">
                <span style="font-size:12pt;">紅利積分明細</span>
                <?php echo "<div style='float:right;'>".$page->page_content."</div>"; ?>
            </div>
            
            <?php 
                $content = '';
                for($i=0; $i<count($swop->rows); $i++) {
                        $content = $content.'<div class="tr" style="border-top:1px solid #E5E5E5;">';
                        $content = $content.'<div class="td" style="padding:5px;">'.$swop->rows[$i]['source'].'</div>';
                        $content = $content.'<div class="td" style="padding:5px;">'.$swop->rows[$i]['sn'].'</div>';
                        $content = $content.'<div class="td" style="padding:5px;">'.($swop->rows[$i]['increase']!=0?"+".$swop->rows[$i]['increase']:"-".$swop->rows[$i]['reduce']).'</div>';
                        $content = $content.'<div class="td" style="padding:5px;">'.substr($swop->rows[$i]['date'],0,10).'</div>';
                        $content = $content.'<div class="td" style="padding:5px;">'.$swop->rows[$i]['remark'].'</div>';
                        $content = $content.'</div>';
                }
            ?>
            
            <div id="address_table" class="table" style="line-height: 40px;border:1px solid #E5E5E5;margin-bottom:30px;">
                    <div id="trHeader" class="tr" style="background:#FAFEFF;">
                            <div class="td" style="padding:5px;">紅利積分來源</div>
                            <div class="td" style="padding:5px;">訂單編號</div>
                            <div class="td" style="padding:5px;">紅利積分點數</div>
                            <div class="td" style="padding:5px;">日期</div>
                            <div class="td" style="padding:5px;">備註</div>
                    </div>
                    <?php echo $content; ?>
            </div>
            <div>
                <div class="bonus_hint" style="display:inline-block;padding:10px 15px 10px 15px;margin:1px 0px;font-weight: bold;cursor:pointer;background:#FFFFFF;text-align:center;width:293px;color:#E93437;border-left:2px solid #D92F19;border-top:2px solid #D92F19;border-right:2px solid #D92F19;position:relative;top:3px;">如何獲得紅利積分</div>
                <div class="bonus_hint" style="display:inline-block;padding:10px 15px 10px 15px;margin:1px 0px;font-weight: bold;cursor:pointer;background:#F5F5F5;text-align:center;width:295px;color:#838383;position:relative;top:1px;">紅利積分使用方式＆兌換方式</div>
                <div class="bonus_hint" style="display:inline-block;padding:10px 15px 10px 15px;margin:1px 0px;font-weight: bold;cursor:pointer;background:#F5F5F5;text-align:center;width:295px;color:#838383;position:relative;top:1px;">紅利積分注意事項</div>
                <div class="bonus_hint_content" style="display:none;">
                        <p>一. 凡在跨域瘋商城上購物, 每購物滿 RMB＄100 即可獲得 10點 的紅利績分點數. </p>
                        <p>二. 購買商品後, 凡在跨域通商城中給予購買商品有效的評價, 系統將自動核給 1點積分.  同一商品只能評價一次. 且只能在 15天內進行評價.</p>
                        <p>三. 新增會員時, 跨域瘋商城即贈送超值 10,000點新會員紅利, 並立即撥付入會員帳戶中, 
                                但此新會員紅利, 在每次商城消費後(注意: 每次購物須滿200元), 自動依次撥給會員紅利 40點. </p>
                        <p>四. 凡消費該月累計消費滿 RMB$2,000 , 從該次以後之消費紅利積點即加倍贈送.</p>
                        <p>五. 會員生日當天消費, 紅利加倍. ( 請務必在會員資料中, 填妥您的生日 ) </p>
                        <p>六. 紅利積分可以在 我的跨域瘋帳戶 查詢餘額. </p>
                </div>
                <div class="bonus_hint_content" style="display:none;">
                        <p>一. 紅利積分須經兌換 跨寶通儲值, 才能折抵購物金.</p>
                        <p>二. 每20點紅利兌換跨寶通儲值 1元 , 跨寶通儲值可以在購物結帳時抵扣.</p>
                        <p>三. 跨寶通儲值可以在 我的跨域瘋帳戶 查詢餘額或 將紅利兌換成跨寶通儲值 . </p>
                </div>
                <div class="bonus_hint_content" style="display:none;">
                        <p>一. 紅利積分伴隨會員資格永久留底存在, 當會員資格取消時, 則紅利與跨寶通儲值 也會一併取消.</p>
                        <p>二. 紅利積分點數專屬各會員帳戶個別使用, 不能移轉 .</p>
                        <p>三. 紅利積分使用, 請在 跨域瘋帳戶 先兌換成 跨寶通儲值 購物時, 才能抵購 購物金.</p>
                        <p>四. 紅利積分一經兌換成  跨寶通儲值 , 無法取消轉回.</p>
                </div>
            </div>
            <div id="hint_content_display" style="border:1px solid #D4D4D4;border-top:2px solid #D92F19;padding:30px;color:#858585;">
                
            </div>

        </div>
    </div>
</div>
