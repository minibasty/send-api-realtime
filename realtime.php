<?php

// require 'config_positions.php';
// require_once 'config_positions.php';
require 'all_function.php';
require 'real_dlt.php';
require 'real_post.php';
require 'real_sugar.php';
date_default_timezone_set('Asia/Bangkok');

// get agrument for select function
if (!empty($argv['1'])) {
    $agrumentSelected = $argv['1'];
    switch ($agrumentSelected) {
        case 'dlt':
            send_data('dlt');
            break;
        case 'dlt2':
            send_data('dlt2');
            break;
        case 'post':
            send_data('post');
            break;
        case 'sugar':
            send_data('sugar');
            break;
    }
} else {
    echo "agrument undefind";
}

function send_data($getConnect)
{
    require 'config_positions.php';

    $sql_config = "SELECT * FROM `config`";
    $sql_position = "SELECT
    `devices`.`name`,
    `devices`.`uniqueid`,
    `devices`.`connect`,
    `devices`.`connect_post`,
    `devices`.`connect_sugar`,
    `devices`.`connect_dlt2`,
    `devices`.`type`,
    `devices`.`driverLicense`,
    `devices`.`dlt`,
    `positions`.*
    FROM
    `devices`
    INNER JOIN `positions` ON `positions`.`deviceid` = `devices`.`id`";

    
    if ($getConnect == "dlt") { // realtime DLT 

        // Configkey
        $configKey = "sendedId";

        $sql_config .= " WHERE `key` = '$configKey'";
        $qr_config = $conn->query($sql_config);
        $row_config = $qr_config->fetch_array();
        $row_config_id = $row_config['value'];

        real_dlt($configKey, $row_config_id);
        exit;
    } elseif ($getConnect == "post") { //realtime POST

        // Config key
        $configKey = "sendedIdPost";

        $sql_config .= " WHERE `key` = '$configKey'";
        $qr_config = $conn->query($sql_config);
        $row_config = $qr_config->fetch_array();
        $row_config_id = $row_config['value'];

        real_post($configKey, $row_config_id);
        exit;
    } elseif ($getConnect == "sugar") { //realtime sugar
        // Config key
        $configKey = "sendedIdSugar";

        $sql_config .= " WHERE `key` = '$configKey'";
        $qr_config = $conn->query($sql_config);
        $row_config = $qr_config->fetch_array();
        $row_config_id = $row_config['value'];

        real_sugar($configKey, $row_config_id);
        exit;
    } elseif ($getConnect == "dlt2") { // realtime DLT 2
        // Configkey
        $configKey = "send_dlt2";

        $sql_config .= " WHERE `key` = '$configKey'";
        $qr_config = $conn->query($sql_config);
        $row_config = $qr_config->fetch_array();
        $row_config_id = $row_config['value'];

        real_dlt2($configKey, $row_config_id);
        exit;
    }
}
