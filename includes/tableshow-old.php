<?php 
require '../components/Components.php';
session_start();    

if($_SERVER["REQUEST_METHOD"] == "GET"){
    if(isset($_SESSION['role'])){
        if((int)$_SESSION['role'] == 0){
            $result = ""; $th = ""; $tr = ""; $h = "";
            if($_GET['type'] == "service"){
                $h = "Service List";
                $result = executeSelect("select s_id as id,service_name as name,rate as t from tblservice");
                $th = "<tr><th>Service ID</th><th>Service Name</th><th>Rate</th></tr>";
                while($row = $result->fetch_assoc()){
                    $tr .= "<tr><td>".$row['id']."</td><td>".$row['name']."</td><td>".$row['t']."</td></tr>";
                }
            }else if($_GET['type'] == "staff"){
                $h = "Staff List";
                $th = "<tr><th>Staff ID</th><th>Staff Name</th><th>Status</th></tr>";
                $result = executeSelect("select w_id as id,worker_name as name,active as t from tblworker");
                while($row = $result->fetch_assoc()){
                    $temp = $row['t'] == "1"?"Active":"Inactive";
                    $tr .= "<tr><td>".$row['id']."</td><td>".$row['name']."</td><td>".$temp."</td></tr>";
                }
            }
 ?>
 <!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?php echo $h; ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="/stylesheet/bootstrap/bootstrap.min.css">
    <link rel="stylesheet" href="/stylesheet/main.css">
    <style>
        .outer{
            display: flex;
            justify-content: center;
            align-items: top !important;
            background-color: rgba(0,0,0,0.7); 
            min-height: 100vh;
        }
        .con{
            margin: 50px auto;                        
            width: 75vw;            
            position: relative;
            background-color: white;
            padding: 10px; 
            overflow:auto;
        }
        .tbl{            
            width:100%;
            table-layout: fixed;
        }
        td{
            overflow-x: hidden;
        }
        @media only screen and (max-width: 768px) {
        /* For mobile phones: */
            .con{
                width:100vw;
                margin: 0px;
            }            
        } 
    </style>    
</head>
<body>
    <div class="outer">
        <div class="con">
            <h4 class="m-head" ><?php echo $h; ?></h4>
            <table class="tbl table table-bordered table-striped" >
                    <col style="width:11%">
                    <col style="width:70%">
                    <col style="width:25%">
                <thead><?php
                    echo $th;
                ?>                    
                </thead>
                <tbody>
                  <?php 
                    echo $tr;
                  ?>  
                </tbody>
            </table>
        </div>        
    </div>
</body>
</html>  
 <?php           
        }else if((int)$_SESSION['role'] == 1){
            header("Location: http://".$_SERVER['SERVER_NAME']."/billing");
            exit();
        }
    }else{
        header("Location: http://".$_SERVER['SERVER_NAME']);
        exit();
    }
}
?>