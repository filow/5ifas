<?php

class ExportAction extends Action {
    function index() {
        $data=json_decode($_POST["export_data"],true);
       // print_r($data);
       // exit;
        header("Content-type:application/vnd.ms-excel");
        header("Content-Disposition:filename=" . time() . ".xls");
        echo'
        <html xmlns:o="urn:schemas-microsoft-com:office:office"
        xmlns:x="urn:schemas-microsoft-com:office:excel"
        xmlns="[url=http://www.w3.org/TR/REC-html40]http://www.w3.org/TR/REC-html40[/url]">
        <head>
        <meta http-equiv="expires" content="Mon, 06 Jan 1999 00:00:01 GMT">
        <meta http-equiv=Content-Type content="text/html; charset=utf-8">
        <!--[if gte mso 9]><xml>
        <x:ExcelWorkbook>
           <x:ExcelWorksheets>
                           <x:ExcelWorksheet>
                                   <x:Name></x:Name>
                                   <x:WorksheetOptions>
                                           <x:DisplayGridlines/>
                                   </x:WorksheetOptions>
                           </x:ExcelWorksheet>
           </x:ExcelWorksheets>
        </x:ExcelWorkbook>
        </xml><![endif]-->
        </head>
        <body link=blue vlink=purple leftmargin=0 topmargin=0>';
        echo'<table   border="0" cellspacing="0" cellpadding="0">';


        foreach ($data as $key => $ln) {
            echo "<tr>";
            foreach ($ln as $ln_key => $ln_value) {
                echo "<td > " . $ln_value . "</td>";
            }
            echo "</tr>";
        }

        echo'</table>';
        echo'</body>';
        echo'</html>';
    }

}