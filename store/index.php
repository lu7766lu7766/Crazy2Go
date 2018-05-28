<?php
    include '../backend/template.php';
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title><?php echo $html_title;?></title>
        <?php echo $html_resource;?>
        <script>
            /******************** user define js ********************/
            $(function(){
                if(location.href.search("www.bigwayasia.com.tw")!=-1)
                {
                    location.href = "http://www.crazy2go.com/store/index.php";
                }
            });
        </script>
        <style>
            /******************** user define css ********************/
            div#wrapper #body{
                text-align: center;
            }
            div#wrapper #body form{
                display: inline-block;
                margin-top: 150px;
            }
        </style>
    </head>
    <body>
        <div id="wrapper">
            <!-- ******************** header ******************** -->
            <div id="header">
                <h3><?php echo $html_title; ?></h3>
            </div>
            <!-- /.header -->
            <!-- ******************** body ******************** -->
            <div id="body">
                <form action="manage_welcome.php" method="post">
                    <table class="table-v">
                        <tr><td>帳號</td><td><input name="account" type="text" autofocus placeholder="請輸入帳號"></td></tr>
                        <tr><td>密碼</td><td><input name="password" type="password" placeholder="請輸入密碼"></td></tr>
                    </table>
                    <input type="submit" value="登入"/>
                </form>
                <?php 
                    if(isset($_GET['status'])){
                        echo "<p style='color:white;background:rgba(0,0,0,0.8);padding:5px;'>".$_GET['status']."</p>";
                    }
                ?>
            </div>
            <!-- /.body -->
            <!-- ******************** footer ******************** -->
            <div id="footer">
                <span><?php echo $html_copyright; ?></span>
            </div>
            <!-- /.footer -->
        </div>
        <!-- /.wrapper -->
    </body>
</html>