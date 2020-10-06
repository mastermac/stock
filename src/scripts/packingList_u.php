<?php
require 'db_config.php';
session_start();

function updateSettings()
{
    $mysqli = getConn();
    $sql = "UPDATE settings set exchangeRt=".$_GET['exchangeRt'].", silverRt=".$_GET['silverRt'].", goldRt=".$_GET['goldRt'].", labourRt=".$_GET['labourRt'].", platingRt=".$_GET['platingRt'].", findingsRt=".$_GET['findingsRt'].", microDiaSettingRt=".$_GET['microDiaRt'].", roundStoneSettingRt=".$_GET['roundStoneRt'].", prongDiaSettingRt=".$_GET['prongDiaRt'].", baguetteDiaSettingRt=".$_GET['baguetteDiaRt'].";";
    $result = $mysqli->query($sql);
    $data['sql'] = $sql;
    echo json_encode($data);
}

switch($_GET["func"]){
    case "updateSettings": updateSettings();
        break;
}

?>