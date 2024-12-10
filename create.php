<?php
$dsn = 'mysql:dbname=DBname;host=localhost';
$user = 'UserName';
$password = 'PassWord';

try {

    $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));

    $sql = "CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        email VARCHAR(255) NOT NULL UNIQUE,
        password_hash VARCHAR(255) NOT NULL,
        created_at DATETIME,
        updated_at DATETIME
    )";
    $pdo->exec($sql);

    $sql = "CREATE TABLE IF NOT EXISTS profiles (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        name VARCHAR(255),
        university VARCHAR(255),
        major VARCHAR(255),
        updated_at DATETIME,
        FOREIGN KEY (user_id) REFERENCES users(id)
    )";
    $pdo->exec($sql);

    $sql = "CREATE TABLE IF NOT EXISTS job_status (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        status_internal VARCHAR(255),
        status_selection VARCHAR(255),
        updated_at DATETIME,
        FOREIGN KEY (user_id) REFERENCES users(id)
    )";
    $pdo->exec($sql);

    $sql = "CREATE TABLE IF NOT EXISTS schedules (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        event_date DATE,
        event_description TEXT,
        updated_at DATETIME,
        FOREIGN KEY (user_id) REFERENCES users(id)
    )";
    $pdo->exec($sql);

    echo "テーブルの作成が完了しました。";
} catch (PDOException $e) {
    echo "エラーが発生しました: " . $e->getMessage();
    exit;
}
