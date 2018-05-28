<?php
/**
 * 分頁物件
 */
class Pager{
    
    private $return = array();
    private $start_page;
    private $end_page;
    private $page_rows;
    private $rows_per_page;
    private $total_rows;
    
    /**
     * 建構式
     * @param array $sql     		sql
     * @param int   $start_page     起始頁數
     * @param int   $rows_per_page  每頁幾筆資料
     */
    function __construct()
    {
        
    }
    
    
    /**
     * 取得目前頁碼
     * @return int   目前頁碼
     */
    public function get_page_number()
    {
        return $this->start_page;
    }
    
    /**
     * 取得結束頁碼
     * @return int   結束頁碼
     */
    public function get_final_page_number()
    {
        return $this->end_page;
    }
    
    /**
     * 取得目前頁面資料筆數
     * @return int   目前頁面資料筆數
     */
    public function get_page_rows()
    {
        return $this->page_rows;
    }
    
    /**
     * 取得每頁資料筆數
     * @return int   每頁資料筆數
     */
    public function get_rows_per_page()
    {
        return $this->rows_per_page;
    }
    
    /**
     * 取得總資料筆數
     * @return int   每頁資料筆數
     */
    public function get_total_rows()
    {
        return $this->total_rows;
    }
    
    /**
     * 顯示分頁區塊
     */
    public function display()
    {
        $params = "";
        if(!empty($_GET))
        {
            foreach($_GET as $key => $value)
            {
                if($key == 'page' || $key== 'rpp')continue;
                $params .= $key."=".$value."&";
            }
        }
                	
        if($this->get_rows_per_page()!="")
        {
            $select = "<select name='start_page'>";
            $plen = ceil($this->get_total_rows()/$this->get_rows_per_page());
            for($i = 1; $i <= $plen; $i++)
            {
                $select.= "<option value='".$i."'>".$i."</option>";
            }
            $select .= "</select>";
            
            $display = "<div class='pager shadowRoundCorner' style='text-align:center;padding:2px;background:rgba(255,255,255,0.2);'>";
            $display .= "<span>總筆數：".$this->get_total_rows()." 筆, 共 ".ceil($this->get_total_rows()/$this->get_rows_per_page())." 頁, 每頁顯示<input name='show_rows_num' type='text' value='' style='width:40px;'>&nbsp;筆記錄，";
            $display .= $this->get_page_number()==1?"":"<a style='text-decoration:none;' href='javascript:void(0);location.href=location.href.split(\"?\")[0]+\"?".$params."page=1&rpp=".$this->get_rows_per_page()."\"'><span style='padding:0px 10px 0px 10px;'>|<</span></a>&nbsp;";
            $display .= $this->get_page_number()==1?"":"<a style='text-decoration:none;' href='javascript:void(0);location.href=location.href.split(\"?\")[0]+\"?".$params."page=".($this->get_page_number()-1<1?1:$this->get_page_number()-1)."&rpp=".$this->get_rows_per_page()."\"'><span style='padding:0px 10px 0px 10px;'><</span></a>&nbsp;";
            $display .= $this->get_page_number()==$this->get_final_page_number()?"":"<a style='text-decoration:none;' href='javascript:void(0);location.href=location.href.split(\"?\")[0]+\"?".$params."page=".($this->get_page_number()+1>$this->get_final_page_number()?$this->get_final_page_number():$this->get_page_number()+1)."&rpp=".$this->get_rows_per_page()."\"'><span style='padding:0px 10px 0px 10px;'>></span></a>&nbsp;";
            $display .= $this->get_page_number()==$this->get_final_page_number()?"":"<a style='text-decoration:none;' href='javascript:void(0);location.href=location.href.split(\"?\")[0]+\"?".$params."page=".$this->get_final_page_number()."&rpp=".$this->get_rows_per_page()."\"'><span style='padding:0px 10px 0px 10px;'>>|</span></a>";
            $display .="跳至:&nbsp;".$select."&nbsp;頁</span>";
            $display .="<input name='show_rows' type='button' value='更新' onclick='var p=$(this).parent().find(\"select[name=start_page]\").val(),r=$(this).parent().find(\"input[name=show_rows_num]\").val();location.href=location.href.split(\"?\")[0]+\"?".$params."page=\"+p+\"&rpp=\"+r'>";
            $display .="</div>";
            /*$display .="<script>
            				$(function(){
            					\$(\".pager\").find(\"select[name=start_page]\").val(\"".$this->get_page_number()."\");
            					\$(\".pager\").find(\"input[name=show_rows_num]\").val(\"".$this->get_rows_per_page()."\");
            					\$(\".pager a\").css({\"color\":\"#000\"});});
            					\$(\".pager\").find(\"select[name=start_page]\").change(function(){
            						var p=$(\".pager\").find(\"select[name=start_page]\").val(),r=$(\".pager\").find(\"input[name=show_rows_num]\").val();
            						location.href=location.href.split(\"?\")[0]+\"?".$params."page=\"+p+\"&rpp=\"+r
            				})
            			</script>";
            /**/
            $display .="<script>
			        	$(function(){
				        	\$(\".pager\").find(\"select[name=start_page]\").val(\"".$this->get_page_number()."\");
            				\$(\".pager\").find(\"input[name=show_rows_num]\").val(\"".$this->get_rows_per_page()."\");
				        	$(\"input[name=show_rows_num]\").keydown(function(event){
					        	if(event.which==13){
						        	$(this).parent().parent().find(\"input[name=show_rows]\").trigger('click');
								}
							});
				        	\$(\"#pager a\").css({\"text-decoration\":\"none\",\"color\":\"#000\"});
				        	\$(\".pager\").find(\"select[name=start_page]\").each(function(){
					        	$(this).change(function(){
	            						var p=$(this).parent().find(\"select[name=start_page]\").val(),r=$(this).parent().find(\"input[name=show_rows_num]\").val();
	            						location.href=location.href.split(\"?\")[0]+\"?".$params."page=\"+p+\"&rpp=\"+r;
	            				})
	            			})
			        	})
			        </script>";
            echo $display;
        }
    }
    
    /**
     * 資料取得
     * @return int   每頁資料筆數
     */
    public function query($sql)
    {
		if(empty($sql))return array();
        
        $start_page= isset($_GET["page"])?$_GET["page"]:1;
        $rows_per_page=isset($_GET["rpp"])?$_GET["rpp"]:20;
        
        if($start_page < 1 || $rows_per_page < 1) return array();
        if( $this->start_page > $this->end_page )  $this->start_page = $this->end_page;
        
        require_once("../swop/library/dba.php");
	    $this->dba = new dba();
	    //sql沒有limit則自動加上去，  limit (頁數-1),單頁顯示筆數
	    if( strpos($sql, "limit")!==false||strpos($sql,"select"===false) ) 
	    {
		    return $this->dba->query($sql);
	    }
	    $where = $this->dba->get_where($sql);
	    $where = $where=="no_where"?"":"where $where ";
	    $table_name = $this->dba->get_table_name($sql);
	    
        $len_sql = "select count(1) as count from $table_name $where ";
        $result = $this->dba->query($len_sql);
        $len = $result[0]["count"];
        
        $this->total_rows = $len;
        $this->start_page = $start_page;
        $this->end_page = ceil($len/$rows_per_page);
        $this->rows_per_page = $rows_per_page;
        
        if( $this->start_page > $this->end_page )  $this->start_page = $this->end_page;
	    if( strpos($sql, "limit")===false ) 
	    {
		    $sql.=" limit ".($this->start_page-1)*$this->rows_per_page.",".$this->rows_per_page;
		    $result = $this->dba->query($sql);
	    }
	    $this->page_rows = count($result);
        return $result;
    }
    
    
    /**
     * @return int   取得dba
     */
    public function get_dba()
    {
        return $this->dba;
    }
    
}

