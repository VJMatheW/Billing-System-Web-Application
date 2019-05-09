<?php

require '../components/Components.php';
header('Content-Type: application/json; charset=utf-8');
error_reporting(E_ERROR | E_PARSE);

if($_SERVER["REQUEST_METHOD"] == "GET"){   
    $obj = array();     
    $billno = "";
    if(startsWith($_SERVER['REQUEST_URI'], "/billdetail")){
        if(isset($_GET['billno']) && !empty($_GET['billno'])){
            $billno = numberOnly($_GET['billno']);
            $obj = getBillDetails($billno);
        }else{
            $obj = FALSE;
        }            
    }
    echo json_encode($obj);
}

function getBillDetails($billno){
    $obj = array();
    $temp = array();
    $result = executeSelect("select a.billno as billno,a.date as date, a.totalamount as total,a.tenderamount as tender, d.name, c.name as custname, c.phone as phone, e.service_name as service, 
    f.worker_name as staff, b.amount as serviceamt, b.discount as discount, famount
    from tblbilling as a inner join tblbillinghelper as b
    on a.billno = b.billno
    inner join tblcustinfo as c 
    on a.cust_id = c.cust_id
    inner join tblpaymode as d
    on a.paymode = d.id
    inner join tblservice as e
    on b.service_id = e.s_id
    inner join tblworker as f
    on b.worker_id = f.w_id
    where a.billno = $billno");

    if($result->num_rows > 0){
        while($row = $result->fetch_assoc()){
            $obj['billno']= $row['billno']; 
            $obj['date']= $row['date'];   
            $obj['total']= $row['total'];
            $obj['tender']= $row['tender'];
            $obj['paymode']= $row['name'];
            $obj['custname']= $row['custname'];
            $obj['phone']= $row['phone'];
            array_push($temp, array("service"=>$row['service'], "staff"=>$row['staff'], "mrp"=>$row['serviceamt'], "discount"=>$row['discount'], "famt"=>$row['famount']));
        }
        $obj['content'] = $temp;
    }else{
        $obj = FALSE;
    }
    return $obj;
}