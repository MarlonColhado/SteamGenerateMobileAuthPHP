<?php
/*
	Created by Marlon Colhado
	admin@kazeta.com.br
*/

include_once("./api/AuthFunctions.php");

$SteamAuth = new SteamAuth;

echo $SteamAuth->GenerateSteamGuardCode("Shared Secret Key");

?>