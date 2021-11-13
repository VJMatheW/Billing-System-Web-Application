<?php

require "../lib/fpdf.php";
require '../components/Components.php';

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
    function headerTable(){
        $this->SetFont('Times','B',12);
        $this->Cell(10,10,"SNo",1,0,"C");
        $this->Cell(40,10,"Date",1,0,"C");
        $this->Cell(60,10,"Name",1,0,"C");
        $this->Cell(40,10,"Amount",1,0,"C");
        $this->Cell(40,10,"Paymode",1,1,"C");       
    }

    function viewTable($result){
        $this->SetFont('Times','',12);
        while($r = $result->fetch_assoc()){
            $this->Cell(10,10,$r["sn"],1,0,"C");
            $this->Cell(40,10,$r["date"],1,0,"C");
            $this->Cell(60,10," ".$r["name"],1,0,"L");
            $this->Cell(40,10,$r["totalamount"],1,0,"C");
            $this->Cell(40,10,$r["paymode"],1,0,"C");
            $this->Ln();
        }
    }
}

$pdf = new myPDF();
$pdf->AliasNbPages(); // for footer page number
$pdf->AddPage('P',"A4");
$pdf->headerTable();
$con = getCon();
$con->query("SET @row_number = 0;");
$result = $con->query("select (@row_number:=@row_number + 1) as sn, date(a.date)as date, b.name,a.totalamount,c.name as paymode
from tblbilling as a inner join tblcustinfo as b 
on a.cust_id=b.cust_id
inner join tblpaymode as c
on a.paymode=c.id");
$pdf->viewTable($result);
$pdf->Output();


?>