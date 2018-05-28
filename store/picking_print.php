<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
        <style>
            body {
                width: 100%;
                height: 100%;
                margin: 0;
                padding: 0;
                background-color: #FAFAFA;
                font: 12pt "Tahoma";
            }
            * {
                box-sizing: border-box;
                -moz-box-sizing: border-box;
            }
            .page {
                width: 210mm;
                min-height: 297mm;
                padding: 0mm;//20mm;
                margin: 10mm auto;
                border: 1px #D3D3D3 solid;
                border-radius: 5px;
                background: white;
                box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
            }
            .subpage {
                padding: 1cm;
                border: 1px gray solid;
                height: 297mm;//257mm;
                outline: 0cm #D3D3D3 solid;
            }

            @page {
                size: A4;
                margin: 0;
            }
            @media print {
                html, body {
                    width: 210mm;
                    height: 297mm;        
                }
                .page {
                    margin: 0;
                    border: initial;
                    border-radius: initial;
                    width: initial;
                    min-height: initial;
                    box-shadow: initial;
                    background: initial;
                    page-break-after: always;
                }
            }
            
            table{
                width:100%;
            }
            
            .header{
                //background: #D3D3D3;
            }
            
            .footer{
                //background: #D3D3D3;
            }
        </style>
    </head>
    <body>
        <div class="book">
            <!--<div class="page">
                <div class="subpage">Page 1/2</div>    
            </div>
            <div class="page">
                <div class="subpage">Page 2/2</div>    
            </div>-->
        </div>
    </body>
</html>
