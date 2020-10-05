<!DOCTYPE html>
<html lang="ja">
 <head>
   <meta charset="UTF-8">
   <title>mission5-1</title>
 </head>
 <body>
<?php
// データベースへの接続
$dsn='データベース名';
$user='ユーザー名';
$password='パスワード';
$pdo=new PDO($dsn,$user,$password,array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));

// データベース内にpostテーブルを作成
$sql="CREATE TABLE IF NOT EXISTS repost"
."("
."id INT AUTO_INCREMENT PRIMARY KEY,"
."name char(32),"
."comment TEXT,"
."password char(32),"
."created DATETIME"
.");";
$stmt=$pdo->query($sql);

// もし名前とコメント、パスワードが入力されていて
if(!empty($_POST["name"]) && !empty($_POST["comment"]) && !empty($_POST["password"])){

// 編集番号と編集パスワードが受信されていた場合
if(!empty($_POST["flag"]) && !empty($_POST["passwordflag"])){
  $flag=$_POST["flag"];
  $flagpassword=$_POST["passwordflag"];

// num_editに値するデータをでーだベースから抽出
$id=$flag;

$sql='SELECT*FROM repost WHERE id=:id';
$stmt=$pdo->prepare($sql);
$stmt->bindParam(':id',$id,PDO::PARAM_INT);
$stmt->execute();
$results=$stmt->fetchAll();

// var_dump($results);
// その値におけるパスワードを取り出す
foreach($results as $row){
  $editnumber=$row['id'];
  $editpassword=$row['password'];

  // var_dump($editpassword);
}
// そのidにおけるデータのパスワードと入力された$editpasswordが等しい場合
if($editpassword==$flagpassword){
  // 受信した番号に値するデータを編集
  $id=$flag; // 変更する投稿番号
  $name=$_POST["name"];
  $comment=$_POST["comment"];
  $password=$_POST["password"];
  $created=date("Y/m/d H:i:s");
  $sql='UPDATE repost SET
name=:name,comment=:comment,password=:password,created=:created WHERE id=:id';
  $stmt=$pdo->prepare($sql);
  $stmt->bindParam(':name',$name,PDO::PARAM_STR);
  $stmt->bindParam(':comment',$comment,PDO::PARAM_STR);
  $stmt->bindParam(':password',$password,PDO::PARAM_STR);
  $stmt->bindParam(':created',$created,PDO::PARAM_STR);
  $stmt->bindParam(':id',$id,PDO::PARAM_INT);
  $stmt->execute();
}
// 編集したデータベースのデータをブラウザ表示
$sql='SELECT*FROM repost';
$stmt=$pdo->query($sql);
$results=$stmt->fetchAll();
foreach($results as $row){
  echo $row['id'].',';
  echo $row['name'].',';
  echo $row['comment'].',';
  echo $row['password'].',';
  echo $row['created'].'<br>';
  echo "<hr>";
}
// 編集番号、編集パスワードが空欄だったとき
}else{
  $sql=$pdo->prepare("INSERT INTO repost(name,comment,password,created) VALUES(:name,:comment,:password,:created)");
  $sql->bindParam(':name',$name,PDO::PARAM_STR);
  $sql->bindParam(':comment',$comment,PDO::PARAM_STR);
  $sql->bindParam(':password',$password,PDO::PARAM_STR);
  $sql->bindParam(':created',$created,PDO::PARAM_STR);
  $name=$_POST["name"];
  $comment=$_POST["comment"];
  $password=$_POST["password"];
  $created=date("Y/m/d H:i:s");
  $sql->execute();
  // データベースに入力したうえでその値を入力する
  $sql='SELECT*FROM repost';
  $stmt=$pdo->query($sql);
  $results=$stmt->fetchAll();
  foreach($results as $row){
    echo $row['id'].',';
    echo $row['name'].',';
    echo $row['comment'].',';
    echo $row['password'].',';
    echo $row['created'].'<br>';
    echo "<hr>";
  }
}
}

// 入力したパスワードが入力したナンバーと等しいとき編集可
if(!empty($_POST["num_del"]) && !empty($_POST["deletepassword"])){
  $id=$_POST["num_del"];
  $sql='SELECT*FROM repost WHERE id=:id';
  $stmt=$pdo->prepare($sql);
  $stmt->bindParam(':id',$id,PDO::PARAM_INT);
  $stmt->execute();
  $results=$stmt->fetchAll();
  foreach($results as $row){
    $deletepassword=$row['password'];
  }
  if($deletepassword==$_POST["deletepassword"]){
    $id=$_POST["num_del"];
    $sql='delete from repost where id=:id';
    $stmt=$pdo->prepare($sql);
    $stmt->bindParam(':id',$id,PDO::PARAM_INT);
    $stmt->execute();

    $sql='SELECT*FROM repost';
    $stmt=$pdo->query($sql);
    $results=$stmt->fetchAll();
    foreach($results as $row){
      echo $row['id'].',';
      echo $row['name'].',';
      echo $row['comment'].',';
      echo $row['password'].',';
      echo $row['created'].'<br>';
      echo "<hr>";
    }
  }
}

// 編集ボタンが押されたとき
if(!empty($_POST["num_edit"]) && !empty($_POST["editpassword"])){
  $id=$_POST["num_edit"];
  $sql='SELECT*FROM repost WHERE id=:id';
  $stmt=$pdo->prepare($sql);
  $stmt->bindParam(':id',$id,PDO::PARAM_INT);
  $stmt->execute();
  $results=$stmt->fetchAll();
  foreach($results as $row){
    $edit_name=$row['name'];
    $edit_comment=$row['comment'];
    $edit_password=$row['password'];
    echo $edit_name;
    echo $edit_comment;
    echo $edit_password;
  }
}
?>


<!--コメント入力フォーム-->
<form action="<?php echo($_SERVER['PHP_SELF'])?>" method="post">
  コメントを投稿する<br>
  <input type="hidden" name="flag" value="<?php if(!empty($_POST["num_edit"])){
    echo $_POST["num_edit"];
  }?>">
  <input type="hidden" name="passwordflag" value="<?php if(!empty($_POST["editpassword"])){
    echo $_POST["editpassword"];
  }?>">
  <input type="text" name="name" placeholder="名前" value="<?php if(!empty($_POST["num_edit"])){
    echo $edit_name;
  }?>">
  <input type="text" name="comment" placeholder="コメント" value="<?php if(!empty($_POST["num_edit"])){
    echo $edit_comment;
  }?>">
  <input type="text" name="password" placeholder="投稿パスワード" value="<?php if(!empty($_POST["num_edit"])){
    echo $edit_password;
  }?>">
  <input type="submit" value="送信">
</form>

<!--コメント削除フォーム-->
<form action="<?php echo($_SERVER['PHP_SELF'])?>" method="post">
  投稿を削除する<br>
  <input type="number" name="num_del" placeholder="削除する投稿の番号">
  <input type="text" name="deletepassword" placeholder="削除パスワード">
  <input type="submit" value="削除">
</form>

<!--コメント編集フォーム-->
<form action="<?php echo($_SERVER['PHP_SELF'])?>" method="post">
  投稿を編集する<br>
  <input type="number" name="num_edit" placeholder="編集する投稿の番号">
  <input type="text" name="editpassword" placeholder="編集パスワード">
  <input type="submit" value="編集">
</form>

</body>
</html>
