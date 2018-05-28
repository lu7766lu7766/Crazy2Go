
<div class="template_center">
    <div style="margin-top: 40px;padding-bottom:18px;border-bottom:1px solid #D4D4D4;">
        <div style="display:inline-block;width:208px;margin-right: 28px;"><span style="font-size:14pt;font-weight:bold">幫助中心</span></div>
        <div id="path" style="display:inline-block;font-weight: bold;">當前的位置 > Q and A</div><div id='qanda_search' style='float: right;'><input type='text' value=''/><input type='button' value='搜尋Q&A'></div>
    </div>
    <div style="margin-top: 15px;">
        <div id="qanda_menu" style="display:inline-block;width:208px;border:1px solid #D4D4D4;margin-right: 28px;font-size:11pt;font-weight: bold;">
        <?php 
            $first_class = array();
            $len = count($swop->data);

            for($i=0;$i<$len;$i++)
            {
                if($swop->data[$i]['index'] == 0)
                {
                    $first_class[] = array_shift($swop->data);
                    $i--;$len--;
                }else{
                    break;
                }
            }

            $len = count($first_class);
            $sub_len = count($swop->data);

            // qanda_menu
            for($i=0;$i<$len;$i++)
            {
                echo "<div><span>".$first_class[$i]['name']."</span><div>";
                for($j=0;$j<$sub_len;$j++)
                {
                    if($swop->data[$j]['index'] == $first_class[$i]['fi_no'])
                    {
                        $defalut_check = "";
                        $defalut_no = "";
                        if(isset($_GET["no"]))
                        if($swop->data[$j]['fi_no'] == $swop->default_fi_no)
                        {
                            $defalut_check = "default=1";
                            $defalut_no = "item=".$_GET["no"];
                        }
                        echo "<span href='javascript:void(0)' no='".$swop->data[$j]['fi_no']."' ".$defalut_check." ".$defalut_no.">".$swop->data[$j]['name']."</span>";
                    }
                }
                echo "</div></div>";
            }
        ?>            
        </div>
        <div id='qanda_content' style="display:inline-block;width:986px;float:right;margin-bottom:30px;">
        </div>
    </div>
    
</div>
