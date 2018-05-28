
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
        <script>
            /******************** user define js ********************/
            
        </script>
        <style>
            /******************** user define css ********************/

        </style>
    </head>
    <body>
        <div id="wrapper">
            <!-- ******************** header ******************** -->
            <div id="header">
                <h3>Upload_XLS</h3>
            </div>
            <!-- /.header -->
            <!-- ******************** body ******************** -->
            <div id="body">
                <form action="<?php echo $this->base['url']."convert/xls2db";?>" method="post" enctype="multipart/form-data">
	                <h4>請上傳2003格式</h4>
	                <input type='file' name='upload_xls' accept="application/vnd.ms-excel">
	            	<br>
	                
	                <label for='formal_false'>測試<input  type="radio" name='formal' id='formal_false' value='0' 
	                <?php
		                if($_GET["formal"]!='1')
		                {
			                echo " checked ";
		                }	
		            ?>
	                /></label>
	                <label for='formal_true'>正式<input  type="radio" name='formal' id='formal_true' value='1' 
	                <?php
		                if($_GET["formal"]=='1')
		                {
			                echo " checked ";
		                }
		            ?>
		            onclick='javascript:alert("資料會寫入(正式)資料庫，請確認檔案格式及正確性！")'/></label> 
	                <br>
	                
	                <label for='formal_false'>保留<input  type="radio" name='delete' id='delete_false' value='0' checked /></label>
	                <label for='formal_true'>刪除<input  type="radio" name='delete' id='delete_true' value='1' onclick='javascript:alert("檔案及資料庫皆會清空，確認刪除？")'/></label> 
	                <br>
	                <input type='submit' value="送出轉檔" style="cursor: pointer;"/>
                </form>
            </div>
            <!-- /.body -->
            <!-- ******************** footer ******************** -->
            <!-- /.footer -->
        </div>
        <!-- /.wrapper -->
    </body>
</html>
