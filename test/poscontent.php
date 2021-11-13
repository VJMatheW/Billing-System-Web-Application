<?php
echo "------------------------------------------------\n";

function addSpace($noofspace){
    $temp = "";
    for($i=0;$i<$noofspace;$i++){
        $temp .= " ";
    }
    return $temp;
}
function adjustSpaceForService($sno='0',$left,$right,$align='L'){
    /**Total Space is 48 */
    $maxSno = 2;    
    $maxR = 11;
    $maxL = 28;
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
            //echo "cap R\n";
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
        return addSpace(4).$left.addSpace(5).$right."\n";    
    }
    return $sno.") ".$left.addSpace(5).$right."\n";
}

echo adjustSpaceForService("1","Haircut","Rs.100.00");
echo adjustSpaceForService("0","Discount","Rs.0.00",'R');
echo adjustSpaceForService("0","Tendered","Rs.100.00",'R');
echo adjustSpaceForService("0","Change","Rs.0.00",'R');

function calcDiscount($amt,$discountPercent){
    $discountAmount = ((100 - (int)$discountPercent)/100)*(int)$amt;
    echo "DisAMT : ".$discountAmount;
    $discountAmount = round($discountAmount);
    $remainder = $discountAmount % 5;
    $discountAmount = $discountAmount - $remainder;
    return (int)$amt - $discountAmount;
}

echo "FINAL".calcDiscount("100","3");
$date = new DateTime(null,new DateTimeZone('Asia/Kolkata'));
echo "\n --- ".$date->format('d-m-Y H:i:s')." -- -- ".date("d/m/Y H:i:s");

