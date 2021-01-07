<?php
if(!isset($_SESSION)){
	session_start();
	} 
	if(!isset($_SESSION['EMAIL'])){
		header('Location: http://localhost:2964/singup.php');
    } 
    //メッセージ用変数
    $log1 = '';

    //<!-- PostGreSQLへの接続 -->

//タイムゾーンの設定(しとかないと時刻がズレる)
ini_set('date.timezone', 'Asia/Tokyo');

//接続パラメーター
$DBHOST = "localhost";
$DBPORT = "5432";
$DBNAME = "reserve_kiki";
$DBUSER = "postgres";
$DBPASS = "admin";

try{
//DB接続
$dbh = new PDO("pgsql:host=$DBHOST;port=$DBPORT;dbname=$DBNAME;user=$DBUSER;password=$DBPASS");
      //   print('<br>'."接続成功".'<br>');

// モーダルの「変更」ボタンが押されたとき
if (filter_input(INPUT_POST, "xxx") === "delete_Sub") {
  $delete_id = filter_input(INPUT_POST, "delete_id");
  // DB更新
  $sql = $dbh->prepare( 'DELETE FROM rsv_timetable WHERE id = :id' );
  $sql->bindValue(':id', $delete_id, PDO::PARAM_INT);
  $rsl = $sql->execute(); //実行
  if ( $rsl == false ){
      $log1 = '<p>削除に失敗しました。</p>';
  } else {
      $log1 = '<p>削除しました。</p>';
  }
}
//予約一覧取得用
//SQL作成
$sql = 'select * from "rsv_timetable"';

//SQL実行
// クエリ実行（データを取得）
$res = $dbh->query($sql);

}catch(PDOException $e){
print("接続失敗".'<br>');
print($e.'<br>');
die();
}

// // 「$res」からデータを取り出し、変数「$result」に代入。
// // 「PDO::FETCH_ASSOC」を指定した場合、カラム名をキーとする連想配列として「$result」に格納される。
while( $result = $res->fetch( PDO::FETCH_ASSOC ) ){
  $rows[] = $result;
}

//データベースへの接続を閉じる
$dbh = null;
$today = date("Y/m/d");
?>

<DOCTYPE html>
    <html>
    
    <head lang = "ja">
        <title>予約システム_管理者</title>
        <meta charset = "utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">  
        <link rel="stylesheet" href="css/bootstrap.css">
        <script type="text/javascript" src="js/jquery-3.5.1.js"></script>
        <script type="text/javascript" src="js/bootstrap.js"></script>
        <script type="text/javascript" src="js/jquery-3.3.1.slim.min.js"></script>
        <script type="text/javascript" src="js/bootstrap.min.js"></script>
        <script type="text/javascript" src="js/pd.js"></script>
    	<!-- jqueryDatePicker利用 -->
        <link rel="stylesheet" href="css/jquery-ui.min.css">
        <script type="text/javascript" src="js/jquery-ui.min.js"></script>
        <script type="text/javascript" src="js/jquery.ui.datepicker-ja.min.js"></script>
        <script type="text/javascript" src="js/pd.js"></script>

        <link href="product.css" rel="stylesheet">
        <link href="../example.css" rel="stylesheet">
        

    </head>
    
    <body>


        <!-- ヘッダ部読込 -->
        <?php include('top.php'); ?>
        
        <div id="content">
        <?php
        /*** メッセージ ***/
        if ( $log1 != '') { //処理メッセージがある場合
            $log1 = '<p class="msg">処理メッセージ</p>'."\n".$log1;
        }
        
        if ( $log1 != '' ) { 
            //echo '<div id="attention">'."\n";
            echo '<div id="attention">'."\n".$log1."\n"."</div>\n"; } //処理メッセージがある場合
            //echo "</div>\n";
        ?>
        <div class="container">
            <h4>機器予約一覧</h4>

            <div class="table-responsive">
            <table class="table table-hover table-bordered">
              <thead class="table-primary">
                <tr>
                  <th scope="col">ID</th>
                  <th scope="col">機器名</th>
                  <th scope="col">部門</th>
                  <th scope="col">予約者</th>
                  <th scope="col">予約日</th>
                  <th scope="col">時間始</th>
                  <th scope="col">時間終</th>
                  <th scope="col">操作</th>
                </tr>
              </thead>
              <tbody>
              <?php
              foreach($rows as $row){
            ?>
                <tr>
                  <th scope="row"><?php echo $row['id']; ?></th>
                  <td><?php echo $row['cpt_name']; ?></td>
                  <td><?php echo $row['sect']; ?></td>
                  <td><?php echo $row['name']; ?></td>
                  <td><?php echo date('Y年m月d日', strtotime($row['time_st']) ); ?></td>
                  <td><?php echo date('H時i分', strtotime($row['time_st'])); ?></td>
                  <td><?php echo date('H時i分', strtotime($row['time_end'])); ?></td>
                  <td>
                    <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#testModal"
                        data-id="<?php echo $row['id']; ?>" 
                        >
                    削除</button>
                  </td>
                </tr>
             <?php
                }
             ?>
              </tbody>
            </table>
            </div>

            <!-- ボタン・リンククリック後に表示される画面の内容 -->
            <div class="modal fade" id="testModal" tabindex="-1" role="dialog" aria-labelledby="basicModal" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title" id="myModalLabel">削除確認画面</h4>
                        </div>
                            <form role="form" id="form1" action="" method="POST">
                                <div class="modal-body">
                                    <p class="modal-cpt"></p>
                                    <label>データを削除しますか？</label>
                                    <input type="hidden" name="delete_id" id="delete_id" readonly >
                                </div>
                                
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-default" data-dismiss="modal">閉じる</button>
                                    <button type="submit" class="btn btn-danger" id="delete_Sub" name="xxx" value="delete_Sub">削除</button>
                                </div>
                            </form>
                    </div>
                </div>
            </div>

            <!-- ボタン・リンククリック後のイベント内容 -->
            <script>
                $('#testModal').on('show.bs.modal', function (event) {
                    //モーダルを開いたボタンを取得
                    var button = $(event.relatedTarget);
                    //モーダル自身を取得
                    var modal = $(this);
                    // data-***の部分を取得
                    var delete_id = button.data('id');
                    // input 欄に値セット
                     modal.find('.modal-body input#delete_id').val(delete_id)

                
                })
            </script> 


        </div>
        
        <footer class="container py-5">
            <div class="row">
                <div class="col-12 col-md">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="d-block mb-2"><circle cx="12" cy="12" r="10"></circle><line x1="14.31" y1="8" x2="20.05" y2="17.94"></line><line x1="9.69" y1="8" x2="21.17" y2="8"></line><line x1="7.38" y1="12" x2="13.12" y2="2.06"></line><line x1="9.69" y1="16" x2="3.95" y2="6.06"></line><line x1="14.31" y1="16" x2="2.83" y2="16"></line><line x1="16.62" y1="12" x2="10.88" y2="21.94"></line></svg>
                <small class="d-block mb-3 text-muted">&copy; 2020- Pro Device Inc</small>
                </div>
                <div class="col-6 col-md">
                <!-- <h5>Features</h5> -->
                <h5>センター特徴</h5>
                <ul class="list-unstyled text-small">
                    <!-- <li><a class="text-muted" href="#">Cool stuff</a></li> -->
                    <li><a class="text-muted" href="#">スタッフ</a></li>
                    <!-- <li><a class="text-muted" href="#">Random feature</a></li> -->
                    <li><a class="text-muted" href="#">サポート内容</a></li>
                    <!-- <li><a class="text-muted" href="#">Stuff for developers</a></li> -->
                    <li><a class="text-muted" href="#">研究者向け</a></li>
                    <!-- <li><a class="text-muted" href="#">Another one</a></li> -->
                    <li><a class="text-muted" href="#">そのほか</a></li>
                </ul>
                </div>
                <div class="col-6 col-md">
                <!-- <h5>Resources</h5> -->
                <h5>機器リソース</h5>
                <ul class="list-unstyled text-small">
                    <!-- <li><a class="text-muted" href="#">Resource</a></li> -->
                    <li><a class="text-muted" href="#">リソース一覧</a></li>
                </ul>
                </div>
                <div class="col-6 col-md">
                <!-- <h5>About</h5> -->
                <h5>このサイトについて</h5>
                <ul class="list-unstyled text-small">
                    <!-- <li><a class="text-muted" href="#">Privacy</a></li> -->
                    <li><a class="text-muted" href="#">プライバシー</a></li>
                    <!-- <li><a class="text-muted" href="#">Terms</a></li> -->
                    <li><a class="text-muted" href="#">利用規約</a></li>
                </ul>
                </div>
            </div>
        </footer>


    </body>
    
    </html>