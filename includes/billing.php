<?php     
    session_start();    
    if(isset($_SESSION['role'])){    
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta http-equiv="Cache-Control" content="no-store" />
    <title>Home</title>    
    <link rel="stylesheet" href="/stylesheet/bootstrap/bootstrap.min.css">
    <link rel="stylesheet" href="/stylesheet/main.css">
    <style type="text/css">
        .billtotal{
            display: inline;            
            padding: 0px 5px;                        
        }
        .billtotal input[type='text']{
            height: 30px;
            width: 45px;
            border: none;
            background-color: transparent;
            outline: none;                        
        } 
        .paymode, #paymodedate, #billinfodate{
            margin-top:5px;
        }
        #paymodehead th{
            padding:10px 10px;            
        }           
        #paymodebody td{
            padding:10px 10px;
        }
        #recentbill tr{
            border-bottom: 1px solid rgba(224,224,224,1);
        }
        #recentbill tr:hover{
            background-color: rgba(0, 0, 0, 0.3) !important;/*rgba(203,95,95,0.5) !important;*/
            color: white;   
            cursor:pointer;
        }
        .outer5{
            margin-top:40px 5px !important;            
            padding-top:40px 5px !important;
            border-top:2px solid black;
        }
        .form-inline{
            padding:5px 5px;
        }
        th{
            background-color: rgba(153, 204, 255, 1);
            color: black;
        }
        #ls th{
            text-align: right;
        }  
        #tonormal{
            font-size: 16px;
            font-weight: bold;  
        }       
        .bg-image {
  /* The image used */
  background-image: url("/resource/logo.jpg");

  /* Add the blur effect */
  filter: blur(8px);
  -webkit-filter: blur(8px);

  /* Full height */
  height: 100%; 

  /* Center and scale the image nicely */
  background-position: center;
  background-repeat: no-repeat;
  background-size: cover;
}          
    </style>
</head>
<body>    
    <!-- <div class="col-sm-2 left">            
        <img src="/resource/logo-whitebg.jpg" alt="DK Profile Logo"/>
        <div>               
            <?php 
                if((int)$_SESSION['role'] == 0){
                    echo "<a href='/admin'>Admin</a>";
                }
            ?>                 
            <a href="/rebill">Edit Bill</a>
        </div>      
    </div> !-->
    <div class="bg-image"></div>
    <nav class="navbar navbar-default">
        <div class="container-fluid">
        <a class="navbar-brand" href="#">
            <img src="/resource/logo.png" style="border-radius:30px;box-shadow:2px 2px 2px black;" width="30" height="30" class="d-inline-block align-top" alt="">            
        </a>            
            <ul class="nav navbar-nav">
                <?php 
                    if((int)$_SESSION['role'] == 0){
                        echo "<li><a href='/admin'>Admin</a></li>";
                    }
                ?>   
                <li class="active"><a href="#">Billing</a></li>                
                <li><a href="/rebill">Edit Bill</a></li>                
            </ul>
            <ul class="nav navbar-nav navbar-right">
                <li><a class="logout btn btn-danger" href="/logout">LOGOUT</a></li>                
            </ul>
        </div>
    </nav>    
    <div class=" col-sm-12 container right">                        
        <h1>Billing System</h1>
        <!-- <a class="logout btn btn-danger" href="/logout">LOGOUT</a>           !-->
        <div>
            <form class="form-inline" action="" method="">
                <div class="form-group">
                    <label for="myInput">Phone Number:</label>
                <div class="autocomplete" > <input type="text" class="form-control" id="myInput"></div>
                </div>
                <div class="form-group">
                    <label for="name">Name:</label>
                    <div class="autocomplete" ><input type="text" name="name" class="form-control" id="name"></div>
                </div>
                <div class="form-group" >
                    <label for="mail">Email-ID:</label>
                    <div class="autocomplete" ><input type="email" class="form-control" id="mail"></div>
                </div>
                <div class="form-group" >
                    <label for="paymode">Payment Mode:</label>
                    <div id="paymode_div" >                    
                    </div>
                </div>
            </form> 
            <div class="row" >
                <div class="col-sm-6" >                         
                    <h3><b>BILLING</b> <button type="button" id="btnnew" class="btn btn-primary btn-add">+</button> 
                    <span id="tonormal" ><b>Print Receipt <input type="checkbox" id="checkprint" ></b></span>
                        <button style="float:right" id="genBill" class="btn btn-success" ><b>Generate Bill</b></button>                
                    </h3>                    
                    <p class="billtotal" ><span><b>Tendered Amt : </b> <input type="text" onClick="this.select();" oninput="numberOnly(this);" onblur="setChangeAmt();" placeholder="0" class="form-inline" id="tamt"></p>
                    <p class="billtotal" ><span><b>Change : </b></span><span id="bamt">0</span></p>
                    <p class="billtotal" ><span><b>Total Amt : </b></span><span id="amt">0</span></p>
                    <div id="billinfo"></div>
                </div>
                <div class="col-sm-6">            
                    <h3><b>HISTORY</b></h3>
                    <p class="histotal hidden" ><span><b>No of times Visited : </b></span><span id="visited"></span>&nbsp;&nbsp;&nbsp;&nbsp;<span><b>Total : </b></span><span id="histotal">1000</span></p>                
                    <div id="custhistory" class="table-responsive hidden" ></div>
                </div> 
            </div>       <!--  RIGHT ROW ENDING -->   
        </div>  
        <br/>          
        <div class="form-inline" >
                <h4><b>Cash Summary</b></h4>
            <div>
                <div class="form-group">
                    <label for="from">From :</label>
                    <input type="date" id="from" class="form-control">
                </div>
                <div class="form-group">
                    <label for="to">To :</label>
                    <input type="date" class="form-control" id="to">
                </div>
                <div class="form-group">
                    <label for="submit" ></label>
                    <button onclick="getPaymodeAmt('with');" class="btn btn-success" ><b>Submit</b></button> 
                    <button onclick="getPaymodeAmt();" class="btn btn-primary" ><b>Refresh</b></button> 
                </div>
                <span>No of Customer : </span><b><span id="noofcustomer" ></span></b>
            </div>  
            <div id="paymodedate" ></div>              
            <table class="table paymode table-bordered">
                <thead id="paymodehead">
                    <tr><th>Billno</th><th>Name</th><th>Date</th><th>Service</th><th>Amount</th></tr>
                </thead>            
                <tbody id="paymodebody">                    
                </tbody>            
            </table>                    
        </div>      
        <br/>          
        <div class="" >
            <h4><b>Billing Summary</b></h4>
            <div class="form-inline" >
                <div class="form-group">
                    <label for="rfrom">From :</label>
                    <input type="date" id="rfrom" class="form-control">
                </div>
                <div class="form-group">
                    <label for="rto">To :</label>
                    <input type="date" class="form-control" id="rto">
                </div>
                <div class="form-group">
                    <label for="submit" ></label>
                    <button onclick="getRecentBill('with');" class="btn btn-success" ><b>Submit</b></button> 
                    <button onclick="getRecentBill();" class="btn btn-primary" ><b>Refresh</b></button> 
                </div> 
                <span>No of Customer : </span><b><span id="rnoofcustomer" ></span></b>               
            </div> 
            <div id="billinfodate" ></div>  
            <table class="table table-bordered recentbill">        
                <thead>
                    <tr><th>Billno</th><th>Name</th><th>Date</th><th>Service Count</th><th>PayMode</th><th>Amount</th></tr>
                </thead>            
                <tbody id="recentbill">                
                </tbody>            
            </table> 
        </div>          
    </div>        
    <div class="bg-modal">
        <div class="modal-content">
            <div class="cle">+</div>            
            <h3 class="m-head hideme" >Success</h3>            
            <p class="m-msg" >Bill Successfully Generated</p>
            <p class="m-billno">Bill Number : 20</p>
        </div>
    </div>
    <div  style="display: none;" >
        <span><b>Total Amt : </b></span><span id="famt">0</span>
        <span><b>Total Amt : </b></span><span id="eamt">0</span>
    </div>
    <div class="bg-modal" id="billdetail">
        <div class="modal-content">
            <div class="cle" style="color:black;" >+</div>            
            <h3 class="m-head hideme" >BILL INFO</h3>            
            <div>
                <table class="table table-bordered" >
                    <thead>
                        <tr><th>Customer Name</th><th>Phone</th></tr>
                    </thead>
                    <tbody id="tblcustinfo" >
                        <tr><td>Vijay</td><td>9042307071</td></tr>
                    </tbody>
                </table>
            </div>
            <div>
                <table class="table table-bordered" >
                    <thead>
                        <tr><th>BillNo</th><th>Date</th><th>PayMode</th></tr>
                    </thead>
                    <tbody id="tblbilldetail" >
                        <tr><td>4</td><td>2018-12-06 16:14:42</td><td>Cash</td></tr>
                    </tbody>
                </table>
            </div>
            <div>
                <table class="table table-bordered" >
                    <thead>
                        <tr><th>Service Name</th><th>Staff</th><th>Mrp</th><th>Discount</th><th>Amount</th></tr>
                    </thead>
                    <tbody id="tblservicedetail" >
                        <tr><td>Streaks Hair Colour</td><td>Vijay</td><td>300</td><td>0</td><td>300</td></tr>
                        <tr><td>Streaks Hair Colour</td><td>Vijay</td><td>300</td><td>0</td><td>300</td></tr>
                        <tr><td>Streaks Hair Colour</td><td>Vijay</td><td>300</td><td>0</td><td>300</td></tr>
                        <tr><td>Streaks Hair Colour</td><td>Vijay</td><td>300</td><td>0</td><td>300</td></tr>
                        <tr id="ls" ><th  colspan="4">Total</th><td>300</td></tr>
                        <tr id="ls" ><th colspan="4">Tendered</th><td>500</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <script src="/js/jquery.min.js"></script>
    <script src="/js/main.js"></script>
    <script>
        document.addEventListener("load", getAll());                            

        function showme(obj){
            billno = $($(obj).children()[0]).html();            
            if(xmlhttp.readyState === 0 || xmlhttp.readyState === 4){
                xmlhttp.open("GET","/billdetail/"+billno,true);
                xmlhttp.onreadystatechange = function(){
                //console.log("Response Code : "+this.status);
                if(this.status === 200){
                    response = xmlhttp.responseText;
                    //console.log("Resp : "+response);
                    obj = response == "" ? "" : JSON.parse(response);  
                    if(obj != ""){
                        setDetailsInModal(obj);
                    }                    
                }else{
                    //alert("Status not 200")
                }
                };
                xmlhttp.send(null);
            }
        }

        function setDetailsInModal(obj){
            $("#billdetail").css({"display":"flex"});
            $("#tblcustinfo").html("<tr><td>"+obj.custname+"</td><td>"+obj.phone+"</td></tr>");
            $("#tblbilldetail").html("<tr><td>"+obj.billno+"</td><td>"+obj.date+"</td><td>"+obj.paymode+"</td></tr>");
            $("#tblservicedetail").html("");
            (obj.content).forEach(function(obj,index,parentArray){
                $("#tblservicedetail").append("<tr><td>"+obj.service+"</td><td>"+obj.staff+"</td><td>"+obj.mrp+"</td><td>"+obj.discount+"</td><td>"+obj.famt+"</td></tr>");
            });
            $("#tblservicedetail").append("<tr id='ls' ><th  colspan='4'>Total</th><td>"+obj.total+"</td></tr>");
            $("#tblservicedetail").append("<tr id='ls' ><th  colspan='4'>Tendered</th><td>"+obj.tender+"</td></tr>");
        }

        $(".cle").click(function(){
            //document.querySelector(".bg-modal").style.display = "none";
            $(".cle").parent().parent().css({"display":"none"});
        });   
        
        $("#tamt").keydown(function(e){            
            if(e.keyCode == 13){ // if enter pressed
                setChangeAmt();                
            }            
        });
    </script>
</body>
</html>

<?php         
    }else{
        header("Location: http://".$_SERVER['SERVER_NAME']);
        exit();
    }
?>