<?php
//关闭PHP的错误提示
error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);


if (isset($_GET['userCommand'])) {

    //【拨号】处理
    if ($_GET['userCommand'] == 'userAuth') {
        $gateway = $_GET['gateway'];
        $userName = $_GET['userName'];
        $userPasswd = $_GET['userPasswd'];
        $userCommand = $_GET['userCommand'];
        $str = "$gateway?userName=$userName&userPasswd=$userPasswd&userCommand=$userCommand";
        // header('Referer: http://172.16.64.1/WebAuth_auth.asp');
        file_get_contents($str);
        if ($http_response_header) {
            echo '{"msg":"完成拨号","code":1}';
        } else {
            echo '{"msg":"拨号无响应","code":0}';
        }
        return;
    }


    //测试与外网的连接情况
    if ($_GET['userCommand'] == 'testing') {
        $testingServer = $_GET['testingServer'];
        $gateway = $_GET['gateway'];
        $gateway = substr($gateway, strpos($gateway, '//') + 2);
        file_get_contents('http://' . $testingServer);
        // 将接收到的响应头信息返回给客户端(浏览器)
        if ($http_response_header) {
            foreach ($http_response_header as $key => $value) {
        //如果响应头中包含黑马翻身校区的网关代理服务器的IP地址,则说明此时弹出了要求验证身份的窗口,
        //可以判断此时并没有真正连接上互联网
                if (strstr($value, $gateway)) {
                    echo '{"msg":"failed to connect Internet","code":0}';
                    return;
                }
            }
            echo '{"msg":"successed to connect Internet","code":1}';
        } else {
            echo '{"msg":"failed to connect Internet","code":0}';
        }

    }


    //【手动下线】处理
    if ($_GET['userCommand'] == 'logoff') {
        $gateway = $_GET['gateway'];
        $userCommand = $_GET['userCommand'];
        $str = "$gateway?userCommand=$userCommand";
        file_get_contents($str);
        if ($http_response_header) echo '{"msg":"successed to be offline","code":1}';
        else echo '{"msg":"failed to be offline","code":0}';
        return;
    }

    //测试与网关的连接情况
    if ($_GET['userCommand'] == 'gatewayTest') {
        $gateway = $_GET['gateway'];
        file_get_contents($gateway);
        if ($http_response_header) echo '{"msg":"successed to connect gateway","code":1}';
        else echo '{"msg":"failed to connect gateway","code":0}';
    }


    //互联网监测
    if ($_GET['userCommand'] == 'watchDod') {
        $testingServer = $_GET['testingServer'];
        if (pingAddress($testingServer))
            echo '{"msg":"本机与互联网的连接已断开!","code":0}';
        else
            echo '{"msg":"本机正常接入互联网!","code":1}';
    }
}



/* $address表示测试的服务器地址
 * 返回值1表示断开,0表示连接
 */
function pingAddress($address)
{
    $status = -1;
    if (strcasecmp(PHP_OS, 'WINNT') === 0) {  
        // Windows 服务器下  
        $pingresult = exec("ping -n 1 {$address}", $outcome, $status);
    } elseif (strcasecmp(PHP_OS, 'Linux') === 0) {  
        // Linux 服务器下  
        $pingresult = exec("ping -c 1 {$address}", $outcome, $status);
    }
    return $status;
}




