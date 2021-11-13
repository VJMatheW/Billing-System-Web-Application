<?php

require_once '../components/Components.php';
include_once '../components/pos.php';
header('Content-Type: application/json; charset=utf-8');
error_reporting(E_ERROR | E_PARSE);
session_start();

if($_SERVER["REQUEST_METHOD"] == "GET"){        
    if(isset($_SESSION['role'])){
        if((int)$_SESSION['role'] >= 0){
            $con = getCon();
            $billno = test_input($_GET['billno']);
            $content = prepareBill($con,$billno);               
            $status = posPrint($content);            
            //$status = 1;
            $con->close();
            echo $status;
        }        
    }else{
        header("Location: http://".$_SERVER['SERVER_NAME']);
        exit();
    }
}

if($_SERVER["REQUEST_METHOD"] == "POST" ){  
    if(isset($_SESSION['role'])){  
        // bill printing    
        $json = file_get_contents('php://input');
        //echo $json;
        $obj = json_decode($json, true);
        $cust_id = "";
        $con = getCon();
        $con->autocommit(false);
        // checking for new user and act accordingly
        if($obj["newuser"]){
            //echo "new user";        
            if($con->query("insert into tblhelper values()") === TRUE){
                $result = $con->query("select last_insert_id() as custId");
                $row = $result->fetch_assoc();
                $cust_id = $row['custId'];
                //echo "New cust id : ".$cust_id;            
                $query = "insert into tblcustinfo values(".$cust_id.",'".$obj['phone']."','".$obj['name']."','".$obj['mail']."')";            
                if($con->query($query)){
                    //echo "new cust added";
                }            
                //echo "New Cust id : ".$cust_id;
            }else{
                echo "Failed to Create New User";
            }
        }else{
            //echo "existing user";
            $cust_id = $obj['custid'];
            //echo $cust_id;
        }    

        // inserting for billing both new or existing customer
        $tempresult = $con->query("select count(billno)+1 as newbillno from tblbilling");
        $r = $tempresult->fetch_assoc();
        $newbillno = $r['newbillno'] == null ? 1 : $r['newbillno'] ;
        $billarray = $obj['bill'];
        $paymode = $obj['paymode'];
        $tenderamt = $obj['tendered']==""?0:$obj['tendered'];
        $totalamount = 0;
        $valq = "";
        for($i=0; $i < sizeof($billarray); $i++){
            $totalamount += (int)$billarray[$i]['finalamt'];
            $single = $billarray[$i];
            if($i == 0){
                $valq .= "(".$newbillno.",".$single['serviceid'].",".$single['workerid'].",".$single['mrp'].",".$single['discount'].",".$single['finalamt'].")";
            }else{
                $valq .= ",(".$newbillno.",".$single['serviceid'].",".$single['workerid'].",".$single['mrp'].",".$single['discount'].",".$single['finalamt'].")";
            }    
        }
        // echo "insert into tblbilling(billno,cust_id,date,totalamount,paymode,tenderamount) values(".$newbillno.",".$cust_id.",now(),".$totalamount.",".$paymode.",".$tenderamt.")";
        if($con->query("insert into tblbilling(billno,cust_id,date,totalamount,paymode,tenderamount) values(".$newbillno.",".$cust_id.",now(),".$totalamount.",".$paymode.",".$tenderamt.")") === TRUE){
            //echo "insert into tblbillinghelper(billno,service_id,worker_id,amount,discount)values".$valq;        
            if($con->query("insert into tblbillinghelper(billno,service_id,worker_id,amount,discount,famount)values".$valq) === TRUE){            
                //$result = $con->query("select last_insert_id() as billno");
                //$row = $result->fetch_assoc();            
                $con->commit();
                $billno = $newbillno;
                $status = FALSE;
                $msg = "";
                if($obj["print"]){
                    $content = prepareBill($con,$billno);               
                    $status = posPrint($content);
                    //$msg = "Bill printed";
                }else{
                    $status = TRUE;
                    //$msg = "Bill NOT printed";
                }             
                //$status = TRUE;
                if($status){
                    echo json_encode(array("status"=>TRUE, "billno"=>$billno, "msg"=>"Bill Successfully Generated"));
                }else{
                    echo json_encode(array("status"=>TRUE, "billno"=>$billno, "msg"=>"Bill Generated but not Printed...Reprint using the above Bill Number"));
                }
            }else{
                $con->rollback();
                echo json_encode(array("status"=>FALSE,"msg"=>"Not inserted into tblbillinghelper"));
            }
        }else{        
            echo json_encode(array("status"=>FALSE,"msg"=>"Not inserted into tblbilling ".$con->error));
            $con->rollback();
        }

        $con->close();  
    }else{
        echo json_encode(array("status"=>FALSE,"msg"=>"Please Login to Generate Bill...<a href='/'>LOGIN</a>"));
    }  
}
?>