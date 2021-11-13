<?php

require '../components/Components.php';
header('Content-Type: application/json; charset=utf-8');
error_reporting(E_ERROR | E_PARSE);

// for get the phone numbers
if($_SERVER["REQUEST_METHOD"] == "GET"){
    $out = array();
    $number = array();
    $service = array();
    $worker = array();
    $paymode = array();
    $rate = array();

    $con = getCon();

    //echo $_SERVER['REQUEST_URI'];
    if($_SERVER['REQUEST_URI'] == "/all"){
        $number = array();
        
        // fetching all the mobile numbers
        $result = $con->query("select phone from tblcustinfo");
        if($result->num_rows > 0 ){        
            while($row = $result->fetch_assoc()){  
                array_push($number,$row['phone']);
            }            
        }else{
            array_push($number);
        }

        // fetching all the service names        
        $result = $con->query("select * from tblservice");
        if($result->num_rows > 0 ){        
            while($row = $result->fetch_assoc()){  
                array_push($service,array("id"=>$row['s_id'],"name"=>$row['service_name']));
                array_push($rate,$row['rate']);
            }            
        }else{
            array_push($service,FALSE); //
        }

        // fetching all worker names
        $result = $con->query("select * from tblworker where active=1");
        if($result->num_rows > 0 ){        
            while($row = $result->fetch_assoc()){  
                array_push($worker,array("id"=>$row['w_id'],"name"=>$row['worker_name']));
            }            
        }else{
            array_push($worker,FALSE); //
        }

        // fetching all payment mode
        $result = $con->query("select * from tblpaymode");
        if($result->num_rows > 0 ){        
            while($row = $result->fetch_assoc()){  
                array_push($paymode,array("id"=>$row['id'],"name"=>$row['name']));
            }            
        }else{
            array_push($paymode,FALSE); //
        }
        
        $out = array("number"=>$number, "service"=>$service, "s_rate"=>$rate, "worker"=>$worker, "paymode"=>$paymode);        
    }elseif(startsWith($_SERVER['REQUEST_URI'],"/info")){        
        // get info about the given number

        $number = test_input($_GET['phoneno']);
        //echo $number."   select * from tblcustinfo where phone='". $number ."'";
        $result = $con->query("select * from tblcustinfo where phone='". $number ."'");
        if($result->num_rows > 0 ){
            //echo "greater than zero";
            $row = $result->fetch_assoc();
            $number = array("status"=>TRUE,"name"=>$row['name'],"mail"=>$row['mail'],"custid"=>$row['cust_id']);
        }else{
            $number = FALSE;
        }        
        array_push($out, $number);
    }elseif(startsWith($_SERVER['REQUEST_URI'], "/history")){
        # history of particular customer id
        $cust_id = test_input($_GET['custid']);
        $result = $con->query("select date(a.date)as date,c.service_name,d.worker_name,a.totalamount as amt
        from tblbilling as a inner join tblbillinghelper as b 
        on a.billno=b.billno
        inner join tblservice as c 
        on b.service_id=c.s_id
        inner join tblworker as d
        on b.worker_id=d.w_id
        where a.cust_id=".$cust_id."
        group by b.billno
        order by a.date desc limit 5");
        $temp = array();
        if($result->num_rows > 0){            
            while($row = $result->fetch_assoc()){
                array_push($temp, array("date"=>$row['date'],"s_name"=>$row['service_name'],"w_name"=>$row['worker_name'],"amt"=>$row['amt']));
            }
            $result = $con->query("select sum(totalamount) as total, count(*) as tvisit from tblbilling where cust_id=".$cust_id);
            $row = $result->fetch_assoc();
            $out = array("total"=>$row['total'],"visited"=>$row['tvisit'], "history"=>$temp);
        }else{
            array_push($out, FALSE);
        }
    }          
    //$out = array('9042307071','9894228324');
    echo json_encode($out);
    $con->close();
}

if($_SERVER["REQUEST_METHOD"] == "POST"){
    // bill printing
    echo "I am working POST";
}



?>