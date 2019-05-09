<?php     
    session_start();    
    if(isset($_SESSION['role'])){    
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Billing</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="/stylesheet/bootstrap/bootstrap.min.css">
    <link rel="stylesheet" href="/stylesheet/main.css">
    <style type="text/css">
        .in{
            padding: 5px 7px;
            text-align: center;
            background-color: rgba(23, 24, 24, .25);
            height: 45px;            
        }
        #exisitngbill{
            position: relative;
        }
        .screen{
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            height: 100%;
            background-color: red;
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
            <a href="/billing">Billing</a>                    
        </div>        
    </div> !-->
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
                <li><a href="/billing">Billing</a></li>                   
                <li class="active" ><a href="/rebill">Edit Bill</a></li>                
            </ul>
            <ul class="nav navbar-nav navbar-right">
                <li><a class="logout btn btn-danger" href="/logout">LOGOUT</a></li>                
            </ul>
        </div>
    </nav>    
    <div class=" col-sm-12 container right">                        
        <h1>Billing Info </h1>
        <!-- <a class="logout btn btn-danger" href="/logout">LOGOUT</a>      !-->
        <div class="content">            
            <form action="/viewbill" method="GET" class="form-inline" target="_blank">                              
                <h3>View & Reprint Bill</h3>
                <div class="form-group">
                    <label for="s_bno">Bill No :</label>
                    <input type="text" oninput="numberOnly(this);" name="bno" required class="form-control">                                           
                </div>                 
                <div class="form-group">
                    <label for="submit" ></label>
                    <input type="submit" class="btn btn-success" value="View">
                </div>
            </form>
        </div>  
        <div class="content">            
                <div style="display: flex;">                              
                    <h3>Edit Bill</h3>
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label for="bno">Bill No :</label>
                            <input type="text" oninput="numberOnly(this);" onblur="checkbillexists();" id="bno" name="bno" required class="form-control">                                           
                        </div>
                        <div class="form-group">
                            <label for="s_bno">Paymode :</label>
                            <div id="paymode_div"></div>
                        </div>
                        <div class="form-group">
                            <label for="s_bno">Tender Amount :</label>
                            <input type="text" oninput="numberOnly(this);" id="tamt" required class="form-control">                                           
                        </div>
                    </div>
                    <div class="col-sm-9">
                        <div>
                            <button id="btnnew" class="btn btn-primary btn-add">+</button>
                            <button class="btn btn-success" onclick="updateBill();" >Update Bill</button>
                            <span><b>Change : </b></span><span id="bamt">0</span>
                            <span><b>Total Amt : </b></span><span id="famt">0</span>
                            <span><b>New TotalAmt : </b></span><span id="amt">0</span>
                            <span><b>Existing Amt : </b></span><span id="eamt">0</span>
                        </div>
                        <div id="billinfo">
                            <!--  Dynamically generated boxes -->
                        </div>                      
                    </div>                                    
                </div>
            </div>      
    </div>
    <div class="bg-modal">
        <div class="modal-content">
            <div class="cle">+</div>            
            <h3 class="m-head hideme" ></h3>            
            <p class="m-msg" ></p>
            <p class="m-billno"></p>
        </div>
    </div>

    <script src="/js/jquery.min.js"></script>
    <script src="/js/main.js"></script>
    <script>
        var paymode_def = 0;

        document.addEventListener("load", getAll()); 
        $("#tamt").keydown(function(e){            
            if(e.keyCode == 13){ // if enter pressed
                setChangeAmt();        
            }            
        });        
        $("#tamt").blur(function(e){
            setChangeAmt();
        });

        function setChangeAmt(){
            tamt = parseInt($("#tamt").val());
            amt = parseInt($("#famt").html());
            $("#bamt").html(tamt - amt);                
        }

        function checkbillexists(){
            billno = $("#bno").val(); 
            console.log('bill no : '+billno);           
            if(billno.length > 0){
                if(xmlhttp.readyState === 0 || xmlhttp.readyState === 4){        
                    xmlhttp.open("GET","/checkbillexists/"+billno,true);
                    xmlhttp.onreadystatechange = function(){
                    //console.log("Response Code : "+this.status);
                    if(this.status === 200 && this.readyState == 4){
                        response = xmlhttp.responseText;
                        //console.log("Resp : "+response);
                        if(!response == ""){ // response not empty
                            obj = JSON.parse(response);  
                            if(obj.status){
                                // means the bill exists
                                $("#eamt").html(obj.totalamt);
                                paymode_def = obj.paymode;
                                console.log("current Paymode : "+paymode_def);
                                $("#paymode").val(obj.paymode);

                            }else{    
                                if($("#bno").val() != ""){
                                    alert("The entered bill number does not exists !!!");
                                    $("#bno").val('');
                                }                                                            
                            }  
                        }                                                
                    }else{
                        //alert("Status not 200")
                    }
                    };
                    xmlhttp.send(null);
                }
            }else{
                $("#eamt").html("");
                $("#paymode").val("");
            }
        }
        
        function updateBill(){
            var final = [];
            var finaal = {};            
            var outer = $("#billinfo").children();
            if(outer.length > 0){                 
                $.each($("#billinfo").children(),function(i,object){ // class : row
                    temp = {};
                    //console.log("ROW "+i+"-"+$(object).children().length);
                    $.each($(object).children(), function(i,obj){ // row > div (2) 
                    //console.log("ROW > div "+i+"-"+$(obj).children().length);
                    $.each($(obj).children(),function(i,obj){ // row > div (2) > div(4 or 3)
                        //console.log($(obj).children().length);
                        //console.log($(obj).children().attr("name")+":"+$(obj).children().val());
                        temp[$(obj).children().attr("name")] = $(obj).children().val();
                    });      
                    });
                    final.push(temp);
                });
                finaal['paymodechange'] = false;
                finaal['billno'] = $("#bno").val();
                finaal['totalamt'] = document.getElementById("famt").innerHTML;
                //console.log(document.getElementById("famt").innerHTML);
                finaal['paymode'] = $("#paymode").val();
                finaal['tenderamt'] = $("#tamt").val();
                finaal['content'] = final;
                console.log(finaal);
                if(xmlhttp.readyState === 0 || xmlhttp.readyState === 4){        
                    xmlhttp.open("POST","/editbill",false);
                    xmlhttp.setRequestHeader("Content-Type", "application/json");
                    xmlhttp.onreadystatechange = function(){      
                        if(this.status === 200){
                            response = xmlhttp.responseText;
                            console.log(response);
                            obj = JSON.parse(response); 
                            console.log(obj.status+"------"+obj.msg);                                   
                            if(obj.status){ // success factor
                                document.querySelector(".bg-modal").style.display = "flex"; // make modal visible
                                $(".m-head").attr("class","m-head hidden"); // hide modal-head
                                $(".m-msg").html(obj.msg); // append modal-msg
                                $(".m-billno").attr("class","m-billno hidden").html("Bill Number : "); // append modal-billno
                                clearAll();
                            }else{
                                document.querySelector(".bg-modal").style.display = "flex"; // make modal visible
                                $(".m-head").attr("class","m-head visible").html("Alert !!!");
                                $(".m-msg").html(obj.msg);
                                $(".m-billno").attr("class","m-billno hidden");
                            }       
                        }else{
                            //alert("Status not 200");
                        }
                    };
                    xmlhttp.send(JSON.stringify(finaal));
                }
            }else{
                if(paymode_def != $("#paymode").val()){
                    console.log("paymode changed");
                    finaal['content'] = final;
                    finaal['billno'] = $("#bno").val();
                    finaal['paymode'] = $("#paymode").val();
                    finaal['paymodechange'] = true;
                    if(xmlhttp.readyState === 0 || xmlhttp.readyState === 4){        
                        xmlhttp.open("POST","/editbill",true);
                        xmlhttp.setRequestHeader("Content-Type", "application/json");
                        xmlhttp.onreadystatechange = function(){      
                            if(this.status === 200 && this.readyState === 4){
                                response = xmlhttp.responseText;
                                console.log(response);
                                obj = JSON.parse(response); 
                                console.log(obj.status+"------"+obj.msg);                                   
                                if(obj.status){ // success factor
                                    document.querySelector(".bg-modal").style.display = "flex"; // make modal visible
                                    $(".m-head").attr("class","m-head hidden"); // hide modal-head
                                    $(".m-msg").html(obj.msg); // append modal-msg
                                    $(".m-billno").attr("class","m-billno hidden").html("Bill Number : "); // append modal-billno
                                    clearAll();
                                }else{
                                    document.querySelector(".bg-modal").style.display = "flex"; // make modal visible
                                    $(".m-head").attr("class","m-head visible").html("Alert !!!");
                                    $(".m-msg").html(obj.msg);
                                    $(".m-billno").attr("class","m-billno hidden");
                                }       
                            }else{
                                //alert("Status not 200");
                            }
                        };
                        xmlhttp.send(JSON.stringify(finaal));
                    }
                }else{
                    console.log("Nothing to update");
                    alert("Nothing to update !!");
                }                
            }
        }

        $(".cle").click(function(){
            document.querySelector(".bg-modal").style.display = "none";
            location.reload(true);
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