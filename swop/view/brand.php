<div class="template_center">
    <?php 
        echo "<h2>品牌大街<span style='color:red;font-size:12pt'>Brands</span></h2>";
        echo "<hr />";
        
        echo "<div><h3>選擇字母搜尋</h3>";
        echo    "<div id='brand_alpha_search' style='font-size:11pt;color:#696969;'><span>All</span><span>A</span><span>B</span><span>C</span><span>D</span>"
             .  "<span>E</span><span>F</span><span>G</span><span>H</span><span>I</span><span>J</span><span>K</span>"
             .  "<span>L</span><span>M</span><span>N</span><span>O</span><span>P</span><span>Q</span><span>R</span>"
             .  "<span>S</span><span>T</span><span>U</span><span>V</span><span>W</span><span>X</span><span>Y</span>"
             .  "<span>Z</span><span>其他</span> <div style='display:inline-block;'>共 <b style='color:red;'>".$swop->brand_total_count."</b> 個</div></div>";
        echo "</div>";
        
        echo "<div><h3>選擇分類搜尋</h3>";
        echo    "<div id='brand_class_search' style='font-size:11pt;color:#696969;'>";
        $len = count($swop->brand_class);
        for($i = 0;$i<$len;$i++)
            echo "<span fi_no='".$swop->brand_class[$i]["fi_no"]."'>".$swop->brand_class[$i]["name"]."</span>"; 
        echo    "</div>";
        echo "</div>";
        
        echo "<div><h3>品牌直接搜尋</h3>";
        echo    "<div id='brand_direct_search'><span style='display:inline-block;padding:3px;border:1px solid #d6d2d4;'><input id='brand_keyword_search' type='text' placeholder='請輸入品牌關鍵字' style='border:0px;position:relative;top:-3px;'><img id='brand_keyword_search_btn' src='http://www.crazy2go.com/public/img/advertisement/magnify.png' style='margin-top:2px;cursor:pointer;width:15px;height:15px;'></span><span id='brand_pager' style='float:right;'>pager</span></div>";
        echo "</div><br/>";
        echo "<hr />";
        
        echo "<div id='brand_logos'></div>";
        echo "<hr />";
        /*
        $brand_footer = array();
        foreach($swop->brand_footer as $v)
        {
            $v["item"] = (array)json_decode($v["item"]);
            $v["item"]["name"] = $v["item"]["name"][0];
            $v["item"]["depiction"] = $v["item"]["depiction"][0];
            $v["item"]["price"] = $v["item"]["price"][0];
            $v["item"]["url"] = $v["item"]["url"][0];
            $brand_footer[$v["fi_no"]] = $v;
        }
        
        echo "<div id='brand_footer'>";
        echo    "<div id='footer_left' style='display:inline-block;width:417px;float:left;'>";
        echo        "<h3>新進品牌</h3>";
        echo        "<div style='border-left:1px solid gray;'>";
        echo            "<div style='display:inline-block;width:400px;height:180px;margin-bottom:10px;'><img class='ad_circle' src='http://www.crazy2go.com/public/img/advertisement/".$brand_footer[4]["images"]."' style='width:150px;height:150px;float:left;padding:30px 30px 30px 50px;'/><span style='display:inline-block;color:black;margin-top:60px;'>".$brand_footer[4]["item"]["name"]."</span><br/><span style='display:inline-block;color:gray;margin-top:10px;'>".$brand_footer[4]["item"]["depiction"]."</span><br/><span style='display:inline-block;color:gray;margin-top:30px;'><span style='font-family:Arial;'>¥</span> ".$brand_footer[4]["item"]["price"]."</span></div>";
        echo            "<div style='display:inline-block;width:400px;height:180px;margin-bottom:10px;'><img class='ad_circle' src='http://www.crazy2go.com/public/img/advertisement/".$brand_footer[5]["images"]."' style='width:150px;height:150px;float:left;padding:30px 30px 30px 50px;'/><span style='display:inline-block;color:black;margin-top:60px;'>".$brand_footer[4]["item"]["name"]."</span><br/><span style='display:inline-block;color:gray;margin-top:10px;'>".$brand_footer[5]["item"]["depiction"]."</span><br/><span style='display:inline-block;color:gray;margin-top:30px;'><span style='font-family:Arial;'>¥</span> ".$brand_footer[5]["item"]["price"]."</span></div>";
        echo            "<div style='display:inline-block;width:400px;height:180px;margin-bottom:10px;'><img class='ad_circle' src='http://www.crazy2go.com/public/img/advertisement/".$brand_footer[6]["images"]."' style='width:150px;height:150px;float:left;padding:30px 30px 30px 50px;'/><span style='display:inline-block;color:black;margin-top:60px;'>".$brand_footer[4]["item"]["name"]."</span><br/><span style='display:inline-block;color:gray;margin-top:10px;'>".$brand_footer[6]["item"]["depiction"]."</span><br/><span style='display:inline-block;color:gray;margin-top:30px;'><span style='font-family:Arial;'>¥</span> ".$brand_footer[6]["item"]["price"]."</span></div>";
        echo            "<div style='display:inline-block;width:400px;height:180px;margin-bottom:10px;'><img class='ad_circle' src='http://www.crazy2go.com/public/img/advertisement/".$brand_footer[7]["images"]."' style='width:150px;height:150px;float:left;padding:30px 30px 30px 50px;'/><span style='display:inline-block;color:black;margin-top:60px;'>".$brand_footer[4]["item"]["name"]."</span><br/><span style='display:inline-block;color:gray;margin-top:10px;'>".$brand_footer[7]["item"]["depiction"]."</span><br/><span style='display:inline-block;color:gray;margin-top:30px;'><span style='font-family:Arial;'>¥</span> ".$brand_footer[7]["item"]["price"]."</span></div>";
        echo        "</div>";
        echo    "</div>";
        echo    "<div id='footer_right' style='display:inline-block;width:804px;float:right;'>";
        echo        "<h3>推薦品牌</h3>";
        echo        "<div style='display:inline-block;width:804px;height:180px;margin-bottom:10px;'><div class='ad_center' style='width:180px;height:180px;float:left;'><img src='http://www.crazy2go.com/public/img/advertisement/".$brand_footer[8]["images"]."' style='width:178px;height:149px;border:1px solid gray;border-bottom:0px;'/><div style='display:inline-block;background:".$brand_footer[8]["item"]["color"].";line-height:30px;text-align:center;width:180px;height:30px;color:white;vertical-align:top;font-size:16px;'><img style='padding-top:5px;' src='http://www.crazy2go.com/public/img/advertisement/".$brand_footer[8]["item"]["icon"]."'>".$brand_footer[8]["item"]["name"]."</div></div><div class='right_ad' style='position:relative;float:right;width:624px;height:180px;'>";
                    $tab_str = '';
                    $tab_img = '';
                    foreach($brand_footer as $v)
                        if($v["index"] == 8)
                        {
                            $tab_str .= '<span style="display:inline-block;margin:5px;width:14px;height:14px;background:gray;-webkit-border-radius:7px;-moz-border-radius:7px;border-radius:7px;"></span>';
                            $tab_img .= "<a href='".$v["url"]."'><div style='position:absolute; top:0px; left:0px; width:624px; height:180px; background:url(http://www.crazy2go.com/public/img/advertisement/".$v["images"].") no-repeat center;'></div></a>";
                        }
        echo        '<div class="ad_right" style="position:absolute; right:20px; bottom:20px; z-index:99">'.$tab_str.'</div>';
        echo        $tab_img;
        echo        "</div></div>";
        echo        "<div style='display:inline-block;width:804px;height:180px;margin-bottom:10px;'><div class='ad_center' style='width:180px;height:180px;float:left;'><img src='http://www.crazy2go.com/public/img/advertisement/".$brand_footer[9]["images"]."' style='width:178px;height:149px;border:1px solid gray;border-bottom:0px;'/><div style='display:inline-block;background:".$brand_footer[9]["item"]["color"].";line-height:30px;text-align:center;width:180px;height:30px;color:white;vertical-align:top;font-size:16px;'><img style='padding-top:5px;' src='http://www.crazy2go.com/public/img/advertisement/".$brand_footer[9]["item"]["icon"]."'>".$brand_footer[9]["item"]["name"]."</div></div><div class='right_ad' style='position:relative;float:right;width:624px;height:180px;'>";
                    $tab_str = '';
                    $tab_img = '';
                    foreach($brand_footer as $v)
                        if($v["index"] == 9)
                        {
                            $tab_str .= '<span style="display:inline-block;margin:5px;width:14px;height:14px;background:gray;-webkit-border-radius:7px;-moz-border-radius:7px;border-radius:7px;"></span>';
                            $tab_img .= "<a href='".$v["url"]."'><div style='position:absolute; top:0px; left:0px; width:624px; height:180px; background:url(http://www.crazy2go.com/public/img/advertisement/".$v["images"].") no-repeat center;'></div></a>";
                        }
        echo        '<div class="ad_right" style="position:absolute; right:20px; bottom:20px; z-index:99">'.$tab_str.'</div>';
        echo        $tab_img;
        echo        "</div></div>";
        echo        "<div style='display:inline-block;width:804px;height:180px;margin-bottom:10px;'><div class='ad_center' style='width:180px;height:180px;float:left;'><img src='http://www.crazy2go.com/public/img/advertisement/".$brand_footer[10]["images"]."' style='width:178px;height:149px;border:1px solid gray;border-bottom:0px;'/><div style='display:inline-block;background:".$brand_footer[10]["item"]["color"].";line-height:30px;text-align:center;width:180px;height:30px;color:white;vertical-align:top;font-size:16px;'><img style='padding-top:5px;' src='http://www.crazy2go.com/public/img/advertisement/".$brand_footer[10]["item"]["icon"]."'>".$brand_footer[10]["item"]["name"]."</div></div><div class='right_ad' style='position:relative;float:right;width:624px;height:180px;'>";
                    $tab_str = '';
                    $tab_img = '';
                    foreach($brand_footer as $v)
                        if($v["index"] == 10)
                        {
                            $tab_str .= '<span style="display:inline-block;margin:5px;width:14px;height:14px;background:gray;-webkit-border-radius:7px;-moz-border-radius:7px;border-radius:7px;"></span>';
                            $tab_img .= "<a href='".$v["url"]."'><div style='position:absolute; top:0px; left:0px; width:624px; height:180px; background:url(http://www.crazy2go.com/public/img/advertisement/".$v["images"].") no-repeat center;'></div></a>";
                        }
        echo        '<div class="ad_right" style="position:absolute; right:20px; bottom:20px; z-index:99">'.$tab_str.'</div>';
        echo        $tab_img;
        echo        "</div></div>";
        echo        "<div style='display:inline-block;width:804px;height:180px;margin-bottom:10px;'><div class='ad_center' style='width:180px;height:180px;float:left;'><img src='http://www.crazy2go.com/public/img/advertisement/".$brand_footer[11]["images"]."' style='width:178px;height:149px;border:1px solid gray;border-bottom:0px;'/><div style='display:inline-block;background:".$brand_footer[11]["item"]["color"].";line-height:30px;text-align:center;width:180px;height:30px;color:white;vertical-align:top;font-size:16px;'><img style='padding-top:5px;' src='http://www.crazy2go.com/public/img/advertisement/".$brand_footer[11]["item"]["icon"]."'>".$brand_footer[11]["item"]["name"]."</div></div><div class='right_ad' style='position:relative;float:right;width:624px;height:180px;'>";
                    $tab_str = '';
                    $tab_img = '';
                    foreach($brand_footer as $v)
                        if($v["index"] == 11)
                        {
                            $tab_str .= '<span style="display:inline-block;margin:5px;width:14px;height:14px;background:gray;-webkit-border-radius:7px;-moz-border-radius:7px;border-radius:7px;"></span>';
                            $tab_img .= "<a href='".$v["url"]."'><div style='position:absolute; top:0px; left:0px; width:624px; height:180px; background:url(http://www.crazy2go.com/public/img/advertisement/".$v["images"].") no-repeat center;'></div></a>";
                        }
        echo        '<div class="ad_right" style="position:absolute; right:20px; bottom:20px; z-index:99">'.$tab_str.'</div>';
        echo        $tab_img;
        echo        "</div></div>";
        echo    "</div>";
        echo "</div>";
        
         */
    ?>
</div>