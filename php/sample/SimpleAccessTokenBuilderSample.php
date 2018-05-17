<?php
include("../src/SimpleTokenBuilder.php");

$appID = "dac19cb04202499a8ee5ca1f7085d0ff";
$appCertificate = "3b7b43b8d5edad5033483a468d0ea0e1";
$channelName = "test";
$uid = 321;

$builder = new SimpleTokenBuilder($appID, $appCertificate, $channelName, $uid);
$builder->initPrivilege(Constants::Role["kRoleAdmin"]);
echo $builder->buildToken();

?>
