<?php
require_once ('userSetting.php');

// データベースからデータを取得
require_once $_SERVER['DOCUMENT_ROOT'] . "/../undefined/DSN.php";
try {
    $pdo = new PDO ( 'mysql:host=' . $dsn ['host'] . ';dbname=' . $dsn ['dbname'] . ';charset=utf8', $dsn ['user'], $dsn ['pass'], array (
    PDO::ATTR_EMULATE_PREPARES => false
    ) );
} catch ( PDOException $e ) {
    exit ( 'connection unsuccess' . $e->getMessage () );
}

if( array_key_exists( 'hourly',$_GET )) {
    $stmt = $pdo->query("SELECT * FROM slstage_aggregater ORDER BY time ASC");
}else{
    $stmt = $pdo->query("SELECT * FROM slstage_aggregater WHERE time_str LIKE '%00:__' ORDER BY time ASC");  
}
$array = $stmt->fetchAll();

?>



<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8" />
<title>デレステプレイしてますけど！ - aki-memo.net</title>
<link rel="stylesheet" href="style.css" />
<link rel="shortcut icon" href="img/favicon.png" type="image/png">

<!--[if lt IE 9]>
<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js" type="text/javascript"></script>
<![endif]-->


  <!-- Resources -->
  <script src="https://www.amcharts.com/lib/3/amcharts.js"></script>
  <script src="https://www.amcharts.com/lib/3/serial.js"></script>
  <script src="https://www.amcharts.com/lib/3/themes/light.js"></script>
  <script src="https://www.amcharts.com/lib/3/plugins/export/export.min.js"></script>
  <link rel="stylesheet" href="https://www.amcharts.com/lib/3/plugins/export/export.css" type="text/css" media="all" />

  <!-- Chart code -->
  <script>
    var chartData = generateChartData();

    var chart = AmCharts.makeChart("chartdiv", {
      "period": "YYYY/M/D H:S",
      "type": "serial",
      "theme": "light",
      "language": "ja",
      "legend": {
        "useGraphSettings": true,
        "valueWidth": 50
      },
      "dataProvider": chartData,
      "synchronizeGrid": true,
      "valueAxes": [{
        "id": "v1",
        "axisColor": "#FF6600",
        "axisThickness": 2,
        "axisAlpha": 1,
        "offset": 0,
        "position": "left",
      }, {
        "id": "v2",
        "axisColor": "#FCD202",
        "axisThickness": 2,
        "axisAlpha": 1,
        "offset": 50,
        "position": "left"
      }, {
        "id": "v3",
        "axisColor": "#bfafff",
        "axisThickness": 2,
        "axisAlpha": 1,
        "offset": 100,
        "position": "left"
      }, {
        "id": "v4",
        "axisColor": "#f9bbc7",
        "axisThickness": 2,
        "axisAlpha": 1,
        "offset": 0,
        "position": "right"
      }, {
        "id": "v5",
        "axisColor": "#B0DE09",
        "axisThickness": 2,
        "offset": 150,
        "axisAlpha": 1,
        "position": "left"
      }],
      "graphs": [{
        "valueAxis": "v1",
        "lineColor": "#FF6600",
        "bullet": "round",
        "bulletBorderThickness": 1,
        "lineThickness": 3,
        "hideBulletsCount": 30,
        "title": "レベル",
        "valueField": "level",
        "fillAlphas": 0
      }, {
        "valueAxis": "v2",
        "lineColor": "#FCD202",
        "bullet": "round",
        "bulletBorderThickness": 1,
        "lineThickness": 3,
        "hideBulletsCount": 30,
        "title": "コミュ達成数",
        "valueField": "commu",
        "fillAlphas": 0
      }, {
        "valueAxis": "v3",
        "lineColor": "#bfafff",
        "bullet": "round",
        "bulletBorderThickness": 1,
        "lineThickness": 3,
        "hideBulletsCount": 30,
        "title": "アルバム達成数",
        "valueField": "album",
        "fillAlphas": 0
      }, {
        "valueAxis": "v4",
        "lineColor": "#f9bbc7",
        "bullet": "round",
        "bulletBorderThickness": 1,
        "lineThickness": 3,
        "hideBulletsCount": 30,
        "title": "ファン",
        "valueField": "fan",
        "fillAlphas": 0
      }, {
        "valueAxis": "v5",
        "lineColor": "#B0DE09",
        "bullet": "round",
        "bulletBorderThickness": 1,
        "lineThickness": 3,
        "hideBulletsCount": 30,
        "title": "PRP",
        "valueField": "prp",
        "fillAlphas": 0
      }],
      "chartScrollbar": {
        "scrollbarHeight": 20,
        "backgroundAlpha": 0,
        "selectedBackgroundAlpha": 0.1,
        "selectedBackgroundColor": "#888888",
        "autoGridCount": true,
        "color": "#AAAAAA"
      },
      "valueScrollbar": {
        "oppositeAxis": false,
        "offset": 10,
        "scrollbarHeight": 10
      },
      "chartCursor": {
        "cursorPosition": "mouse"
      },
      "categoryField": "date",
      "categoryAxis": {
        "axisColor": "#DADADA",
        "minPeriod": "mm",
        "parseDates": true,
        "minorGridEnabled": false
      },
      "export": {
        "enabled": false,
        "position": "bottom-right"
      }
    });

    chart.addListener("dataUpdated", zoomChart);
    zoomChart();

    function generateChartData() {

      var chartData = [];
      var firstDate = new Date();
      firstDate.setDate(firstDate.getDate() - 100);

      var array = <?php echo json_encode($array, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;
      array.forEach(function(value) {

        var newDate = new Date(firstDate);
        newDate.setDate(newDate.getDate());
        var time = value[0] * 1000;

        var date = value[0];
        var level = value[2];
        var commu = value[3];
        var album = value[4];
        var fan = value[5];
        var prp = value[6];

        chartData.push({
          date: time,
          level: level,
          commu: commu,
          album: album,
          fan: fan,
          prp: prp
        });
      })
      return chartData;
    }

    function zoomChart() {
      chart.zoomToIndexes(chart.dataProvider.length - 20, chart.dataProvider.length - 1);
    }
  </script>

<?php include_once("analytics.php") ?>


</head>
<body>
<header role="banner">
<h1>デレステプレイしてますけど！</h1>
<h2 class="grayMini">User : <?php echo $userName . '<a href="https://twitter.com/' . $twitterId . '" target="_blank">@' . $twitterId . "</a> (" . $gameid . ")"?></h2>
</header>
<nav>
<!-- ここにメニューだとか -->  
</nav>
<div role="main">
<p>デレステをどれ位やっているか、<a href="https://deresute.me/" target="_blank">deresute.me</a>さんのjsonをお借りしてグラフ化しています。</p>
<p><a href="./">簡易表示(１日毎)</a> / <a href="./?hourly">詳細表示(１時間毎)</a></p>
  <div id="chartdiv"></div>
</div>
<footer role="contentinfo">
<p>
©BANDAI NAMCO Entertainment Inc. <br>
©BNEI / PROJECT CINDERELLA
</p>
<p>
<a class="f" href="https://github.com/Slime-hatena/slStageAggregater" target="_blank">slStageAggregater</a> is released under the MIT License by <a class="f" href="https://twitter.com/Slime_hatena" target="_blank">Slime_hatena</a><br>
<a class="f" href="https://github.com/mpyw/cowitter" target="_blank">cowitter</a> under the MIT license by <a class="f" href="https://github.com/mpyw" target="_blank">mpyw</a>
</p>
</footer>
</body>
</html>