<?php

require '../components/Components.php';
error_reporting(E_ERROR | E_PARSE);

$con = getCon();

$result = $con->query("select * from tblworker");
$totalStaff = $result->num_rows;
$const = 190-75; // 115
$equal = round($const/$totalStaff); // 38
$temp = $equal*($totalStaff-1);
$forlaststaff = $const-$temp;
$counter = 0;

echo $totalStaff;
echo "-".$const;
echo "-".$equal;
echo "-".$temp;
echo "-".$forlaststaff;

$staffDataArr = array();

while ($row = $result->fetch_assoc()){    
    $temp = array();

    $query = "select date(a.date) as date,b.worker_id, sum(b.famount) as amount
    from tblbilling as a inner join tblbillinghelper as b
    on a.billno=b.billno
    where b.worker_id=".$row['w_id']."
    group by date(a.date)";
    $res = $con->query($query);
    echo "----work id : ".$row['w_id']."-----";
    while($r = $res->fetch_assoc()){
        if($r['amount']){
            $temp[$r['date']] = $r['amount'];            
        }else{
            $temp[$r['date']] = 0;
        }        
    }

    $staffDataArr[$row['w_id']] = $temp;
}

$con->close();

echo "Staff 1 ". $staffDataArr['1']['2018-10-21'];

$var = "1";
$t = array("1"=>"vijay");
if($t[$var]){
    //echo "-".$t[$var];
}else{
    echo "-index not present";
}




?>