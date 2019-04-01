<?php

ini_set('display_errors', 'On');
error_reporting(E_ALL);

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
        $data = isset($_POST["data"]) ? json_decode($_POST["data"]) : "";
        $result = Tools\Http::Send($_POST["url"], $data, $_POST["refererUrl"] ?? "", $method = "POST");
        if ($result === false) throw new Error("请求失败", -1);
        $Message["Result"] = $result;
    } else if (isset($_GET["url"])) {
        $data = isset($_GET["data"]) ? json_decode($_GET["data"]) : "";
        $result = Tools\Http::Send($_GET["url"], $data, $_GET["refererUrl"] ?? "");
        if ($result === false) throw new Error("请求失败", -1);
        $Message["Result"] = Tools\Tool::ConvertString($result);
    }

} catch (Throwable $e) {
    $Message["Code"] = $e->getCode() > 0 ? (-$e->getCode()) : $e->getCode();
    $Message["Message"] = $e->getMessage();
}

echo json_encode($Message);
