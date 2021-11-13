<?php

require __DIR__ . '/../lib/escpos-php/autoload.php';
use Mike42\Escpos\Printer;
use Mike42\Escpos\EscposImage;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;

try {

    //$date = date('l jS \of F Y h:i:s A');
    //$curdate = new DateTime(null,new DateTimeZone('Asia/Kolkata'));
    //$curdate = $curdate->format('d-m-Y H:i:s');
    //$logo = EscposImage::load('C:\xampp\htdocs\DKProfile-Billing\resource\logo.png', false);
    
    // Enter the share name for your USB printer here
    $connector = null;
    $connector = new WindowsPrintConnector("POS-80-Series");
    $printer = new Printer($connector);

    /* Print top logo */    
    $printer -> setJustification(Printer::JUSTIFY_CENTER);
    //$printer -> graphics($logo);    
    //$printer -> qrCode("Hello World");
    $printer -> text("Most simple example\n");
    $printer -> feed();

    /* Name of shop */    
    /*$printer -> selectPrintMode(Printer::MODE_DOUBLE_WIDTH);
    $printer -> setTextSize(4, 3);
    $printer -> text("DK PROFILE\n");
    $printer -> selectPrintMode();
    $printer -> text("No:5/7,CN Mahadevan Street,\n");
    $printer -> text("( new bus stand backside )\n");
    $printer -> text("Chengalpattu - 603001.\n");
    $printer -> text("Ph : 636-928-7790\n");
    //$printer -> feed();

    /* Receipt No texts         
    $printer -> feed();
    $printer -> setTextSize(1, 1);
    $printer -> setJustification(Printer::JUSTIFY_LEFT);
    $printer -> text("Mr ".$obj['custname']."\n");
    $printer -> text("Receipt No : ".$obj['billno']."   ".$obj['billdate']."\n" );            
    $printer -> setJustification(Printer::JUSTIFY_CENTER);    
    $printer -> text("------------------------------------------------\n");

    /** Cash Bill Heading 
    //$printer -> setUnderline(Printer::UNDERLINE_SINGLE);
    $printer -> selectPrintMode(Printer::MODE_DOUBLE_WIDTH);
    $printer -> setEmphasis(true);
    $printer -> setTextSize(2, 1);
    $printer -> text("CASH BILL\n");
    $printer -> setEmphasis(false);
    $printer -> selectPrintMode();
    $printer -> feed();
    
    /* Content of bill 
    $i = 1;
    foreach ($obj['content'] as $content){
        $printer -> text(adjustSpaceForService($i."",$content['servicename'],"Rs.".$content['amt'].".00"));
        $printer -> text(adjustSpaceForService("0","Discount","Rs.".$content['discount'].".00",'R'));
        $i = $i + 1;
    }
    $printer -> feed();
    
    /* Footer    
    $printer -> setJustification(Printer::JUSTIFY_CENTER);
    $printer -> text("------------------------------------------------\n");
    $printer -> setJustification(Printer::JUSTIFY_LEFT);
    $printer -> text(adjustSpaceForService("0","Total","Rs.".$obj['totalamt'].".00",'R'));
    $printer -> text(adjustSpaceForService("0","Tendered","Rs.".$obj['tenderamt'].".00",'R'));
    $printer -> text("Payment Method : ".$obj['paymode']."\n");
    $printer -> setJustification(Printer::JUSTIFY_CENTER);
    $printer -> text("Thank you for choosing DK Profile\n"); 
    $printer -> text($curdate."\n");               
    $printer -> feed(2);

    /* Cut the receipt and open the cash drawer */
    $printer -> cut();    
    $printer -> close();
    return TRUE;
} catch (Exception $e) {
    return FALSE;
    echo "Couldn't print to this printer: " . $e -> getMessage() . "\n";
}

//varun@everycom.in