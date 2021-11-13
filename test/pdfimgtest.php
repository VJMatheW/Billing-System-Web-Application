<?php

require "../lib/fpdf.php";
require '../components/Components.php';
error_reporting(E_ERROR | E_PARSE);

class myPDF extends FPDF{

    // override methods
    function header(){
        //$this->Image('logo.png',10,6);        
        $this->Image('logo.png',83,-2,50,50);
        $this->SetFont('Arial','B',20);
        //$this->Cell(0,10,"DK - Profile",0,1,'C');        
        $this->SetFont('Times','B',15);
        $this->Ln(25);
        $this->Cell(0,10,'MONTHLY BILLING SUMMARY',0,0,'C');
        $this->Ln(10);
        //$this->Image('logo.png',10,10,100,100);
    }
    function footer(){
        $this->SetY(-15);
        $this->SetFont('Arial','',8);
        //$this->Cell(0,10,'Page '. $this->PageNo().'/{nb}',0,0,'C');
    }   
}




$pdf = new myPDF();
$pdf->AddPage('P',"A4");
$pdf->Output();
?>