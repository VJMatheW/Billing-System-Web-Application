<?php

require "../lib/fpdf.php";
require '../components/Components.php';
error_reporting(E_ERROR | E_PARSE);

class myPDF extends FPDF{

    // override methods
    function header(){
        //$this->Image('logo.png',10,6);        
        $this->SetFont('Arial','B',20);
        $this->Cell(0,10,"DK - Profile",0,1,'C');        
        $this->SetFont('Times','B',15);
        $this->Cell(0,10,'Billing Information',0,0,'C');
        $this->Ln(10);
    }
    function footer(){
        $this->SetY(-15);
        $this->SetFont('Arial','',8);
        $this->Cell(0,10,'Page '. $this->PageNo().'/{nb}',0,0,'C');
    }

    // user defined methods
    function headerTable($result,$totalStaff,$equal,$forlaststaff){
        $this->SetFont('Times','B',12);
        $this->Cell(10,10,"SNo",1,0,"C");
        $this->Cell(25,10,"Date",1,0,"C");
        $this->Cell(20,10,"Clients",1,0,"C");
        $this->Cell(20,10,"Amount",1,0,"C"); // total 190 -until now 75
        
        $counter = 0;
        while ($row = $result->fetch_assoc()){
            if (++$counter == $totalStaff) {
                $this->Cell($forlaststaff,10,$row['name'],1,1,"C"); // for last staff
            } else {
                $this->Cell($equal,10,$row['name'],1,0,"C"); // for normal  staff
            }
        }

        //$this->Cell(40,10,"Paymode",1,1,"C");       
    }

    // table body 
    function viewTable($result,$staffAmtData,$staffIdArr,$w_otr,$w_last){
        $this->SetFont('Times','',12);
        while($r = $result->fetch_assoc()){
            $this->Cell(10,10,$r["sn"],1,0,"C");
            $this->Cell(25,10,$r["date"],1,0,"C");
            $this->Cell(20,10," ".$r["noofclients"],1,0,"C");
            $this->Cell(20,10,$r["amount"],1,0,"C");

            $counter = 0;
            $temp = count($staffIdArr);
            foreach($staffIdArr as $id){
                $amt = $staffAmtData[$id][$r['date']] ? $staffAmtData[$id][$r['date']] : 0;
                if(++$counter == $temp){
                    $this->Cell($w_last,10,$amt,1,1,"C"); // last staff
                }else{
                    $this->Cell($w_otr,10,$amt,1,0,"C");
                }
            }
        }
    }

    // table total row 
    function tableTotal($totalVals, $w_otr, $w_last){
        $this->SetFont('Times','B',12);
        $this->Cell(35,10,"Total",1,0,"C");
        $this->SetFont('Times','',12);
        $this->Cell(20,10," ".$totalVals['clients'],1,0,"C");
        $this->Cell(20,10,$totalVals['tamount'],1,0,"C");

        $staffTotal = $totalVals['staffTotal'];
        $counter = 0;
        $temp = count($staffTotal);
        //echo "otr : ".$w_otr. " Last : ".$w_last. " stfTot : ".$staffTotal; 
        foreach($staffTotal as $id => $amt){            
            if(++$counter == $temp){
                $this->Cell($w_last,10,$staffTotal[$id],1,1,"C"); // last staff
            }else{
                $this->Cell($w_otr,10,$staffTotal[$id],1,0,"C");
            }
        }
    }
}

$con = getCon();
$con2 = getCon();

$staffForHeader = $con->query("select w_id,worker_name as name from tblworker");
$staff = $con2->query("select w_id,worker_name as name from tblworker");

// calculation of width for staffs 
$totalStaff = $staff->num_rows;
$const = 190-75; // 115
$equal = round($const/$totalStaff); // 38
$temp = $equal*($totalStaff-1);
$forlaststaff = $const-$temp;

// setting up array for table body data
$staffDataArr = array();
$staffIdArr = array();
while ($row = $staff->fetch_assoc()){    
    array_push($staffIdArr,$row['w_id']);
    $temp = array();
    $query = "select date(a.date) as date,b.worker_id, sum(b.famount) as amount
    from tblbilling as a inner join tblbillinghelper as b
    on a.billno=b.billno
    where b.worker_id=".$row['w_id']."
    group by date(a.date)";
    $res = $con->query($query);
    //echo "----work id : ".$row['w_id']."-----";
    while($r = $res->fetch_assoc()){
        if($r['amount']){
            $temp[$r['date']] = $r['amount'];            
        }else{
            $temp[$r['date']] = 0;
        }        
    }

    $staffDataArr[$row['w_id']] = $temp;
}



$pdf = new myPDF();
$pdf->AliasNbPages(); // for footer page number
$pdf->AddPage('P',"A4");
$pdf->headerTable($staffForHeader,$totalStaff,$equal,$forlaststaff);

$con->query("SET @row_number = 0;");
$data = $con->query("select (@row_number:=@row_number + 1) as sn, date(a.date) as date, count(date(a.date)) as noofclients, sum(a.totalamount) as amount
from tblbilling as a inner join tblbillinghelper as b
on a.billno=b.billno
where date(a.date) between '2018-10-21' and '2018-11-21'
group by date(a.date)");
$pdf->viewTable($data,$staffDataArr,$staffIdArr,$equal,$forlaststaff);

$totalVals = array();

// for total no of clients
$data = $con->query("select count(date(date)) as clients from tblbilling where date(date) between '2018-10-21' and '2018-11-21'");
$totalVals['clients'] = $data->fetch_assoc()['clients'];

// for total amount
$data = $con->query("select sum(totalamount) as tamount from tblbilling where date(date) between '2018-10-21' and '2018-11-21'");
$totalVals['tamount'] = $data->fetch_assoc()['tamount'];

// for each staffs
$staffTotalAmt = array();
foreach($staffIdArr as $id){
    $data = $con->query("select sum(b.famount) as amount
    from tblbilling as a inner join tblbillinghelper as b
    on a.billno=b.billno
    where b.worker_id=".$id." and date(a.date) between '2018-10-21' and  '2018-11-21'");
    $t = $data->fetch_assoc()['amount'];
    $staffTotalAmt[$id] = $t ? $t : 0;
}
$totalVals['staffTotal'] = $staffTotalAmt; 
$pdf->tableTotal($totalVals,$equal,$forlaststaff);
$pdf->Output();

$con->close();
$con2->close();

?>