<?php
	//ini_set("display_errors", "On");
	//error_reporting(E_ALL);
class Main_Models {
	
	private $start_row = 2;
	
	public function __construct() {
		
	}
	//xls上傳轉檔進db
	public function xls2db()
	{
		require_once "swop/library/dba.php";
		require_once "swop/library/excel_reader.php";
		
		$b_formal = $_POST["formal"]=="1"?true:false;
		$b_delete = $_POST["delete"]=="1"?true:false;
		
		$con = mysqli_connect("localhost", "global2user", "wRusUdRAjesWuqE3rech", "global2buy");
		if(mysqli_connect_errno()) {echo "Failed to connect to MySQL: ".mysqli_connect_error();}
		mysqli_query($con, "SET NAMES 'UTF8'"); 
		mysqli_query($con, "SET CHARACTER SET UTF8");
		mysqli_query($con, "SET CHARACTER_SET_RESULTS=UTF8");
			
		if(!$b_formal)
		{
			echo "測試<br>";
			
			$sql = "delete from goods_index_copy;";
			mysqli_query($con, $sql);
			$sql = "ALTER TABLE goods_index_copy AUTO_INCREMENT=1;";
			mysqli_query($con, $sql);
			
			echo "clear complete!!<br>";
		    $files = glob( "public/img/goods_tmp/".'*', GLOB_MARK );
		    foreach( $files as $file )
		    {  
		        @unlink( $file );
		    }
		    /*上傳資料夾*/
			$target_path = "public/img/goods_tmp/";
			if ( substr(sprintf('%o', fileperms($target_path)), -3)!=777 )
				$error .= $target_path."資料夾權限非 777 ，請聯繫管理員處理。<br>";
			if( $error!="" )
				die($error);
			/**/
		}
		else
		{
			echo "正式<br>";
			/*正式上傳資料夾*/
			//資料夾路徑設定
			$target_path  = "public/img/goods/";
			$sub_path     = "public/img/introduction/";
			$thumb_path   = "public/img/thumbnail/";
			$minsize_path = "public/img/minimize/";
			
			if($b_delete)
			{
				//資料表內容清除
				$sql = "truncate table `global2buy`.`goods_vevaluate`";
				mysqli_query($con, $sql);
				$sql = "truncate table `global2buy`.`goods_index`";
				mysqli_query($con, $sql);
				$sql = "truncate table `global2buy`.`brand`";
				mysqli_query($con, $sql);
				$sql = "truncate table `global2buy`.`category`";
				mysqli_query($con, $sql);
				$sql = "truncate table `global2buy`.`store_supplier`";
				mysqli_query($con, $sql);
				$sql = "truncate table `goods0`.`goods_info`";
				mysqli_query($con, $sql);
				$sql = "truncate table `goods1`.`goods_info`";
				mysqli_query($con, $sql);
				$sql = "truncate table `goods2`.`goods_info`";
				mysqli_query($con, $sql);
				$sql = "truncate table `goods3`.`goods_info`";
				mysqli_query($con, $sql);
				$sql = "truncate table `goods4`.`goods_info`";
				mysqli_query($con, $sql);
				$sql = "truncate table `goods5`.`goods_info`";
				mysqli_query($con, $sql);
				$sql = "truncate table `goods6`.`goods_info`";
				mysqli_query($con, $sql);
				$sql = "truncate table `goods7`.`goods_info`";
				mysqli_query($con, $sql);
				$sql = "truncate table `goods8`.`goods_info`";
				mysqli_query($con, $sql);
				$sql = "truncate table `goods9`.`goods_info`";
				mysqli_query($con, $sql);
				
				//資料刪除
				$files = glob( $target_path.date("y",strtotime("today -365 day")).'*', GLOB_MARK );//date("ymd",time()).//連同去年一起刪掉
			    foreach( $files as $file )
			    {  
			        @unlink( $file );
			    }
			    $files = glob( $target_path.date("y",time()).'*', GLOB_MARK );//date("ymd",time()).
			    foreach( $files as $file )
			    {  
			        @unlink( $file );
			    }
			    $files = glob( $sub_path.date("y",strtotime("today -365 day")).'*', GLOB_MARK );//date("ymd",time()).//連同去年一起刪掉
			    foreach( $files as $file )
			    {  
			        @unlink( $file );
			    }
			    $files = glob( $sub_path.date("y",time()).'*', GLOB_MARK );//date("ymd",time()).
			    foreach( $files as $file )
			    {  
			        @unlink( $file );
			    }
			    $files = glob( $thumb_path.date("y",strtotime("today -365 day")).'*', GLOB_MARK );//date("ymd",time()).
			    foreach( $files as $file )
			    {  
			        @unlink( $file );
			    }
			    $files = glob( $thumb_path.date("y",time()).'*', GLOB_MARK );//date("ymd",time()).
			    foreach( $files as $file )
			    {  
			        @unlink( $file );
			    }
			    $files = glob( $minsize_path.date("y",strtotime("today -365 day")).'*', GLOB_MARK );//date("ymd",time()).
			    foreach( $files as $file )
			    {  
			        @unlink( $file );
			    }
			    $files = glob( $minsize_path.date("y",time()).'*', GLOB_MARK );//date("ymd",time()).
			    foreach( $files as $file )
			    {  
			        @unlink( $file );
			    }
			
			
			}
			//判斷資料夾權限，不滿777則報錯
			if ( substr(sprintf('%o', fileperms($target_path)), -3)!=777 )
				$error .= $target_path."資料夾權限非 777 ，請聯繫管理員處理。<br>";
			if ( substr(sprintf('%o', fileperms($sub_path)), -3)!=777 )
				$error .= $sub_path."資料夾權限非 777 ，請聯繫管理員處理。<br>";
			if ( substr(sprintf('%o', fileperms($thumb_path)), -3)!=777 )
				$error .= $thumb_path."資料夾權限非 777 ，請聯繫管理員處理。<br>";
			if ( substr(sprintf('%o', fileperms($minsize_path)), -3)!=777 )
				$error .= $minsize_path."資料夾權限非 777 ，請聯繫管理員處理。<br>";
			if ( $error!="" )
				die($error);
			/**/
		}
		
		mysqli_close($con);
		/**/
		echo "<pre>";
		$dba=new dba();
		
		include_once "/var/www/html/crazy2go_com/demo_dirsacn.php";
		$dir = new Library_DIR();
		$other_path = $dir->scan();
		//print_r($other_path);
		//$dba-debug_mode = true;
		
		$a_start = microtime(true);
		print_r($_FILES);
		if ( !is_array($_FILES['upload_xls']) || !move_uploaded_file($_FILES['upload_xls']['tmp_name'], 'jacconvert.xls') ) {
			die($_FILES['upload_xls']['name']."檔案上傳失敗");
		}else{
			echo "upload success!!<br>";
		}
		
		$data = new Spreadsheet_Excel_Reader("jacconvert.xls");
		//分頁數判斷
		$sheet_len = 0;
		while( $data->rowcount($sheet_len)!="" && $data->colcount($sheet_len)!="" )
		{++$sheet_len;}
		echo "sheet_len:".$sheet_len."^^".$data->rowcount($sheet_len)."^^".$data->colcount($sheet_len)."<br>";
		$process_num = 0;
		for ($sheet=0;$sheet<$sheet_len;$sheet++ )
		{
			//欄位尋找即預設
			$rows = $data->rowcount($sheet);//行
			$cols = $data->colcount($sheet);//列
			echo "sheet:".$sheet."^^rows:".$rows."^^cols:".$cols."<br>";
			$direct_col = $this->get_colnum("直送",$data,$sheet);
			$direct_col = $direct_col==0?1:$direct_col;
			
			$supplier_col = $this->get_colnum("供應商",$data,$sheet);
			$supplier_col = $supplier_col==0?2:$supplier_col;
			
			$category1_col = $this->get_colnum("類目一",$data,$sheet);
			$category1_col = $category1_col==0?3:$category1_col;
			$category2_col = $this->get_colnum("類目二",$data,$sheet);
			$category2_col = $category2_col==0?4:$category2_col;
			$category3_col = $this->get_colnum("類目三",$data,$sheet);
			$category3_col = $category3_col==0?5:$category3_col;
			
			$brand_col = $this->get_colnum("品牌",$data,$sheet);
			$brand_col = $brand_col==0?6:$brand_col;
			
			$name_col = $this->get_colnum("品名",$data,$sheet);
			$name_col = $name_col==0?7:$name_col;
			
			$other_price_col = $this->get_colnum("進價;NTD",$data,$sheet);
			$other_price_col = $other_price_col==0?8:$other_price_col;
			
			$import_col = $this->get_colnum("進價;RMB",$data,$sheet);
			$import_col = $import_col==0?9:$import_col;
			
			$promotions_col = $this->get_colnum("市價",$data,$sheet);
			$promotions_col = $promotions_col==0?10:$promotions_col;
			
			$long_col = $this->get_colnum("長",$data,$sheet);
			$long_col = $long_col==0?11:$long_col;
			
			$width_col = $this->get_colnum("寬",$data,$sheet);
			$width_col = $width_col==0?12:$width_col;
			
			$height_col = $this->get_colnum("高",$data,$sheet);
			$height_col = $height_col==0?13:$height_col;
			
			$weight_col = $this->get_colnum("重量",$data,$sheet);
			$weight_col = $weight_col==0?14:$weight_col;
			
			//die("直送：".$direct_col."<br>供應商：".$supplier_col."<br>類目一：".$category1_col."<br>類目二：".$category2_col."<br>類目三：".$category3_col
			//	."<br>品牌：".$brand_col."<br>品名：".$name_col."<br>進價NTD:".$other_price_col."<br>進價RMB:".$import_col."<br>市價:".$promotions_col
			//	."<br>長：".$long_col."<br>寬：".$width_col."<br>高：".$height_col."<br>重量：".$weight_col."<br>開始行數：".$this->start_row);
			
			//資料筆數
			for( $i=$this->start_row ;$i<=$rows ;$i++ )
			{
				//db process
				if( $data->val($i,$supplier_col,$sheet)==""&& $data->val($i,$category1_col,$sheet)==""&& $data->val($i,$brand_col,$sheet)=="" )
				{
					echo $i."供應商，類目，品牌皆空白。請確認資料完整。<br>";
					continue;
				}
				$long = strtr(trim($data->val($i,$long_col,$sheet)),array(")"=>""));
				$width = strtr(trim($data->val($i,$width_col,$sheet)),array(")"=>""));
				$height = strtr(trim($data->val($i,$height_col,$sheet)),array(")"=>""));
				$weight = strtr(trim($data->val($i,$weight_col,$sheet)),array(")"=>""));
				if( ((float)$long==0  &&   $long!="") ||
					((float)$width==0 &&   $width!="") ||
					((float)$height==0 &&  $height!="") ||
					((float)$weight==0 &&  $weight!="") )
				{
					
					echo "sheet: $sheet , rows: $i , long:".(float)$long." , width:".(float)$width." , height:".(float)$height." , weight:".(float)$weight." 處理失敗。<br>";
					continue;
				}
				
				//echo "-----------------------goods_convert_start---------------------------<br>";
				
				//echo "第 ".++$process_num." 筆資料<br>";
				$direct = trim($data->val($i,$direct_col,$sheet))=="Y"?1:0;//台灣直送
				//echo "^^".trim($data->val($i,$direct_col,$sheet))."^^$driect<br>";
				//continue;
				$supplier_name = $data->val($i,$supplier_col,$sheet);//供應商
				//判斷store_supplier是否存在于store_supplier
				$result = $dba->query("select fi_no from `store_supplier` where `name`='$supplier_name' or `nickname`='$supplier_name' limit 1");
				if(count($result)>0)
				{
					$supplier = $result[0]["fi_no"];
				}
				else
				{
					//沒有的話新增
					$company='{"corporate":"","date_establishment":"'.date("Y-m-d",time()).'","registered_capital":"","organization_code":"","zip":"", "address":"","invoice":"","invoice_zip":"","invoice_address":"","http":"","phone":"","mobile":"","fax":""}';
					$returns='{"zip":"","address":"","phone":"","fax":""}';
					$operate='{"production_plant":"0","merchant":"0","agents":"0","dealer":"0","other":"0","other_context":""}';
					$license='{"produce":"0","license":"0","agent_distributor":"0","patent":"0","other":"0","other_context":""}';
					$bank='{"bank":"","branch":"","account":"","username":"","contract_start":"","contract_end":"","name":"", "position":"","phone":"","fax":"","mobile":"","email":""}';
					$service='[{"name":"","position":"","phone":"","fax":"","mobile":"","email":""},{"name":"","position":"","phone":"","fax":"","mobile":"","email":""}]';
					$shipper='{"name":"","position":"","phone":"","fax":"","mobile":"","email":""}';
					$check='{"name":"","position":"","phone":"","fax":"","mobile":"","email":""}';
					
					$dba->query("insert into `store_supplier` (`store`,`name`,`nickname`,`company`,`returns`,`operate`,`license`,`bank`,`service`,`shipper`,`check`)
							values ('1','$supplier_name','$supplier_name','$company','$returns','$operate','$license','$bank','$service','$shipper','$check')");
					$supplier = $dba->get_insert_id();
					/*echo "新增 store_supplier -- fi_no:{$supplier} , store:1 , name:{$supplier_name} , company:".$company." , returns:".$returns." , 
							operate:".$operate." , license:".$license." , bank:".$bank." , service:".$service." , 
							shipper:".$shipper." , check:".$check." <br>";
					/**/
							
				}
				//
				//echo $data->val($i,${"category".((string)1)."_col"},$sheet);
				//判斷category是否存在于category
				$category = 0;
				for($j=1; $col = ${"category".((string)$j)."_col"} ;$j++)
				{
					if( $col == "" )
						break;
					$category_name = $data->val($i,$col,$sheet);
					$result = $dba->query("select fi_no from `category` where `name`='$category_name' limit 1");
					if(count($result)==0)
					{
						//沒有的話新增
						$dba->query("insert into `category` (`name`,`index`) values('$category_name','$category')");
						$category = $dba->get_insert_id();
						//echo "insert name: $category_name , no: $category<br>";
					}
					else
					{
						$category = $result[0]["fi_no"];
						//echo "get: $category<br>";
					}
				}/**/
				//continue;
				/*$category_name = $data->val($i,$category3_col,$sheet)!=""?$data->val($i,$category3_col,$sheet):
									$data->val($i,$category2_col,$sheet)!=""?$data->val($i,$category2_col,$sheet):
										$data->val($i,$category1_col,$sheet);//分類
				$result = $dba->query("select fi_no from `category` where `name`='$category_name' limit 1");
				if(count($result)>0)
				{
					$category = $result[0]["fi_no"];
				}
				else
				{
					$dba->query("insert into `category` (`name`) values('$category_name')");
					$category = $dba->get_insert_id();
					//echo "新增 category -- fi_no:{$category} , name:{$category_name}<br>";
				}*/
				//判斷brand是否存在于brand
				$brand_name = $data->val($i,$brand_col,$sheet);//品牌
				$result = $dba->query("select fi_no from `brand` where `category`='$category' and `name`='$brand_name' limit 1");
				if(count($result)>0)
				{
					//$brand = $result[0]["fi_no"];
				}
				else
				{
					//沒有的話新增
					$dba->query("insert into `brand` (`category`,`name`) values('$category','$brand_name')");
					//$brand = $dba->get_insert_id();
					//echo "新增 brand -- fi_no:{$brand} , category:{$category} , name:{$brand_name}<br>";
				}
				//判斷brand是否存在于brand_group
				$result = $dba->query("select `fi_no`,`category` from `brand_group` where `name`='$brand_name' limit 1");
				if(count($result)>0)
				{
					$brand = $result[0]["fi_no"];
					$json_category = $result[0]["category"];
					$a_category = json_decode($json_category,true);
					if(!in_array($category, $a_category)
					{
						//有但沒有該分類則更新
						$a_category[]=$category;
						$json_category = json_encode($a_category);
						$dba->query("update `brand_group` set `category`='$json_category' where fi_no='$brand'");
					}
				}
				else
				{
					//沒有的話新增
					$dba->query("insert into `brand_group` (`category`,`name`) values('[$category]','$brand_name')");
					$brand = $dba->get_insert_id();
				}
				//
				$name = $data->val($i,$name_col,$sheet);//品名
				$other_price = json_encode(array("NTD"=>array($data->val($i,$other_price_col,$sheet),"","","")));//進價ＮＴＤ
				$import = $data->val($i,$import_col,$sheet);//進價ＲＭＢ
				$promotions = $data->val($i,$promotions_col,$sheet);//銷價
				$price = $promotions+50;//市價
				$volumetric_weight = "{\"長\":\"".$long."\",\"寬\":\"".$width ."\",\"高\":\"".$height."\",\"重量\":\"".$weight."\"}";
				$specifications = json_encode(array("default"=>array("default")));
				$inventory = json_encode(array("0"));
				$related = json_encode(array("news"=>array(),"hots"=>array()));
				
				//寫入goods_index
/*--正測式轉換--*/if(!$b_formal)
				{	
					/*測試資料庫*/
					
					$dba->query("insert into `goods_index_copy` (`direct`,`supplier`,`category`,`brand`,`name`,`product`,`other_price`,`import`,`price`,`promotions`
																,`price`,`specifications`,`inventory`,`store`,`volumetric_weight`,`related`) 
								values('$direct','$supplier','$category','$brand','$name','$name','$other_price','$import','$price','$promotions'
																,'$price','$specifications','$inventory','1','$volumetric_weight','$related')");
					/*$goods_no = $dba->get_insert_id();
					echo "sql:"."insert into `goods_index_copy` (`direct`,`supplier`,`category`,`brand`,`name`,`product`,`other_price`,`import`,`promotions`"
															   .",`specifications`,`inventory`,`store`) 
								values('$driect','$supplier','$category','$brand','$name','$name','$other_price','$import','$promotions'"
															   .",'$specifications','$inventory','1')<br>";
					echo "新增 goods_index_copy -- fi_no:{$goods_no} , direct:{$driect} , supplier:{$supplier} , category:{$category} , brand:{$brand} , name:{$name} "
								.", product:{$name} , other_price:{$other_price} , import:{$import} , promotions:{$promotions} "
								.", specifications:{$specifications} , inventory:{$inventory} , store:1<br>";
					/**/
				}
				else
				{
					/*正式資料庫*/
					$dba->query("insert into `goods_index` (`direct`,`supplier`,`category`,`brand`,`name`,`product`,`other_price`,`import`,`promotions`
															,`price`,`specifications`,`inventory`,`store`,`volumetric_weight`) 
								values('$direct','$supplier','$category','$brand','$name','$name','$other_price','$import','$promotions'
															,'$price','$specifications','$inventory','1','$volumetric_weight')");
					$goods_no = $dba->get_insert_id();
					
					$dba->query("insert into `goods_info` (`fi_no`,`direct`,`supplier`,`category`,`brand`,`name`,`product`,`other_price`,`import`,`promotions`
															,`price`,`specifications`,`inventory`,`store`,`volumetric_weight`,`related`) 
								values('$goods_no','$direct','$supplier','$category','$brand','$name','$name','$other_price','$import','$promotions'
															,'$price','$specifications','$inventory','1','$volumetric_weight','$related')");
					
					$dba->query("insert into `goods_vevaluate` (`fi_no`,`store`,`view`,`respond`,`evaluate_date`,`delete`) 
								values('$goods_no','1','0','0','0000-00-00 00:00:00','0')");
															
					/*echo "sql:"."insert into `goods_index` (`supplier`,`category`,`brand`,`name`,`product`,`other_price`,`import`,`promotions`
															,`specifications`,`inventory`,`store`,`volumetric_weight`,`related`) 
								values('$supplier','$category','$brand','$name','$name','$other_price','$import','$promotions'
															,'$specifications','$inventory','1','$volumetric_weight','$related')<br>";
					/*echo "新增 goods_index , goods_info , goods_vevaluate -- fi_no:{$goods_no} , direct:{$driect} , supplier:{$supplier} , category:{$category} "
								.", brand:{$brand} , name:{$name} , product:{$name} , other_price:{$other_price} , import:{$import} , promotions:{$promotions} "
								.", specifications:{$specifications} , inventory:{$inventory} , store:1<br>";
					/**/
				}
				//file process
				$category1 	= $data->val($i,$category1_col,$sheet);
				$category2 	= $data->val($i,$category2_col,$sheet);
				$category3 	= $data->val($i,$category3_col,$sheet);
				$brand 		= $data->val($i,$brand_col,$sheet);
				$name 		= $data->val($i,$name_col,$sheet);
				$path = "jackson/";
				$path.= $category1==""?"":trim($category1)."/";
				$path.= $category2==""?"":trim($category2)."/";
				$path.= $category3==""?"":trim($category3)."/";
				$path.= $brand==""?"":trim($brand)."/";
				$path.= $name==""?"":trim($name)."/";
				$path = strtr($path,array("//"=>"/"));
				//路徑判斷
				if(!is_dir($path))
				{
					if( $other_path[trim($name)]!="" )
					{
						$path = "jackson/".$other_path[trim($name)]."/";
						$path = strtr($path,array("//"=>"/"));
						if( !is_dir($path) )
						{
							echo "sheet: $sheet , rows: $i , product_name: $path >> 找不到資料夾<br>";
							continue; 
						}
					}
				}
				//主圖上傳
				$main_img = $path."p1.jpg";
				$ext = end(explode('.', $main_img));
				list($width,$height,,) = getimagesize($main_img);
				//copy
				$new_file_name = date("ymdHis",time())."_0_{$width}x{$height}_{$goods_no}.{$ext}";
				$new_main_img = $target_path.$new_file_name;
				$images = array($new_file_name);
				$isok = copy($main_img , $new_main_img);
				if(!$isok)
				{
					echo "主圖複製失敗<br>";
				}
				else
				{
					//縮圖轉換
					if(!$b_formal)
					{
						/*測試縮圖*/
						exec("convert -resize '230x230>' {$new_main_img} {$new_main_img}_thumb");
						exec("convert -resize '25x25>' {$new_main_img} {$new_main_img}_minsize");
						/*echo "convert -resize '230×230>' {$new_main_img} {$new_main_img}_thumb<br>"
							."convert -resize '25x25>' {$new_main_img} {$new_main_img}_minsize<br>";
						/**/
						//public/img/minimize 25x25
					}
					else
					{
						/*正式縮圖*/
						exec("convert -resize '230x230>' {$new_main_img} {$thumb_path}{$new_file_name}");
						exec("convert -resize '25x25>' {$new_main_img} {$minsize_path}{$new_file_name}");
						/*echo "convert -resize '230×230>' {$new_main_img} {$thumb_path}{$new_file_name}<br>"
							."convert -resize '25x25>' {$new_main_img} {$minsize_path}{$new_file_name}<br>";
						/**/
					}
					//echo $main_img." to ".$new_main_img." 複製成功<br>";
				}
				
				$j=2;
				$content = "";
				$introduction_images = array();
				//批次子圖片上傳
				while( file_exists($path."p{$j}.jpg") )
				{	
					$sub_img = $path."p{$j}.jpg";
					$ext = end(explode('.', $sub_img));
					list($width,$height,,) = getimagesize($sub_img);
					$new_sub_file_name = date("ymdHis",time())."_".($j-1)."_{$width}x{$height}_{$goods_no}.{$ext}";
					$new_sub_img = $sub_path.$new_sub_file_name;
					$introduction_images[] = $new_sub_file_name;
					$isok = copy($sub_img , $new_sub_img);
					if(!$isok)
					{
						echo $sub_img." to ".$new_sub_img." 複製失敗~~^^!!!!@!##@$#$<br>";
					}
					else
					{
						//echo $sub_img." to ".$new_sub_img." 複製成功<br>";
					}
					$content.="<img src=\\'../$new_sub_img\\'/><br>";
					$j++;
				}
				if( file_exists($path."123.txt") )
				{
					$content .= nl2br(file_get_contents($path."123.txt"));
				}
				$images = json_encode($images);
				$introduction_images = json_encode($introduction_images);
/*--正測式轉換--*/if(!$b_formal)
				{
					/*測試資料庫*/
					$dba->query("update `goods_index_copy` set 
									 images='$images'
									,attribute='{$introduction_images}--{$content}' where fi_no='$goods_no'");
					//echo "更新 goods_index_copy -- images:".$images."<br>";
					/**/
				}
				else
				{
					/*正式資料庫*/
					$dba->query("update `goods_index` set images='$images' where fi_no='$goods_no'");
					$dba->query("update `goods_info` set images='$images' , introduction='$content' , introduction_images='$introduction_images' 
									where fi_no='$goods_no'");
					//echo  "sql:"."update `goods_info` set images='$images' , introduction_images='$introduction_images' where fi_no='$goods_no'<br>";
					//echo "<font color='#f00'>更新 goods_index , goods_info -- images:".$images." , introduction_images:".$introduction_images."</font><br>";
					/**/
				}
				//echo "-----------------------{$main_img}  goods_convert_end----------------------------<br><br>";
			}
		}
		echo "<a href='".$this->base['url']."upload_xls?formal=".$_POST["formal"]."'>回上傳頁</a>";
		@unlink("jacconvert.xls");
		/**/
		/*
		echo "<table>";
		for($i=0 ; $i<=$cols;$i++)
		{
			echo "<tr>";
			for( $j=0 ; $j<= $rows ; $j++ )
			{
				echo "<td>";
				echo $data->val($i,$j);
				echo "</td>";
			}
			echo "</tr>";
		}
		echo "</table>";
		*/
		
		$a_end = microtime(true);
		$space = $a_end - $a_start;
		echo "<br>{$space}秒<br>";
		
		return $this;
	}
	//從關鍵字尋早“列”數
	private function get_colnum($colname,$data,$sheet=0)
	{
		$cols = $data->colcount($sheet);//列
		$rows = $data->colcount($sheet);//列
		$key_words = explode(";", $colname);
		$key_len = count($key_words);
		$b_find = false;
		
		for($row=1;$row<=$rows&&!$b_find;$row++)
		{
			for($i=1;$i<=$cols;$i++)
			{
				$tmp_v="";
				foreach($key_words as $key_word)
				{
					if( strpos($data->val($row,$i,$sheet),$key_word)!==false )
					{
						$tmp_v.="1";
					}
					else
					{
						break;
					}
						
					if(strlen($tmp_v)==$key_len)
					{
						$this->start_row = $row+1;
						$b_find = true;
						return $i;
					}
				}
			}
		}
		
		return 0;
	}
	
	public function home() {
		
	}
}
?>