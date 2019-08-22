<?php

require_once 'ClassUser.php';

try
{
   $db = new PDO('mysql:host=localhost:3308;dbname=cryptocheck', 'root', '');
   $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}
catch (PDOException $e)
{
   echo $e->getMessage();
}

$user = new User($db);

?>