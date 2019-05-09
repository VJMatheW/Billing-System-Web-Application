<?php

require '../components/Components.php';
header('Content-Type: application/json; charset=utf-8');

if($_SERVER["REQUEST_METHOD"] == "POST"){ 
    //echo "server hit with post";
    $obj = array(); 
    if(startsWith($_SERVER['REQUEST_URI'], "/service")){        
        $s_id =  test_input($_POST['sid']);
        $s_name = test_input($_POST['sname']) ;
        $rate = test_input($_POST['rate']);
        if($s_id == "0"){
            // new service
            $result = executeInsert("insert into tblservice(service_name,rate) values('$s_name',$rate)");
            if($result){
                $obj = TRUE;
            }else{
                $obj = FALSE;
            }
        }else{
            // updating existing service            
            $result = executeInsert("update tblservice set service_name = '$s_name',rate=$rate where s_id=$s_id");
            if($result){
                $obj = TRUE;
            }else{
                $obj = FALSE;
            }
        }        
    }
    if(startsWith($_SERVER['REQUEST_URI'], "/staff")){
        $w_id =  test_input($_POST['wid']);
        $w_name = test_input($_POST['wname']) ;
        $status = test_input($_POST['status']);
        if($w_id == "0"){
            // new service
            $result = executeInsert("insert into tblworker(worker_name,active) values('$w_name',$status)");
            if($result){
                $obj = TRUE;
            }else{
                $obj = FALSE;
            }
        }else{
            // updating existing service            
            $result = executeInsert("update tblworker set worker_name = '$w_name',active=$status where w_id=$w_id");
            if($result){
                $obj = TRUE;
            }else{
                $obj = FALSE;
            }
        }        
    }
    echo json_encode($obj);   

}

if($_SERVER["REQUEST_METHOD"] == "GET"){   
    $obj = array(); 
    if(startsWith($_SERVER['REQUEST_URI'], "/service")){
        $sid = test_input($_GET['s_id']);                
        $result = executeSelect("select * from tblservice where s_id=$sid");
        if($result){
            $row = $result->fetch_assoc();
            $obj['servicename'] = $row['service_name'];
            $obj['rate'] = $row['rate'];
        }else{
            $obj = FALSE;
        }
    }
    if(startsWith($_SERVER['REQUEST_URI'], "/staff")){
        $wid = test_input($_GET['w_id']);        
        $result = executeSelect("select * from tblworker where w_id=$wid");        
        if($result){            
            $row = $result->fetch_assoc();
            $obj['workername'] = $row['worker_name'];
            $obj['status'] = $row['active'];
        }else{
            $obj = FALSE;
        }         
    }
    echo json_encode($obj);
}
