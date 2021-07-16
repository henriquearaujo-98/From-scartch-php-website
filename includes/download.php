<?php
if (isset($_GET['file_path'])) {

    $file_path = "../uploads/".$_GET['file_path'];
    $filename = basename($file_path);
    echo $file_path;

    if (file_exists($file_path)) {
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename='.basename($file_path));
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        header('Content-Length: ' . filesize($file_path));
        ob_clean();
        flush();
        readfile($file_path);
        exit;
    }else{
        echo "Could not find file";
    }
    


}else{
    header("Location: index.php");
}
?>