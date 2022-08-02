<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>misson_5</title>
</head>
<body>
    <?php
    //4-1データベース作成（これ以降のミッションはすべてこれの下に書いて行うもの）
    $dsn = '******';
    $user = '******';
    $password = '*****';
    $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
    
    //4-2テーブル作成 テーブル名にマイナス記号は使えない
    $sql = "CREATE TABLE IF NOT EXISTS mission5"
    ." ("
    . "id INT AUTO_INCREMENT PRIMARY KEY,"
    . "name char(32),"
    . "comment TEXT,"
    . "time TEXT,"
    . "pas TEXT"
    .");";
    $stmt = $pdo->query($sql);

    if(isset($_POST["nam"],$_POST["del"],$_POST["hnum"],)){
        $numnum = $_POST["numnum"];
        $name = $_POST["nam"];
        $comment = $_POST["com"];
        $del = $_POST["del"];
        $hnum = $_POST["hnum"];
        $pas1 = $_POST["pas1"];
        $pas2 = $_POST["pas2"];
        $pas3 = $_POST["pas3"];
        $dat = date("Y年m月d日 H時i分s秒");
        if((!strlen($hnum))||(!strlen($pas3))){
            //誤送信
            if((!strlen($name))||(!strlen($comment))){
                if((!strlen($del))||(!strlen($pas2))){
                echo "名前とコメントの両方、編集対象番号、削除対象番号のいずれか、及びパスワードを入力してください。※パスワードのない投稿は編集、削除ができません。";
                }
                //削除
                else{
                    $id = $del ; // idがこの値のデータだけを抽出したい、とする
                    $sql = 'SELECT * FROM mission5 WHERE id=:id ';
                    $stmt = $pdo->prepare($sql);                  // ←差し替えるパラメータを含めて記述したSQLを準備し、
                    $stmt->bindParam(':id', $id, PDO::PARAM_INT); // ←その差し替えるパラメータの値を指定してから、
                    $stmt->execute();                             // ←SQLを実行する。
                    $result = $stmt->fetch();                     
                    if($pas2!==$result['pas']){
                        echo"パスワードを正しく入力してください。投稿が既に削除されている場合、復元はできません。<br>";
                    }
                    else{
                            //4-8データレコード削除
                        $sql = 'delete from mission5 where id=:id';
                        $stmt = $pdo->prepare($sql);
                        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                        $stmt->execute();    
                    }  
                }    
            }
            ////新規投稿
            elseif(!strlen($numnum)){    
                   //4-5データレコード挿入
                $sql = $pdo -> prepare("INSERT INTO mission5 (name, comment, time, pas) VALUES (:name, :comment, :time, :pas)");
                $sql -> bindParam(':name', $name, PDO::PARAM_STR);
                $sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
                $sql -> bindParam(':time', $dat, PDO::PARAM_STR);
                $sql -> bindParam(':pas', $pas1, PDO::PARAM_STR);
                $sql -> execute();
                
            }
            //編集追記
            else{
                $id = $numnum; //変更する投稿番号
                $sql = 'UPDATE mission5 SET name=:name,comment=:comment, time=:time, pas=:pas WHERE id=:id';
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':name', $name, PDO::PARAM_STR);
                $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
                $stmt->bindParam(':time', $dat, PDO::PARAM_STR);
                $stmt->bindParam(':pas', $pas1, PDO::PARAM_STR);
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                $stmt->execute();
            }
        }
        //編集
        else{
            $id = $hnum ; // idがこの値のデータだけを抽出したい、とする
            $sql = 'SELECT * FROM mission5 WHERE id=:id ';
            $stmt = $pdo->prepare($sql);                  // ←差し替えるパラメータを含めて記述したSQLを準備し、
            $stmt->bindParam(':id', $id, PDO::PARAM_INT); // ←その差し替えるパラメータの値を指定してから、
            $stmt->execute();                             // ←SQLを実行する。
            $result = $stmt->fetch(); 
            if (($pas3 == $result['pas'])){
                $hna = $result['name'] ;
                $hco = $result['comment'];
                $tou3 = $hnum;
                $pas4 = $result['pas'];
            }
            else{
                echo "パスワードを正しく入力してください。投稿が既に削除されている場合、編集はできません。<br>";
            }
        }
    }    
    ?>
    <form action="" method="post">
        <div>
            <label>
            投稿:<br>
            投稿用フォームに名前とコメントとパスワードを入力 ※パスワードがなくても投稿できますが、その投稿は編集、削除ができません。<br>              
            削除:<br>
            削除用フォームに投稿番号とパスワードを入力→パスワードが一致すると削除されます。<br>
            編集:<br>
            編集用フォームに投稿番号とパスワードを入力→パスワードが一致すると投稿用フォームに内容が反映されるので、
            書き換えて再び投稿してください。<br><br>
            <label>投稿用フォーム:<br>
                <input type="hidden" name="numnum" value="<?php if(isset($tou3)){echo $tou3;} ?>" >
                <input type="text" name="nam" value="<?php if(isset($hna)){echo $hna;} ?>" placeholder="名前">
                <input type="text" name="com" value="<?php if(isset($hco)){echo $hco;} ?>" placeholder="コメント">
                <input type="password" name="pas1" value="<?php if(isset($pas4)){echo $pas4;} ?>" placeholder="パスワード">
                <input type="submit" name="submit" value="投稿">
            </label>
        </div>
        <div>
            <label>削除用フォーム:<br>
                <input type="num" name="del" placeholder="削除対象番号(半角数字)">
                <input type="password" name="pas2" placeholder="パスワード">
                <input type="submit" name="submit" value = "削除">
            </label>
        </div>
        <div>
            <label>編集用フォーム:<br>
                <input type="num" name="hnum" placeholder="編集対象番号(半角数字)">
                <input type="password" name="pas3" placeholder="パスワード">
                <input type="submit" name="submit" value = "編集">
            </label>
        </div>
    </form>
    <?php
    echo "<br>投稿番号, 名前, コメント, 投稿日時 <br><br>";
    //4-6データレコード挿入入力したデータレコードを抽出し、表示する
    $sql = 'SELECT * FROM mission5';
    $stmt = $pdo->query($sql);
    $results = $stmt->fetchAll();
    foreach ($results as $row){
        //$rowの中にはテーブルのカラム名が入る
        echo $row['id'].',';
        echo $row['name'].',';
        echo $row['comment'].',';
        echo $row['time'].'<br>';
        echo "<hr>";
    }
    ?>            
</body>
</html>