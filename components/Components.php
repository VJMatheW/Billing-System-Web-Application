<?php

require 'pos.php';

$dbHost = "localhost";
$dbUserName = "root";
$dbPassword = "oracle";
$dbName = "dkprofile";

function getCon(){
    global $dbHost,$dbUserName,$dbPassword,$dbName;
    $conn = new mysqli($dbHost, $dbUserName, $dbPassword, $dbName);
    if ($conn->connect_error) {
        return "Not Connected successfully";        
    }     
    return $conn;
}

function executeSelect($selectQuery){    
    $con = getCon();
    if(gettype($con) == "object"){
        $result = $con->query($selectQuery);
        if($result->num_rows > 0){
            $con->close();
            return $result;
        }else{
            $con->close();
            return FALSE;
        }   
    }else{
        echo "Mysql Connection Failed";
    }     
}

function executeInsert($insertQuery){
    $con = getCon();
    if(gettype($con) == "object"){
        //echo $insertQuery;
        if($con->query($insertQuery) === TRUE){
            $con->close();
            return TRUE;                
        }else{
            $con->close();
            return $con->error;
        }   
    }else{
        echo "MySql Connection Failed";
    }     
}

function test_input($data) {
    //$data = mysql_real_escape_string($data);
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    if($data == ""){
        $data = "NA";
    }
    return $data;
}

function startsWith($string, $query){
    return substr($string, 0, strlen($query)) === $query;
}

function prepareBill($con,$billno){
    $query = "select  a.billno,b.date,b.totalamount,b.tenderamount,c.name,e.service_name,a.amount,a.discount ,d.name as pay
    from tblbillinghelper as a inner join tblbilling as b
    on a.billno=b.billno
    inner join tblcustinfo as c 
    on b.cust_id=c.cust_id
    inner join tblpaymode as d
    on b.paymode=d.id
    inner join tblservice as e
    on a.service_id=e.s_id
    where a.billno=$billno";

    $obj = array();      

    $result = $con->query($query);
    if($result->num_rows > 0){
        $temp = array();
        while($row = $result->fetch_assoc()){
            $obj['billno'] = $row['billno'];            
            $obj['billdate'] = $row['date'];
            $obj['totalamt'] = $row['totalamount'];
            $obj['tenderamt'] = $row['tenderamount'];
            $obj['custname'] = $row['name'];
            $obj['paymode'] = $row['pay'];
            $s_name = $row['service_name'];
            $amt = $row['amount'];
            //$dpercent = $row['discount'];
            //$discount = $dpercent =="0"?"0":calcDiscount($amt,$dpercent);
            $discount = $row['discount'];
            array_push($temp,array("servicename"=>$s_name,"amt"=>$amt,"discount"=>$discount));
        }
        $obj['content'] = $temp;        
    }else{
        $obj = FALSE;
    }
    return $obj;
}

function calcDiscount($amt,$discountPercent){
    //$discountAmount = ((100 - (int)$discountPercent)/100)*(int)$amt;
    //$discountAmount = round($discountAmount);
    //$remainder = $discountAmount % 5;
    //$discountAmount = $discountAmount - $remainder;
    //return (int)$amt - $discountAmount;
    return (int)$amt - $discountPercent;
}

function numberOnly($str){
    return preg_replace( '/[^0-9]/', '', $str);
}

function getPaymodeAmt($con,$from, $to){
    $paymodeid = $con->query("select id from tblpaymode");
    $temp = array(); 
    $totalcustomer = array();
    $totalnoofcustomer = 0;   
    while($row = $paymodeid->fetch_assoc()){
        $id = $row['id'];
        $result = $con->query("select count(billno) as noofcustomer,name, sum(totalamount) as amt
        from tblbilling right outer join tblpaymode
        on tblbilling.paymode = tblpaymode.id
        where date(date) between '$from' and '$to' and 
        tblbilling.paymode=$id");        
        $row = $result->fetch_assoc();
        $paymode = $row['name'];
        $amt = $row['amt']== null ? 0 : $row['amt'] ;
        $totalnoofcustomer += (int)$row['noofcustomer'];
        $temp[$paymode]=$amt;
    }    
    return array("paymode"=>$temp, "noofcustomer"=>$totalnoofcustomer);
}

?>