<?php
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
    $results = $pdo->prepare(
        'SELECT * FROM "M_Kubun" WHERE bunrui_cd = :bunrui_cd'   
    );
    //機器分類絞り込み用
    $results->bindValue(':bunrui_cd', '3', PDO::PARAM_STR);
    $results->execute();
		  
} catch (Exception $e) {
	exit('データベース接続に失敗しました。'.$e->getMessage());
}
        // 「$res」からデータを取り出し、変数「$result」に代入。
        // 「PDO::FETCH_ASSOC」を指定した場合、カラム名をキーとする連想配列として「$result」に格納される。
        while( $result = $results->fetch( PDO::FETCH_ASSOC ) ){
            $rows[] = $result;
		}
		//取り出した配列から機器名だけをただの配列として抜き出す_樋田
        $value = array_column($rows, 'kubun_nm');
        $key = array_column($rows, 'kubun_cd');
        $chapters = array_combine($key, $value);
        


        
        if (!empty( $_GET['cd'])){
            $kiki_bunrui_cd = $_GET['cd'];
            // DB更新
            $results2 = $pdo->prepare(
                'SELECT * FROM "M_Kiki" WHERE kiki_bunrui_cd = :kiki_bunrui_cd'   
            );
            //機器分類絞り込み用
            $results2->bindValue(':kiki_bunrui_cd', $kiki_bunrui_cd, PDO::PARAM_STR);
            $results2->execute();


                // 「$res」からデータを取り出し、変数「$result」に代入。
                // 「PDO::FETCH_ASSOC」を指定した場合、カラム名をキーとする連想配列として「$result」に格納される。
                while( $result2 = $results2->fetch( PDO::FETCH_ASSOC ) ){
                    $rows2[] = $result2;
                }
                //取り出した配列から機器名だけをただの配列として抜き出す
                $value2 = array_column($rows2, 'kiki_name');
                $key2 = array_column($rows2, 'ID');
                $chapters2 = array_combine($key2, $value2);

                
            if ( $results == false ){
                $log1 = '<p>削除に失敗しました。</p>';
            } else {
                $log1 = '<p>削除しました。</p>';
            }
           // var_dump($kiki_bunrui_cd);
        }

?>

<DOCTYPE html>
    <html>
    
    <head lang = "ja">
        <title>予約機器選択</title>
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
	<?php include('top.php'); ?>
	<div id="content">
<div class="container">
	<!-- 登録フォーム -->
	<div class="row">
		<div class="col-md-5">
            
                <label>機器分類</label>
                <input type="hidden" id="target">
                    <select name="cpt_bunrui" id = "cpt_bunrui">
                    <?php
                    $test = $cpt_bunrui;
                    
                        //ポストバック対策
                        if($kiki_bunrui_cd <> null) {//ポストバック後に格納される変数がNULLでなければ
                            foreach($chapters as $key => $value) {
                            if($key == $kiki_bunrui_cd){//ループしたキーの値が変数と一致すれば
                                // 選択状態のコードを加える
                                echo "<option value='$key' selected>".$value."</option>";
                              }else{
                                // ただの選択肢を追加
                                echo "<option value='$key'>".$value."</option>";
                              }
                            }
                        }else{//ポストバック前の初期状態なら
                            echo '<option value=--->---</option>';
                            foreach ($chapters as $key => $value) {
                                echo '<option value="'.$key.'">'.$value.'</option>';
                        }
                        }
            
                    ?>
                    </select>
                    
                    <!-- 選択された分類の値を取得 -->
                    <script type="text/javascript">
                    // セレクトが変更されたら
                        $("#cpt_bunrui").change( function(){
                            // 値を取得
                            var sVal = $("#cpt_bunrui").val();
                            //別の非表示コントロールに値を渡す
                            document.getElementById( "target" ).value = sVal ;
                            //URLパラメータにセットしてポストバック
                            location.href = "http://localhost:2964/kiki_search.php?cd="+sVal;
                            
                        })
                    </script> 
                    <BR/>
                    <form action="gant2-1.php" method = "POST">
                    <label>予約機器</label>
                    <select name="kiki_id" id="kiki_id">
                    <?php
                    foreach ($chapters2 as $key => $value) {
                        echo '<option value="'.$key.'">'.$value.'</option>';
                    }
                    ?>
                    
                    </select>
                    <BR/>
                    <input type="submit"name="submit"value="予約画面へ"/>
                    </form>		
		</div><!-- /#col -->
	</div><!-- /#row -->
</div><!-- /#container -->

</div><!-- #content -->

</body>
</html>