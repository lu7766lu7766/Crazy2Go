<?php
class Main_Controllers {

	public function __construct($base) {
		$this->base = $base;
	}

	public function home() {
		$this->js = array('member');
		
                require_once "swop/models/member.php";
		$swop = new Main_Models();
                $swop->home();
                
		include_once "swop/language/".$this->base['lang']."/common.php";
		include_once "swop/view/template/top.php";
		include_once "swop/view/member.php";
		include_once "swop/view/template/bottom.php";
		return $this;
	}

        public function code_login() {
		require_once "swop/library/verification.php";
		$verification = new Library_Verification();
		$verification->kernel();
		$_SESSION['code_login'] = $verification->code;
	}
        
	public function ajax_login() {
		if($_SESSION['info'] != "") {
			$this->echo_json("2", "您已經登入了");
		}
		
		if($_POST['account'] == "" || $_POST['password'] == "") {
			$this->echo_json("3", "請輸入帳號密碼");
		}
                
                if(strtolower($_POST['verification']) != strtolower($_SESSION['code_login'])) {
			$this->echo_json("4", "驗證碼錯誤");
		}

		require_once "swop/models/member.php";
		$swop = new Main_Models();
		$swop->ajax_login();
                
		if(count($swop->rows) == 1) {
			$_SESSION['info'] = $swop->rows[0];
                        unset($_SESSION["code_login"]);
			$this->echo_json("0", "登入成功", 1);
		}
		else {
			$this->echo_json("1", "登入失敗", 1);
		}
	}
	
	public function ajax_logout() {
		unset($_SESSION['info']);
		
		$this->echo_json("0", "登出成功", 1);
	}
	
	public function register() {
		$this->js = array('member');
		
		include_once "swop/language/".$this->base['lang']."/common.php";
		include_once "swop/view/template/top.php";
		include_once "swop/view/member_register.php";
		include_once "swop/view/template/bottom.php";
		return $this;
	}
        
        public function register2() {
		$this->js = array('member');
		
		include_once "swop/language/".$this->base['lang']."/common.php";
		include_once "swop/view/template/top.php";
		include_once "swop/view/member_register2.php";
		include_once "swop/view/template/bottom.php";
		return $this;
	}
	
	public function ajax_register() {
                
		if($_POST['id'] == "" || $_POST['email'] == "" || $_POST['password'] == "" || $_POST['phone_head'] == "" || $_POST['phone_body'] == "") {
                        $this->echo_json("2", "必填欄位不得空白");
		}
                
		if(strlen($_POST['id']) < 2 || strlen($_POST['id']) > 10) {
			$this->echo_json("3", "暱稱長度不得小於2碼或大於10碼");
		}
		
		if(!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
			$this->echo_json("4", "請輸入正確的信箱");
		}
		
		if(strlen($_POST['password']) < 8 || strlen($_POST['password']) > 16) {
			$this->echo_json("5", "密碼長度不得小於8碼或大於16碼");
		}
		
                if(strlen($_POST['phone_head']) < 1 || strlen($_POST['phone_head']) > 3) {
			$this->echo_json("6", "國際碼長度不得小於1碼或大於3碼");
		}
                
		if(strlen($_POST['phone_body']) != 11) {
			$this->echo_json("7", "手機號碼長度應為11碼");
		}
		
                if(!is_numeric($_POST['phone_head']) || !is_numeric(substr($_POST['phone_head'], 0, 1)) || substr_count($_POST['phone_head'], ".") > 0) {
			$this->echo_json("8", "國際號碼僅能輸入數字");
		}
                
		if(!is_numeric($_POST['phone_body']) || !is_numeric(substr($_POST['phone_body'], 0, 1)) || substr_count($_POST['phone_body'], ".") > 0) {
			$this->echo_json("9", "手機號碼僅能輸入數字");
		}
                
                $_POST["phone"] = $_POST['phone_head']."-".$_POST['phone_body'];
                $_POST["birthday"] = date("Y-m-d",mktime(0,0,0,$_POST["bdm"],$_POST["bdd"],$_POST["bdy"]));
		
		require_once "swop/models/member.php";
		$swop = new Main_Models();
		$swop->ajax_register();
 
                function sendMail($swop)
                {
                    $paddAddress = $swop->mail;
                    $pname = $swop->name;
                    $pSubject = 'Crazy2Go 會員註冊';
                    $pBody = '完成註冊請點選 <a href="http://www.crazy2go.com/member/register2/?key='.$swop->vali_code.'&email='.$swop->mail.'">http://www.crazy2go.com/member/register2/?key='.$swop->vali_code.'&email='.$swop->mail.'</a>';
                    
                    require_once "swop/library/mailer.php";
                    $mail = new Library_Mailer();
                    $mail->postal($paddAddress, $pname, $pSubject, $pBody); //收信人Email, 收信人名稱, 寄件標題, 信件HTML
                    
                    if($mail->mailer_content == 0) {
                            //echo "成功";
                    }
                    else {
                            //echo "失敗";
                    }
                }
                
		if($swop->repeat == 0) {
			if($swop->results) {
                                sendMail($swop);
				$this->echo_json("0", "註冊成功，請至信箱驗證", 1);
			}
			else {
				$this->echo_json("1", "註冊失敗", 1);
			}
		}
		else {
			$this->echo_json("2", "信箱或手機重複", 1);
		}
	}
        
        public function ajax_member_register() {
            
                if(strlen($_POST['password']) !=0 && strlen($_POST['repassword']) != 0) {
                        if($_POST['password'] != $_POST['repassword'])
			$this->echo_json("1", "密碼驗證失敗！");
		}
            
                $_POST["phone"] = $_POST['phone_head']."-".$_POST['phone_body'];
                $_POST["birthday"] = date("Y-m-d",mktime(0,0,0,$_POST["bdm"],$_POST["bdd"],$_POST["bdy"]));
            
		require_once "swop/models/member.php";
		$swop = new Main_Models();
		$swop->ajax_member_register();
                
		if($swop->pw_update)
                {
                    $add = array(checkmail=>1);
                    $this->echo_json("0", "更新成功",1,$add);
                }
                else
                {
                    $add = array(checkmail=>0);
                    $this->echo_json("0", "更新成功",1,$add);
                }    
	}
        
        public function ajax_register2(){
                if(!isset($_GET))return;
                require_once "swop/models/member.php";
		$swop = new Main_Models();
		$swop->ajax_register2();
                
                require_once "swop/library/dba.php";
                $dba=new dba();

                if(isset($swop->verification))
                {
                    if($swop->verification[0]["verification"]==1)
                    {
                        $this->echo_json("0", "已驗證通過");
                        return;
                    }
                    if($swop->verification[0]["verification_key"]==$_POST["key"])
                    {
                        $dba->query("update member_index set verification=1,verification_key='',verification_num=0 where fi_no=".$swop->verification[0]["fi_no"]);
                        $this->echo_json("0", "驗證成功");
                    }
                    else
                    {
                        if($swop->verification[0]["verification_num"] >=5)
                        {
                            $this->echo_json("2", "驗證失敗五次請聯絡客服");
                        }
                        else
                        {
                            $dba->query("update member_index set verification_num=".($swop->verification[0]["verification_num"]+1)." where fi_no=".$swop->verification[0]["fi_no"]);
                            $this->echo_json("1", "驗證失敗");
                        }
                    }
                }
        }
        
        public function ajax_get_register(){
                require_once "swop/models/member.php";
		$swop = new Main_Models();
		$swop->ajax_get_register();
                if(isset($swop->member_data[0]))
                {
                    $swop->member_data[0]["phone"] = explode("-", $swop->member_data[0]["phone"]);
                    $swop->member_data[0]["birthday"] = explode("-", $swop->member_data[0]["birthday"]);
                    $data = array();
                    $data["id"] = $swop->member_data[0]["id"];
                    $data["phone_head"] = $swop->member_data[0]["phone"][0];
                    $data["phone_body"] = $swop->member_data[0]["phone"][1];
                    $data["email"] = $swop->member_data[0]["email"];
                    $data["bdy"] = $swop->member_data[0]["birthday"][0];
                    $data["bdm"] = $swop->member_data[0]["birthday"][1];
                    $data["bdd"] = $swop->member_data[0]["birthday"][2];
                    $data["sex"] = $swop->member_data[0]["sex"];
                    $data["qq"] = $swop->member_data[0]["qq"];
                    $this->echo_json("0", "查詢成功",1,$data);
                }else{
                    $this->echo_json("1", "無會員資料");
                }   
        }
	
	public function center() {
		$this->check_on();
		
		$this->js = array('member');
                
                require_once "swop/models/member.php";
		$swop = new Main_Models();
		$swop->center();
		
		include_once "swop/language/".$this->base['lang']."/common.php";
		include_once "swop/view/template/top.php";
		include_once "swop/view/member_center.php";
		include_once "swop/view/template/bottom.php";
		return $this;
	}
        
        public function center_demo() {
                include_once "swop/view/member_center_demo.php";
        }
        
        public function ajax_change_sticker(){
                $this->check_on(1);
                
                require_once "swop/models/member.php";
		$swop = new Main_Models();
		$swop->ajax_change_sticker();
                
                $this->echo_json($swop->type, $swop->error, 1);
        }
        
        public function order_wait2pay(){
                $this->check_on();
            
                $this->js = array('member');
                
                require_once "swop/models/member.php";
		$swop = new Main_Models();
		$this->order = $swop->order(array(array(1,1,0,0)));
                
		include_once "swop/language/".$this->base['lang']."/common.php";
		include_once "swop/view/template/top.php";
		include_once "swop/view/member_order_wait2pay.php";
		include_once "swop/view/template/bottom.php";
		return $this;
        }
        
        public function order_hadpaid(){
                $this->check_on();
            
                $this->js = array('member');
                
                require_once "swop/models/member.php";
		$swop = new Main_Models();
		$this->order = $swop->order(array(array(1,2,1,0)));
                
		include_once "swop/language/".$this->base['lang']."/common.php";
		include_once "swop/view/template/top.php";
		include_once "swop/view/member_order_hadpaid.php";
		include_once "swop/view/template/bottom.php";
		return $this;
        }
        
        public function ajax_order_remind(){
                $this->check_on(1);
                
                require_once "swop/models/member.php";
		$swop = new Main_Models();
		$swop->ajax_order_remind();
                
                $this->echo_json("0", "已提醒廠商發貨！", 1);
        }
        
        public function ajax_order_bonus(){
                $this->check_on(1);
                
                require_once "swop/models/member.php";
		$swop = new Main_Models();
		$swop->ajax_order_bonus();
                $add = array('sum_currency'=>$swop->sum_currency);
                $this->echo_json("0", "已撈出跨寶通幣！", 1,$add);
        }
        
        public function ajax_order_cancel(){
                $this->check_on(1);
                if(!isset($_POST["order"]))
                {
                    $this->echo_json("1", "訂單取消失敗！", 1);
                    return;
                }
                require_once "swop/models/member.php";
		$swop = new Main_Models();
		$swop->ajax_order_cancel();
                
                $this->echo_json("0", "訂單取消成功！", 1);
        }
        
        public function ajax_order_receiving_complete(){
                $this->check_on(1);
                if(!isset($_POST["order"]))
                {
                    $this->echo_json("1", "確認收貨失敗！", 1);
                    return;
                }
                require_once "swop/models/member.php";
		$swop = new Main_Models();
		$swop->ajax_order_receiving_complete();
                
                $this->echo_json("0", "已確認收貨！", 1);
        }
        
        public function ajax_order_transaction_complete(){
                $this->check_on(1);
                if(!isset($_POST["order"]))
                {
                    $this->echo_json("1", "確認交易完成失敗！", 1);
                    return;
                }
                require_once "swop/models/member.php";
		$swop = new Main_Models();
		$swop->ajax_order_transaction_complete();
                
                $this->echo_json("0", "已確認交易完成！", 1);
        }
        
        public function order_sendout(){
                $this->check_on();
            
                $this->js = array('member');
                
                require_once "swop/models/member.php";
		$swop = new Main_Models();
		$this->order = $swop->order(array(array(1,2,2,1)));
                
		include_once "swop/language/".$this->base['lang']."/common.php";
		include_once "swop/view/template/top.php";
		include_once "swop/view/member_order_sendout.php";
		include_once "swop/view/template/bottom.php";
		return $this;
        }
        
        public function ajax_order_logistics(){
                $this->check_on(1);
                if(!isset($_POST["order"]))
                {
                    $this->echo_json("1", "物流查詢失敗！", 1);
                    return;
                }
                require_once "swop/models/member.php";
		$swop = new Main_Models();
		$swop->ajax_order_logistics();
                $add = array(trace=>$swop->order_trace);
                
                $this->echo_json("0", "物流查詢成功！", 1,$add);
        }
        
        public function ajax_order_rank(){
                $this->check_on(1);
                if(!isset($_POST["order"]))
                {
                    $this->echo_json("1", "物品查詢失敗！", 1);
                    return;
                }
                require_once "swop/models/member.php";
		$swop = new Main_Models();
		$swop->ajax_order_rank();
                $add = array(goods=>$swop->order_goods,date_diff=>$swop->date_diff,pre_rank_date=>$swop->pre_rank_date);
                
                $this->echo_json("0", "物品查詢成功！", 1,$add);
        }
        
        public function ajax_order_rank_update(){
                $this->check_on(1);
                if(!isset($_POST["order"]))
                {
                    $this->echo_json("1", "物品評價失敗！", 1);
                    return;
                }
                require_once "swop/models/member.php";
		$swop = new Main_Models();
		$swop->ajax_order_rank_update();
                $add = $_POST;
                
                $this->echo_json("0", "物品評價成功！", 1,$add);
        }
        
        public function ajax_order_rank_update_add(){
                $this->check_on(1);
                if(!isset($_POST["order"]))
                {
                    $this->echo_json("1", "追加評價失敗！", 1);
                    return;
                }
                require_once "swop/models/member.php";
		$swop = new Main_Models();
		$swop->ajax_order_rank_update_add();
                $add = $_POST;
                
                $this->echo_json("0", "追加評價成功！", 1,$add);
        }
        
        public function appeal(){
                $this->check_on();
            
                $this->js = array('member');
                
                require_once "swop/models/member.php";
		$swop = new Main_Models();
		$swop->appeal();
                
		include_once "swop/language/".$this->base['lang']."/common.php";
		include_once "swop/view/template/top.php";
		include_once "swop/view/member_appeal.php";
		include_once "swop/view/template/bottom.php";
		return $this;
        }
        
        public function ajax_appeal(){
                $this->check_on(1);
                
                require_once "swop/models/member.php";
		$swop = new Main_Models();
		$swop->ajax_appeal();
                
                $this->echo_json("0", "申訴申請中", 1);
        }
        
        public function order_confirm(){
                $this->check_on();
            
                $this->js = array('member');
                
                require_once "swop/models/member.php";
		$swop = new Main_Models();
		$this->order = $swop->order(array(array(1,2,2,2)));
                
		include_once "swop/language/".$this->base['lang']."/common.php";
		include_once "swop/view/template/top.php";
		include_once "swop/view/member_order_confirm.php";
		include_once "swop/view/template/bottom.php";
		return $this;
        }
        
        public function order_history(){
                $this->check_on();
            
                $this->js = array('member');
                
                require_once "swop/models/member.php";
		$swop = new Main_Models();
		$this->order = $swop->order(array(array(2,0,0,0),array(3,2,2,2),array(3,3,2,2)));
                
		include_once "swop/language/".$this->base['lang']."/common.php";
		include_once "swop/view/template/top.php";
		include_once "swop/view/member_order_history.php";
		include_once "swop/view/template/bottom.php";
		return $this;
        }
        
        public function order_returns(){
                $this->check_on();
            
                $this->js = array('member');
                
                require_once "swop/models/member.php";
		$swop = new Main_Models();
                $swop->order_returns();
                if($swop->progress[0])
                {
                    if($swop->progress[0]["progress"]>0)
                    {
                        $this->progress = $swop->progress[0]["progress"];
                    }
                }
                include_once "swop/language/".$this->base['lang']."/common.php";
		include_once "swop/view/template/top.php";
		include_once "swop/view/member_order_returns.php";
		include_once "swop/view/template/bottom.php";
		return $this;
        }
        
        public function ajax_order_returns(){
                $this->check_on(1);
                
                require_once "swop/models/member.php";
		$swop = new Main_Models();
		$swop->ajax_order_returns();
                
                $this->echo_json("0", $swop->error, 1);
                
        }
        
        public function bonus(){
                $this->check_on();
            
                $this->js = array('member');
                
                require_once "swop/models/member.php";
		$swop = new Main_Models();
		$swop->bonus();
                
                $now_url = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
                
                require_once "swop/library/pagination.php";
                $page = new Library_Pagination();
                $page->pagination($swop->num, 5, 5, $now_url, 0, 0, 1, $_GET['page']);
                
		include_once "swop/language/".$this->base['lang']."/common.php";
		include_once "swop/view/template/top.php";
		include_once "swop/view/member_bonus.php";
		include_once "swop/view/template/bottom.php";
		return $this;
        }
        
        public function ajax_bonus(){
                $this->check_on(1);
                
                require_once "swop/models/member.php";
		$swop = new Main_Models();
		$swop->ajax_bonus();
                
                $this->echo_json("0", "兌換成功", 1);
        }


        public function account(){
                $this->check_on();
            
                $this->js = array('member');
                
                include_once "swop/language/".$this->base['lang']."/common.php";
		include_once "swop/view/template/top.php";
		include_once "swop/view/member_account.php";
		include_once "swop/view/template/bottom.php";
		return $this;
        }
	
	public function address() {
		$this->check_on();
		
		require_once "swop/models/member.php";
		$swop = new Main_Models();
		$swop->address();
		
		$this->js = array('member');
		
		include_once "swop/language/".$this->base['lang']."/common.php";
		include_once "swop/view/template/top.php";
		include_once "swop/view/member_address.php";
		include_once "swop/view/template/bottom.php";
		return $this;
	}	
	
	public function ajax_address() {
		$this->check_on(1);
		
		if($_POST['fi_no'] != "") {
			if(!is_numeric($_POST['fi_no']) || !is_numeric(substr($_POST['fi_no'], 0, 1)) || substr_count($_POST['fi_no'], ".") > 0) {
				$this->echo_json("2", "未選擇正確修改項目");
			}
			
			$word = "修改";
		}
		else {
			$word = "新增";
		}
		
		if($_POST['consignee'] == "" || $_POST['postal_code'] == "" || $_POST['street'] == "" || $_POST['contact_phone'] == "") {
			$this->echo_json("3", "欄位不得空白");
		}
		
		if(mb_strlen($_POST['consignee'],"utf-8") < 2 || mb_strlen($_POST['consignee'],"utf-8") > 10) {
			$this->echo_json("4", "收件者姓名長度不得小於2碼或大於10碼");
		}
		
		if(strlen($_POST['postal_code']) < 5 || strlen($_POST['postal_code']) > 7) {
			$this->echo_json("5", "郵政編號長度不得小於5碼或大於7碼");
		}
		
		if(!is_numeric($_POST['postal_code']) || !is_numeric(substr($_POST['postal_code'], 0, 1)) || substr_count($_POST['postal_code'], ".") > 0) {
			$this->echo_json("6", "郵政編號僅能輸入數字");
		}
		
		if(mb_strlen($_POST['address'],"utf-8") < 5 || mb_strlen($_POST['address'],"utf-8") > 120) {
			$this->echo_json("7", "詳細地址長度不得小於5碼或大於120碼");
		}
		
                if(strlen($_POST['contact_phone']) == 0 && strlen($_POST['contact_mobile'])==0){
                    $this->echo_json("8", "請至少選擇輸入一種聯繫方式");
                }
                
                if($_POST['contact_phone'] != "--")
		if(strlen($_POST['contact_phone']) < 14 || strlen($_POST['contact_phone']) > 15) {
			$this->echo_json("9", "電話號碼 ".$_POST['contact_phone']." 長度為14~15碼");
		}
                
                if(($_POST['contact_mobile']) != "-")
                if(strlen($_POST['contact_mobile']) < 13 || strlen($_POST['contact_mobile']) > 13) {
			$this->echo_json("10", "手機號碼 ".$_POST['contact_mobile']." 總長度為13碼");
		}
		
		require_once "swop/models/member.php";
		$swop = new Main_Models();
		$swop->ajax_address();
                
		if($swop->results) {
			if($_POST['fi_no'] == "") {
				$add = array('newid'=>$swop->newid);
			}
			$this->echo_json("0", $word."成功", 1, $add);
		}
		else {
			$this->echo_json("1", $word."失敗", 1);
		}
	}
	
	public function ajax_address_delete() {
		$this->check_on(1);
		
		if($_POST['fi_no'] == "" || !is_numeric($_POST['fi_no']) || !is_numeric(substr($_POST['fi_no'], 0, 1)) || substr_count($_POST['fi_no'], ".") > 0) {
			$this->echo_json("2", "未選擇正確刪除項目");
		}
		
		require_once "swop/models/member.php";
		$swop = new Main_Models();
		$swop->ajax_address_delete();
		
		if($swop->results) {
			$this->echo_json("0", "刪除成功", 1);
		}
		else {
			$this->echo_json("1", "刪除失敗", 1);
		}
	}
	
	public function ajax_address_usual() {
		$this->check_on(1);
		
		if($_POST['fi_no'] == "" || !is_numeric($_POST['fi_no']) || !is_numeric(substr($_POST['fi_no'], 0, 1)) || substr_count($_POST['fi_no'], ".") > 0) {
			$this->echo_json("2", "未選擇正確項目");
		}
		
		require_once "swop/models/member.php";
		$swop = new Main_Models();
		$swop->ajax_address_usual();
		
		if($swop->results) {
			$this->echo_json("0", "設置成功", 1);
		}
		else {
			$this->echo_json("1", "設置失敗", 1);
		}
	}
	
	public function collect(){
                $this->check_on();
                
                if(!isset($_GET["type"]) || !isset($_GET["order"]) || !isset($_GET["by"]))
                {
                    header('Location: http://www.crazy2go.com/member/');
                }
                
                $now_url = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
                
                require_once "swop/models/member.php";
		$swop = new Main_Models();
		$swop->collect();
                
                $this->js = array('member');
                
                require_once "swop/library/pagination.php";
                $page = new Library_Pagination();
                $page->pagination($swop->num, 5, 20, $now_url, 0, 0, 0, $_GET['page']);
                
                include_once "swop/language/".$this->base['lang']."/common.php";
		include_once "swop/view/template/top.php";
		include_once "swop/view/member_collect.php";
		include_once "swop/view/template/bottom.php";
		return $this;
        }
        
        public function ajax_collect_delete(){
                $this->check_on(1);
		
		if($_POST['type'] == "" || $_POST['delete'] == "") {
			$this->echo_json("2", "請選擇刪除項目");
		}
		
		require_once "swop/models/member.php";
		$swop = new Main_Models();
		$swop->ajax_collect_delete();
		$this->echo_json("0", "刪除成功", 1);
        }
        
        public function recommand(){
                $this->check_on();
                $now_url = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
                
                require_once "swop/models/member.php";
		$swop = new Main_Models();
		$swop->recommand();
                
                $this->js = array('member');
                
                require_once "swop/library/pagination.php";
                $page = new Library_Pagination();
                $page->pagination($swop->num, 5, 20, $now_url, 0, 0, 0, $_GET['page']);
                
                include_once "swop/language/".$this->base['lang']."/common.php";
		include_once "swop/view/template/top.php";
		include_once "swop/view/member_recommand.php";
		include_once "swop/view/template/bottom.php";
		return $this;
        }
        
        public function historylog(){
                $this->check_on();
                
                $now_url = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
                
                require_once "swop/models/member.php";
		$swop = new Main_Models();
		$swop->historylog();
                
                $this->js = array('member');
                
                require_once "swop/library/pagination.php";
                $page = new Library_Pagination();
                $page->pagination($swop->num, 5, 20, $now_url, 0, 0, 0, $_GET['page']);
                
                include_once "swop/language/".$this->base['lang']."/common.php";
		include_once "swop/view/template/top.php";
		include_once "swop/view/member_historylog.php";
		include_once "swop/view/template/bottom.php";
		return $this;
        }
	
	public function ajax_historylog_delete(){
                $this->check_on(1);
		
		if($_POST['delete'] == "") {
			$this->echo_json("2", "請選擇刪除項目");
		}
		
		require_once "swop/models/member.php";
		$swop = new Main_Models();
		$swop->ajax_historylog_delete();
		$this->echo_json("0", "刪除成功", 1);
        }
	
	public function ajax_product_spec(){
                $this->check_on(1);
		require_once "swop/models/member.php";
		$swop = new Main_Models();
		$swop->ajax_product_spec();
		$this->echo_json("0", "資料獲取成功", 1, $swop->spec);
        }
	
	
	
	
	
	
	
	
	
	
	
	
	
	public function forget() {
		$this->js = array('member');
		
		include_once "swop/language/".$this->base['lang']."/common.php";
		include_once "swop/view/template/top.php";
		include_once "swop/view/member_forget.php";
		include_once "swop/view/template/bottom.php";
		return $this;		
	}
	
	public function forget_email() {
		$this->js = array('member');
		
		include_once "swop/language/".$this->base['lang']."/common.php";
		include_once "swop/view/template/top.php";
		include_once "swop/view/member_forget_email.php";
		include_once "swop/view/template/bottom.php";
		return $this;		
	}
	
	public function forget_phone() {
		$this->js = array('member');
		
		include_once "swop/language/".$this->base['lang']."/common.php";
		include_once "swop/view/template/top.php";
		include_once "swop/view/member_forget_phone.php";
		include_once "swop/view/template/bottom.php";
		return $this;		
	}
	
	public function code_forget() {
		require_once "swop/library/verification.php";
		$verification = new Library_Verification();
		$verification->kernel();
		$_SESSION['code'] = $verification->code;
	}
	
	public function ajax_forget_email() {
		if($_POST['email'] == "" || $_POST['verification'] == "") {
			$this->echo_json("2", "欄位不得空白");
		}
		
		if(!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
			$this->echo_json("3", "請輸入正確的信箱");
		}
		
		if(strtolower($_POST['verification']) != strtolower($_SESSION['code'])) {
			$this->echo_json("4", "驗證碼錯誤");
		}
		
		require_once "swop/models/member.php";
		$swop = new Main_Models();
		$swop->ajax_forget_email();
                
                if($swop->times >= 5) {
			$this->echo_json("5", "註冊過多次");
		}
		
                function sendMail($swop)
                {
                    $paddAddress = $swop->mail;
                    $pname = '親愛的跨域通用戶';
                    $pSubject = 'Global2buy 忘記密碼';
                    $pBody = '完成密碼更新請點選 <a href="http://www.crazy2go.com/member/restart/?key='.$swop->vali_code.'&email='.$swop->mail.'">http://www.crazy2go.com/member/restart/?key='.$swop->vali_code.'&email='.$swop->mail.'</a>';
                    
                    require_once "swop/library/mailer.php";
                    $mail = new Library_Mailer();
                    $mail->postal($paddAddress, $pname, $pSubject, $pBody); //收信人Email, 收信人名稱, 寄件標題, 信件HTML
                    
                    if($mail->mailer_content == 0) {
                            //echo "成功";
                    }
                    else {
                            //echo "失敗";
                    }
                }
                
		if($swop->results && $swop->num) {
                        unset($_SESSION["code_login"]);
                        sendMail($swop);
			$this->echo_json("0", "發送成功，身份驗證碼郵件已發送至您的信箱", 1);
		}
		else {
			$this->echo_json("1", "發送失敗", 1);
		}
	}
	
	public function ajax_forget_phone() {
		if($_POST['phone'] == "" || $_POST['verification'] == "") {
			$this->echo_json("2", "欄位不得空白");
		}
		
		if(!is_numeric($_POST['phone'])) {
			$this->echo_json("3", "請輸入正確的手機");
		}
		
		if(strtolower($_POST['verification']) != strtolower($_SESSION['code'])) {
			$this->echo_json("4", "驗證碼錯誤");
		}
		
		require_once "swop/models/member.php";
		$swop = new Main_Models();
		$swop->ajax_forget_phone();
		
		if($swop->results && $swop->num) {
                        unset($_SESSION["code_login"]);
			$this->echo_json("0", "發送成功，身份驗證碼短訊已發送至您的手機", 1);
		}
		else {
			$this->echo_json("1", "發送失敗", 1);
		}
	}
	
	public function verification_email() {
		$this->js = array('member');
		
		include_once "swop/language/".$this->base['lang']."/common.php";
		include_once "swop/view/template/top.php";
		include_once "swop/view/member_verification_email.php";
		include_once "swop/view/template/bottom.php";
		return $this;		
	}
	
	public function verification_phone() {
		$this->js = array('member');
		
		include_once "swop/language/".$this->base['lang']."/common.php";
		include_once "swop/view/template/top.php";
		include_once "swop/view/member_verification_phone.php";
		include_once "swop/view/template/bottom.php";
		return $this;		
	}
	
	public function ajax_verification_email() {
		if($_POST['email'] == "" || $_POST['verification_key'] == "") {
			$this->echo_json("2", "欄位不得空白");
		}
		
		if(!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
			$this->echo_json("3", "請輸入正確的信箱");
		}
		
		if(strlen($_POST['verification_key']) < 6 || strlen($_POST['verification_key']) > 32) {
			$this->echo_json("4", "驗證碼錯誤");
		}
		
		require_once "swop/models/member.php";
		$swop = new Main_Models();
		$swop->ajax_verification_email();
		
		if($swop->num == 1) {
			$this->echo_json("0", "驗證碼正確", 1);
			$_SESSION['verification']['fi_no'] = $swop->rows[0]['fi_no'];
			$_SESSION['verification']['key'] = $_POST['verification_key'];
		}
		else {
			$this->echo_json("1", "驗證碼錯誤", 1);
		}
	}
	
	public function ajax_verification_phone() {
		if($_POST['phone'] == "" || $_POST['verification_key'] == "") {
			$this->echo_json("2", "欄位不得空白");
		}
		
		if(!is_numeric($_POST['phone'])) {
			$this->echo_json("3", "請輸入正確的手機");
		}
		
		if(strlen($_POST['verification_key']) < 6 || strlen($_POST['verification_key']) > 32) {
			$this->echo_json("4", "驗證碼錯誤");
		}
		
		require_once "swop/models/member.php";
		$swop = new Main_Models();
		$swop->ajax_verification_phone();
		
		if($swop->num == 1) {
			$this->echo_json("0", "驗證碼正確", 1);
			$_SESSION['verification']['fi_no'] = $swop->rows[0]['fi_no'];
			$_SESSION['verification']['key'] = $_POST['verification_key'];
		}
		else {
			$this->echo_json("1", "驗證碼錯誤", 1);
		}
	}
	
	public function restart() {
		if($_GET['key'] == "" || $_GET['email'] == "") {
			header('Location: http://www.crazy2go.com/member/forget/');
		}
		
		$this->js = array('member');
		
		include_once "swop/language/".$this->base['lang']."/common.php";
		include_once "swop/view/template/top.php";
		include_once "swop/view/member_restart.php";
		include_once "swop/view/template/bottom.php";
		return $this;		
	}
	
	public function ajax_restart() {
		if($_GET['key'] == "" || $_GET['email'] == "") {
			header('Location: http://www.crazy2go.com/member/forget/');
		}
		
		if($_POST['password'] == "") {
			$this->echo_json("2", "欄位不得空白");
		}
		
		if(strlen($_POST['password']) < 8 || strlen($_POST['password']) > 16) {
			$this->echo_json("3", "密碼長度不得小於8碼或大於16碼");
		}
		
		require_once "swop/models/member.php";
		$swop = new Main_Models();
		$swop->ajax_restart();
		
		if($swop->results && $swop->num) {
			$this->echo_json("0", "修改成功", 1);
		}
		else {
			$this->echo_json("1", "修改失敗", 1);
		}
	}
        
        public function ajax_postal_province() {
		if($_POST['fi_no'] != '') {
			require_once "swop/models/member.php";
			$swop = new Main_Models();
			$swop->ajax_postal_province();
			
			echo json_encode($swop->postal_province);
		}
	}
        
        public function ajax_postal_province_group() {
		if($_POST['fi_no_1'] != '' && $_POST['fi_no_2'] != '' && $_POST['fi_no_3'] != '' && $_POST['fi_no_4'] != '') {
			require_once "swop/models/member.php";
			$swop = new Main_Models();
			$swop->ajax_postal_province_group();
			echo json_encode($swop->postal_province);
		}
	}
        
        public function ajax_postal_street() {
		if($_POST['fi_no'] != '') {
			require_once "swop/models/member.php";
			$swop = new Main_Models();
			$swop->ajax_postal_street();
			
			echo json_encode($swop->postal_street);
		}
	}
	
	public function check_on($ajax=0) {
		if($_SESSION['info'] == "") {
			if($ajax == 1) {
				$this->echo_json("1", "請登入會員", 1);
			}
			else {
				header('Location: http://www.crazy2go.com/member/');
			}
			
			exit;
		}
	}
	
	public function echo_json($error="-1", $message="nil", $exit=0, $add=array()) {
		$results["error"] = $error;
		$results["message"] = $message;
		
		if(is_array($add) && count($add)>0) {
			foreach($add as $k => $v) {
				$results['add'][$k] = $v;
			}
		}
		
		echo json_encode($results);
		
		if($exit==0) {
			exit;
		}
	}
        
}
?>