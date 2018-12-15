<?php

/**
 * Each of these functions make POST and GET RESTful requests to the middleman server.
 * If you need to add more functions, add an endpoint to the middleman server and then query that endpoint with the new function.
 * I'm sorry this isn't easier :( . I'll try to automate this process.
 */

function get_screening_form($type){
    
    $configs = parse_ini_file(dirname(__FILE__, $levels=2) . "/config.ini");

    $data = array(
        'token' => $configs['redcap_api_token'],
        'content' => 'metadata',
        'format' => 'json',
        'returnFormat' => 'json',
        'forms' => array($type)
    );
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $configs['redcap_url']);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_VERBOSE, 0);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_AUTOREFERER, true);
    curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
    curl_setopt($ch, CURLOPT_FRESH_CONNECT, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data, '', '&'));
    $output = curl_exec($ch);
    curl_close($ch);
    $data = json_decode($output, true);
    return $data;

}

function get_next_record_number() {
    $configs = parse_ini_file(dirname(__FILE__, $levels=2) . "/config.ini");

    $data = array(
        'token' => $configs['redcap_api_token'],
        'content' => 'generateNextRecordName'
    );
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $configs['redcap_url']);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_VERBOSE, 0);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_AUTOREFERER, true);
    curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
    curl_setopt($ch, CURLOPT_FRESH_CONNECT, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data, '', '&'));
    $output = curl_exec($ch);
    curl_close($ch);
    return $output;
}


function create_owner_record($input_data) {

    $configs = parse_ini_file(dirname(__FILE__, $levels=2) . "/config.ini");

    $data = array(
        'token' => $configs['redcap_api_token'],
        'content' => 'record',
        'format' => 'json',
        'type' => 'flat',
        'overwriteBehavior' => 'normal',
        'forceAutoNumber' => 'false',
        'data' => $input_data,
        'returnContent' => 'count',
        'returnFormat' => 'json'
    );
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $configs['redcap_url']);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_VERBOSE, 0);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_AUTOREFERER, true);
    curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
    curl_setopt($ch, CURLOPT_FRESH_CONNECT, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data, '', '&'));
    $output = curl_exec($ch);
    //print $output;
    //print "<br>";
    curl_close($ch);
    return $output;
}

function add_dog_record($input_data) {

    $configs = parse_ini_file(dirname(__FILE__, $levels=2) . "/config.ini");

    $data = array(
        'token' => $configs['redcap_api_token'],
        'content' => 'record',
        'format' => 'json',
        'type' => 'flat',
        'overwriteBehavior' => 'normal',
        'forceAutoNumber' => 'false',
        'data' => $input_data,
        'returnContent' => 'count',
        'returnFormat' => 'json'
    );
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $configs['redcap_url']);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_VERBOSE, 0);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_AUTOREFERER, true);
    curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
    curl_setopt($ch, CURLOPT_FRESH_CONNECT, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data, '', '&'));
    $output = curl_exec($ch);
    //print $output;
    //print "<br>";
    curl_close($ch);
    return $output;
}

    