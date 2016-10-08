<?php
// ツイートする用のファイル getUserDataでrequire_onceするためのファイル 一応単体でも叩ける。

require_once dirname ( __FILE__ ) . "/../../undefined/NonoMorikubo.php";
require_once ('userSetting.php');

require __DIR__ . '/vendor/autoload.php';
use mpyw\Co\Co;
use mpyw\Co\CURLException;
use mpyw\Cowitter\Client;
use mpyw\Cowitter\HttpException;

// データベースからデータを取得
require_once $_SERVER['DOCUMENT_ROOT'] . "/../undefined/DSN.php";
try {
    $pdo = new PDO ( 'mysql:host=' . $dsn ['host'] . ';dbname=' . $dsn ['dbname'] . ';charset=utf8', $dsn ['user'], $dsn ['pass'], array (
    PDO::ATTR_EMULATE_PREPARES => false
    ) );
} catch ( PDOException $e ) {
    exit ( 'connection unsuccess' . $e->getMessage () );
}


$aaa = "%" . date("Y/m/d",strtotime("-1 day")) . "%00%00%";
$stmt = $pdo->prepare('SELECT * FROM slstage_aggregater WHERE time_str LIKE :likes ORDER BY time ASC');
$stmt->bindParam(":likes" , $aaa);
$stmt->execute();
$array[0] = $stmt->fetch();

$aaa = "%" . date("Y/m/d",strtotime("-2 day")) . "%00%00%";
$stmt = $pdo->prepare('SELECT * FROM slstage_aggregater WHERE time_str LIKE :likes ORDER BY time ASC');
$stmt->bindParam(":likes" , $aaa);
$stmt->execute();
$array[1] = $stmt->fetch();



$url = "http://svr.aki-memo.net/slStageAggregater/";

$tweetStr = "#デレステプレイしてますけど
" . $array[0]['time_str'] . " (vs" . $array[1]['time_str'] . ")

レベル：" . $array[0]['level'] . ' (+' . ($array[0]['level'] - $array[1]['level']) . ")
ファン数：" . number_format($array[0]['fan']) . ' (+' . number_format($array[0]['fan'] - $array[1]['fan']) . ')
詳細：' . $url;



echo "<pre>" . $tweetStr . "</pre>";

$client = new Client([$consumer_key, $consumer_secret, $access_token, $access_token_secret]);
$client = $client->withOptions([CURLOPT_CAINFO => __DIR__ . '/vendor/cacert.pem']);

$client->post('statuses/update', ['status' => $tweetStr]);