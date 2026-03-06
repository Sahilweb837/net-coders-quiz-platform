<?php
session_start();
session_destroy();
$baseUrl = $_SERVER['REQUEST_SCHEME']."://".$_SERVER['SERVER_NAME'];
 header("Location: ".$baseUrl."/cstm_quiz/index.php");
exit();
?>
