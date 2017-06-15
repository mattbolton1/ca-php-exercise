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
</head>
<body>
<div id="fileDiv">
    <form method="post" enctype="multipart/form-data" name="fileForm" action="index.php">
        <input type="hidden" name="fAction" value="FileSubmitted"/>
        <input type="file" name="fileField" title="Upload File"/>
    </form>
</div>
<?php
if(isset($_POST['fAction'])){
    if(is_uploaded_file($_FILES['fileField']['tmp_name'])){
        $fileProcessor = new fileProcessor();
?>
<div id="processedResults">

</div>
<?php
    }else{
        echo "<div id=\"uploadError\">No file was uploaded</div>";
    }
}
?>
</body>
</html>