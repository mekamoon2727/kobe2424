<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="UFT-8">
        <title>mission5-1</title>
    </head>
    <body>
        <?php
        //DBに接続
        $dsn='データベース名';
        $user='ユーザー名';
        $password='パスワード';
        $pdo=new PDO($dsn,$user,$password,
        /*array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING) 」とは、
        データベース操作でエラーが発生した場合に警告（Worning: ）として表示するために
        設定するオプションです。
        */
        array(PDO::ATTR_ERRMODE =>PDO::ERRMODE_WARNING));
        
            //データベース内にテーブルを作成する。
            //「 IF NOT EXISTS 」は「もしまだこのテーブルが存在しないなら」という意味を持ちます.
            $sql = "CREATE TABLE IF NOT EXISTS tbtest"
        	."("
        	/*テーブル名は「tbtest」として、そこに登録できる項目（カラム）は
        	id ・自動で登録されていうナンバリング。
            name ・名前を入れる。文字列、半角英数で32文字。
            comment ・コメントを入れる。文字列、長めの文章も入る。
            pass･パスワードを入れる。文字列
            date･年月日を入れる。文字列
            とします。*/
        	."id INT AUTO_INCREMENT PRIMARY KEY,"
        	."name char(32),"
        	."comment TEXT,"
        	."pass TEXT,"
        	."date TEXT"
        	.");";
            //stmtはstatementの略です.つまりそのSQLを指すこと.stmtをつかうのは伝統です
            $stmt=$pdo->query($sql);
            //編集行番号を受信
            //もしeditとpass3が空でないのならば、
            if(!empty($_POST["edit"])and !empty($_POST["pass3"])){
                //以下のように変数を定義する。
                $edit=$_POST["edit"];
                $pass3=$_POST["pass3"];
                //SELECT文：入力したデータレコードを抽出し、表示する.
                //SELECT文はその名前の通りデータを「絞り込んで選ぶ」、つまり抽出する事ができます。
                $sql='SELECT * FROM tbtest';
                $stmt=$pdo->query($sql);
                $results=$stmt->fetchAll();
                //idが一致したらnameとcommentを取得する。取得したらbreakでforeachのループを抜け出す。
                foreach($results as $row){
                    if($row['id']==$edit and $row['pass']==$pass3){
                        $editname=$row['name'];
                        $editcomment=$row['comment'];
                        break;
                    }
                }
            }
            ?>
            
            <br><br>mission5-1だよ<br><br>
            <!--名前、コメント入力欄-->
            <form action="" method="post">
                <!--もし$editnameという変数が存在するならば、$editnameを表示させる。-->
                <input type="text" name="name" placeholder="名前" value=
                "<?php if(isset($editname)){echo($editname);} ?>">
                <!--もし$editcommentという変数が存在するならば、$editcommentを表示させる。-->
                <input type="text" name="comment" placeholder="コメント" value=
                "<?php if(isset($editcomment)){echo($editcomment);}?>">
                <input type="hidden" name="judge" value=
                "<?php if(isset($edit)){echo($edit);}?>">
                <input type="text" name="pass1" placeholder="パスワード">
                <input type="submit" name="submit">
            </form>
            <br>
            <!--削除ボタン-->
            <form action="" method="post">
                <input type="number" name="delete" placeholder="削除対象番号">
                <input type="text" name="pass2" placeholder="パスワード">
                <input type="submit" name="削除" value="削除">
            </form>
            <br>
            <!--編集ボタン-->
            <form action="" method="post">
                <!--入力フォームを作る。タイプはOOで名前はOO-->
                <input type="number" name="edit" placeholder="編集対象番号">
                <input type="text" name="pass3" placeholder="パスワード">
                <input type="submit" name="編集" value="編集">
            </form>
            <br><br>
    
            <?php
        	//テーブルに書き込み。先に定義する必要がある。
        	//もしnameが空でない　かつ　commentが空でない　かつ　pass1が空でない　かつ　judgeが空であるならば、
        	if(!empty($_POST["name"])and !empty($_POST["comment"])and !empty($_POST["pass1"])and empty($_POST["judge"])){
        	    //以下のように変数を定義する。
        	    $name=$_POST["name"];
        	    $comment=$_POST["comment"];
        	    $pass1=$_POST["pass1"];
        	    $date=date("Y/m/d H:i:s");
        	    
        	    //INSERT文：データを入力（データレコードの挿入）
        	    //データベースに作ったテーブルにデータを入力するための作業。
            	$sql = $pdo -> prepare("INSERT INTO tbtest (name, comment,pass,date) VALUES (:name, :comment, :pass, :date)");
                $sql -> bindParam(':name', $name, PDO::PARAM_STR);
                $sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
                $sql -> bindParam(':date', $date, PDO::PARAM_STR);
                $sql -> bindParam(':pass', $pass1, PDO::PARAM_STR);
                $sql -> execute();
            	}
                
            //編集
            if(!empty($_POST["name"])and !empty($_POST["comment"])and !empty($_POST["pass1"])and !empty($_POST["judge"])){
                $name=$_POST["name"];
                $comment=$_POST["comment"];
                $pass1=$_POST["pass1"];
                $date=date("Y/m/d H:i:s");
                $id = $_POST["judge"]; //変更する投稿番号 
                //UPDATE文：入力されているデータレコードの内容を編集
                //データベースのテーブルに登録したデータレコードは、UPDATE文で更新することが可能。
            	$sql = 'UPDATE tbtest SET name=:name,comment=:comment,pass=:pass1,date=:date WHERE id=:id';
            	$stmt = $pdo->prepare($sql);
            	$stmt->bindParam(':name', $name, PDO::PARAM_STR);
            	$stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
            	$stmt->bindParam(':id', $id, PDO::PARAM_INT);
            	$stmt->bindParam(':pass1',$pass1, PDO::PARAM_STR);
            	$stmt->bindParam(':date',$date,PDO::PARAM_STR);
            	$stmt->execute();
            }
                    
            //削除
            if(!empty($_POST["delete"])and !empty($_POST["pass2"])){
                $id=$_POST["delete"];
                $pass2=$_POST["pass2"];
                //idとpass2が一致した行を消す
                $sql='delete from tbtest where id=:id and pass=:pass2';
                $stmt=$pdo->prepare($sql);
                $stmt->bindParam(':id',$id,PDO::PARAM_INT);
                $stmt->bindParam(':pass2',$pass2,PDO::PARAM_STR);
                $stmt->execute();
            }
        	
            //テーブルの中身を表示⇒tbtestからデータを抽出する
            	$sql = 'SELECT * FROM tbtest';
            	$stmt = $pdo->query($sql);
            	$results = $stmt->fetchAll();
            	foreach ($results as $row){
            		//$rowの中にはテーブルのカラム名が入る
            		//$row[]を表示させる。
            		echo $row['id'].',';
            		echo $row['name'].',';
            		echo $row['comment'].',';
            		echo $row['date'].'<br>';
            	echo "<hr>";
            	}
        ?>
    </body>
</html>