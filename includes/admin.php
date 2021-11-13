<?php 
require '../components/Components.php';
    session_start();    
    if(isset($_SESSION['role'])){
        if((int)$_SESSION['role'] == 0){
            $selectWorker =""; 
            $servicetbody = "";
            $result = executeSelect('select * from tblworker where active=1');
            if($result){
                $selectWorker .= "<option value=''>Select Staff</option>";
                while($row = $result->fetch_assoc()){                      
                    $selectWorker .= "<option value='".$row['w_id'].",".$row['worker_name']."'>".$row['worker_name']."</option>";
                }
            }
            $result = executeSelect('select * from tblservice');
            if($result){
                while($row = $result->fetch_assoc()){
                    $servicetbody .= "<tr><td>".$row['service_name']."</td><td>".$row['rate']."</td></tr>";
                }
            }
            $result = executeSelect('select * from tblworker');
            if($result){
                while($row = $result->fetch_assoc()){
                    $servicetbody .= "<tr><td>".$row['worker_name']."</td><td>".$row['active']."</td></tr>";
                }
            }
            ?> 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta http-equiv="Cache-Control" content="no-store" />
    <title>Admin</title>
    <link rel="stylesheet" href="/stylesheet/bootstrap/bootstrap.min.css">
    <link rel="stylesheet" href="/stylesheet/main.css">
    <style type="text/css">
              
    </style>
</head>
<body>
    <!-- <div class="col-sm-2 left">
        <img src="/resource/logo-whitebg.jpg" alt="DK Profile Logo"/>      
        <div>
            <a href="/billing">Billing</a>  
            <a href="/rebill">Edit Bill</a>                    
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
                        echo "<li class='active' ><a href='/admin'>Admin</a></li>";
                    }
                ?>   
                <li><a href="/billing">Billing</a></li>                
                <li><a href="/rebill">Edit Bill</a></li>                
            </ul>
            <ul class="nav navbar-nav navbar-right">
                <li><a class="logout btn btn-danger" href="/logout">LOGOUT</a></li>                
            </ul>
        </div>
    </nav>    
    <div class=" col-sm-12 container right">                        
        <h1>Admin Centre</h1>
        <!-- <a class="logout btn btn-danger" href="/logout">LOGOUT</a>        !-->
        <div class="content">            
            <form action="/summary" method="POST" class="form-inline" target="_blank">                              
                <h3>Monthly Summary</h3>
                <div class="form-group">
                    <label for="from">From :</label>
                    <input type="date" id="from" name="from" class="form-control">
                </div>
                <div class="form-group">
                    <label for="to">To :</label>
                    <input type="date" class="form-control" name="to" id="to">
                </div>                 
                <div class="form-group">
                    <label for="submit" ></label>
                    <input type="submit" class="btn btn-success" value="Generate">
                </div>
            </form>
        </div>
        <div class="content">            <!-- STAFF Service summary -->
            <form action="/summary" method="GET" class="form-inline" target="_blank">                              
                <h3>STAFF Service Summary</h3>
                <div class="form-group sid">
                    <label for="from">Staff :</label>
                    <select class="form-control" name="wid">
                        <?php 
                            echo $selectWorker;
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="from"> From :</label>
                    <input type="date" id="from" name="from" class="form-control">
                </div>
                <div class="form-group">
                    <label for="to"> To :</label>
                    <input type="date" class="form-control" name="to" id="to">
                </div>                 
                <div class="form-group">
                    <label for="submit" ></label>
                    <input type="submit" class="btn btn-success" value="Generate">
                </div>
            </form>
        </div>
        <div class="content">            <!-- STAFF summary by date -->         
            <form action="/summarybydate" method="GET" class="form-inline" target="_blank">                              
                <h3>STAFF Summary By DATE </h3>
                <div class="form-group sid">
                    <label for="from">Staff :</label>
                    <select class="form-control" name="wid">
                        <?php 
                            echo $selectWorker;
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="from"> From :</label>
                    <input type="date" id="from" name="from" class="form-control">
                </div>
                <div class="form-group">
                    <label for="to"> To :</label>
                    <input type="date" class="form-control" name="to" id="to">
                </div>                 
                <div class="form-group">
                    <label for="submit" ></label>
                    <input type="submit" class="btn btn-success" value="Generate">
                </div>
            </form>
        </div>       
        <div class="content">  <!--    FOR SERVICE LIST        -->
            <div>                              
                <h3>Add / Update Service</h3>
                <div class="form-group">
                    <label for="from">Service ID :</label>
                    <input type="text" oninput="numberOnly(this);" id="sid" name="sid" class="form-control">                                           
                </div>
                <div class="form-group">
                    <label for="from"> Service Name :</label>
                    <input type="text" id="sname" name="sname" class="form-control">
                </div>
                <div class="form-group">
                    <label for="to"> Rate :</label>
                    <input type="number" class="form-control" name="rate" id="rate">
                </div>                 
                <div class="form-group">
                    <label for="submit" ></label>
                    <button class="btn btn-success" id="sbtn" >Add / Update</button>
                    <a href="/list/service" class="btn btn-primary" target = "_blank" >Show All Service</a>
                </div>
            </div>
        </div>
        <div class="content">  <!--    FOR STAFF LIST        -->
            <div>                              
                <h3>Add / Update Staff</h3>
                <div class="form-group">
                    <label for="from">Staff ID :</label>
                    <input type="text" oninput="numberOnly(this);" id="wid" name="wid" class="form-control">                                           
                </div>
                <div class="form-group">
                    <label for="from"> Staff Name :</label>
                    <input type="text" id="wname" name="wname" class="form-control">
                </div>
                <div class="form-group">
                    <label for="to"> Status :</label>
                    <select class="form-control" id="status" name="status">
                        <option value="">Select Status</option>
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                     </select>
                </div>                 
                <div class="form-group">
                    <label for="submit" ></label>
                    <button class="btn btn-success" id="wbtn" >Add / Update</button>
                    <a href="/list/staff" class="btn btn-primary" target = "_blank" >Show All Staff</a>
                </div>
            </div>
        </div>           
    </div> 
    
    <script src="/js/jquery.min.js"></script>
    <script src="/js/main.js"></script>    
    <script>
        var xmlhttp = null;
        if (window.XMLHttpRequest) {
            // code for IE7+, Firefox, Chrome, Opera, Safari
            xmlhttp = new XMLHttpRequest();
        }else{
            // code for IE6, IE5
            xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
        }
        
        $("#wid").keydown(function(e) {            
            if(e.keyCode == 13 || e.keyCode == 9){ // if enter pressed
                if($("#wid").val() != ""){
                    if(xmlhttp.readyState === 0 || xmlhttp.readyState === 4){        
                        xmlhttp.open("GET","/staff/"+$('#wid').val(),true);
                        xmlhttp.onreadystatechange = function(){
                        //console.log("Response Code : "+this.status);
                        if(this.status === 200){
                            response = xmlhttp.responseText;
                            //console.log("Resp : "+response);
                            obj = response == "" ? "" : JSON.parse(response);;                         
                            if(obj){
                                $("#wname").val(obj.workername);
                                $("#status").val(obj.status);                            
                            }else{
                                $("#wname").val("");
                                $("#status").val("");
                            }
                        }else{
                            return "Status not 200";
                        }
                        };
                        xmlhttp.send(null);
                    }                
                }else{
                    alert("Fields Cannot be empty");
                }
            }
        });
        
        $("#sid").keydown(function(e) {            
            if(e.keyCode == 13 || e.keyCode == 9){ // if enter/tab pressed
                if($("#sid").val() != ""){
                    if(xmlhttp.readyState === 0 || xmlhttp.readyState === 4){        
                        xmlhttp.open("GET","/service/"+$('#sid').val(),true);
                        xmlhttp.onreadystatechange = function(){
                        //console.log("Response Code : "+this.status);
                        if(this.status === 200){
                            response = xmlhttp.responseText;
                            //console.log("Resp : "+response);
                            obj = response == "" ? "" : JSON.parse(response);;   
                            if(obj){
                                $("#sname").val(obj.servicename);
                                $("#rate").val(obj.rate)
                            }else{
                                $("#sname").val("");
                                $("#rate").val("");
                            }                    
                        }else{
                            return "Status not 200";
                        }
                        };
                        xmlhttp.send(null);
                    }                               
                }else{
                    alert("Fields Cannot be empty");
                }
            }
        });
        
        $("#sbtn").click(function(){  
            //console.log(" service submission triggered");                                  
            sid= $("#sid").val();
            sname = $("#sname").val();
            rate = $("#rate").val();   
            if($("#sid").val() != ""){                     
            if(xmlhttp.readyState === 0 || xmlhttp.readyState === 4){        
                xmlhttp.open("POST","/service",false);
                //xmlhttp.setRequestHeader("Content-Type", "application/json");
                xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                xmlhttp.onreadystatechange = function(){
                //console.log("Response Code : "+this.status);
                    if(this.status === 200){
                        response = xmlhttp.responseText;
                        //console.log("Resp : "+response);
                        obj = response == "" ? "" : JSON.parse(response);;  
                        if(obj){
                            if(sid == "0"){
                                alert("New Service Successfully Added !!!");
                            }else{
                                alert("Service Successfully Updated !!!");
                            }
                            $("#sid").val("");
                            $("#sname").val("");
                            $("#rate").val("");
                        }else{
                            alert("Operation unsuccessful !!!");
                        }                                          
                    }else{
                        return "Status not 200";
                    }
                };                
                xmlhttp.send("sid="+sid+"&sname="+sname+"&rate="+rate);
            } }           
        });
        
        $("#wbtn").click(function(){
            //console.log("staff submission triggered");                                  
            wid = $("#wid").val();
            wname = $("#wname").val();
            status = $("#status").val();            
            if(xmlhttp.readyState === 0 || xmlhttp.readyState === 4){        
                xmlhttp.open("POST","/staff",false);
                //xmlhttp.setRequestHeader("Content-Type", "application/json");
                xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                xmlhttp.onreadystatechange = function(){
                //console.log("Response Code : "+this.status);
                    if(this.status === 200){
                        response = xmlhttp.responseText;
                        //console.log("Resp : "+response);
                        obj = response == "" ? "" : JSON.parse(response);;  
                        if(obj){
                            if(wid == "0"){
                                alert("New Staff Successfully Added !!!");
                                location.reload();
                            }else{
                                alert("Staff Details Successfully Updated !!!");
                                location.reload();
                            }
                            $("#wid").val("");
                            $("#wname").val("");
                            $("#status").val("");
                        }else{
                            alert("Operation unsuccessful !!!");
                        }                                       
                    }else{
                        return "Status not 200";
                    }
                };                
                xmlhttp.send("wid="+wid+"&wname="+wname+"&status="+status);
            }        
        }); 
        
       
    </script>  
</body>
</html>            
<?php 
    }else if((int)$_SESSION['role'] == 1){
            header("Location: http://".$_SERVER['SERVER_NAME']."/billing");
            exit();
        }        
    }else{
        header("Location: http://".$_SERVER['SERVER_NAME']);
        exit();
    }

?>