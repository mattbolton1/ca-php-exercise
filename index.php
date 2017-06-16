<?php
/**
 * Created by PhpStorm.
 * User: Matt
 * Date: 6/15/2017
 * Time: 2:55 PM
 */
require_once('fileProcessor.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>Calculate Totals and Profit Margins</title>
    <style>
        body{
            font-size: 80%;
            line-height: 1.3em;
            margin: 2em;
            font-family:Verdana,Arial,Helvetica,sans-serif;
        }
        table{
            border-spacing: 0;
            border-collapse: collapse;
        }
        td, th{
            text-align: right;
            border: solid #333 1px;
            padding: 3px;
        }
        #fileDiv{
            padding:6px;
            border: dashed 1px #333;
            margin-bottom: 10px;
        }
        .upVal{
            color:#292;
        }
        .downVal{
            color:#922;
        }
    </style>
</head>
<body>
<div id="fileDiv">
    <form method="post" enctype="multipart/form-data" name="fileForm" action="index.php">
        <input type="hidden" name="fAction" value="FileSubmitted"/>
        <input type="file" name="fileField" title="Upload File"/>
        <input type="submit" name="bsubmit" value="Upload File"/>
    </form>
</div>
<div id="processedResults">
<?php
if(isset($_POST['fAction'])){
    if(is_uploaded_file($_FILES['fileField']['tmp_name'])){
        $fileProcessor = new fileProcessor();
        if($fileProcessor->processFile("fileField")){
            $dataTable = $fileProcessor->outputTable();
            echo $dataTable;
        }else{
            echo "<h2>Trouble Processing File</h2>";
        }

    }else{
        echo "<div id=\"uploadError\">No file was uploaded</div>";
    }
}
?>
</div>
</body>
</html>