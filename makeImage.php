<?php
// ボツファイル 画像が寂しいしテキストでよかった

require_once 'userSetting.php';

// データベースからデータを取得
require_once $_SERVER['DOCUMENT_ROOT'] . "/../undefined/DSN.php";
try {
    $pdo = new PDO ( 'mysql:host=' . $dsn ['host'] . ';dbname=' . $dsn ['dbname'] . ';charset=utf8', $dsn ['user'], $dsn ['pass'], array (
    PDO::ATTR_EMULATE_PREPARES => false
    ) );
} catch ( PDOException $e ) {
    exit ( 'connection unsuccess' . $e->getMessage () );
}



$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);  // debug


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

var_dump($array);


$font = 'img/mplus-2c-regular.ttf';
$img = imagecreatefrompng ( 'img/bg.png' );
$black = ImageColorAllocate ( $img, 0x00, 0x00, 0x00 );

$mask = imagecreatefrompng ( 'img/mask.png' );
imagecopymerge($img, $mask, 0, 0, 0, 0, 960, 620, 75);

$text = $userName;
ImageTTFText ( $img, 50, 0, 15, 70, $black, $font, $text);

$text = "@" . $twitterId . " (" . $gameid . ")";
ImageTTFText ( $img, 26, 0, 15, 110, $black, $font, $text);

$text = $array[0]['time_str'] . " (vs" . $array[1]['time_str'] . ")";
ImageTTFText ( $img, 28, 0, 15, 160, $black, $font, $text);

$pos = 160;
$addPos = 70;

$text = 'プロデューサーレベル';
$pos += $addPos;
ImageTTFText ( $img, 24, 0, 15, $pos, $black, $font, $text);
$a = $array[0]['level'] - $array[1]['level'];
$text = $array[0]['level'] . ' ( +' . $a . ' )';
ImageTTFText ( $img, 22, 0, 50, $pos + 32, $black, $font, $text);

$text = 'PRP';
$pos += $addPos;
ImageTTFText ( $img, 24, 0, 15, $pos, $black, $font, $text);
$a = $array[0]['prp'] - $array[1]['prp'];
$text = $array[0]['prp'] . ' ( +' . $a . ' )';
ImageTTFText ( $img, 22, 0, 50, $pos + 32, $black, $font, $text);

$text = 'ファン';
$pos += $addPos;
ImageTTFText ( $img, 24, 0, 15, $pos, $black, $font, $text);
$a = $array[0]['fan'] - $array[1]['fan'];
$text = number_format($array[0]['fan']) . ' ( +' . number_format($a) . ' )';
ImageTTFText ( $img, 22, 0, 50, $pos + 32, $black, $font, $text);

$text = 'アルバム登録数';
$pos += $addPos;
ImageTTFText ( $img, 24, 0, 15, $pos, $black, $font, $text);
$a = $array[0]['album_no'] - $array[1]['album_no'];
$text = $array[0]['album_no'] . ' ( +' . $a . ' )';
ImageTTFText ( $img, 22, 0, 50, $pos + 32, $black, $font, $text);

$text = 'コミュ達成数';
$pos += $addPos;
ImageTTFText ( $img, 24, 0, 15, $pos, $black, $font, $text);
$a = $array[0]['commu_no'] - $array[1]['commu_no'];
$text = $array[0]['commu_no'] . ' ( +' . $a . ' )';
ImageTTFText ( $img, 22, 0, 50, $pos + 32, $black, $font, $text);



$file_name = "twitter.png";
imagepng ( $img, $file_name );

echo '<div style="padding: 20px;background-color: #000;"><img src="' . $file_name . '"></div>';