<?php
ini_set('display_errors', 'On');
error_reporting(E_ALL);

header('Access-Control-Allow-Origin:*');
header('Access-Control-Allow-Headers:Origin, X-Requested-With, Content-Type, Accept');
header('Content-Type:application/json');
include dirname(__FILE__) . "/Tools/Tool.php";
include dirname(__FILE__) . "/Tools/Http.php";


$Message = [
    "Code" => 0,
    "Message" => "",
    "Result" => "",
];
$Headers = [];
if (isset($_POST["Referer"]) || isset($_GET["Referer"])) {
    $Headers[] = "Referer:" . ($_POST["Referer"] ?? $_GET["Referer"] ?? "");
}
$isPc = false;
if (isset($_POST["isPc"]) || isset($_GET["isPc"])) {
    $isPc = ($_POST["isPc"] ?? $_GET["isPc"] ?? false);
}
if (isset($_POST["Host"]) || isset($_GET["Host"])) {
    $Headers[] = "Host:" . ($_POST["Host"] ?? $_GET["Host"] ?? "");
}

try {
    if (Tools\Tool::isPost() && (isset($_POST["url"]) || isset($_GET["url"]))) {
        $data = $_POST["data"] ?? "";
        if (!($_POST["isJson"] ?? false)) {
            $data1 = json_decode($data, TRUE);
            if (json_last_error() == 0) {
                $data = $data1;
            }
        }
        $url = $_POST["url"] ?? $_GET["url"];
        $result = Tools\Http::Send(
            [
                "url" => $url,
                "data" => $data,
                "refererUrl" => $_POST["refererUrl"] ?? "",
                "method" => "POST",
                "contentType" => $_SERVER["HTTP_CONTENT_TYPE"] ?? "application/x-www-form-urlencoded",
                "Headers" => $Headers,
                "timeout" => 60,
                "isPc" => $isPc == "TRUE"
            ]
        );
        if ($result === false) throw new Error("请求失败", -1);
        $Message["Result"] = Tools\Tool::ConvertString($result);
    } else if (isset($_GET["url"])) {
        $data = isset($_GET["data"]) ? json_decode($_GET["data"], TRUE) : "";
        $result = Tools\Http::Send([
            "url" => $_GET["url"],
            "data" => $data,
            "refererUrl" => $_GET["refererUrl"] ?? "",
            "method" => "GET",
            "contentType" => $_SERVER["HTTP_CONTENT_TYPE"] ?? "text/html;charset=utf-8",
            "Headers" => $Headers,
            "timeout" => 60,
            "isPc" => $isPc == "TRUE"
        ]);
        if ($result === false) throw new Error("请求失败", -1);
        $Message["Result"] = Tools\Tool::ConvertString($result);
    }

} catch (Throwable $e) {
    $Message["Code"] = $e->getCode() > 0 ? (-$e->getCode()) : $e->getCode();
    $Message["Message"] = $e->getMessage();
}

echo json_encode($Message);
