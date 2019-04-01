<?php

ini_set('display_errors', 'On');
error_reporting(E_ALL);

header('Access-Control-Allow-Origin:*');
header('Content-Type:application/json');
include dirname(__FILE__) . "/Tools/Tool.php";
include dirname(__FILE__) . "/Tools/Http.php";


$Message = [
    "Code" => 0,
    "Message" => "",
    "Result" => "",
];

try {
    if (Tools\Tool::isPost() && isset($_POST["url"])) {
        $data = $_POST["data"] ?? "";
        if (!($_POST["isJson"] ?? false)) {
            $data = json_decode($data, TRUE);
        }
        $result = Tools\Http::Send($_POST["url"], $data, $_POST["refererUrl"] ?? "", $method = "POST");
        if ($result === false) throw new Error("请求失败", -1);
        $Message["Result"] = $result;
    } else if (isset($_GET["url"])) {
        $data = isset($_GET["data"]) ? json_decode($_GET["data"], TRUE) : "";
        $result = Tools\Http::Send($_GET["url"], $data, $_GET["refererUrl"] ?? "");
        if ($result === false) throw new Error("请求失败", -1);
        $Message["Result"] = Tools\Tool::ConvertString($result);
    }

} catch (Throwable $e) {
    $Message["Code"] = $e->getCode() > 0 ? (-$e->getCode()) : $e->getCode();
    $Message["Message"] = $e->getMessage();
}

echo json_encode($Message);
