<?php

if(isset($_GET['page'])){
    include("pages/".$_GET['page'].".html");
}
else if(isset($_POST['page'])){
    include("pages/".$_POST['page'].".html");
}

?>