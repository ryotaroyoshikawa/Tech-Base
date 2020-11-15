
		<?php

			// DB接続設定
			$dsn = 'データベース名';
			$user = 'ユーザー名';
			$pass = 'パスワード';
			// PDOオブジェクトの生成（DB接続）
			$pdo = new PDO($dsn, $user, $pass, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));

			

			$sql = "CREATE TABLE IF NOT EXISTS keijiban"
			//フィールド名とデータ型指定
			." ("
			."id INT AUTO_INCREMENT PRIMARY KEY,"  //idに整数型で自動格納する
			."name char(32),"  //nameにCHAR型を指定
			."comment TEXT," //commentにTEXT型を指定
			."postedAt DATETIME,"  //postedAtにDATETIMEを指定(後にTEXTに修正)
			."password char(30)"  //passwordにCHAR型を指定
			.");";
			$stmt = $pdo->query($sql);
			
			
			/*投稿or編集*/
			//もしフォーム内が空でないときに以下を実行する
			if (!empty($_POST['name']) && !empty($_POST['comment']) && !empty($_POST['password'])) {

				// editNoがないときは新規投稿、ある場合は編集を判断 
				if(empty ($_POST['editNo'])){
									
					/*投稿機能*/
					//データを入力
					//name, comment, postedAt, passwordの各カラムに関数を入れる？
					//構文　INSERT INTO テーブル名(列名) VALUES (値);
					$sql = $pdo -> prepare("INSERT INTO keijiban (name, comment, postedAt, password) VALUES (:name, :comment, now(), :password)");
				
					//valueの値をPHP関数にバインド（くっつける）
					$sql -> bindParam(':name', $name, PDO::PARAM_STR);
					$sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
					$sql -> bindParam(':password', $password, PDO::PARAM_STR);


					//関数の中身を指定
					$name = $_POST['name'];
					$comment = $_POST['comment']; 
					$password =$_POST['password'];
				
					//INSERTクエリを実行
					$sql -> execute();

				}else{

				   	//入力されているデータレコードの内容を編集
					$id = $_POST['editNo']; //変更する投稿番号
					$name = $_POST['name'];
					$comment = $_POST['comment']; 
					$password =$_POST['password'];

					//編集実行（/*投稿機能参照*/）
					$sql = 'UPDATE keijiban SET name=:name,comment=:comment,password=:password WHERE id=:id';
					$stmt = $pdo->prepare($sql);
					$stmt->bindParam(':name', $name, PDO::PARAM_STR);
					$stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
					$stmt->bindParam(':password', $password, PDO::PARAM_STR);
					$stmt->bindParam(':id', $id, PDO::PARAM_INT);
					$stmt->execute();
				}
			}


			/*削除機能*/
			//フォーム内が空でないとき
			if (!empty ($_POST['deleteNo']) && !empty($_POST['delpassword'])){
				
				$sql='SELECT*FROM keijiban';
				$stmt=$pdo->query($sql);
				$results=$stmt->fetchAll();
				foreach($results as $row){

					//削除フォームで入力された番号と投稿番号が一致する，かつパスワードと入力されたパスワードが一致したら
					if($row['id']==$_POST['deleteNo'] && $row['password']==$_POST['delpassword']){
				
						//入力データの受け取りを変数に代入
						$id=$_POST['deleteNo'];

						//指定されたid行の削除実行
						$sql='delete from keijiban where id=:id';
						$stmt=$pdo->prepare($sql);
						$stmt->bindParam(':id',$id,PDO::PARAM_INT);
						$stmt->execute();
					
					}
				}
				
			}		
				
			


			/*編集データ読み込み*/
			if(!empty($_POST["edit"]) && !empty($_POST["editpassword"])){

				$sql='SELECT*FROM keijiban';
				$stmt=$pdo->query($sql);
				$results=$stmt->fetchAll();
				foreach($results as $row){

					//編集フォームで入力された番号と投稿番号が一致する，かつパスワードと入力されたパスワードが一致したら
					if($row['id']==$_POST['edit'] && $row['password']==$_POST['editpassword']){
						
						//投稿のそれぞれの値を取得し変数に代入
						$editnumber = $row['id'];
						$editname = $row['name'];
						$editcomment = $row['comment'];
						$editpassword2 = $row['password'];

						//既存の投稿フォームに、上記で取得した「名前」と「コメント」の内容が既に入っている状態で表示させる
						//formのvalue属性で対応						
			
					}
				
				}
			}
			
		?>

<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="UTF-8">
        <title>mission_5-1</title>
    </head>
	
	<body bgcolor='#f2f2f4' text='#303232'>
			<h1><span style="color:#183cea">昨日の夕飯はなんでしたか？？？？</span></h1>
			<p><form action="" method="post">
			  
			  <input type="text" name="name" placeholder="名前" value="<?php if(isset($editname)) {echo $editname;} ?>"><br>
			  <input type="text" name="comment" placeholder="コメント" value="<?php if(isset($editcomment)) {echo $editcomment;} ?>"><br>
			  <input type="password" name="password" placeholder="パスワード" value="<?php if(isset($editpassword2)) {echo $editpassword2;} ?>">
			  <input type="submit" name="submit" value="送信">
			  <input type="hidden" name="editNo" value="<?php if(isset($editnumber)) {echo $editnumber;} ?>">
			</form></p>
		
			<p><form action="" method="post">
			  <input type="number" name="deleteNo" placeholder="削除対象番号"><br>
			  <input type="password" name="delpassword" placeholder="パスワード" value="">
			  <input type="submit" name="delete" value="削除">
			</form></p>
		
			<p><form action="" method="post">
			  <input type="number" name="edit" placeholder="編集対象番号"><br>
			  <input type="password" name="editpassword" placeholder="パスワード" value="">
			  <input type="submit" value="編集">
			</form></p>

投稿番号，名前，コメント，投稿された日時<br><br>
	<?php		
			/*表示機能*/
			$sql = 'SELECT * FROM keijiban';
			$stmt = $pdo->prepare($sql);                  // ←差し替えるパラメータを含めて記述したSQLを準備し、
			$stmt->execute();                             // ←SQLを実行する。
			$results = $stmt->fetchAll(); 
			foreach ($results as $row){
				//$rowの中にはテーブルのカラム名が入る
				echo $row['id'].', ';
				echo $row['name'].'さん, ';
				echo $row['comment'].', ';
				echo $row['postedAt'].'<br>';
				
			
			}
	?>
	</body>
</html>
