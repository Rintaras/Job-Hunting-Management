<?php
$dsn = 'mysql:dbname=DBname;host=localhost';
$user = 'UserName';
$password = 'PassWord';

try {
    $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
    $pdo->beginTransaction();



    $pdo->commit();

} catch (PDOException $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo "データベースの更新に失敗しました: " . $e->getMessage();
}
?>
