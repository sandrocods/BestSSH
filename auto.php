<?php
/*
 * Auto Create SSH Account in https://bestvpnssh.com/
 * Created by Sandroputraa
 * sandroputraa.my.id
 * 04-22-2021
 */

define('API', 'https://bestvpnssh.com');
function curl(
    $url,
    $method = null,
    $postfields = null,
    $followlocation = null,
    $headers = null,
    $conf_proxy = null
) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_HEADER, 1);
    if ($conf_proxy !== null) {
        curl_setopt($ch, CURLOPT_PROXY, $conf_proxy['Proxy']);
        curl_setopt($ch, CURLOPT_PROXYPORT, $conf_proxy['Proxy_Port']);
        if ($conf_proxy['Proxy_Type'] == 'SOCKS4') {
            curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS4);
        }
        if ($conf_proxy['Proxy_Type'] == 'SOCKS5') {
            curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5);
        }
        if ($conf_proxy['Proxy_Type'] == 'HTTP') {
            curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);
            curl_setopt($ch, CURLOPT_PROXYTYPE, CURLOPT_HTTPPROXYTUNNEL);
        }
        if ($conf_proxy['Auth'] !== null) {
            curl_setopt($ch, CURLOPT_PROXYUSERPWD, $conf_proxy['Auth']['Username'] . ':' . $conf_proxy['Auth']['Password']);
            curl_setopt($ch, CURLOPT_PROXYAUTH, CURLAUTH_BASIC);
        }
    }
    if ($followlocation !== null) {
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_MAXREDIRS, $followlocation['Max']);
    }
    if ($method == "PUT") {
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
    }
    if ($method == "GET") {
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
    }
    if ($method == "POST") {
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
    }
    if ($headers !== null) {
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    }
    $result = curl_exec($ch);
    $header = substr($result, 0, curl_getinfo($ch, CURLINFO_HEADER_SIZE));
    $body = substr($result, curl_getinfo($ch, CURLINFO_HEADER_SIZE));
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    preg_match_all('/^Set-Cookie:\s*([^;]*)/mi', $result, $matches);
    $cookies = array();
    foreach ($matches[1] as $item) {
        parse_str($item, $cookie);
        $cookies = array_merge($cookies, $cookie);
    }
    return array(
        'HttpCode' => $httpcode,
        'Header' => $header,
        'Body' => $body,
        'Cookie' => $cookies,
        'Requests Config' => [
            'Url' => $url,
            'Header' => $headers,
            'Method' => $method,
            'Post' => $postfields,
        ],
    );
}

function getStr($string, $start, $end)
{
    $str = explode($start, $string);
    $str = explode($end, ($str[1]));
    return $str[0];
}

function save($fileName, $line, $opt)
{
    $file = fopen($fileName, $opt);
    fwrite($file, $line);
    fclose($file);
}

function readable_random_string($length = 6)
{
    $string = '';
    $vowels = array("a", "e", "i", "o", "u");
    $consonants = array(
        'b', 'c', 'd', 'f', 'g', 'h', 'j', 'k', 'l', 'm',
        'n', 'p', 'r', 's', 't', 'v', 'w', 'x', 'y', 'z',
    );
    srand((double) microtime() * 1000000);
    $max = $length / 2;
    for ($i = 1; $i <= $max; $i++) {
        $string .= $consonants[rand(0, 19)];
        $string .= $vowels[rand(0, 4)];
    }
    return $string;
}

$HeaderStatic = [
    'Host: bestvpnssh.com',
    'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/89.0.4389.128 Safari/537.36',
];

echo "[ Auto Create Account Best SSH | Code by Sandroputraa ]\n\n";
echo "[!] Please Wait Getting Region\n\n";
$GetRegion = curl(
    API . '/pages/select-region',
    'GET',
    null,
    null,
    $HeaderStatic,
    null
);
preg_match_all('/href="\/select-country\/(.*?)\/ssh"/m', $GetRegion['Body'], $match);
for ($i = 0; $i < count($match[1]); $i++) {
    echo "[" . $i . "] " . $match[1][$i] . "\n";
}
echo "\n[?] Select Number Region : ";
$NumReg = trim(fgets(STDIN));
echo "\n[!] Please Wait Getting Country\n\n";
$GetCountry = curl(
    API . '/select-country/' . $match[1][$NumReg] . '/ssh',
    'GET',
    null,
    null,
    $HeaderStatic,
    null
);
preg_match_all('/href="\/select-server\/(.*?)\/ssh"/m', $GetCountry['Body'], $matches);
for ($i = 0; $i < count($matches[1]); $i++) {
    echo "[" . $i . "] " . $matches[1][$i] . "\n";
}

echo "\n[?] Select Number Country : ";
$NumCountry = trim(fgets(STDIN));

$GetServer = curl(
    API . '/select-server/' . $matches[1][$NumCountry] . '/ssh',
    'GET',
    null,
    null,
    $HeaderStatic,
    null
);

preg_match_all('/href="\/create-account\/ssh\/(.*?)\/' . $matches[1][$NumCountry] . '"/m', $GetServer['Body'], $matchese);
echo "\n" . count($matchese[1]) . " ID Server Collected \n\n";
echo "[?] How Many Create Account / ID : ";
$HowMany = trim(fgets(STDIN));

foreach ($matchese[1] as $value) {

    for ($i = 0; $i < $HowMany; $i++) {
        $HeaderStatic = [
            'Host: bestvpnssh.com',
            'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/89.0.4389.128 Safari/537.36',
        ];
        echo "[!] Create Account with ID : " . $value . "\n";

        $Username = readable_random_string(12);
        $Password = readable_random_string(6);

        $FetchFirst = curl(
            API . '/create-account/ssh/' . $value . '/' . $matches[1][$NumCountry],
            'GET',
            null,
            null,
            $HeaderStatic,
            null
        );

        if (getStr($FetchFirst['Body'], 'Created ', ' from') == getStr($FetchFirst['Body'], 'from ', ' Accounts')) {
            echo "[!] Oops Server Full \n";
            goto Last;
        }else{
            echo "[!] Remaining servers : " . ((float)getStr($FetchFirst['Body'], 'Created ', ' from') - (float)getStr($FetchFirst['Body'], 'from ', ' Accounts')). "\n\n";
        }
        $GetCaptcha = curl(
            API . '/plugins/captcha.php?width=100&height=34&characters=5',
            'GET',
            null,
            null,
            $HeaderStatic,
            null
        );
        save('Captcha.jpg', $GetCaptcha['Body'], 'w');
        $file = file_get_contents("Captcha.jpg");
        $bypassCaptcha = curl(
            'http://sandroputraa.my.id/API/OCR.php',
            'POST',
            '{
        "img": "' . base64_encode($file) . '"
        }',
            null,
            [
                'Content-Type: application/json',
                'Auth: sandrocods',
            ],
            null
        );
        if (!empty(json_decode($bypassCaptcha['Body'], true)['Data'])) {
            array_push($HeaderStatic, "Cookie: PHPSESSID=" . $GetCaptcha['Cookie']['PHPSESSID'] . "");
            $CreateAccount = curl(
                API . '/c-user.php?sess=' . getStr($FetchFirst['Body'], '/c-user.php?sess=', '"'),
                'POST',
                'username=' . $Username . '&password=' . $Password . '&captcha=' . str_replace("\n", "", json_decode($bypassCaptcha['Body'], true)['Data']) . '&type=ssh&server=' . $value . '&csrf=' . getStr($FetchFirst['Body'], '<input type="hidden" name="csrf" value="', '" id="csrf">'),
                null,
                $HeaderStatic,
                null
            );
            $HeaderStatic = null;
            if (strpos($CreateAccount['Body'], 'successfully created')) {
                echo "[ Account Information ]\n\n";
                echo "[+] Server            : " . getStr($CreateAccount['Body'], '<h4>Server: ', ' Please') . "\n";
                echo "[+] Username          : " . getStr($CreateAccount['Body'], '<b>Username:</b> ', '<br/>') . "\n";
                echo "[+] Password          : " . getStr($CreateAccount['Body'], 'Password:</b> ', '<br/>') . "\n";
                echo "[+] Created date      : " . getStr($CreateAccount['Body'], 'Created date:</b> ', '<br/>') . "\n";
                echo "[+] Expired date      : " . getStr($CreateAccount['Body'], 'Expired date:</b> ', '<br/>') . "\n";
                echo "[+] SSL Port          : " . getStr($CreateAccount['Body'], 'SSL Port:</b> ', '<br/>') . "\n";
                echo "[+] OpenSSH & Dropbear: " . getStr($CreateAccount['Body'], 'OpenSSH & Dropbear:</b> ', '<br/>') . "\n";
                echo "[+] UDP / BadVPN Port : " . getStr($CreateAccount['Body'], 'UDP / BadVPN Port:</b> ', '</p>') . "\n";
                echo "\n\n[ Account Information ]\n\n";
                save('AccountResult.txt', "\n[+] Server            :" . getStr($CreateAccount['Body'], '<h4>Server: ', ' Please') . "\n[+] Username          :" . getStr($CreateAccount['Body'], '<b>Username:</b> ', '<br/>') . "\n[+] Password          :" . getStr($CreateAccount['Body'], 'Password:</b> ', '<br/>') . "\n[+] Created date      :" . getStr($CreateAccount['Body'], 'Created date:</b> ', '<br/>') . "\n[+] Expired date      :" . getStr($CreateAccount['Body'], 'Expired date:</b> ', '<br/>') . "\n[+] SSL Port          :" . getStr($CreateAccount['Body'], 'SSL Port:</b> ', '<br/>') . "\n[+] OpenSSH & Dropbear:" . getStr($CreateAccount['Body'], 'OpenSSH & Dropbear:</b> ', '<br/>') . "\n[+] UDP / BadVPN Port :" . getStr($CreateAccount['Body'], 'UDP / BadVPN Port:</b> ', '</p>') . "\n", 'a');
            } else {
                echo "[ Error Creating Account ]\n";
                continue;
            }

        } else {
            echo "[+] Captcha Not Ready \n";
        }

    }
    Last:;
}
echo "[!] Process Done \n";
