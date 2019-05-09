<?php

require '../components/Components.php';
header('Content-Type: application/json; charset=utf-8');
error_reporting(E_ERROR | E_PARSE);

if($_SERVER["REQUEST_METHOD"] == "POST"){ 
    //echo "server hit with post";
    $json = file_get_contents('php://input');
    //echo $json;
    $obj = json_decode($json, true);
    $billno = $obj['billno'];
    $paymode = $obj['paymode'];
    $totalamt = $obj['totalamt'];
    $tenderamt = $obj['tenderamt'];
    $billarray = $obj['content'];
    $valq = "";
    for($i=0; $i < sizeof($billarray); $i++){
        //$totalamount += (int)$billarray[$i]['finalamt'];
        $single = $billarray[$i];
        if($i == 0){
            $valq .= "(".$billno.",".$single['serviceid'].",".$single['workerid'].",".$single['mrp'].",".$single['discount'].",".$single['finalamt'].")";
        }else{
            $valq .= ",(".$billno.",".$single['serviceid'].",".$single['workerid'].",".$single['mrp'].",".$single['discount'].",".$single['finalamt'].")";
        }    
    }         

    $con = getCon();
    $con->autocommit(false);
    //echo "update tblbilling set date=now(), totalamount=$totalamt, paymode=$paymode, tenderamount=$tenderamt where billno = $billno";
    if($con->query("update tblbilling set date=now(), totalamount=$totalamt, paymode=$paymode, tenderamount=$tenderamt where billno = $billno") === TRUE){    
        if($con->query("insert into tblbillinghelper(billno,service_id,worker_id,amount,discount,famount)values".$valq) === TRUE){                            
            $con->commit();
            echo json_encode(array("status"=>TRUE, "billno"=>$billno, "msg"=>"Bill Successfully Updated"));        
        }else{        
            $con->rollback();
            echo  json_encode(array("status"=>FALSE,"msg"=>"Bill Not Updated tblhelper"));
        }
    }else{
        $con->rollback();
        echo  json_encode(array("status"=>FALSE,"msg"=>"Bill Not Updated billing".$con->error));
    }        
    $con->close();   
}

if($_SERVER["REQUEST_METHOD"] == "GET"){   
    $obj = array(); 
    $con = getCon();
    $from = "";
    $to = "";
    if(startsWith($_SERVER['REQUEST_URI'], "/recentbill")){
        if(isset($_GET['from']) && !empty($_GET['from'])){
            $from = $_GET['from'];
        }else{
            $curdate = new DateTime(null,new DateTimeZone('Asia/Kolkata'));
            $curdate = $curdate->format('Y-m-d');
            $from = $curdate;            
        }
        if(isset($_GET['to']) && !empty($_GET['to'])){
            $to = $_GET['to'];            
        }else{
            $curdate = new DateTime(null,new DateTimeZone('Asia/Kolkata'));
            $curdate = $curdate->format('Y-m-d');            
            $to = $curdate;
        }
        $obj = getBills($con,$from,$to);
    }elseif(startsWith($_SERVER['REQUEST_URI'], "/paymodeamt")){                
        if(isset($_GET['from']) && !empty($_GET['from'])){
            $from = $_GET['from'];
        }else{
            $curdate = new DateTime(null,new DateTimeZone('Asia/Kolkata'));
            $curdate = $curdate->format('Y-m-d');
            $from = $curdate;            
        }
        if(isset($_GET['to']) && !empty($_GET['to'])){
            $to = $_GET['to'];            
        }else{
            $curdate = new DateTime(null,new DateTimeZone('Asia/Kolkata'));
            $curdate = $curdate->format('Y-m-d');            
            $to = $curdate;
        }
        $obj = getPaymodeAmt($con,$from, $to);
    }else{
        $billno = test_input($_GET['billno']);
        $result = executeSelect("select totalamount from tblbilling where billno=".numberOnly($billno));
        if($result){ // if the billno not present it resturns FALSE
            $row = $result->fetch_assoc();
            $obj['status'] = TRUE;
            $obj['totalamt'] = $row['totalamount'];
        }else{
            $obj['status'] = FALSE;
        }
    }
    echo json_encode($obj);
}

function getBills($con, $from, $to){
    $temp = array();  
    $noofcustomer = 0;  
    $queryLast3Bill = "select a.billno as billno,b.name as name, a.date as date ,count(a.billno) as noofservice, 
        e.name as paymode, a.totalamount
        from tblbilling as a inner join tblcustinfo as b
        on a.cust_id=b.cust_id
        inner join tblbillinghelper as c
        on a.billno=c.billno
        inner join tblservice as d
        on c.service_id=d.s_id
        inner join tblpaymode as e
        on a.paymode = e.id
        where date(a.date) between '$from' and '$to'
        group by c.billno        
        order by date desc"; 
        //echo $queryLast3Bill;       
    $result = $con->query($queryLast3Bill);
    if($result->num_rows > 0){
        while($row = $result->fetch_assoc()){
            $noofcustomer++;
            array_push($temp,array("billno"=>$row['billno'],"name"=>$row['name'], "date"=>date('d-m-Y', strtotime($row['date'])), "servicecount"=>$row['noofservice'],"paymode"=>$row['paymode'], "totalamt"=>$row['totalamount']));
        }
    }else{
        $temp = FALSE;
    }
    return array("billinfo"=>$temp, "noofcustomer"=>$noofcustomer);
}

