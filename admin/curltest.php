<?php 
	
	require_once '../swop/library/dba.php';
    $dba = new dba();
    $dba->query("delete from test_table");
    
    $target_path = "article/";
    $files = glob( $target_path.'*', GLOB_MARK );
    foreach( $files as $file )
    {  
        @unlink( $file );
    }
	echo "<div style='line-height:30px'>";
	$s_title;
	
	$page=1;
	
	$article_num = 1;
	
	$tot_word = "";
	
	while($page<=1716)
	{
		$allurl = "http://cht.sgilibrary.org/ResultDetail.php?pageNo={$page}&searchType=&BookArticle=1";

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $allurl);
		curl_setopt($ch, CURLOPT_HEADER, 1);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION,1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$all = curl_exec($ch);
		curl_close($ch);
		
		if( strpos($all,'暫無任何內容！')!==false)
		{
			echo "<br>{$page}沒資料！！<br>";
			$page++;
			continue;
		}
		$r_title = explode('<span class="txt_title">',$all);
		$len = count($r_title)-1;
		for($i=1;$i<=$len;$i++)
		{
			$title = explode('</span>',$r_title[$i]);
			
			$title = $title[0];
			
			$r_word = explode('<td align="left" valign="top" class="txt">', $all);
			//$word = explode('<span onclick="getDictionary();">', $word[1]);
			$word = explode('</span>',$r_word[$i]);
			$word = strtr($word[0],array('<span onClick="getDictionary();">'=>""));
			
			if($s_title!=$title)
			{
				if($page!=1)
				{
					//into db
					/*--*/
					$tot_word = strtr(strtolower($tot_word),array("<br>"=>'\r\n',"<br />"=>'\r\n'));
					while(substr_count($tot_word, "  "))
						$tot_word=strtr($tot_word,array("  "=>" "));
						
					$fp = fopen($target_path.$article_num."_".$s_title.'.txt', 'w');
					fwrite($fp, $s_title.'\r\n'.$tot_word);
					fclose($fp);
					$sql = "insert into test_table (v_id,v_title,t_word) values ('$article_num','$s_title','$tot_word');";
					$dba->query($sql);
					$tot_word = "";
					$article_num++;
					/*--*/
				}
				$s_title=$title;
				if(strpos($word,$title)!==false)
				{
					$word = strtr($word,array($title=>""));
				}
				if($page!=1)
				{
					echo "</div><br><br><hr></hr>";
				}
				echo "頁數：$page<br><div class='article'><br><div class='title'>".$title."</div><div class='describe'>".$word;
				$tot_word = $word;
			}
			else
			{
				$tot_word .= $word;
				echo $word;
			}
		}
		
		$page++;
	}
	
	echo "</div>";
	
	//print_r($title);
	//echo $title ."<br><br>". $word;
?>