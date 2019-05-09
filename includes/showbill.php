<?php 
require '../components/Components.php';
session_start();    

if($_SERVER["REQUEST_METHOD"] == "GET"){
    if(isset($_SESSION['role'])){
        if((int)$_SESSION['role'] >= 0){
            $bno = test_input($_GET['bno']); 
            $con = getCon();            
            $obj = prepareBill($con,$_GET['bno']);            
            if($obj){
                $content = "";$i=0;
                foreach ($obj['content'] as $arr){
                    $i++;
                    $content .= "<tr><td>$i)</td><td>".$arr['servicename']."</td><td>Rs.".$arr['amt'].".00</td></tr>
                    <tr><td></td><td class='dis' >Discount :</td><td>Rs.".$arr['discount'].".00</td></tr>";
                }
 ?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>View Bill</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="/stylesheet/bootstrap/bootstrap.min.css">
    <link rel="stylesheet" href="/stylesheet/main.css">
    <style>
        .outer{
            display: flex;
            justify-content: center;
            align-items: top !important;
            background-color: rgba(0,0,0,0.7); 
            min-height: 100vh;
        }
        .con{
            margin: 25px auto;                        
            width: 30vw;            
            position: relative;
            background-color: white;
            padding: 10px; 
            overflow:auto;
        } 
        .dis{
            text-align: right;
        } 
        .logout{
            position: absolute;
            right: 15px;
            top: 15px;
        }
        @media only screen and (max-width: 768px) {
        /* For mobile phones: */
            .con{
                width:100vw;
                margin: 0px;
            }            
        } 
    </style>    
</head>
<body>
    <div class="outer">
        <div class="con">
            <h4 class="m-head" >Cash Bill</h4>
            <button class="logout btn btn-primary" id="print" onclick="print(<?php echo $obj['billno']; ?>)" >Print</button>
            <table class="table table-bordered" >
                    <col style="width:25%">
                    <col style="width:75%">                    
                <tbody>
                    <tr><td>Name : </td><td>Mr. <?php echo $obj['custname']; ?></td></tr>
                    <tr><td>Bill No : </td><td><span id="billno"><?php echo $obj['billno']; ?></span>  &nbsp;&nbsp;&nbsp; <?php echo $obj['billdate']; ?></td></tr>                
                </tbody>                
            </table>                        
            <table class="table table-bordered" >
                    <col style="width:10%">
                    <col style="width:75%">
                    <col style="width:15%">                    
                    <div><b>Service:</b></div>
                <tbody>
                <?php echo $content; ?>
                </tbody>                
            </table>                  
            <table class="table table-bordered" >
                    <col style="width:75%">
                    <col style="width:15%">                    
                <tbody>
                    <tr><td class="dis" >Total: </td><td>Rs.<?php echo $obj['totalamt'];?>.00</td></tr>
                    <tr><td class="dis">Tendered : </td><td>Rs.<?php echo $obj['tenderamt']?>.00</td></tr>                
                </tbody>                
            </table>
        </div>        
    </div>
    <script src="/js/jquery.min.js"></script>
    <script src="/js/main.js"></script>
    <script>

        function print(billno){
            console.log(billno);
            if(xmlhttp.readyState === 0 || xmlhttp.readyState === 4){        
                xmlhttp.open("GET","/print/"+$("#billno").html(),false);                                
                xmlhttp.onreadystatechange = function(){
                //console.log("Response Code : "+this.status);
                    if(this.status === 200){
                        response = xmlhttp.responseText;
                        //console.log("Resp : "+response);
                        obj = response == "" ? "" : JSON.parse(response);;  
                        if(obj){
                            alert("Bill Printed !!!");
                        }else{
                            alert("Operation unsuccessful !!!");
                        }                                       
                    }else{
                        return "Status not 200";
                    }
                };                
                xmlhttp.send();
            }        
        }
    </script>
</body>
</html>  
    <?php    }else{ // if prepareBill returns false;
               echo "Bill infomation Could not be retrived";
            }   
            $con->close();       
        }
    }else{
        header("Location: http://".$_SERVER['SERVER_NAME']);
        exit();
    }
}
?>