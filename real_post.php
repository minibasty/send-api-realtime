<?php
function real_post($configKey, $configValue)
{
    require 'config_positions.php';

    // require 'config_positions.php';
    $url = 'http://122.155.167.137:3000/WSDLSERV11_GPS/service.asmx/AddLocation';
    $apiKey = "mirada.thp:mirada@2018";

    //vender id 
    $vender_id = "17";
    $vender_3bit = "017";
    $vender_4bit = "0001";

    //SQL for positions
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
    INNER JOIN `positions` ON `positions`.`deviceid` = `devices`.`id` WHERE `positions`.`id` > $configValue LIMIT 9000";
    $qr_positions = $conn->query($sql_position);

    $location_array = array();
    $location_arrayBackup = array();
    $locations_count = 0;
    $locations_countBackup = 0;

    while ($row_positions = $qr_positions->fetch_assoc()) {
        $position_id = $row_positions['id'];
        if ($row_positions['connect_post'] == 1) {

            // check protocol
            $protocol_value = $row_positions['protocol'];
            $ext_power_status = 0;
            
            $to_time = strtotime(date("Y-m-d H:i:s"));
            $from_time = strtotime($row_positions['devicetime']);

            // รหัส 4 ตัวท้ายเก็บจาก ฟิล type กรณีเป็นรถ ปณ ให้เป็น 0000 รับมาจากตัวแปร $vender_4bit
            // $device_type = str_pad($row_positions['type'], 4, "0", STR_PAD_LEFT);
            // $vendor_type = isset($vender_4bit) ? $vender_4bit : $device_type;

            $vender_code = $vender_3bit . $vender_4bit; //first unit_id

            // คำนวณความต่างของเวลาเครื่องกับเวลาปุจจับัน
            $time_interval = round(abs($to_time - $from_time) / 60, 2);

            if ($time_interval < 10) {
                $locations_count++;

                // ค่า attributes แปลง Json
                $attributesJson = get_jsonattributes($row_positions['attributes']);

                $driverLicense = get_driverId($row_positions['driverLicense']);  //driverLicense
                $date_utc_ts = dateTimeUTC($row_positions['devicetime']); //date_utc_ts
                $date_recv_utc_ts = dateTimeUTC($row_positions['servertime']);  //date_recv_utc_ts

                if ($row_positions['dlt']) {
                    $imei = $row_positions['dlt'];
                } else {
                    $imei = $row_positions['uniqueid'];
                }

                $unit_id = get_unitId($vender_code, $imei); //unit_id
                $hdop = property_exists($attributesJson, 'hdop') ? round($attributesJson->hdop) : 0; //hdop


                if (property_exists($attributesJson, 'status')) {
                    if ($attributesJson->status == 2400 || $attributesJson->status == 6400 || $attributesJson->status == 2000) {
                        $ext_power_status = 1;
                    }
                }

                $engine_status = getEngineStatus($protocol_value, $attributesJson); //engine_status

                array_push($location_array, array(
                    'driver_id' => $driverLicense,
                    'unit_id' => $unit_id,
                    'seq' => '0',
                    'utc_ts' => $date_utc_ts,
                    'recv_utc_ts' => $date_recv_utc_ts,
                    'lat' => $row_positions['latitude'],
                    'lon' => $row_positions['longitude'],
                    'alt' => $row_positions['altitude'],
                    "speed" => intval($row_positions['speed'] * 1.852),
                    "engine_status" => $engine_status,
                    "fix" => 0,
                    "license" => $imei,
                    "course" => $row_positions['course'],
                    "hdop" => $hdop,
                    "num_sats" => 0,
                    "gsm_cell" => 0,
                    "gsm_loc" => 0,
                    "gsm_rssi" => 0,
                    "mileage" => 0,
                    "ext_power_status" => $ext_power_status,
                    "ext_power" => 0,
                    "high_acc_count" => "",
                    "high_de_acc_count" => "",
                ));
            } else {
                $locations_countBackup++;
                // ค่า attributes แปลง Json
                $attributesJson = get_jsonattributes($row_positions['attributes']);

                $driverLicense = get_driverId($row_positions['driverLicense']);  //driverLicense
                $date_utc_ts = dateTimeUTC($row_positions['devicetime']); //date_utc_ts
                $date_recv_utc_ts = dateTimeUTC($row_positions['servertime']);  //date_recv_utc_ts

                if ($row_positions['dlt']) {
                    $imei = $row_positions['dlt'];
                } else {
                    $imei = $row_positions['uniqueid'];
                }

                $unit_id = get_unitId($vender_code, $imei); //unit_id
                $hdop = property_exists($attributesJson, 'hdop') ? round($attributesJson->hdop) : 0; //hdop

                if (property_exists($attributesJson, 'status')) {
                    if ($attributesJson->status == 2400 || $attributesJson->status == 6400 || $attributesJson->status == 2000) {
                        $ext_power_status = 1;
                    }
                }
                $engine_status = getEngineStatus($protocol_value, $attributesJson); //engine_status

                array_push($location_arrayBackup, array(
                    'driver_id' => $driverLicense,
                    'unit_id' => $unit_id,
                    'seq' => '0',
                    'utc_ts' => $date_utc_ts,
                    'recv_utc_ts' => $date_recv_utc_ts,
                    'lat' => $row_positions['latitude'],
                    'lon' => $row_positions['longitude'],
                    'alt' => $row_positions['altitude'],
                    "speed" => intval($row_positions['speed'] * 1.852),
                    "engine_status" => $engine_status,
                    "fix" => 0,
                    "license" => $imei,
                    "course" => $row_positions['course'],
                    "hdop" => $hdop,
                    "num_sats" => 0,
                    "gsm_cell" => 0,
                    "gsm_loc" => 0,
                    "gsm_rssi" => 0,
                    "mileage" => 0,
                    "ext_power_status" => $ext_power_status,
                    "ext_power" => 0,
                    "high_acc_count" => "",
                    "high_de_acc_count" => "",
                ));
            } // end if
        }
    } //end while
    if ($locations_count) {
        $data_array = array('vender_id' => $vender_id, 'locations_count' => $locations_count, 'locations' => $location_array);
        $data_arrayJson = json_encode($data_array);
        $send_res = raeltime_api($data_arrayJson, $url, $apiKey);
        log_realtime($send_res, "post");
    } //end if ($locations_count) {

    // backup post
    if ($locations_countBackup) {
        $url_Backup = 'http://122.155.167.137:3000/gps/backup/add/locations';
        $apiKey_Backup = "mirada.thp:mirada@2018";

        $data_arrayBackup = array('vender_id' => $vender_id, 'locations_count' => $locations_countBackup, 'locations' => $location_arrayBackup);
        $dataBackup_arrayJson = json_encode($data_arrayBackup);
        $send_res = raeltime_api($dataBackup_arrayJson, $url_Backup, $apiKey_Backup);
        log_realtime($send_res, "postBackup");
    }
    // update Config In database
    $sql_updateConfig = "UPDATE config SET value = $position_id WHERE `key` = '$configKey'";
    $qr_updateConfig = $conn->query($sql_updateConfig);
    // $res_Data = $send_res . $data_arrayJson;
} //end function
