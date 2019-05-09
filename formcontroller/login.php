<?php

require '../components/Components.php';
session_start();
unset($_SESSION['error']);


if($_SERVER["REQUEST_METHOD"] == "POST"){

    $uname = test_input($_POST['uname']);
    $pass = test_input($_POST['password']);
    $con = getCon();
    //echo "select role from tbllogin where uname='".$uname."' and password='".$pass."'";
    $result = $con->query("select role from tbllogin where uname='".$uname."' and password='".$pass."'");
    $role = $result->fetch_assoc()['role'];
    if($result->num_rows > 0){
        if((int)$role === 0){            
            $_SESSION['role'] = 0;
            header("Location: http://".$_SERVER['SERVER_NAME']."/admin");
            exit();
        }else if((int)$role === 1){
            $_SESSION['role'] = 1;            
            header("Location: http://".$_SERVER['SERVER_NAME']."/billing");
            exit();
        }
    }else{
        echo "UserName / Password Incorrect";
        $_SESSION['error'] = "Error";            
        header("Location: http://".$_SERVER['SERVER_NAME']);
        exit();
    }

}

if($_SERVER["REQUEST_METHOD"] == "GET"){        
    if(isset($_SESSION['role'])){
        if((int)$_SESSION['role'] >= 0){
            echo $_GET['bno'];
        }        
    }else{
        header("Location: http://".$_SERVER['SERVER_NAME']);
        exit();
    }
}