<?php

require __DIR__ . '/../lib/escpos-php/autoload.php';
use Mike42\Escpos\Printer;
use Mike42\Escpos\EscposImage;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;

function posPrint(){
    try {
        //$date = date('l jS \of F Y h:i:s A');
        $curdate = new DateTime(null,new DateTimeZone('Asia/Kolkata'));
        $curdate = $curdate->format('d-m-Y h:i:s a');
        //$logo = EscposImage::load('C:\xampp\htdocs\DKProfile-Billing\includes\logo.png', false);
        
        // Enter the share name for your USB printer here
        $connector = null;
        $connector = new WindowsPrintConnector("THERMAL-Receipt-Printer");
        //$connector = new WindowsPrintConnector("USB001");
        $printer = new Printer($connector);

        /* Print top logo */
        $printer -> setJustification(Printer::JUSTIFY_CENTER);
        //$printer -> graphics($logo);    

        /* Name of shop */    
        $printer -> selectPrintMode(Printer::MODE_DOUBLE_WIDTH);
        $printer -> setTextSize(4, 3);
        $printer -> text("DK PROFILE\n");
        $printer -> selectPrintMode();
        $printer -> text("No:5/7,CN Mahadevan Street,\n");
        $printer -> text("( new bus stand backside )\n");
        $printer -> text("Chengalpattu - 603001.\n");
        $printer -> text("Ph : 636-928-7790\n");
        //$printer -> feed();

        /* Receipt No texts */        
        $printer -> feed();
        $printer -> setTextSize(1, 1);
        $printer -> setJustification(Printer::JUSTIFY_LEFT);
        $printer -> text("Mr Vijay R\n");
        $printer -> text("Receipt No : 345 03-10-2020\n" );            
        $printer -> setJustification(Printer::JUSTIFY_CENTER);    
        $printer -> text("------------------------------------------------\n");

        /** Cash Bill Heading */
        //$printer -> setUnderline(Printer::UNDERLINE_SINGLE);
        $printer -> selectPrintMode(Printer::MODE_DOUBLE_WIDTH);
        $printer -> setEmphasis(true);
        $printer -> setTextSize(2, 1);
        $printer -> text("CASH BILL\n");
        $printer -> setEmphasis(false);
        $printer -> selectPrintMode();
        $printer -> feed();
        
        /* Content of bill */
        // $i = 1;
        // foreach ($obj['content'] as $content){
            $printer -> text(adjustSpaceForService("1","Haircut","Rs.120.00"));
            $printer -> text(adjustSpaceForService("0","Discount","Rs.120.00",'R'));
            // $i = $i + 1;
        // }
        $printer -> feed();
        
        /* Footer */    
        $printer -> setJustification(Printer::JUSTIFY_CENTER);
        $printer -> text("------------------------------------------------\n");
        $printer -> setJustification(Printer::JUSTIFY_LEFT);
        $printer -> text(adjustSpaceForService("0","Total","Rs.20.00",'R'));
        $printer -> text(adjustSpaceForService("0","Tendered","Rs.50.00",'R'));
        $printer -> text("Payment Method : GPAY\n");
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
}

function adjustSpaceForService($sno='0',$left,$right,$align='L'){
    /**Total Space is 48 */
    $maxSno = 2;    
    $maxR = 11;
    $maxL = 30;
    $lenSno = strlen($sno);
    $lenL = strlen($left);
    $lenR = strlen($right);

    /** For serial No */
    if($lenSno < $maxSno){
        $sno = addSpace($maxSno-$lenSno).$sno;
    }
    /** Fill blank space to left side */
    if($lenL > $maxL){ 
        $left = substr($left,0,$maxL);        
    }else if($lenL < $maxL){        
        if($align == 'R'){
            $left = addSpace($maxL -$lenL) . $left;    
        }else{
            $left = $left . addSpace($maxL -$lenL);
        }        
    }

    /** Fill blank space to Right side */
    if($lenR < $maxR){
        $right = addSpace($maxR-$lenR).$right;
    }

    if($sno== "0"){
        return addSpace(4).$left.addSpace(3).$right."\n";    
    }
    return $sno.") ".$left.addSpace(3).$right."\n";
}

function addSpace($noofspace){
    $temp = "";
    for($i=0;$i<$noofspace;$i++){
        $temp .= " ";
    }
    return $temp;
}

posPrint();