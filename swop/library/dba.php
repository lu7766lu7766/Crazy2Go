

<?php 
/* db資料庫物件
 * dba資料庫控制物件
 * db table field 必須全小寫
 * table 必須要有fi_no欄位
 * table field 不可含有右述關鍵字 "order by","limit","union" 否則get_where會取不到
 * 不可下 子select 
 * update不可更新no
 * update 沒給fi_no，db會回傳所有db以供全部更新
 * insert 一次一筆
 * where 不要用sql涵式
 * between 不給用，請用>= <=取代
 * 資料庫分割fi_no不要下多重判斷 select trace from order_goods where fi_no=1 and order in (1,2,3) and order like '1'
 * 資料庫分割資料表同名，資料庫不同名
 * 水平分割資料庫同名，資料表不同名
 * Ver3.0 beta 把程式大量簡化，並把horisplit 分別存進cache 新增horisplit會新增對應view
 
 */	
 	
 	//require_once "swop/library/php_sql_parser/PHPSQLParser.php";//速度太慢捨棄不用
	class dba {
		//關鍵欄位
		static private $no_field_name="fi_no";
		//member 十個分割ip
		static private $dbsplit_member_ip=array("localhost","localhost","localhost","localhost","localhost",
									  "localhost","localhost","localhost","localhost","localhost");
		//goods 十個分割
		static private $dbsplit_goods_ip =array("localhost","localhost","localhost","localhost","localhost",
									  "localhost","localhost","localhost","localhost","localhost");
		//member資料庫分割份數(預設十份)
		static private $dbsplit_member_len;
		//goods資料庫分割份數(預設十份)
		static private $dbsplit_goods_len;
		//member資料庫分割名
		static private $member_db_name="member";
		//goods資料庫分割名
		static private $goods_db_name="goods";
		//comment資料庫ip
		static private $common_ip="localhost";
		//資料庫連線id
		static private $user_id="global2user";
		//資料庫連線pwd
		static private $password="wRusUdRAjesWuqE3rech";
		//資料庫連線port
		static private $port=3306;
		//comment 資料庫名稱
		static private $common_db_name="global2buy";
		//memcache物件
		static private $mem;
		//memcache ip
		static private $mem_ip="192.168.0.122";
		//memcache port
		static private $mem_port=9901;
		//member資料庫分割的資料表
		static private $dbsplit_member_table=array("member_info"	,"order_goods"		,"collect_goods"	,"member_address"	,"collect_store"	
												  ,"goods_history"	,"order_application","member_currency"//--
												  ,"order_form"	//--
												  ,"appeal_info"//--	
												  );//資料庫分割的資料表//不同資料庫
		//goods資料庫分割的資料表
		static private $dbsplit_goods_table=array("goods_respond",	"goods_evaluate",	"goods_info"	,"goods_tag");
		//水平分割資料表
		static private $horisplit_table=array();//"goods_index",	"order_index",		"member_index",		"evaluate_index");//水平分割的資料表
		//水平分割筆數限制
		static private $horisplit_limit=50000;//商品每50000筆，水平分割資料表
		
		static private $db_member=array();
		static private $db_goods=array();
		static private $db;
		//用於存放原始sql（大小寫問題）
		static private $s_sql = "";
		//用於存放修改後的sql（水平分割資料表名不同）
		static private $sql = "";
		//memcache用，用於存放所有資料表目前最後一筆資料的no(一般會自動遞增)，insert時記得+1再丟回memcache
		static private $last_no=array();
		static private $last_mem_name="last_no";
		
		/////// mem_cache used
		//用於存放資料表的sql筆數。key=>table_name,value=>table_sql_count, select時記得+1再丟回memcache
		static private $table_sql_count=array();
		static private $sql_count_mem_name="table_sql_count";
		//分割字元
		static private $dot=";";
		
		/////// attr return 
		//update 影響筆數
		static private $affected_rows=0;
		//insert 的 fi_no
		static private $insert_id=0;
		
		/////// db_select bind，ex:會員資訊是跟著會員編號走
		//綁定欄位的資料表
		static private $bind_table=array("order_form"=>"member"//fi_no資料結構設not null，此即可不用鎖
										,"appeal_info"=>"member"//fi_no
										
										,"order_goods"=>"member"
										,"member_address"=>"member"
										,"collect_goods"=>"member"
										,"collect_store"=>"member"
										,"goods_history"=>"member"
										,"order_application"=>"member"
										,"member_currency"=>"member"
										
										 
										,"goods_respond"=>"goods"
										,"goods_evaluate"=>"goods"
										,"goods_tag"=>"goods");
										 
		//insert 該table一定要有 $no_field_name ，否則回傳false   ps:goods_order跟著goods_index走，所以必須附
		static private $required_key_table=array("order_form"=>1
												,"member_info"=>1
												,"goods_info"=>1
												,"goods_vevaluate"=>1
												);
		//private $parser;
		//設定為true時可以輸出自己想要看到的資訊，不影響其他開發者的使用。
		public $debug_mode = false;
		
	    function __construct(){
	    	//所有資料庫物件(不含連線)，mem物件，實體化
	    	
	    	self::$dbsplit_member_len=count(self::$dbsplit_member_ip);
	    	self::$dbsplit_goods_len=count(self::$dbsplit_goods_ip);
	    	
	    	for($i=0;$i<self::$dbsplit_member_len;$i++){
	    		self::$db_member[$i]=
	    			new db( self::$dbsplit_member_ip[$i],
	    					self::$user_id,
	    					self::$password,
							self::$member_db_name.$i,
							self::$port);//$db_name=memberX
							
	    	}
	    	
	    	for($i=0;$i<self::$dbsplit_goods_len;$i++){
	    		
    			self::$db_goods[$i] =
	    			new db( self::$dbsplit_goods_ip[$i] ,
	    					self::$user_id,
	    					self::$password,
							self::$goods_db_name.$i ,
							self::$port);//$db_name=goodsX
							
	    	}
	    	
	    	self::$db=new db(self::$common_ip,self::$user_id,self::$password,self::$common_db_name,self::$port);
	    	/////////////////////////////////////////////////////////////////////////mem cache
	    	self::$mem=new Memcache or die("Mem doesn't install!");
	    	self::$mem->connect(self::$mem_ip, self::$mem_port) or die ("Mem Error!");
	    	
	    	self::$table_sql_count=self::$mem->get(self::$sql_count_mem_name);
	    	
	    }
	    
		
		/**
		 * 過濾多餘空白，sql小寫(方便判斷)
		 * @param string $sql     sql語句
		 */
		function filter_sql($sql){
			self::$s_sql = $sql;
			$sql = preg_replace("/\n/"," ",preg_replace("/\r/"," ",preg_replace("/\r\n/"," ",preg_replace("/\t/"," ",trim(strtolower($sql))))));
			
			while(substr_count($sql, "  "))
				$sql=strtr($sql,array("  "=>" "));
				
			return $sql;
			
		}
		
		/**
		 * 判斷語句屬 select,insert,update,delete.
		 * @param string $sql     sql語句
		 * @param bool 	 $b_mem   select是否存入mem_cache
		 */
		function query($sql,$b_mem=true){
			//小寫，過濾換行，tab，重複空白。否則strpos會算錯位置。
			$sql = $this->filter_sql($sql);
			
			$select_pos = strpos($sql,"select");
			$select_pos = $select_pos===false?99999:$select_pos;
			
			$insert_pos = strpos($sql,"insert");
			$insert_pos = $insert_pos===false?99999:$insert_pos;
			
			$update_pos = strpos($sql,"update");
			$update_pos = $update_pos===false?99999:$update_pos;
			
			$delete_pos = strpos($sql,"delete");
			$delete_pos = $delete_pos===false?99999:$delete_pos;
			
			$min_pos = min($select_pos,$insert_pos,$update_pos,$delete_pos);
			
			if($min_pos == 99999){
				
				return false;
				
			}else if( $select_pos==$min_pos ){
				
				return $this->select($sql,$b_mem);//,$b_mem加入可控制是否要存入cache
				
			}else if( $insert_pos==$min_pos ){
				
				return $this->insert($sql);
				
			}else if( $update_pos==$min_pos ){
				
				return $this->update($sql);
				
			}else if( $delete_pos==$min_pos ){
				
				return $this->delete($sql);
				
			}
		}
		/**
		 * select執行
		 * @param string $sql     sql語句
		 * @param bool 	 $b_mem   select是否存入mem_cache
		 */
		private function select($sql,$b_mem){//dbsplitzontal horisplitical normal
			//取得資料表名稱
			$s_table_name = $this->get_table_name($sql);
			#1
			//判斷是否有select子句
			if( substr_count( $sql ,"select" )>1 && substr_count( $s_table_name ,"select" )>0 )return $this->sub_select_from($sql,$s_table_name,$b_mem);
			//memkey產生
			$mem_name = md5($sql);
			//取得limit
			$limit_value  = $this->get_limit($sql);
			#2
			//若是水平分割，若有設定limit則取得限制筆數（未設計完成）
			if( in_array($s_table_name,self::$horisplit_table) && !is_null($limit_value) ){
				
				if( count($limit_value)==0 ){
					
					$start_row  = 0;
					$end_row	= $limit_value[0];
					
				}
				
			}
			//試取memcache
			$row=self::$mem->get($mem_name);
			#3
			if(!$row){
				
				$row=array();
				$s_parser_where=$this->get_where($sql);//不屬於本table比較不過！！
				#4.1
				//in() 回傳空陣列
				$pattern = "/`?[\w]+`?[\s]*in[\s]*\(([^%]?)\)/";
				preg_match($pattern,$s_parser_where, $matches);
				if($matches[0]!=""&&$matches[1]=="")
				{
					#4.2
					return array();
				}
				
				
				#5
				//判斷是否有屬綁定資料表
				if( is_null(self::$bind_table[$s_table_name]) )
				{
					//取得fi_no
					$data_no=$this->get_sql_no($sql);//
				}
				else
				{
					//判斷綁定值是否存在
					if( strpos($s_parser_where,self::$bind_table[$s_table_name])===false )
						return ("{$sql}:請給予".self::$bind_table[$s_table_name]."查尋條件");
					//取得綁定欄位值	
					$data_no=$this->get_sql_no($sql,self::$bind_table[$s_table_name]);//
				}
				#6
				//取得資料褲等物件
				$db=$this->db_selected($s_table_name,$data_no);
				$table_name=$db['table'];
				$db_type=$db['type'];
				$db=$db['db'];
				
				//debug
				if($this->debug_mode)
				{
					echo "data_no:".$data_no."<br>".
						 "table_name:".$table_name."<br>".
						 "parser_where:".$s_parser_where."<br>".
						 "bind_field:".self::$bind_table[$s_table_name]."<br><pre>";
					print_r($db);
					
				}
				//現有規劃中不會回傳多資料表，給水平分割，資料庫分割多選用。
				if(is_array($table_name))
				{
					$s_sql=$sql;
					$sql="";
					$arr=array();
					foreach($table_name as $per_table)
					{
						$sql=strtr($s_sql,array($s_table_name=>$per_table));
						self::$sql=strtr(self::$s_sql,array($s_table_name=>$per_table));
						
						$arr = $db->getAll(self::$sql);
						if(is_array($row) && is_array($arr))
							$row=array_merge($row,$arr);
					}
				}
				else
				{
					//$sql分析用(統一小寫方便分析)，self::$sql執行用(確保大小寫正確)
					$sql=strtr($sql,array($s_table_name=>$table_name));
					self::$sql=strtr(self::$s_sql,array($s_table_name=>$table_name));
					#7
					$row=$db->getAll(self::$sql);
					if(!is_array($row))die("Worng SQL:".self::$sql);
				}
				//取得資料庫名稱
				$db_name=$db->get_db_name();
				//取得資料筆數
				self::$affected_rows=count($row);
				//若為空陣列，$b_mem===false，直接返回結果，無需進行memcache存取
				if(self::$affected_rows==0 || is_array($db) || $b_mem===false){return $row;}
				
				/*---------------------------------------------- mem_cache db_table process ---------------------------------------------*/
				$tmp_table=isset($table_name[0])?$table_name[0]:$table_name;
				$get_row_sql="select * from $tmp_table limit 1;";
				//隨意取得任一筆資料
				$tmp_row=$db->getRow($get_row_sql);
				
				#8
				//判斷是否是view
				$view_result = $db->query("select view_definition from information_schema.views where table_name='$table_name';");
				
				if($db->get_rows()>0)
				{
					$view_definition=$db->fetch_assoc();
					#9
					//取得組成view的所有資料表
					$tables=$this->parser_view($view_definition['view_definition']);
					
					if($this->debug_mode)
					{
						echo "is_view:<br>";
						echo "<br>source sql:".$view_definition['view_definition']."<br>";
						print_r($tables);
					}
					#9_1
					foreach($tables as $table_name)
					{
						if($db_type=="horisplit")
						{
							//若是水平分割過濾最後的數字
							$table_name=$this->process_end_num($s_table_name);
						}
						
						$clumns_mem_name=md5("columns:$table_name");
						$row_tables=self::$mem->get($clumns_mem_name);
						//判斷是否有事先存在memcache裏，沒有就從資料表中取出
						if(!$row_tables)
						{
							$db->query("select column_name from information_schema.columns where table_name='$table_name';");
							
							while($arr = $db->fetch_row())
							{
								$row_tables[]=$arr[0];
							}
							self::$mem->set($clumns_mem_name,$row_tables);
						}
						//是否有where條件
						if($s_parser_where!="no_where")
						{
							#10
							//過濾非本資料表的欄位
							$parser_where=$this->filter_uncompare($s_parser_where,$row_tables);
							#11
							//把sql轉換為php看得懂的格式存取
							$parser_where=$this->convert_tomem($parser_where);
						}
						else
						{
							$parser_where=$s_parser_where;
						}
						#12
						if($parser_where!="no_where")//驗證 filter_where_compare 有沒有過，沒過寫進資料庫中。
						{
							//過濾判斷式
							$filter_after=$this->filter_where_compare($parser_where,$tmp_row);
							ini_set("display_errors", "On");
							//試解析，無法則寫入資料庫並返回搜尋結果
							if(is_null(@eval("return {$filter_after} ;")))
							{
								self::$db->get_connect();
								self::$db->query("insert into sql_error (`sql`,`where`,`filter_after`,`fi_time`) values('$sql','$parser_where','$filter_after',NOW());");
								#14
								return $row;
							}
							ini_set("display_errors", "Off");
						}
						
						if(self::$table_sql_count[$table_name]=="")
						{
							self::$table_sql_count[$table_name]=0;
						}
						#13
						//寫入memcache
						$index_mem_name=$db_name."_".$table_name."_".self::$table_sql_count[$table_name]++;
						$select_index=$parser_where.self::$dot.$mem_name;
						self::$mem->set($index_mem_name,$select_index);
						self::$mem->set(self::$sql_count_mem_name,self::$table_sql_count);
					}
				}
				else
				{
					if($db_type=="horisplit")
					{
						$table_name=$this->process_end_num($s_table_name);
					}
					
					$parser_where=$s_parser_where;
					#11
					//把sql轉換為php看得懂的格式存取
					$parser_where=$this->convert_tomem($parser_where);
					#12
					if($parser_where!="no_where")
					{
						//過濾判斷式
						$filter_after=$this->filter_where_compare($parser_where,$tmp_row);
						ini_set("display_errors", "On");
						//試解析，無法則寫入資料庫並返回搜尋結果
						if(is_null(@eval("return {$filter_after} ;")))
						{
							self::$db->get_connect();
							self::$db->query("insert into sql_error (`sql`,`where`,`filter_after`,`fi_time`) values('$sql','$parser_where','$filter_after',NOW());");
							#14
							return $row;
						}
						ini_set("display_errors", "Off");
					}
					
					if(self::$table_sql_count[$table_name]=="")
					{
						self::$table_sql_count[$table_name]=0;
					}
					#13
					//寫入memcache
					$index_mem_name = $db_name ."_" .$table_name."_" .self::$table_sql_count[$table_name]++;
					$select_index	= $parser_where .self::$dot .$mem_name;
					self::$mem->set($index_mem_name,$select_index);
					self::$mem->set(self::$sql_count_mem_name,self::$table_sql_count);
				}
				
				if($this->debug_mode)
				{
					echo "index_mem_name : {$index_mem_name}:{$select_index}<br>";
					echo "sql_count : ".self::$sql_count_mem_name.":".self::$table_sql_count."<br>";
				}
				#13
				self::$mem->set($mem_name, $row);
				#14
				return $row;
				
			}else{
				
				if($this->debug_mode)
					echo "MEMCACHE RUNNING<BR>";
				//返回筆數	
				self::$affected_rows=count($row);
				#4
				//直接返回結果
				return $row;
				
			}
				
		}
		
		
		private function insert($sql)
		{
			//取得insert筆數
			$insert_rows = $this->get_insert_rows($sql);
			//取得資料表名稱
			$s_table_name=$this->get_table_name($sql);
			#15
			//若存在於必需要no的table array中，沒給值則回傳false
			if( isset(self::$required_key_table[$s_table_name]) )
			{
				if(strpos($sql,self::$no_field_name)!==false)
				{
					$data_no=$this->get_sql_no($sql,self::$no_field_name);
				}
				else
				{
					#16
					//return ("{$sql}:請給予".self::$no_field_name."查尋條件");
					throw new exception("{$sql}:請給予".self::$no_field_name."查尋條件!");
				}
				
			}
			//取得資料表
            $table_name=$table_name_unum=$this->process_end_num($s_table_name);
			
            #17
            //取得資料庫物件
            if( !is_null(self::$bind_table[$table_name]) )
            {
	            //綁定非fi_no的資料表，直接從sql解析需要的欄位值
				$db=$this->db_selected($table_name,$sql);
			}
			else
			{
				if(isset($data_no))//isset(self::$required_key_table[$s_table_name])必須要給fi_no
				{
					$db=$this->db_selected($table_name,$data_no);
				}
				else
				{
					$db=$this->get_table_next_no($table_name);
					$data_no=$db["next_no"];
				}
			}
			
			$table_name=$db['table'];
			$db_type=$db['type'];
			$db=$db['db'];
			
			if($this->debug_mode){
				echo "insert_rows:".$insert_rows."<br>".
					 "table_name:".$table_name."<br>".
					 "bind_field:".self::$bind_table[$s_table_name]."<br>".
					 "data_no:".$data_no."<br><pre>";
				print_r($db);
			}
			
			if($s_table_name!=$table_name)
			{
				$sql=strtr( $sql,array($s_table_name=>$table_name) );
				self::$sql=strtr( self::$s_sql,array($s_table_name=>$table_name) );
			}
			else
			{
				self::$sql = self::$s_sql;
			}
            
            #18
            $result = $db->query(self::$sql);
            
            //若是有多筆insert，回傳第一筆
            if( !is_array($data_no) )
            	$data_no = (is_numeric($db->insert_id())&&$db->insert_id()>0) ? $db->insert_id() : $data_no;
            	
            if( is_numeric($data_no) )
            	$a_no[0] = $data_no;
            else if( is_array($data_no) )
            	$a_no = $data_no;
            	
            if( $insert_rows>1 && is_numeric($data_no) )
            	for($i=0;$i<$insert_rows;$i++)
            		$a_no[$i] = $data_no+$i;
			
			self::$insert_id=$a_no[0];
			
			//if(!$b_mem){return $db->result;}
			
			if(!$result){return false;}
			
			$db_name=$db->get_db_name();
			
			$a_row_after = array();
			#19
			//取得資料庫異動（後）資料
			foreach( $a_no as $per_no )
			{
				$get_row_sql="select * from ".$table_name." where ".self::$no_field_name."='".$per_no."';";
				$a_row_after = array_merge( $a_row_after,$db->getAll($get_row_sql) );
			}
			
			if($this->debug_mode)
			{
				echo "<br>a_no:";
				print_r($a_no);
				echo "<br>get_row_sql:".$get_row_sql;
				echo "<br>a_row_after:";
				print_r($a_row_after);
			}
			///////////////////////////////////////////////////////////database process
			////////////////////////////////////////// mem_cache db_table process
			
			$table_name=$table_name_unum;
			
			if(self::$table_sql_count[$table_name]==""){self::$table_sql_count[$table_name]=0;}
			#20
			$table_sql_rows=self::$table_sql_count[$table_name];
			/**/
			#19_1
			//針對每比資料和對存在mem的所有sql進行比對，比對有影響即刪除mem中的sql
			foreach($a_row_after as $row_after)
			{
				#20_1
				for($i=0;$i<$table_sql_rows;$i++)//若index條件符合刪除，下一個index不會自動往前遞補，優點速度快，缺點數字不漂亮，迴圈會跑越多次
				{
					//判斷是否已經被清除
					if( isset($clear_exists[$i]) )
	                    continue;
	                    
	                $index_mem_name=$db_name."_".$table_name."_".$i;
	                $value=self::$mem->get($index_mem_name);
	                if($value)
	                {
	                    $dot_pos=strpos($value,self::$dot);
	                    $dot_len=strlen(self::$dot);
	                    $parser_where=substr( $value,0,$dot_pos );
	                    #22
	                    //沒有where條件即刪除
	                    if($parser_where=="no_where")
	                    {
		                    $clear_exists[$i]=1;
	                        $mem_name=substr($value,$dot_pos+$dot_len);
	                        self::$mem->delete($mem_name);
	                        self::$mem->delete($index_mem_name);
	                        continue;
	                    }
	                    #21
	                    ini_set("display_errors", "On");
	                	$filter_parser_where_after	= @eval("return ".$this->filter_where_compare($parser_where,$row_after)." ;");
	                	ini_set("display_errors", "Off");
	                	$b_clear_mem_after	  = is_null($filter_result_after)?true:$filter_result_after;
	                    #22
	                    //符合條件即刪除
	                    if( $b_clear_mem_after )
	                    {
		                    $clear_exists[$i]=1;
	                        $mem_name=substr($value,$dot_pos+$dot_len);
	                        self::$mem->delete($mem_name);
	                        self::$mem->delete($index_mem_name);
	                    }       
	                            
	                }
	                else
	                {
		                //已遭其他sql刪除
		                $clear_exists[$i]=1;
	                }
				}
			}
			return true;
		}
		
		private function update($sql){
			
			$s_table_name=$this->get_table_name($sql);
			$parser_where=$this->get_where($sql);
			#23
			//判對是否有綁定欄位，取得欄位值
			if( is_null(self::$bind_table[$s_table_name]) )
			{
				$data_no=$this->get_sql_no($sql);//
			}
			else
			{
				#24
				if( strpos($parser_where,self::$bind_table[$s_table_name])===false )
					return ("{$sql}:請給予".self::$bind_table[$s_table_name]."查尋條件");
				$data_no=$this->get_sql_no($sql,self::$bind_table[$s_table_name]);//
			}
			
			self::$affected_rows=0;
			
			if($parser_where!="no_where")
				$parser_where=" where ".$parser_where;
			else
				$parser_where="";
				
			#25
			//取得資料褲物件
			$db=$this->db_selected($s_table_name,$data_no);
			$table_name=$db['table'];
			$db_type=$db['type'];
			$db=$db['db'];
			if($this->debug_mode)
			{
				echo "data_no:".$data_no."<br>".
					 "table_name:".$table_name."<br>".
					 "parser_where:".$parser_where."<br>".
					 "bind_field:".self::$bind_table[$s_table_name]."<br><pre>";
				print_r($db);
			}
			
			$get_row_sql="select * from ".$table_name.$parser_where;//多table在執行sql時會自動演算
			//資料庫分割回傳陣列資料庫時（以現在規劃基本上不會有）
			if(is_array($db)){
				foreach($db as $pdb){
					
					$pdb->get_connect();
					$pdb->query($get_row_sql);
					
					if($pdb->affected_rows()>0){
						
						$db=$pdb;
						while($row = $db->fetch_assoc())
							$row_before[]=$row;
						
						//$db->query($sql);
						$db->query(self::$s_sql);
						
						$db->query($get_row_sql);
						while($row = $db->fetch_assoc())
							$row_after[]=$row;
							
						$db_name=$db->get_db_name();
						break;
					}
				}
			}else{
				if(is_array($table_name))//水平分割有多資料表時（現在規劃不會有）
				{
					$s_sql=$sql;
					foreach($table_name as $val){
						
						$get_row_sql="select * from ".$val.$parser_where;
						$db->query($get_row_sql);
						while($row = $db->fetch_assoc())
							$row_before[]=$row;
						
						$sql=strtr($s_sql,array($s_table_name=>$val));
						self::$sql=strtr(self::$s_sql,array($s_table_name=>$val));
						$db->query(self::$sql);
						
						$db->query($get_row_sql);
						while($row = $db->fetch_assoc())
							$row_after[]=$row;
					}
				}else{
					
					if($s_table_name!=$table_name)//水平分割的資料表名會不一樣
					{
						$sql=strtr($sql,array($s_table_name=>$table_name));
						self::$sql=strtr(self::$s_sql,array($s_table_name=>$table_name));
					}
					else
					{
						self::$sql=self::$s_sql;
					}
						
					#27
					//取得資料庫異動（前）資料
					$row_before=$db->getAll($get_row_sql);
					#26
					$result = $db->query(self::$sql);
					//if(!$result)die(self::$sql);
					#27
					//取得資料庫異動（後）資料
					$row_after=$db->getAll($get_row_sql);
				}
				$db_name=$db->get_db_name();
			}
			///////////////////////////////////////////////////////////database process
			self::$affected_rows=count($row_before);
			//若是異動資料筆數為0則返回false
			if(self::$affected_rows==0)return false;
			if($db_type=="horisplit")//水平分割
			{
				$table_name=$this->process_end_num($s_table_name);
			}
			if(self::$table_sql_count[$table_name]==""){self::$table_sql_count[$table_name]=0;}
			#28
			//取得資料表儲存sql數
			$table_sql_rows=self::$table_sql_count[$table_name];
			
			if(!is_null($row_before[0]))
			{
				$clear_exists=array();
				#27_1
                foreach($row_before as $key=>$val){
	                #28_1
	                for($i=0;$i<$table_sql_rows;$i++)
	                {
	                    if( isset($clear_exists[$i]) )//已經被清除
                            continue;

                        $index_mem_name=$db_name."_".$table_name."_".$i;
                        $value=self::$mem->get($index_mem_name);
                        //若有資料才進行比對
                        if($value)
                        {
                            $dot_pos=strpos($value,self::$dot);
                            $dot_len=strlen(self::$dot);
                            $parser_where=substr( $value,0,$dot_pos );
                            #30
                            //沒有where條件即刪除
                            if($parser_where=="no_where")
                            {
		                        $clear_exists[$i]=1;
                                $mem_name=substr($value,$dot_pos+$dot_len);
                                self::$mem->delete($mem_name);
                                self::$mem->delete($index_mem_name);
		                        continue;
		                    }
		                    #29
		                    ini_set("display_errors", "On");
		                    //解析測試
                            $filter_result_before = @eval("return ".$this->filter_where_compare($parser_where,$row_before[$key])." ;");
					    	$b_clear_mem_before	  = is_null($filter_result_before)?true:$filter_result_before;
					    	$filter_result_after  = @eval("return ".$this->filter_where_compare($parser_where,$row_after[$key])." ;");
					    	$b_clear_mem_after	  = is_null($filter_result_after)?true:$filter_result_after;
					    	ini_set("display_errors", "Off");
					    	if($this->debug_mode){
						    	echo "where:".$parser_where."<br>";
						    	//echo "change_before_source:";
						    	//print_r($row_before[$key]);
						    	
						    	//echo "<br>";
						    	echo "change_before:".$this->filter_where_compare($parser_where,$row_before[$key])."<br>";
						    	echo "b_before:";
						    	var_dump($b_clear_mem_before);
						    	echo "<br><br>";
						    	//echo "change_after_source:";
						    	//print_r($row_before[$key]);
						    	
						    	//echo "<br>";
						    	echo "change_after:".$this->filter_where_compare($parser_where,$row_after[$key])."<br>";
						    	echo "b_after:";
						    	var_dump($b_clear_mem_after);
						    	echo "<br><br>";
					    	}
					    	#30
					    	//有所符合即刪除
                            if( $b_clear_mem_before || $b_clear_mem_after )
                            {
                                $clear_exists[$i]=1;
                                $mem_name=substr($value,$dot_pos+$dot_len);
                                self::$mem->delete($mem_name);
                                self::$mem->delete($index_mem_name);
                            }
                        }
                        else
                        {
	                        //已由其他sql清除
	                        $clear_exists[$i]=1;
                        }
                    }
				}
            }
			return true;
		}
		
		private function delete($sql)
		{
			$s_table_name=$this->get_table_name($sql);
			$parser_where=$this->get_where($sql);
			#31
			//判斷是否有綁定欄位
			if( is_null(self::$bind_table[$s_table_name]) )
			{
				$data_no=$this->get_sql_no($sql);//
			}
			else
			{
				#32
				if( strpos($parser_where,self::$bind_table[$s_table_name])===false )
					return ("{$sql}:請給予".self::$bind_table[$s_table_name]."查尋條件");
				$data_no=$this->get_sql_no($sql,self::$bind_table[$s_table_name]);//
			}
			
			self::$affected_rows=0;
			
			if($parser_where!="no_where")
				$parser_where=" where ".$parser_where;
				
			else
				$parser_where="";
			#33
			//取得資料褲物件
			$db=$this->db_selected($s_table_name,$data_no); 
			
			$table_name=$db['table'];
			$db_type=$db['type'];
			$db=$db['db'];
			
			if($this->debug_mode){
				
				echo "data_no:".$data_no."<br>".
					 "table_name:".$table_name."<br>".
					 "parser_where:".$parser_where."<br>".
					 "bind_field:".self::$bind_table[$s_table_name]."<br><pre>";
				print_r($db);
				
			}
			$get_row_sql="select * from ".$table_name.$parser_where;
				
			if(is_array($db))//資料庫分割選取多資料庫的狀態（現在規劃中不會發生）
			{
				foreach($db as $pdb)
				{
					$pdb->get_connect();
					//$pdb->query($sql);
					$pdb->query(self::$s_sql);
					
					if($pdb->affected_rows()>0){
						
						$db=$pdb;
						$db->query($get_row_sql);
						while($row = $db->fetch_assoc())
							$row_after[]=$row;
							
						$db_name=$db->get_db_name();
						break;
						
					}
				}
			}
			else
			{
				if(is_array($table_name))//水平分割選取多資料表的狀態（現在規劃中不會發生）
				{
					$s_sql=$sql;
					foreach($table_name as $val)
					{
						$get_row_sql="select * from ".$val.$parser_where;
						$sql=strtr($s_sql,array($s_table_name=>$val));
						self::$sql = strtr( self::$s_sql,array($s_table_name=>$val));
						$row_before=$db->getAll($get_row_sql);
						//$db->query($sql);
						$db->query(self::$sql);
					}
				}
				else
				{
					if($s_table_name!=$table_name)//水平分割資料表名稱不同（現在規劃中不會發生）
					{
						$sql=strtr($sql,array($s_table_name=>$table_name));
						self::$sql=strtr($self::$s_sql,array($s_table_name=>$table_name));
					}
					else
					{
						self::$sql=self::$s_sql;
					}
					#35
					//取得資料庫異動（前）資料
					$row_before=$db->getAll($get_row_sql);
					#34
					$result = $db->query(self::$sql);
					//if(!$result)die(self::$sql);
				}
				$db_name=$db->get_db_name();
			}
			///////////////////////////////////////////////////////////database process
			self::$affected_rows=count($row_before);
			if(self::$affected_rows==0)return false;
			
			if($db_type=="horisplit")//水平分割資料表名稱不同（現在規劃中不會發生）
			{
				$table_name=$this->process_end_num($s_table_name);
			}
			
			if(self::$table_sql_count[$table_name]==""){self::$table_sql_count[$table_name]=0;}
			#36
			$table_sql_rows=self::$table_sql_count[$table_name];
			//限制數量
			if(!is_null($row_before[0])){
				#35_1
		    	foreach($row_before as $key=>$val){
			    	#36_1
		    		for($i=0;$i<$table_sql_rows;$i++){
			    		
		    			if( isset($clear_exists[$i]) )
		    				continue;
		    				
                    	$index_mem_name=$db_name."_".$table_name."_".$i;
						$value=self::$mem->get($index_mem_name);
						//若有資料才進行比對
						if($value)
						{
							$dot_pos=strpos($value,self::$dot);
				    		$dot_len=strlen(self::$dot);
					    	$parser_where=substr( $value,0,$dot_pos );
					    	#38
					    	//沒有where即刪除
				    		if( $parser_where=="no_where" )
				    		{
				    			$clear_exists[$i]=1;
					    		$mem_name=substr($value,$dot_pos+$dot_len);
						    	self::$mem->delete($mem_name);
						    	self::$mem->delete($index_mem_name);
						    	continue;
					    	}
					    	#37
					    	ini_set("display_errors", "On");
					    	//比對測試
					    	$filter_result_before=@eval("return ".$this->filter_where_compare($parser_where,$row_before[$key])." ;");
					    	$b_clear_mem_before = is_null($filter_result_before)?true:$filter_result_before;
					    	ini_set("display_errors", "Off");
				    		#38
				    		//符合即刪除
				    		if( $b_clear_mem_before )
				    		{
				    			$clear_exists[$i]=1;
					    		$mem_name=substr($value,$dot_pos+$dot_len);
						    	self::$mem->delete($mem_name);
						    	self::$mem->delete($index_mem_name);
					    	}
					    }
					    else
					    {
						    //已被其他sql刪除
						    $clear_exists[$i]=1;
					    }
					}
		    	}
		    }
			return true;
		}//end delete
		
		private function sub_select_from($sql,$sub_sql,$b_mem){//dbsplitzontal horisplitical normal
		
			$mem_name = md5($sql);
			//取得子資料表
			$s_table_name = $this->get_table_name($sub_sql);
			$limit_value  = $this->get_limit($sql);
			#2
			if( in_array($s_table_name,self::$horisplit_table) && !is_null($limit_value) )
			{
				if( count($limit_value)==0 )
				{
					$start_row  = 0;
					$end_row	= $limit_value[0];
				}
			}
			
			$row=self::$mem->get($mem_name);
			#3
			if(!$row)
			{
				$row=array();
				//取得where
				$s_parser_where=$this->get_where($sub_sql);
				
				#5
				//判斷是否有綁定欄位，取得欄位值
				if( is_null(self::$bind_table[$s_table_name]) )
				{
					$data_no=$this->get_sql_no($sql);
				}
				else
				{
					if( strpos($s_parser_where,self::$bind_table[$s_table_name])===false )
						return ("{$sql}:請給予".self::$bind_table[$s_table_name]."查尋條件");
						
					$data_no=$this->get_sql_no($sql,self::$bind_table[$s_table_name]);//
				}
				#6
				//取得資料褲物件
				$db=$this->db_selected($s_table_name,$data_no);
				$table_name=$db['table'];
				$db_type=$db['type'];
				$db=$db['db'];
				
				if($this->debug_mode){
					
					echo "function name : sub_select_from<br>"
						."data_no:".$data_no."<br>"
						."table_name:".$table_name."<br>"
						."parser_where:".$s_parser_where."<br>"
						."bind_field:".self::$bind_table[$s_table_name]."<br><pre>";
					print_r($db);
					
				}
				
				if(is_array($table_name))//水平分割不同資料表（目前規劃不會發生）
				{
					$s_sql=$sql;
					$sql="";
					$arr=array();
					
					foreach($table_name as $per_table)
					{
						$sql=strtr($s_sql,array($s_table_name=>$per_table));
						
						if(is_array($row) && is_array($db->getAll($sql)))
							$row=array_merge($row,$db->getAll($sql));
					}
					
				}
				else
				{
					$sql=strtr($sql,array($s_table_name=>$table_name));
					self::$sql=strtr(self::$s_sql,array($s_table_name=>$table_name));
					#7
					$row=$db->getAll(self::$sql);
					//返回非陣列即出錯
					if(!is_array($row))die("sub wrong sql:".self::$sql);
					
					if($this->debug_mode)
					{
						echo "sql : ".$sql."<br>";
						echo "sub_sql : ".$sub_sql."<br>";
					}
				}
				
				$db_name=$db->get_db_name();
				
				self::$affected_rows=count($row);
				//無需memcache即返回結果
				if(self::$affected_rows==0 | is_array($db) | $b_mem===false){return $row;}
				
				/*------------------------------------- mem_cache db_table process ------------------------------------------*/
				$tmp_table=isset($table_name[0])?$table_name[0]:$table_name;
				$get_row_sql="select * from $tmp_table limit 1;";
				$tmp_row=$db->getRow($get_row_sql);
				
				if($db_type=="horisplit"){//水平分割（目前規劃不會用到）
					$table_name=$this->process_end_num($s_table_name);
				}
				#11
				$parser_where=$s_parser_where;
				//sql條件轉換為php可辨視算式
				$parser_where=$this->convert_tomem($parser_where);
				#12
				if($parser_where!="no_where")
				{
					//符號轉換
					$filter_after=$this->filter_where_compare($parser_where,$tmp_row);
					ini_set("display_errors", "On");
					//解析測試
					if(is_null(@eval("return {$filter_after} ;")))
					{
						self::$db->get_connect();
						self::$db->query("insert into sql_error (`sql`,`where`,`filter_after`,`fi_time`) values('$sql','$parser_where','$filter_after',NOW());");
						return $row;
					}
					ini_set("display_errors", "Off");
				}
				
				if(self::$table_sql_count[$table_name]==""){self::$table_sql_count[$table_name]=0;}
				#13
				$index_mem_name=$db_name."_".$table_name."_".self::$table_sql_count[$table_name]++;
				$select_index=$parser_where.self::$dot.$mem_name;
				//cache刪除
				self::$mem->set($index_mem_name,$select_index);
				self::$mem->set(self::$sql_count_mem_name,self::$table_sql_count);
				self::$mem->set($mem_name, $row);
				if($this->debug_mode)
				{
					echo "index_mem_name : {$index_mem_name}:{$select_index}<br>";
					echo "sql_count : ".self::$sql_count_mem_name.":".self::$table_sql_count."<br>";
				}
				return $row;
			}
			else
			{
				if($this->debug_mode)
					echo "MEMCACHE RUNNING<BR>";
				#4	
				self::$affected_rows=count($row);
				return $row;
			}	
		}
		
		///////////////////////////////////////////////////////////////////////////////////////////////////
		/*------------------------------------------dba get	------------------------------------------*/
		///////////////////////////////////////////////////////////////////////////////////////////////////
		//返回資料影響筆數
		function get_affected_rows()
		{
			return self::$affected_rows;
		}
		//返回insert_id
		function get_insert_id()
		{
			return self::$insert_id;
		}
		///////////////////////////////////////////////////////////////////////////////////////////////////
		/*------------------------------------------dba function------------------------------------------*/
		///////////////////////////////////////////////////////////////////////////////////////////////////	
		
		/**
		 * 解析view結構，取出所有table
		 * @param string $structure  view結構
		 */
		private function parser_view($structure){//
			
			$structure=strtolower($structure);
			$start_pos=strpos($structure,"from");
			$start_len=strlen("from");
			$start_end_pos=$start_pos+$start_len;
			
			$end_pos=strlen($structure);
			
			$structure=explode( "join" ,strtr( substr($structure,$start_end_pos,$end_pos) , array("`"=>"") ) );
			
			$table_name[]=trim(substr($structure[0],strpos($structure[0],".")+1));
			
			$rows=count($structure);
			
			for($i=1;$i<$rows;$i++)
			{
				$table_name[]=trim( substr( $structure[$i],strpos($structure[$i],".")+1,strpos($structure[$i],"on") ) );
			}
			
			foreach($table_name as $key=>$val)
			{
				$tmp_array = explode( " " , $val );
				$table_name[$key] = $tmp_array[0];
			}
			
			return $table_name;//array
			
		}//parser_view() end
		
		/**
		 * view 組合table 在做條件比對可能會出現非自己table的欄位，需要過濾
		 * @param string $parser_where  where條件
		 * @param array  $row 			從information_schema取出的field_name
		 */
		private function filter_uncompare($parser_where,$row){//
			//view欄位過濾必須在strtr轉換之前
			preg_match_all('/[^&^|]+/i',$parser_where,$arr);
			
			foreach($arr[0] as $per_where){
				
				preg_match('/[^<^>^=]+/i',$per_where,$field);
				
				if( !isset($row[ $field[0] ]) ){
					//$a_per_where=strtr($per_where,array($field[0]=>$field[1]);
					$parser_where=strtr($parser_where,array($per_where=>"1=1"));//字串越少速度越快
					
				}
				
			}
			
			return $parser_where;
			
		}//filter_uncompare() end
		
		/**
		 * 過濾比對符號
		 * @param string $parser_where  where條件
		 */
		private function filter_where_compare($parser_where,$row){
			
			$row["="]="==";
			while(true){//filter field is null
				
				$key=array_search("",$row);
				
				if(!empty($key))
					$row[$key]="''";
					
				else
					break;
			}
			
			$parser_where=strtr(strtr($parser_where,$row),array(">=="=>">=","<=="=>"<=","()"=>"(1==1)","''''"=>"''","===="=>"=="));
			
			while(true){
				
				$last_str=substr($parser_where,-1);
				
				if($last_str=="&"| $last_str=="|")
					$parser_where=substr($parser_where,-1);
					
				else
					return $parser_where;
					
			}
			
		}
		
		/**
		 * where內容轉換為eval可比較的內容好存入memcache
		 * @param string $parser_where  where條件
		 */
		private function convert_tomem($parser_where)//where內容轉換為eval可比較的內容好存入memcache
		{
			//欄位單引號轉換為一般單引號
			$parser_where=strtr($parser_where,array("`"=>"'"));
			//where欄位部分加上單引號，避免取直時有空白eval比較時會出錯
			$pattern = "/'?([\w]+)'?([\s]*[><=!]|[\s]*like)/";
			$replacement = "'$1'$2";
			$parser_where = preg_replace($pattern, $replacement, $parser_where);
			//filter like
			$pattern = "/'?([\w]+)'?[\s]*like[\s]*'%?([^%]+)%?'/";
			$replacement = "strpos('$1','$2')!==false";
			$parser_where = preg_replace($pattern, $replacement, $parser_where);
			//filter not in
			$pattern = "/'?([\w]+)'?[\s]*not[\s]+in[\s]*\(([^%]+)\)/";
			$replacement = "!in_array('$1',array($2))";
			$parser_where = preg_replace($pattern, $replacement, $parser_where);
			//filter in
			$pattern = "/'?([\w]+)'?[\s]*in[\s]*\(([^%]+)\)/";
			$replacement = "in_array('$1',array($2))";
			$parser_where = preg_replace($pattern, $replacement, $parser_where);
			
			//filter between
			$pattern = "/'?([\w]+)'?[\s]*between[\s]*'?([\S]+)'?[\s]*and[\s]*'?([\S]+)'?/";
			$replacement = "'$1'>='$2' && '$1'<='$3'";
			$parser_where = preg_replace($pattern, $replacement, $parser_where);
			//filter between &&
			$pattern = "/'?([\w]+)'?[\s]*between[\s]*'?([\S]+)'?[\s]*&&[\s]*'?([\S]+)'?/";
			$replacement = "'$1'>='$2' && '$1'<='$3'";
			$parser_where = preg_replace($pattern, $replacement, $parser_where);
			//filter time 
			$parser_where=strtr($parser_where,array("curdate()"=>"date('Y-m-d',time())" ,"now()"=>"date('Y-m-d H:i:s',time())" ,"curtime()"=>"date('H:i:s',time())"));
			//filter date format
			//date_format(time_history, '%Y-%m-%d') => strftime("%Y-%m-%d",strtotime("time_history"));
			$pattern = "/date_format\([\s]*([\w]+)[\s]*,[\s]*'?([\w]+)'?[\s]*\)/";
			$replacement = "strftime('$2',strtotime('$1')";
			$parser_where = preg_replace($pattern, $replacement, $parser_where);
			return $parser_where;
			
		}
		
		/**
		 * 從sql語句中分析取得where條件回傳(不含where，若沒有where則回傳"no where"字串)
		 * @param string $sql  sql語句
		 */	
		public function get_where($sql){//取ㄍwhere字串
			
			$b_str=" where ";
			$start_pos=strpos($sql,$b_str);
			
			if($start_pos!==false){
				
				$sql=substr($sql,$start_pos+strlen($b_str));
				//過濾view的"資料表."
				$rows=substr_count($sql, ".");
				$after_str=$sql;
				
				for($i=0;$i<$rows;$i++){
					
					$dot_pos=strpos($tmp_sql,".");
					
					if($dot_pos===false)break;
					
					$before_str=substr($tmp_sql,0,$dot_pos+1);
					$sql=strtr( $sql, array( substr($before_str,strpos($before_str," ")+1) =>"") );
					$after_str=substr($after_str,$dot_pos+1);
					
				}
				$a_str=array(" order by " ," limit " ,";" ," union " );
				
				$rows=count($a_str);
				$str_length=strlen($b_str);
				$after_pos=strlen($sql);
				
				foreach($a_str as $val){
					
					$tmp_pos=strpos($sql,$val);
					
					if( $tmp_pos!==false && $tmp_pos<$after_pos ){
						
						$after_pos=$tmp_pos;
						
					}
					
				}
				
				$sql=strtr( substr($sql,0,$after_pos), array(" and "	=>	" && ",
															 " or "		=>	" || ", 
															 " is "		=>	" = ", 
															 " null "	=>	" '' ") );
				
				return  $sql;
				
			}else{
				
				return "no_where";
				
			}
			
		}//get_where() end
		
		/**
		 * 從sql語句中分析取得limit條件回傳
		 * @param string $sql  sql語句
		 */
		private function get_limit($sql){//取limit比數
			
			$limit_pos = strpos($sql," limit ");
			$limit_len = strlen(" limit ");
			
			if( $limit_pos!==false ){
				$limit_value = explode(",",trim(substr($sql,$limit_pos+$limit_len)));
				
				if( count($limit_value)==1 )
					return array($limit_value[0]);
					
				else
					return array($limit_value[0],$limit_value[1]);
					
			}
			
			return null;
			
		}//get_limit() end
		
		/**
		 * 從sql語句中分析取得order by條件回傳
		 * @param string $sql  sql語句
		 */
		private function get_order_by($sql){//取得order順序
			
			$order_by_pos = strpos($sql," order by ");
			$order_by_len = strlen(" order by ");
			
			if( $order_by_pos!==false ){
				
				$order_value = explode(",",trim(substr($sql,$order_by_pos+$order_by_len)));
				
				if( count($limit_value)==1 )
					return array($limit_value[0]);
					
				else
					return array($limit_value[0],$limit_value[1]);
					
			}
			
			return null;
			
		}//get_order_by() end
		
		/**
		 * 從sql語句中分析取得table名稱
		 * @param string $sql  sql語句
		 */
		public function get_table_name($sql){
			
			$key_word=array("from","update","into");
			
			foreach($key_word as $value){
				
				if(strpos( $sql, $value )!==false){
					
					$key_word_pos = strpos( $sql, $value );
					$key_word_len = strlen($value);
					
					$end_word	  = $value=="into"?"(":" ";
					$tmp_table	  = trim(substr($sql,$key_word_pos+$key_word_len));
					
					if(strpos( $tmp_table, "(" )===0){
						
						return $this->correspond_parentheses($tmp_table);
						
					}
					
					$table_name	  = explode( $end_word , $tmp_table );
					$del_char 	  = array(',',';');  //需要濾掉的字元
					
					foreach($del_char as $val)
						$table_name[0] = strtr($table_name[0],array($val=>"","`"=>""));
						//$table_name[0] = str_replace($val,"",$table_name[0]); 
					return trim($table_name[0]);
					
				}
				
			}
			
			return false;
			
		}//end get_table_name
		
		/**
		 * 從字串中判斷出該句的前後括號，回傳中間字串(不含最外圍括號)
		 * @param string $str  字串
		 */
		private function correspond_parentheses($str){
			
			$tmp_word = $str;
			$ago_parentheses_pos = strpos( $tmp_word, "(" );
			$after_parentheses_pos = strpos( $tmp_word, ")" );
			$end_parentheses_pos = 0;
			
			if( $ago_parentheses_pos!==false ){
				
				$end_word_count = 1;
				$start_parentheses_pos = $ago_parentheses_pos + 1;
				$tmp_word = substr( $tmp_word ,$start_parentheses_pos );
				
			}else{
				
				return "";
				
			}
			
			while( $after_parentheses_pos!==false ){
				
				$ago_parentheses_pos = strpos( $tmp_word ,"(" );
				$after_parentheses_pos = strpos( $tmp_word ,")" );
				
				if( $ago_parentheses_pos<$after_parentheses_pos && $ago_parentheses_pos!==false ){
					
					++$end_word_count;
					$tmp_word = substr( $tmp_word ,$ago_parentheses_pos+1 );
					$end_parentheses_pos += $ago_parentheses_pos+1;
					
				}else{
					
					--$end_word_count;
					
					if( $end_word_count==0 ){
						
						$end_parentheses_pos += $after_parentheses_pos;
						return substr( $str ,$start_parentheses_pos ,$end_parentheses_pos );
						
					}else{
						
						$tmp_word = substr( $tmp_word ,$after_parentheses_pos+1 );
						$end_parentheses_pos += $after_parentheses_pos+1;
						
					}
					
				}
				
			}
			
			return "";
			
		}
		
		/*
		 *僅insert用到
		 *從資料表名稱去判斷下一個no數
		 *資料庫分割絕對不會用到這個涵式(必須給fi_no的table可透過get_sql_no，剩下的(bind_table)必須給索引值用db_selected取得資料庫)
		 */
		private function get_table_next_no($table_name){
            
        	$db=self::$db;
        	$db_type = "NORMAL";
        	
        	if( in_array($table_name,self::$horisplit_table) ){
	        	
        		$db_type = "horisplit";
        		$table_tree = $db->get_table_tree();
        		$table_tree_str = implode(";",$table_tree);
        		$index = substr_count($table_tree_str,";".$table_name);//加上前綴字初步防止資料表內涵有相同開頭的關鍵字
        		
        		while(strpos($table_tree_str,$table_name.--$index)===false){}//實際用全名去驗證資料表是否存在，否則逐一減一(因為取來是次數，而index由0編起，因此要先減一)
        		
        		$table_name = $table_name.$index;
        		
        	}
        	
            $db->get_connect();
            $db_name=$db->get_db_name();
            $sql="SELECT `AUTO_INCREMENT`
					FROM  INFORMATION_SCHEMA.TABLES
					WHERE TABLE_SCHEMA = '$db_name'
					AND   TABLE_NAME   = '$table_name';";
			$db->query($sql);
			$row = $db->fetch_assoc();
			$next_no = $row["AUTO_INCREMENT"];
			
			if( $db_type=="horisplit" && $next_no%self::$horisplit_limit==1 ){
				
				$new_table_name=$table_name.(++$index);
				$db->query("CREATE TABLE ".$new_table_name." AS SELECT * FROM ".$table_name);
				$db->query("truncate table ".$$new_table_name);
				$db->query("ALTER TABLE ".$new_table_name." AUTO_INCREMENT=".$next_no);
				$table_name=$new_table_name;
				
			}
			
			return array("db"=>$db,"table"=>$table_name,"type"=>$db_type,"next_no"=>$next_no);
            //return $next_no;
		}

		/**
		 * 從sql中判斷出該句fi_no的值
		 * @param string $sql  sql語句
		 */
		private function get_sql_no($sql){
			
			$field=func_num_args()==2?func_get_arg(1):(self::$no_field_name);
			
			if(strpos($sql,'insert')!==false){
				
				return $this->get_value_insert_field($sql,$field);
				
			}else{
				
				$parser_where=$this->get_where($sql);
				
				if($parser_where!="no_where")
					return $this->get_value_after_field($parser_where,$field);
				
				else
					return false;
					
			}
			
		}
		
		private function get_value_insert_field($sql,$field){//僅供insert 
			
			$filter=array(" "=>"", "("=>"", ")"=>"", "'"=>"", '"'=>"", "`"=>"");
			$b_str="(";//before
			$a_str=")";//after
			
			$b_pos=strpos($sql,$b_str);
			$b_len=strlen($b_str);
			$b_end_pos=$b_pos+$b_len;
			
			$a_pos=strpos($sql,$a_str);
			
			$tmp_row=explode(",", strtr( substr($sql,$b_end_pos,$a_pos-$b_end_pos) ,$filter ) );
			$tmp_fields=count($tmp_row);//欄位數
			
			$field_index=array_search($field,$tmp_row,true);
			
			if($field_index===false)return false;
			
			$b_str="values";
			$b_pos=strpos($sql,$b_str);
			$b_len=strlen($b_str);
			$b_end_pos=$b_pos+$b_len;
			
			$insert_value=substr(trim(substr($sql,$b_end_pos)),1,-1);
			$split_dot=",";//分割欄位符號
			$start_pos=strpos($insert_value,"'");//找到內容引號
			
			if($start_pos!==false){
				
				$start_pos2=strpos($insert_value,'"');
				$quotes="'";
				
				if($start_pos2!==false){
					
					if($start_pos2<$start_pos){
						
						$quotes='"';
						
					}
					
				}
				
			}
			$in_quotes=false;
			$a_value=str_split($insert_value);
			$quotes_num=0;
			
			foreach($a_value as $val){
				
				if( $val==$split_dot && !$in_quotes){
					
					$result[]=$tmp_word;
					$tmp_word="";
					continue;
					
				}
				
				if( $val==$quotes && $perv_val!="\\" ){
					
					$in_quotes=$in_quotes?false:true;
					continue;
					
				}
				
				$prev_val=$val;
				$tmp_word.=$val;
				
			}
			
			$result[]=$tmp_word;
			$rows=count($result)/$tmp_fields;
			
			if(!is_int($rows)){die('insert 欄位數不符');}
			
			if($rows==1)
				return $result[$field_index];
				
			elseif($rows>1)
				for($i=0;$i<$rows;$i++)
					$arr[]=$result[$tmp_fields*$i+$field_index];
					
			return $arr;
			
		}
		
		private function get_value_after_field($sql,$field){//找到字串後面第一串數字(單字)，不含有大於小於//可與get_value_range合併，效能會較好
			
			$s_sql=$sql;
			$key_word_pos=strpos($sql,$field);
			$b_first_number = false;
			
			if($key_word_pos!==false){
				
				$key_word_len=strlen($field);
				$sql= trim(substr($sql,$key_word_pos+$key_word_len));
				
			}else{
				
				return false;// 沒有該欄位;
				
			}
			
			$arr=str_split($sql);
			
			if( substr_count($sql, $field)>1)//出現兩次以上
			{
				return $this->get_value_range($s_sql,$field);
			}
			
			foreach($arr as $value)
			{
				if($value==">"||$value=="<")
				{
					return $this->get_value_range($s_sql,$field);
				}
				if( preg_match("/^([a-z0-9]+)$/", $value) )//用正規表示法，過濾是否是0~9 a~z
				{
					$tmp_val.=$value;
					$b_first_number=true;//判斷第一個數字(英文)出現時
				}
				else if ( $b_first_number )
				{
					break;
				}
			}
			
			return $tmp_val;
			
		}//end get_num_after_no
		
		private function get_value_range($sql,$field){
			
			$end_dot=array(")","&","|",";","(");
			$rows_dot=count($end_dot);
			$rows=substr_count($sql, $field);
			
			for($i=0;$i<$rows;$i++){
				
				$key_pos=strpos($sql,$field);
				$dot_pos=strlen($sql);
				
				for($j=0;$j<$rows_dot;$j++){
					
					$tmp_pos= strpos($sql,$field);
					$dot_pos=($tmp_pos!==false)? ($dot_pos<$tmp_pos)?$tmp_pos:$dot_pos :$dot_pos;
					
				}
				
				$tmp_val.=substr($sql,$key_pos,$dot_pos-$key_pos);
				$sql=substr($sql,$dot_pos+1);//if縮寫無法記算end_dot length
				
			}
			
			if( in_array(substr($tmp_val,-1),$end_dot) ){
				
				return substr($tmp_val,0,-1);
				
			}else{
				
				return $tmp_val;
				
			}
		}
		
		private function get_insert_rows($sql){//僅供insert 使用 僅供insert 1筆 
			
            return substr_count( strtr($sql ,array(" "=>"")) ,"),(" ) + 1;
            //不判斷字串中是否有insert
		}
		
		/**
		 * 給table_name會給出相對應的db，且做好連線 no適用於水平分割
		 * @param string $table_name  由table_name抓取該屬性是水平分割或資料庫分割或一般資料表
		 * @param string $data_no  直接給予fi_no可快速演算出水平分割編號
		 */
		private function db_selected($table_name,$data_no=false){
			
			if( in_array($table_name,self::$dbsplit_member_table) || in_array($table_name,self::$dbsplit_goods_table) ){
				
				if( in_array($table_name,self::$dbsplit_member_table) )
				{
					$db			= self::$db_member;
					$dbsplit_len= self::$dbsplit_member_len;
				}
				else
				{
					$db			= self::$db_goods;
					$dbsplit_len= self::$dbsplit_goods_len;
				}
				
				$bind_table=self::$bind_table;
				
				if($this->debug_mode){echo "table_name:".$table_name."<br>bind_table:".$bind_table[$table_name]."<br>data_no:".$data_no."<br>";}
				
				if( !is_null($bind_table[$table_name])&&$data_no!==false ){//存在於需要綁定(追隨上一層資料放在同一資料庫內)的資料時
					
					$field		= $bind_table[$table_name];
					//select update delete 皆給數字 insert給sql
					$field_val	= is_numeric($data_no)?$data_no:$this->get_value_insert_field($data_no,$field);
					$index		= $field_val%$dbsplit_len;
					$db[$index]->get_connect();
					
					return array("db"=>$db[$index],"table"=>$table_name,"type"=>"dbsplit");
					
				}else if(is_numeric($data_no)){
					
					$index		= $data_no%$dbsplit_len;
					$db[$index]->get_connect();
					
					return array("db"=>$db[$index],"table"=>$table_name,"type"=>"dbsplit");
					
				}else{
					
					throw new exception("didn't find key word!");
					
				}
				
			}else{
				
				$type="NORMAL";
				$db=self::$db;
				$horisplit_split=self::$horisplit_limit;
				$db->get_connect();
				$table_name_unum=$this->process_end_num($table_name);
				$index = "";
				
				if( in_array($table_name_unum,self::$horisplit_table) ){
					
                    $type="horisplit";
                    $index=0;
                    $table_tree=$db->get_table_tree();
                    natsort($table_tree);//自然排序法//有大小寫之分
                    $b_find=false;

                    if(is_numeric($data_no)){
	                    
	                    $table_name=$table_name.floor($data_no/$horisplit_split);
	                    
					}else if( strpos($data_no,self::$no_field_name)!==false ){//可選依照編號選取多個table
					
                        $data_no	= strtr($data_no,array(" "=>""));
                        $tmp_arr	= $this->get_table_next_no($table_name);
                        $last_no	= $tmp_arr["next_no"]-1;
                        $start_no	= 0;
                        $end_no		= self::$horisplit_limit;
                        $index		= 0;
                        $b_start	= false;
                        
                        $table_tree_str = implode(";",$table_tree);
		        		$index_max 	= substr_count($table_tree_str,";".$table_name);
		        		
                        while( strpos($table_tree_str,$table_name.--$index_max) ){}
                        ini_set("display_errors", "On");
                        while( $index<=$index_max ){
                        	if( eval("return (".
                               		strtr( $data_no,array(self::$no_field_name.">"=>"$end_no>", 
                               							  self::$no_field_name."<"=>"$start_no<", 
                                                          "<".self::$no_field_name=>"<$end_no", 
                                                          ">".self::$no_field_name=>">$start_no"  ) ).
                                            ") & (".$start_no."<".$last_no.");") ){
	                                            
	                            $arr[]=$table_name.$index++;
	                            $start_no+=$horisplit_split;
	                            $end_no+=$horisplit_split;
	                            $b_start=true;
	                            
                            }else if($b_start){
	                            
	                            break;
	                            
                            }
                            
                        }
                        ini_set("display_errors", "Off");
                        $table_name=$arr;
                        
                    }else{
                    
                        $table_tree_str = implode(";",$table_tree);
		        		$index = substr_count($table_tree_str,";".$table_name);
		        		
		        		while($index>0){
			        		
			        		$arr[]=$table_name.--$index;
			        		
		        		}
		        		
                        $table_name=$arr;
                        
                    }
                    
                    return array("db"=>$db,"table"=>$table_name,"type"=>$type);
                    
				}
				
				return array("db"=>$db,"table"=>$table_name.$index,"type"=>$type);
				
			}
			
		} //db_selected
		
		private function process_end_num($key,$type="STR"){
			
			$i=0;
			$num = "";
			
			while(true){
				
				$result	 = is_numeric(substr($key,--$i));
				
				if($result)
					$num = substr($key,$i);
					
				else
					break;
			}
			
			if( $type=="STR" )
				return !$num?$key:strtr($key,array($num=>""));
				
			else if($type=="NUM")
				return $num;
				
		}
		
	}
	
	class db{
 		
 		private $conn;
 		private $result;
 		
	 	private $server;
		private $db_name;
		private $user_id;
		private $password;
		private $prot;//mysql默認
		//預設連global2buy資料庫
		function db($_server="localhost",$_user_id="global2user",$_password="wRusUdRAjesWuqE3rech",$_db_name="global2buy",$_port=3306){
	    	//echo "start"; 
	    	$this->server=$_server;
	    	$this->user_id=$_user_id;
	    	$this->password=$_password;
	    	$this->db_name=$_db_name;
	    	$this->port=$_port;
	    }
	    
	    function query($sql){//sql執行
			//connect
			//echo $sql."<br>";
			if(is_null($this->conn))$this->connect();
			$this->result=mysqli_query($this->conn,$sql);//die("MySQL query error".mysqli_error($this->conn));
			return $this->result;
			//close
		}
		
		function fetch_row(){
			//connect
			return mysqli_fetch_row($this->result);
			//close
		}
		
		function fetch_assoc(){
			return mysqli_fetch_assoc($this->result);
		}
		
		function getAll($sql){//get all data
			$this->query($sql);
			$arr=array();
			if(!$this->result)
			{
				return false;
			}
			else
			{
				while($row = mysqli_fetch_assoc($this->result))
				{
					$arr[]=$row;
				}
				return $arr;
			}
		}
		function getRow($sql){//get once data
			$this->query($sql);
			if(!$this->result){
				return false;
			}else{
				return mysqli_fetch_assoc($this->result);//mysqli_fetch_row//取出陣列的key為數字
			}
		}
		
		function get_rows(){
			if(!$this->result){
				return false;
			}else{
				return mysqli_num_rows($this->result);
			}	
		}
		
		function get_all($sql){//全部塞進一個array
			$this->query($sql);
			$arr=array();
			if(!$this->result)
			{
				return false;
			}
			else
			{
				while($row = mysqli_fetch_assoc($this->result))
				{
					foreach($row as $value)
					{
						$arr[]=$value;
					}
				}
				return $arr;
			}
		}
		
		function get_table_tree(){
			$sql = "SHOW TABLES FROM ".$this->db_name;
			return $this->get_all($sql);
		}
		
		function insert_id(){
			return mysqli_insert_id($this->conn);
		}
		
		function affected_rows(){
			return mysqli_affected_rows($this->conn);
		}
		
		function select_db($db_name){
			mysqli_select_db($this->conn,$db_name);
		}
		
		function connect(){//由dba實做
			try{
				$this->conn=mysqli_connect($this->server,$this->user_id,$this->password,$this->db_name,$this->port);
			}catch (Exception $e) {
			    if($this->conn){
				    die("Could not connect to MySQL".mysqli_error($this->conn));
			    }else{
					die("Could not connect to MySQL");
			    }
			}
			$this->query("SET NAMES 'UTF8'"); 
			$this->query("SET CHARACTER SET UTF8");
			$this->query("SET CHARACTER_SET_RESULTS=UTF8");
			return $this->conn;
			/*$this->conn=mysqli_connect($this->server.":".self::$port,self::$user_id,self::$password)
				or die("Could not connect to MySQL".mysql_error());
			select_db(self::$db_name);
			return true;*/
		}
		
		function get_connect(){//判斷是否連線，否則進行連線
			return $this->conn?true:$this->connect();
			//return $this->conn?'t':'f';
		}
		
		function close(){
			return mysqli_close($this->conn);
		}
		
		function get_db_name(){
			return $this->db_name;
		}
		
		function getIp(){
			return $this->server;
		}
 	}
//ini_set("display_errors", "On");
//error_reporting(E_ALL);

//date_default_timezone_set("Asia/Taipei");
	
?>