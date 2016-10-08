<?php
/*
deresute.meさんのjsonを拝借してユーザーデータを取得する。
cronでいい感じの感覚で叩くとデータベースに保存される。
*/

require_once ("userSetting.php");



// 最初に時間を求めておく 
$GLOBALS['time'] = time();
$GLOBALS['time_str'] = date("Y/m/d H:i",$GLOBALS['time']);

function printLog($str){
    // ログ出力用にまとめたやつ
    $t = sprintf('%.3f', microtime(true) - $GLOBALS['time']);
    echo "(" . $t . "ms) " . $str;
    flush();
    ob_flush();
}

// いつもの
require_once "./../../undefined/DSN.php";
try {
    $pdo = new PDO ( 'mysql:host=' . $dsn ['host'] . ';dbname=' . $dsn ['dbname'] . ';charset=utf8', $dsn ['user'], $dsn ['pass'], array (
    PDO::ATTR_EMULATE_PREPARES => false
    ) );
} catch ( PDOException $e ) {
    exit ( 'connection unsuccess' . $e->getMessage () );
}
printLog("PDOロード\n");

// jsonを取得する
$url = "https://deresute.me/" . $gameid . "/json";
$json = file_get_contents($url);
$json = mb_convert_encoding($json, 'UTF8', 'ASCII,JIS,UTF-8,EUC-JP,SJIS-WIN');
$arr = json_decode($json, true);
printLog("jsonロード\n");

// 送信する配列 あとで書くと長くなるのでここで。
$pushArr = array(
":uptime" => $GLOBALS['time'],
":time_str" => $GLOBALS['time_str'],
":level" => $arr['level'],
":commu" => $arr['commu_no'],
":album" => $arr['album_no'],
":fan" => $arr['fan'],
":prp" => $arr['prp']
);

// 送信しておしまい 失敗したら次回頑張ろう
$sql = 'INSERT INTO slstage_aggregater (time ,time_str ,level ,commu_no ,album_no ,fan ,prp) VALUES (:uptime , :time_str , :level , :commu , :album , :fan , :prp)';
$stmt=$pdo->prepare($sql);
$res=$stmt->execute($pushArr);
if ($res) {
   printLog("insert成功\n");
}else{
   printLog("insert失敗\n");
}

// 指定時間にツイートする処理
if (date("H") == 0){
    echo "０時なので実行";
    include_once('tweet.php');
}