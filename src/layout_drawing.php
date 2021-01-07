<?php
if(!isset($_SESSION)){
	session_start();
	} 
	if(!isset($_SESSION['EMAIL'])){
		header('Location: http://localhost:2964/singup.php');
		} 
/********** 手動設定 **********/
$tbl_flg = true; //時間を横軸 → true, 縦軸 → falseにする
$master_key = 'special';
$color_red = '#FF4500';
$color_blue = '#00BFFF';
$color_yellow = '#FFD700';
$color_green = '#228B22';
/********** ここまで **********/
require_once("fnc/gant1_functions.php");

/*** DB接続 ***/
  //接続パラメーター
  $DBHOST = "localhost";
  $DBPORT = "5432";
  $DBNAME = "reserve_kiki";
  $DBUSER = "postgres";
  $DBPASS = "admin";

  try{
	//DB接続
	$pdo = new PDO("pgsql:host=$DBHOST;port=$DBPORT;dbname=$DBNAME;user=$DBUSER;password=$DBPASS");

          //SQL作成
          $sql = 'select * from "M_Kiki"';
        
          //SQL実行
          // クエリ実行（データを取得）
		  $res = $pdo->query($sql);
		  
} catch (Exception $e) {
	exit('データベース接続に失敗しました。'.$e->getMessage());
}

        // // 「$res」からデータを取り出し、変数「$result」に代入。
        // // 「PDO::FETCH_ASSOC」を指定した場合、カラム名をキーとする連想配列として「$result」に格納される。
        while( $result = $res->fetch( PDO::FETCH_ASSOC ) ){
            $rows[] = $result;
		}


/*** ページ読込前の設定部分 ***/
//エラー出力する
ini_set( 'display_errors', 1 );
//タイムゾーンセット
date_default_timezone_set('Asia/Tokyo');
	
	$row = $chapters; //横軸 → 設定項目
	$row_n = count($row); //横の数


//メッセージ用変数
$log1 = '';
$log2 = '';


/*** タイムテーブル生成のための下準備をする部分 ***/

foreach ($chapters as $cpt) {
	for ( $i = 0; $i < count($hours); $i++ ) {
		$data_meta[$cpt][$i] = null; //配列を定義しておく（エラー回避）
	}
}

$err_n = 0; //エラー件数カウント用

?>
<html>
<head>
		<link rel="stylesheet" href="css/gant1.css">
        <script type="text/javascript" src="js/gant1.js"></script>
		<link href="product.css" rel="stylesheet">
		<meta name="viewport" content="width=device-width, initial-scale=1">  
        <link rel="stylesheet" href="css/bootstrap.css">
        <script type="text/javascript" src="js/jquery-3.5.1.js"></script>
        <script type="text/javascript" src="js/bootstrap.js"></script>
        <script type="text/javascript" src="js/pd.js"></script>

		<!-- /*左列固定*/ -->
		<style>
			.table-fixed th:first-child, td:first-child {
			position: sticky;  position: -webkit-sticky;
			left: 0;
			background-color: #fff;
			}
		</style>
</head>
<body>
	<?php include('top.php'); ?>
<div id="content">
<?php
/*** メッセージ ***/
if ( $log1 != '' ) { //処理メッセージがある場合
	$log1 = '<p class="msg">処理メッセージ</p>'."\n".$log1;
}
if ( $log2 != '' ) { //エラーメッセージがある場合
	$log2 = '<p class="msg">'.$err_n."件の不整合データを表示できませんでした。</p>\n<ul>\n".$log2;
	$log2 .= "</ul>";
}
if ( $log1 != '' || $log2 != '' ) { //どちらかのメッセージがある場合
	echo '<div id="attention">'."\n";
	if ( $log1 != '' ) { echo $log1."\n"; } //処理メッセージがある場合
	if ( $log1 != '' && $log2 != '' ) { echo "<br />\n"; } //両方ある場合は改行も
	if ( $log2 != '' ) { echo $log2."\n"; } //エラーメッセージがある場合
	echo "</div>\n";
}
?>


<!-- タイムテーブル -->
<div class="container-fluid">

<?php $sp_date = explode("-", $date); ?>
	<h3><?php printf('%s年%s月%s日', $sp_date[0], $sp_date[1], $sp_date[2]); ?></h3>

<div class="table-responsive">
	<div id="timetable_box">


	<?php
	
	for ( $i = 0; $i < $clm_n; $i++ ) {
		$span_n[$i] = 0; //rowspan結合数を格納する配列にゼロを入れておく
	}
	//ここから $timetable_output へ table の記述を入れていく
	$timetable_output = '<table id="timetable" class="table table-fixed">'."\n<thead>\n<tr>\n".'<th id="origin">時間</th>'."\n";
	for ( $i = 0; $i < $clm_n; $i++ ) {
		$timetable_output .= '<th class="cts">'.$clm[$i]."</th>\n"; //横軸見出し
	}
	$timetable_output .= "</tr>\n</thead>\n<tbody>\n";
	for ( $i = 0; $i < $row_n; $i++ ) { //縦軸の数繰り返す
		$timetable_output .= "<tr><td><a href='gant2-1.php?kikiname=".$row[$i]."'>".$row[$i].'</a></td>'; //縦軸見出し
		for ( $j = 0; $j < $clm_n; $j++ ) { //横軸の数繰り返す
			if ( $tbl_flg == false && $span_n[$j] > 0 ) { //時間軸が縦の場合の繰り上げ処理
				$span_n[$j]--; //rowspan結合の数だけtd出力をスルー
			} else { //通常時
				$block = '';
				$data_n = 0; //ゼロはデータ無し
				if ( $tbl_flg == true ) { //時間軸が横なら
					$data_n = $data_meta[$row[$i]][$j];
				} else { //時間軸が縦なら
					$data_n = $data_meta[$clm[$j]][$i];
				}
				if ( $data_n == 0 ) { //データが無いとき
					$timetable_output .= '<td>&nbsp;</td>'; //空白を入れる
				} else { //データが有るとき
                    if ( $ar_block[$data_n] > 1 ) { //ブロックが2つ以上
                        
                        if ($tbl_flg == true) { //時間軸が横だったら
                            if ($ar_user_id[$data_n] == $_SESSION['USER_ID']&&$ar_kubun_cd[$data_n] == 1) {//自分の予約データ

                                $block = ' colspan="'.$ar_block[$data_n].'" style="background-color: '.$color_red.'"' ; //赤色に変えて横方向へ結合

                            }elseif ($ar_kubun_cd[$data_n] == 2) {//修理の場合
                                $block = ' colspan="'.$ar_block[$data_n].'" style="background-color: '.$color_green.'"' ; //緑色に変えて横方向へ結合


                            }elseif ($ar_kubun_cd[$data_n] == 3) {//その他の場合
                                $block = ' colspan="'.$ar_block[$data_n].'" style="background-color: '.$color_yellow.'"' ; //黄色を変えて横方向へ結合
                                
                            
                            }elseif ($ar_user_id[$data_n] <> $_SESSION['USER_ID']&&$ar_kubun_cd[$data_n] == 1) {//自分の予約データ以外

                                $block = ' colspan="'.$ar_block[$data_n].'" style="background-color: '.$color_blue.'"' ; //青色に変えて横方向へ結合
                            }
                            $j = $j + $ar_block[$data_n] - 1; //colspan結合ぶん横軸数を繰り上げ
                            
                        } else { //時間軸が縦だったら
							$block = ' rowspan="'.$ar_block[$data_n].'"'; //縦方向へ結合
							$span_n[$j] = $ar_block[$data_n] - 1; //rowspan結合数を格納→冒頭で繰り上げ処理
						}
                    }
                    elseif ( $ar_block[$data_n] = 1 ) { //ブロックが1つ
                        if ($ar_user_id[$data_n] == $_SESSION['USER_ID']&&$ar_kubun_cd[$data_n] == 1) {//自分が予約したデータ
                            $block = ' style="background-color: '.$color_red.'"' ; //赤色出力
                            
                        }elseif ($ar_kubun_cd[$data_n] == 2) {//修理の場合
                            $block = ' style="background-color: '.$color_green.'"' ; //緑色出力
                        }elseif ($ar_kubun_cd[$data_n] == 3) {//その他の場合
                            $block = ' style="background-color: '.$color_yellow.'"' ; //黄色出力
                        }elseif ($ar_user_id[$data_n] <> $_SESSION['USER_ID']&&$ar_kubun_cd[$data_n] == 1) {//自分の予約データ以外
                            $block = ' style="background-color: '.$color_blue.'"' ; //青色出力
                        }
                    }
                    $cts = h($ar_name[$data_n]).'（'.h($ar_sect[$data_n]).'）<br />'.h($ar_notes[$data_n]); //htmlエスケープしながら中身成形					$cts = h($ar_name[$data_n]).'（'.h($ar_sect[$data_n]).'）<br />'.h($ar_notes[$data_n]); //htmlエスケープしながら中身成形
					if ( $ar_kwd[$data_n] === '' ) { //削除キー無
						//onsubmitでJavaScriptを呼び出す
						$dlt = '<form action="" method="post" onsubmit="return dltChk()"><input type="hidden" name="date" value="'.$date.'" /><input type="hidden" name="id" value="'.$ar_id[$data_n].'" /><input type="submit" name="delete" value="×"></form>';
					} else { //削除キー有
						//カギ画像付加
						$dlt = '<form action="" method="post"><input type="hidden" name="date" value="'.$date.'" /><input type="hidden" name="id" value="'.$ar_id[$data_n].'" /><input type="submit" name="kwd_delete" value="×"></form><img src="key.gif" width="18" height="18" />';
					}
					$timetable_output .= '<td class="exist"'.$block.'>'.$cts.$dlt.'</td>'; //tdの中に出力
				}
			}
		} //横軸for
		$timetable_output .= "</tr>\n";
	} //縦軸for
	$timetable_output .= "</tbody>\n</table>\n";
	echo $timetable_output; //出力
	?>
</div>	
</div>	
</div><!-- /#timetable_box -->




</div><!-- #content -->


</body>
</html>