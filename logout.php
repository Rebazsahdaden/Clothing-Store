<?php
// logout.php
session_start();
session_destroy();
header("Location: http://localhost/Clothing-Store-Management-System/login.php");
exit;
