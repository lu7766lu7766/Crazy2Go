<div class="template_center">
    <?php 
        echo "<div id='bs_top' style='margin-top:50px;'>";
        echo    "<div id='bs_logo' style='display:inline-block;width:220px;border:1px solid gray;height:160px;'>";
        echo        "<img src='".$this->base["lgo"].$swop->logo["graphic"]."' style='padding:10px 0px;width:220px;'>";
        echo        "<div style='width:220px;display:inline-block;line-height:30px;text-align:center;background:black;color:white;'>".$swop->logo["text"]."</div>";
        echo    "</div>";
        
        if(!isset($_GET["brand"]))
        {
            
            echo    "<div style='display:inline-block;border:1px solid gray;float:right;width:960px;height:90px;margin-top:100px;white-space:nowrap;'>";
            echo        "<div id='brand_left_btn' style='float: left; width: 30px; height: 66px; cursor: pointer; background: url(http://www.crazy2go.com/public/img/template/arrow_left.png) 50% 50% no-repeat;'></div>";
                        $len = count($swop->store_brand);
            echo        "<div id='brand_slide' style='float:left;width:900px;overflow:hidden;'>";
                        for($i = 0; $i < $len; $i++)
                        {
                            echo "<span fi_no='".$swop->store_brand[$i]["fi_no"]."'><img src='".$swop->store_brand[$i]["logo"]."' style='display:inline-block;border:1px solid gray;width:178px;height:90px;'></span>";
                        }
            echo        "</div>";
            echo        "<div id='brand_right_btn' style='float: left; width: 30px; height: 66px; cursor: pointer; background: url(http://www.crazy2go.com/public/img/template/arrow_right.png) 50% 50% no-repeat;'></div>";
            echo    "</div>";
        }
        echo "</div>";
        
        if(!isset($_GET["brand"]))
        {
            echo "<div id='ad_slide' style='position:relative;border:1px solid gray;height:270px;width:1223px;margin-top:20px;'>";
            $len = count($swop->ad_banner);
            $tab_str = '';
            for($i=0; $i<$len; $i++) {
                $tab_str .= '<span style="display:inline-block;margin:5px;width:14px;height:14px;background:gray;-webkit-border-radius:7px;-moz-border-radius:7px;border-radius:7px;"></span>';
            }
            echo '<div style="position:absolute; right:20px; bottom:20px; z-index:99">'.$tab_str.'</div>';
            for($i=0; $i<$len; $i++) {
                echo '<a href="'.$swop->ad_banner[$i]['item'].'"><div style="z-index:'.($len-$i).';position:absolute; top:0px; left:0px; width:1223px; height:270px; background:url('.$this->base['sto'].$swop->ad_banner[$i]['images'].') no-repeat center;"></div></a>';
            }
            echo "</div>";
        }
        
        echo "<div id='bs_content' style='margin-top:20px;'>";
        echo    "<div style='display:inline-block;width:220px;'>";
                    $len = count($swop->store_info);
                    for($i = 0; $i < $len; $i++)
                    {
                        if((int)$swop->store_info[$i]["service_number"] == 0)
                        {
                            $service_rank = 0;
                        }
                        else
                        {
                            $service_rank = (float)$swop->store_info[$i]["service_score"]/(float)$swop->store_info[$i]["service_number"];
                            $service_rank = round($service_rank,2);
                        }
                        if((int)$swop->store_info[$i]["quality_number"] == 0)
                        {
                            $quality_rank = 0;
                        }
                        else
                        {
                            $quality_rank = (float)$swop->store_info[$i]["quality_score"]/(float)$swop->store_info[$i]["quality_number"];
                            $quality_rank = round($quality_rank,2);
                        }
                        $place = explode("＿",$swop->store_info[$i]["location"]);
                        $place = $place[1];
                        echo "<div fi_no='".$swop->store_info[$i]["fi_no"]."' style='border:1px solid gray;margin-bottom:10px;'>";
                        echo    "<div style='line-height:80px;text-align:center;background:#f2f2f2;'>".$swop->store_info[$i]["name"]."</div>";
                        echo    "<div style='line-height:30px;padding:10px;border-bottom:1px solid gray;'>公司全名：".$swop->store_info[$i]["company"]."<br/>公司所在地：".$place."</div>";
                        echo    "<div><span style='display:inline-block;width:33%;border-right:1px solid gray;text-align:center;line-height:40px;'>服務 <b style='color:red;'>".$service_rank."</b></span><span style='display:inline-block;width:33%;border-right:1px solid gray;text-align:center;line-height:40px;'>品質 <b style='color:red;'>".$quality_rank."</b></span><span onclick='win=window.open(\"\",\"_blank\");$.post(\"".$this->base["url"]."service/ajax_select_service/\",{type:1,store:".$swop->store_info[$i]["fi_no"]."},function(qq){openWin($.trim(qq));});' style='cursor:pointer;display:inline-block;width:32%;text-align:center;line-height:40px;'>客服<b style='color:red;'></b></span></div>";
                        echo "</div>";
                    }
        echo    "</div>";
        if(isset($_GET["brand"]))
        {
            echo "<div style='display:inline-block;float:right;width:960px;position:relative;top:-220px;'>";
        }
        else
        {
            echo "<div style='display:inline-block;float:right;width:960px;'>";
        }
        echo        "<p><b>品牌直接搜尋</b> <input id='bs_keyword' type='text' placeholder='請輸入欲搜尋的品牌名稱' style='width:250px;'> <b>品牌價錢搜尋</b> <input id='bs_low_price' type='text' placeholder='最低價'> - <input id='bs_high_price' type='text' placeholder='最高價'></p>";
                    $len = count($swop->store_category);
                    $output = "<div id='bs_subset' class='table' style='width:".($len<8?$len*100:900)."px'>";
                    for($i = 0; $i < $len; $i++)
                    {
                        if($i % 8 == 0)$output .= "<div class='tr'>";
                        $output .= "<div class='td' style='width:100px;' fi_no='".$swop->store_category[$i]["fi_no"]."'><input type='checkbox'>".$swop->store_category[$i]["name"]."</div>";
                        if($i == $len-1)
                        {
                            $output .= "</div>";
                            break;
                        }
                        if($i % 8 == 7)$output .= "</div>";
                    }
                    $output .= "</div>";
        echo        "<p><b>商品分類</b><br/>".$output." <br/><input id='bs_search' type='button' value='搜尋' style='background:red;color:white;border:0px;'></p>";
        echo        "<div class='table' style='border-bottom:1px solid red;margin-bottom:20px;'><div class='tr'><div class='td'><div id='filter_selection' style='position:relative;margin:10px;width:400px;'><span>全部商品</span> <span>品牌新品</span> <span>品牌熱銷</span></div></div><div id='sorter' class='td' style='position:relative;top:2px;'>".$sort->sort_content."<div style='position:relative;top:-1px;border-bottom:1px solid red;'></div></div></div></div>";
        echo        "<div id='bs_product_list' style='width:960px;float:right;'>";
                        $len = count($swop->product_info);
                        echo "<div class='table'>";
                        for($i = 0; $i < $len; $i++)
                        {
                            if($i % 4 == 0)echo "<div class='tr'>";
                            $swop->product_info[$i]["images"] = json_decode($swop->product_info[$i]["images"]);
                            $swop->product_info[$i]["images"] = $swop->product_info[$i]["images"][0];
                            echo "<div class='td' fi_no='".$swop->product_info[$i]["fi_no"]."' category='".$swop->product_info[$i]["category"]."' brand='".$swop->product_info[$i]["brand"]."' style='position:relative;display:inline-block;border:1px solid gray;padding:10px;margin:0px 25px 20px 0px;width:20%;height:270px;'>";
                            echo "<img src='".$this->base["thu"].$swop->product_info[$i]["images"]."' style='width:192px;height:192px;'>";
                            echo "<br><span style='font-size:20px;'><span style='font-family:Arial;'>¥</span> ".$swop->product_info[$i]["promotions"]."</span>";
                            echo "<br>".$swop->product_info[$i]["name"];
                            echo "</div>";
                            if($i % 4 == 3)echo "</div>";
                        }
                        echo "</div>";
        echo        "</div>";
        echo        "<div style='float:right'>".$page->page_content."</div>";
        echo    "</div>";
        echo "</div>";
    ?>
</div>