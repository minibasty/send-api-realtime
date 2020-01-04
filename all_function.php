<?php

function raeltime_api($data, $url, $apiKey, $auth = 0)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    if ($auth == 1) {
        $headers = array(
            'Content-Type: application/json; charset=utf-8',
            'Authorization: ' . $apiKey
        );
    } else {
        $headers = array(
            'Content-Type: application/json; charset=utf-8',
        );
        curl_setopt($ch, CURLOPT_USERPWD, $apiKey);
    }
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    $response  = curl_exec($ch);
    return $response;
    curl_close($ch);
}

function log_realtime($result, $what){
    require 'config_positions.php';
    $result_json = json_decode($result);
    if ($result_json->code) { 

        $code = $result_json->code;
        $what_connect = $what;
        $message = isset($result_json->message) ? $result_json->message : '';
        $received_records = isset($result_json->received_records) ? $result_json->received_records : '';
        $message_reject = isset($result_json->message_reject) ? $result_json->message_reject : '';
        $reject_records = isset($result_json->reject_records) ? $result_json->reject_records : '';
        $reject_data = isset($result_json->reject_data) ? $result_json->reject_data : '';
        $reject_dataJson = json_encode($reject_data);
        $sql_insertlog = "INSERT INTO `log_realtime` VALUES ('',NOW(),'$what_connect','$code','$message','$received_records','$message_reject','$reject_records', '$reject_dataJson')";
        $qr_insertlog = $conn->query($sql_insertlog);
    }
}


function log_realtime_sugar($result, $what){
    require 'config_positions.php';
    $result_json = json_decode($result);
    print_r($result_json);
    if (isset($result_json->what)) { 

        $code = isset($result_json->code) ? $result_json->code : 0;
        $what_connect = $what;
        $message = isset($result_json->what) ? $result_json->what : '';
        $received_records = isset($result_json->received_records) ? $result_json->received_records : '';
        $message_reject = isset($result_json->message_reject) ? $result_json->message_reject : '';
        $reject_records = isset($result_json->reject_records) ? $result_json->reject_records : '';
        $reject_data = isset($result_json->reject_data) ? $result_json->reject_data : '';
        $reject_dataJson = json_encode($reject_data);
        $sql_insertlog = "INSERT INTO `log_realtime` VALUES ('',NOW(),'$what_connect','$code','$message','$received_records','$message_reject','$reject_records', '$reject_dataJson')";
        $qr_insertlog = $conn->query($sql_insertlog);
    }
}

function get_driverId($licenseParam)
{
    require 'config_positions.php';
    $countStr = strlen($licenseParam);

    if ($countStr >= 112) {

        //";6007643200200516551=220119571016=?+             22            1            0069552  20302                     ???"
        //";6007641200100638801=201219960426=?+             12            1            0064560  20300                     ?"

        $license = explode("?",$licenseParam);
        $license = $license['1']; //"+             12            1            0064560  20300                     "
        $license = explode("+",$license);
        $license = $license['1']; //"             12            1            0064560  20300                     "
        $licenseFinal = str_replace(' ', '', trim($license));
        if (is_numeric($licenseFinal)) {
            if (strlen($licenseFinal) >= 15) {
                return $licenseFinal;
            }else{
                $sql_logLicense="INSERT INTO `log_driverLicense` VALUES ('',NOW(),$countStr,'$licenseParam')";
                $qr_insertlog = $conn->query($sql_logLicense);
                return $licenseFinal;
            }
        }else{
            $sql_logLicense="INSERT INTO `log_driverLicense` VALUES ('',NOW(),$countStr,'$licenseParam')";
            $qr_insertlog = $conn->query($sql_logLicense);
            return '';
        }
    } elseif ($countStr == 110) {
        $license = substr($licenseParam, 49, 39);
        $license = str_replace(' ', '', trim($license));
        return $license;
    } elseif ($countStr == 65) {
        $license = substr($licenseParam, 3, 41);
        $license = str_replace(' ', '', trim($license));
        return $license;
    } elseif ($countStr == 63) {
        $license = substr($licenseParam, 0, 41);
        $license = str_replace(' ', '', trim($license));
        return $license;
    } elseif ($countStr == 41) {
        $license = $licenseParam;
        $license = str_replace(' ', '', trim($license));
        return $license;
    } elseif ($countStr == 70) {
        $license = substr($licenseParam, 4, 44);
        $license = str_replace(' ', '', trim($license));
        return $license;
    } elseif ($countStr == 74 or $countStr == 77 or $countStr == 75) {
        // count 75 //"             23            1            0002261  60501                     "
        // count 74 //"            23            1            0002149  40900                     "
        $license = $licenseParam;
        $license = str_replace(' ', '', trim($license));
        return $license;
    } elseif ($countStr == 0) {
        return '';
    } else {
        $license = $licenseParam;
        $license = explode(' ',trim($license));
        $license = array_slice($license,3,59);
        $license = array_slice($license,3,59);
        $licenseStr = implode("",$license);
        if (is_numeric($licenseStr)) {
            if (strlen($licenseStr) >= 15) {
                return $licenseStr;
            }else{
                $sql_logLicense="INSERT INTO `log_driverLicense` VALUES ('',NOW(),$countStr,'$licenseParam')";
                $qr_insertlog = $conn->query($sql_logLicense);
                return $licenseStr;
            }
        }else{
            $sql_logLicense="INSERT INTO `log_driverLicense` VALUES ('',NOW(),$countStr,'$licenseParam')";
            $qr_insertlog = $conn->query($sql_logLicense);
            return '';
        }
        
        
    }
}

function get_jsonattributes($attributes)
{
    $json = json_decode($attributes);
    return $json;
}

function get_unitId($vender, $imei)
{
    $unitImei = str_pad($imei, 20, "0", STR_PAD_LEFT);
    $unit_id = $vender . $unitImei;
    $unit_id = trim($unit_id);
    return $unit_id;
}

function get_unitId_sugar($vender, $imei){
    $unitImei = str_pad($imei, 19, "0", STR_PAD_LEFT);
    $unit_id = $vender . $unitImei;
    $unit_id = trim($unit_id);
    return $unit_id;
}

function getEngineStatus($protocolParam, $attributesParam)
{
    $engine = 0;

    // check protocol 
    switch ($protocolParam) {
        case 'gt06':
            if (property_exists($attributesParam, 'ignition')) {
                if ($attributesParam->ignition === true) {
                    $engine = 1;  //key on
                } elseif ($attributesParam->ignition === false) {
                    $engine = 0;  //key off
                }
            }
            break;
        case 'h02':
            if (property_exists($attributesParam, 'status')) {
                if ($attributesParam->status == '4294942719') {
                    $engine = 1;  //key on
                } elseif ($attributesParam->status == '4294949887') {
                    $engine = 0;  //key off
                }
            }
            break;
        case 'meiligao':
            if (property_exists($attributesParam, 'status')) {
                if ($attributesParam->status == '2400' || $attributesParam->status == '6400') {
                    $engine = 1;  //key on
                } elseif ($attributesParam->status == '2000') {
                    $engine = 0;  //key off
                }
            }
            break;
        case 'meitrack':
            if (property_exists($attributesParam, 'status')) {
                if ($attributesParam->status == '0400') {
                    $engine = 1;  //key on
                } elseif ($attributesParam->status == '0000') {
                    $engine = 0;  //key off
                }
            }
            break;
        case 'teltonika':
            # code...
            break;
        case 'totem':
            if (property_exists($attributesParam, 'status')) {
                if (!$attributesParam->status == '18004000') {
                    $engine = 1;  //key on
                } else {
                    $engine = 0;  //key off
                }
            }
            break;
    }
    return $engine;
}

function dateTimeUTC($dateInput)
{

    $datetime = new DateTime($dateInput);
    $bk_time = new DateTimeZone('Asia/Bangkok');
    $datetime->setTimezone($bk_time);
    $arr_utc = explode(" ", $datetime->format('Y-m-d H:i:s'));
    $date_utc_final = $arr_utc[0] . "T" . $arr_utc[1] . ".000+07:00";

    return $date_utc_final;
}
