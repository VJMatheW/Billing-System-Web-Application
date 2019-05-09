<?php

require "../lib/fpdf.php";
require '../components/Components.php';
error_reporting(E_ERROR | E_PARSE);
session_start();

define('FPDF_FONTPATH',dirname(getcwd()).'/lib/font');

class myPDF extends FPDF{

    var $header='test';
    function __construct($header) {
        parent::__construct('P', 'mm', 'A4');	
        $this->header = $header;	        	
    }

    // override methods
    function header(){                
    }
    function footer(){
        $this->SetY(-15);
        $this->SetFont('Arial','',8);
        $this->Cell(0,10,'Page '. $this->PageNo().'/{nb}',0,0,'C');
    }

    // user defined methods
    function setLogoandTitle(){
        $this->Image(__DIR__.'/../resource/logo.png',83,-2,50,50);       
        $this->Ln(25);                       
        $this->SetFont('Arial','B',15);
        $this->Cell(0,10,$this->header,0,0,'C');                
        $this->Ln(7);
    }

    function setDate($from,$to){
        $this->SetFont('Times','',13); 
        $this->Cell(0,10,$from."  -  ".$to,0,1,'C');
        $this->Ln(4);
    }
    
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

// user defined function outside PDF class
function arrangeDate($date){
    $t = explode('-',$date);
    return $t[2].'/'.$t[1].'/'.$t[0];
}

function tableHeadForStaffSummary($pdf){
    $pdf->SetFont('Times','B',12);
    $pdf->Cell(10,10,"Sno",1,0,"C");
    $pdf->Cell(15,10,"Bill No",1,0,"C");
    $pdf->Cell(22,10,"Date",1,0,"C");
    $pdf->Cell(45,10,"Name",1,0,"C");
    $pdf->Cell(26,10,"Phone",1,0,"C");
    $pdf->Cell(37,10,"Service",1,0,"C");
    $pdf->Cell(12,10,"Mrp",1,0,"C");
    $pdf->Cell(10,10,"%",1,0,"C");
    $pdf->Cell(13,10,"Amt",1,1,"C");    
}

function tableBodyForStaffSummary($pdf, $result){
    $pdf->SetFont('Times','',12);
    $sno = 1;
    while($row = $result->fetch_assoc()){
        $pdf->Cell(10,10,$sno,1,0,"C");
        $pdf->Cell(15,10,$row['billno'],1,0,"C");
        $pdf->Cell(22,10,$row['date'],1,0,"C");
        $pdf->Cell(45,10," ".ucfirst($row['name']),1,0,"L");
        $pdf->Cell(26,10,$row['phone'],1,0,"C");
        $pdf->Cell(37,10," ".ucfirst(strtolower(substr($row['service_name'],0,17))),1,0,"L");
        $pdf->Cell(12,10,$row['amount']." ",1,0,"R");
        $pdf->Cell(10,10,$row['discount']." ",1,0,"R");
        $pdf->Cell(13,10,$row['famount']." ",1,1,"R");
        $sno++;
    }
}

function setPaymodeTable($pdf, $obj){
    $obj = $obj['paymode'];
    $length = count($obj);    
    $equal = round(190/$length);
    $temp = $equal*($length-1);
    $forlaststaff = 190-$temp;        
    $counter = 0;
    $pdf->SetFont('Times','B',12); // heading starts
    $pdf->Ln(4);
    foreach($obj as $k => $v){
        if (++$counter == $length) {
            $pdf->Cell($forlaststaff,10,$k,1,1,"C"); // for last paymode            
        } else {
            $pdf->Cell($equal,10,$k,1,0,"C"); // for normal  paymode
        }
    }

    $counter = 0;
    $pdf->SetFont('Times','',12); // heading starts
    foreach($obj as $k => $v){
        if (++$counter == $length) {
            $pdf->Cell($forlaststaff,10,$v,1,1,"C"); // for last paymode
        } else {
            $pdf->Cell($equal,10,$v,1,0,"C"); // for normal  paymode
        }
    }
}


// for MONTHLY SUMMARY
if($_SERVER["REQUEST_METHOD"] == "POST"){

    $from = test_input($_POST['from']);
    $to = test_input($_POST['to']);
    
    $con = getCon();
    $con2 = getCon();

    $staffForHeader = $con->query("select w_id,worker_name as name from tblworker where active=1");
    $staff = $con2->query("select w_id,worker_name as name from tblworker where active=1");

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
        where b.worker_id=".$row['w_id']." and date(a.date) between '".$from."' and  '".$to."'
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

    $pdf = new myPDF('MONTHLY BILLING SUMMARY');
    $pdf->AliasNbPages(); // for footer page number
    $pdf->AddPage('P',"A4");
    $pdf->setLogoandTitle();
    $pdf->setDate(arrangeDate($from),arrangeDate($to));
    $pdf->headerTable($staffForHeader,$totalStaff,$equal,$forlaststaff);

    $con->query("SET @row_number = 0;");
    $data = $con->query("select (@row_number:=@row_number + 1) as sn, date(a.date) as date, count(date(a.date)) as noofclients, sum(a.totalamount) as amount
    from tblbilling as a
    where date(a.date) between '".$from." 00:00:00' and '".$to." 00:00:00'
    group by date(a.date)");
    $pdf->viewTable($data,$staffDataArr,$staffIdArr,$equal,$forlaststaff);

    $totalVals = array();

    // for total no of clients
    $data = $con->query("select count(date(date)) as clients from tblbilling where date(date) between '".$from."' and '".$to."'");
    $totalVals['clients'] = $data->fetch_assoc()['clients'];

    // for total amount
    $data = $con->query("select sum(totalamount) as tamount from tblbilling where date(date) between '".$from."' and '".$to."'");
    $totalVals['tamount'] = $data->fetch_assoc()['tamount'];

    // for each staffs
    $staffTotalAmt = array();
    foreach($staffIdArr as $id){
        $data = $con->query("select sum(b.famount) as amount
        from tblbilling as a inner join tblbillinghelper as b
        on a.billno=b.billno
        where b.worker_id=".$id." and date(a.date) between '".$from."' and  '".$to."'");
        $t = $data->fetch_assoc()['amount'];
        $staffTotalAmt[$id] = $t ? $t : 0;
    }
    $totalVals['staffTotal'] = $staffTotalAmt; 
    $pdf->tableTotal($totalVals,$equal,$forlaststaff);
    $obj = getPaymodeAmt($con,$from,$to);
    setPaymodeTable($pdf, $obj);
    $pdf->Output();

    $con->close();
    $con2->close();
}

// for INDIVIDUAL STAFF SERVICE SUMMARY
if($_SERVER["REQUEST_METHOD"] == "GET"){
    $from = test_input($_GET['from']);
    $to = test_input($_GET['to']);    
    $arr = explode(',',$_GET['wid']); // worker_id = 0, worker_name = 1
    $wid = test_input($arr[0]);
    //echo $from." / ".$to." / ".$wid;
    $query = "select b.billno,date(b.date) as date,e.name,e.phone,c.service_name,a.amount,a.discount,a.famount
    from tblbillinghelper as a inner join tblbilling as b
    on a.billno=b.billno
    inner join tblservice as c
    on a.service_id=c.s_id
    inner join tblworker as d
    on a.worker_id=d.w_id
    inner join tblcustinfo as e
    on b.cust_id=e.cust_id
    where date(b.date) between '".$from."' and '".$to."' and a.worker_id=".$wid."
    order by b.date asc;";

    $result = executeSelect($query);
    if($result){       
        $pdf = new myPDF('STAFF SERVICE SUMMARY');
        $pdf->AliasNbPages(); // for footer page number
        $pdf->AddPage('P',"A4");
        $pdf->setLogoandTitle();
        $pdf->SetFont('Times','',12);
        $pdf->cell(0,10,"STAFF NAME : ".ucfirst($arr[1]),0,1,"C");                
        $pdf->setDate(arrangeDate($from),arrangeDate($to));
        tableHeadForStaffSummary($pdf);
        tableBodyForStaffSummary($pdf, $result);
        $pdf->Output();
    }else{
        echo "<h1>No result</h1>";
    }
}
?>