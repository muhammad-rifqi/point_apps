<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

include "config/config.php";

if($_GET['act'] == 'register'){
    $ambildata = file_get_contents("php://input");
    $toarray = json_decode($ambildata,true);
    $sql = mysqli_query($koneksi,"insert into users(username,password,email,no_hp)values('".strtolower(str_replace(" ","",$toarray['username']))."','".md5($toarray['password'])."','".$toarray['email']."','".$toarray['no_hp']."')");
    if($sql){
        echo json_encode(array("message"=>"success"));
    }else{
        echo json_encode(array("message"=>"failed"));
    }
}

if($_GET['act'] == 'login'){
    $ambildata = file_get_contents("php://input");
    $toarray = json_decode($ambildata,true);
    $sql = mysqli_query($koneksi,"select id,username,password,no_hp from users where username = '".$toarray['username']."' and password = '".md5($toarray['password'])."'");
    $data = mysqli_fetch_assoc($sql);
    if($sql){
        setcookie("user_id", $data['id'] , time() + (15 * 60) , "/", "", false, false);
        setcookie("username", $data['username'] , time() + (15 * 60) , "/", "", false, false);
        setcookie("phone", $data['no_hp'] , time() + (15 * 60) , "/", "", false, false);
        echo json_encode(array("message"=>"success"));
    }else{
        echo json_encode(array("message"=>"failed"));
    }
}

if($_GET['act'] == 'point'){
    $ambildata = file_get_contents("php://input");
    $toarray = json_decode($ambildata,true);
    $sql = mysqli_query($koneksi,"insert into point(user_id,username,val)values('".$toarray['user_id']."','".$toarray['username']."','".$toarray['point']."')");
    if($sql){
        echo json_encode(array("message"=>"success"));
    }else{
        echo json_encode(array("message"=>"failed"));
    }
}

if($_GET['act'] == 'logout'){
    setcookie("user_id", "", time() - 3600, $path);
    setcookie("username", "", time() - 3600, $path);
    setcookie("phone", "", time() - 3600, $path);
    header('location:/point_apps/login');
}

if($_GET['act'] == 'list_users'){
    $sql = mysqli_query($koneksi,"select * from users order by id desc");
    $data = array();
    while($row = mysqli_fetch_assoc($sql)){
        $data[] = $row;
    }
    echo json_encode($data);
}

if($_GET['act'] == 'list_ranking'){
    $sql = mysqli_query($koneksi,"select id,user_id,username,val,sum(val) as jumlah from point group by username order by jumlah desc");
    $data = array();
    while($row = mysqli_fetch_assoc($sql)){
        $data[] = $row;
    }
    echo json_encode($data);
}

