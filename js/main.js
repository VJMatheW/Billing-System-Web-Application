var xmlhttp = null;
if (window.XMLHttpRequest) {
    // code for IE7+, Firefox, Chrome, Opera, Safari
    xmlhttp = new XMLHttpRequest();
} else {
    // code for IE6, IE5
    xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
}
var worker_names;
var service_names;
var paymode_names;
var rate;
var newUser = false;
var custid;
var numbers;
var lastbill;
var paymodeAmt;

function getAll(){    // this will be called onfocus of number field   
  if(xmlhttp.readyState === 0 || xmlhttp.readyState === 4){        
    xmlhttp.open("GET","/all",true);
    xmlhttp.onreadystatechange = function(){
      //console.log("Response Code : "+this.status);
      if(this.status === 200){
        response = xmlhttp.responseText;
        //console.log("Resp : "+response);
        obj = response == "" ? "" : JSON.parse(response);
        worker_names = obj.worker;
        service_names = obj.service; 
        paymode_names = obj.paymode;        
        rate = obj.s_rate;                   
        numbers = obj.number; 
        getRecentBill();        
        // creating & appending payMode Options
        $("#paymode_div").html("");
        //console.log(createSelect(paymode_names,"Mode of Payment", "paymode"));
        $("#paymode_div").append(createSelect(paymode_names,"Mode of Payment", "paymode"));        
      }else{
          //alert("Status not 200")
      }
    };
    xmlhttp.send(null);
  }
}

function getPaymodeAmt(mode="without"){  
  from = '',to ='';  
  if(mode == "with"){    
    from = $("#from").val();
    to = $("#to").val();
    if(from == "" || to == ""){
      alert("Date Fields Cannot be empty... Select Date");
      return;
    }else{
      $("#paymodedate").html("<b>"+from +"  /  "+to+"</b>");
    }
  }else{
    $("#from").val("");
    $("#to").val("");
    $("#noofcustomer").html("");
    $("#paymodedate").html("");
  } 
  //console.log(mode+"-   - "+from+"-   - "+to);                         
  if(xmlhttp.readyState === 0 || xmlhttp.readyState === 4){        
    if(mode == "with"){      
      xmlhttp.open("GET","/paymodeamt/"+from+"/"+to,true);
    }else{
      xmlhttp.open("GET","/paymodeamt",true);
    }      
      xmlhttp.onreadystatechange = function(){
      //console.log("Response Code : "+this.status);
        if(this.status === 200){
            response = xmlhttp.responseText;
            //console.log("Resp : "+response);
            obj = response == "" ? "" : JSON.parse(response);            
            setPaymodeAmt(obj.paymode);          
            $("#noofcustomer").html(obj.noofcustomer);
        }else{
            //alert("Status not 200");
        }
      };
      xmlhttp.send(null);
  }
}

function setPaymodeAmt(obj){
  var thead="", tbody="", total = 0;
  $.each(obj,function(index,value){       
    total += parseInt(value);    
    thead += "<th>"+index+"</th>";
    tbody += "<td>"+value+"</td>";
  });                        
  thead += "<th>Total</th>";
  tbody += "<td>"+total+"</td>";
  $("#paymodehead").html(thead);
  $("#paymodebody").html(tbody);
}

function getRecentBill(mode='without'){  
  from = '',to ='';  
  if(mode == "with"){    
    from = $("#rfrom").val();
    to = $("#rto").val();
    if(from == "" || to == ""){
      alert("Date Fields Cannot be empty... Select Date");
      return;
    }else{
      $("#billinfodate").html("<b>"+from +"  /  "+to+"</b>");
    }
  }else{
    $("#rfrom").val("");
    $("#rto").val("");    
    $("#rnoofcustomer").html("");
    $("#billinfodate").html("");
  }                       
  if(xmlhttp.readyState === 0 || xmlhttp.readyState === 4){
      if(mode == 'with'){
        xmlhttp.open("GET","/recentbill/"+from+"/"+to,true);
      }else{
        xmlhttp.open("GET","/recentbill",true);
      }      
      xmlhttp.onreadystatechange = function(){
      //console.log("Response Code : "+this.status);
      if(this.status === 200){
          response = xmlhttp.responseText;
          //console.log("Resp : "+response);
          obj = response == "" ? "" : JSON.parse(response);  
          $("#recentbill").html("");
          $.each(obj.billinfo,function(i,value){
            $("#recentbill").append("<tr onclick='showme(this);' ><td>"+value.billno+"</td><td>"+value.name+"</td><td>"+value.date+"</td><td>"+value.servicecount+"</td><td>"+value.paymode+"</td><td>"+value.totalamt+"</td></tr>");
          });
          $("#rnoofcustomer").html(obj.noofcustomer);
          getPaymodeAmt();
      }else{
          //alert("Status not 200")
      }
      };
      xmlhttp.send(null);
  }
}    

$("#myInput").focus(function(){
  autocomplete(document.getElementById("myInput"), numbers);
});

function getInfo(phone_no){
  if(xmlhttp.readyState === 0 || xmlhttp.readyState === 4){  
    //console.log("/info/"+phone_no);      
    xmlhttp.open("GET","/info/"+phone_no,true);
    xmlhttp.onreadystatechange = function(){
      //console.log("Response Code : "+this.status);
      if(this.status === 200){
        response = xmlhttp.responseText;        
        resObj = response == "" ? "" : JSON.parse(response);;   
        //console.log(resObj);
        if(resObj[0]){          
          obj = resObj[0];           
          $("#name").val(obj.name).attr("readonly","true");
          $("#mail").val(obj.mail).attr("readonly","true");
          $("#myInput").blur();
          custid = obj.custid;
          newUser = false;
          console.log("custid : "+custid);
          $("#paymode").focus();
          getHistory(custid);          
        }else{
          console.log("new user bitches");
          $("#name").focus();
          $("#name").val('').removeAttr("readonly");
          $("#mail").val('').removeAttr("readonly");
          newUser = true;
          $("#custhistory").html("<h3 style='text-align:center'>New Customer !!!</h3>").attr("class","table-responsive visible");
          $(".histotal").attr("class","histotal hidden");
        }
      }else{
          //alert("Status not 200")
      }
    };
    xmlhttp.send(null);
  }
}

function getHistory(custid){
  if(xmlhttp.readyState === 0 || xmlhttp.readyState === 4){        
    xmlhttp.open("GET","/history/"+custid,true);
    xmlhttp.onreadystatechange = function(){
      //console.log("Response Code : "+this.status);
      if(this.status === 200){
        response = xmlhttp.responseText;
        //console.log("Resp : "+response);
        obj = response == "" ? "" : JSON.parse(response);;         
        setHistory(obj);                  
      }else{
        return "Status not 200";
      }
    };
    xmlhttp.send(null);
  }
}

function setHistory(obj){
  //console.log("called ");
  if(obj){  // obj becomes false when there is no history of customer but customer data available
    $(".histotal").attr("class","histotal visible");
    $("#histotal").html(obj.total);
    $("#visited").html(obj.visited);
    $("#custhistory").attr("class","table-responsive visible").html("<table class='table table-bordered table-striped'><thead><tr><th>Date</th><th>Service</th><th>Staff</th><th>Amount</th></tr></thead><tbody id='custbody'></tbody></table>");
    $("#custbody").html("");
    obj = obj.history ;
    //console.log(obj.length); 
    $.each(obj, function(index,value){
      $("#custbody").append("<tr><td>"+value.date+"</td><td>"+value.s_name+"</td><td>"+value.w_name+"</td><td>"+value.amt+"</td></tr>");
    });
  }else{
    $("#histotal").html(0);
    $("#visited").html(0);
  }
}

// funtion for creating new billing box
$("#btnnew").click(function() {
  var div = $("<div></div>").attr({
    "class" : "row"
  });  

  div.append(getDivXs4().append(createSelect(service_names,"Service Name", "serviceid")));
  div.append(getDivXs4().append(createSelect(worker_names,"Staff Name", "workerid")));  
  div.append($("<div class='col-xs-4' style='text-align:center;' ></div>").append($("<button></button>").attr({ 
    "onclick" : "deleteMe(this);",
    "class": "btn btn-rmv",
    "name" : "btn"
  }).html('&times;')));

  div1 = $("<div></div>").attr({
    "class" : "row"
  });
  div1.append(getDivXs4().append(createInput("text", "Mrp", "mrp")));
  div1.append(getDivXs4().append(createInput("text", "Discount", "discount")));
  div1.append(getDivXs4().append(createInput("text", "Final Amount", "finalamt")));

  $("#billinfo").prepend($("<div></div>").append(div).append(div1).attr("class","bill"));         
});

$("#genBill").click(function(){  
  //console.log("Clicked");
  var outer = $("#billinfo").children();
  var temp = {};
  var final = [];
  var finaal = {};
  //console.log(outer.length);
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
  //console.log(final);
  finaal["newuser"] = newUser;
  finaal["name"] = $("#name").val();
  finaal["mail"] = $("#mail").val();
  finaal["phone"] = $("#myInput").val();
  finaal["paymode"] = $("#paymode").val();  
  finaal["tendered"] = $("#tamt").val();
  finaal["change"] = $("bamt").val();
  finaal["print"] = $("#checkprint").prop("checked");
  if(!newUser){
    finaal["custid"] = custid;
  }
  finaal["bill"] = final;
  //console.log(finaal);
  if(xmlhttp.readyState === 0 || xmlhttp.readyState === 4){        
    xmlhttp.open("POST","/billme",false);
    xmlhttp.setRequestHeader("Content-Type", "application/json");
    xmlhttp.onreadystatechange = function(){      
      if(this.status === 200){
        response = xmlhttp.responseText;
        console.log(response);
        obj = response == "" ? "" : JSON.parse(response);        
        if(obj.status){ // success factor
          document.querySelector(".bg-modal").style.display = "flex"; // make modal visible
          $(".m-head").attr("class","m-head hidden"); // hide modal-head
          $(".m-msg").html(obj.msg); // append modal-msg
          $(".m-billno").attr("class","m-billno visible").html("Bill Number : "+obj.billno); // append modal-billno
          clearAll();
          getRecentBill();
        }else if(!obj.status){
          document.querySelector(".bg-modal").style.display = "flex"; // make modal visible
          $(".m-head").attr("class","m-head visible").html("Alert !!!");
          $(".m-msg").html(obj.msg);
          $(".m-billno").attr("class","m-billno hidden");
          getRecentBill();
        }        
      }else{
          //alert("Status not 200");
      }
    };
    xmlhttp.send(JSON.stringify(finaal));
  }  
});

function clearAll(){
  $("#billinfo").html("");
  $("#name").val("");
  $("#mail").val("");
  $("#myInput").val("");
  $("#paymode").val("");
  $("#tamt").val("");
  $("#bamt").html("0");
  $("#amt").html("0");
  $(".histotal").attr("class","histotal hidden");
  $("#custhistory").html("");
  $("#name").removeAttr("readonly");
  $("#mail").removeAttr("readonly");
  if(newUser){
    getAll();
  }
  newUser = false;
}

function calculate(obj){  
  $(obj).keydown(function(e) {    
    objparent = $(obj).parent().parent();
    objmrp = $($($(objparent).children()[0]).children()[0]);
    objfinalamount = $($($(objparent).children()[2]).children()[0]);
    if(e.keyCode == 13){            
      mrp = $(objmrp).val(); 
      discount = $(obj).val();
      //amount = ((100 - discount)/100)*mrp;
      //amount = Math.round(amount);
      //remainder = amount % 5;
      //console.log("MRP: "+mrp+" Discount: "+discount+" FinalAmt: "+amount+" Remainder: "+remainder);      
      //$(objfinalamount).val(amount-remainder);
      $(objfinalamount).val(mrp-discount);
      $(obj).blur();
    }
  });  
  $(obj).blur(function(e) {    
    objparent = $(obj).parent().parent();
    objmrp = $($($(objparent).children()[0]).children()[0]);
    objfinalamount = $($($(objparent).children()[2]).children()[0]);    
    mrp = $(objmrp).val(); 
    discount = $(obj).val();
    //amount = ((100 - discount)/100)*mrp;
    //amount = Math.round(amount);
    //remainder = amount % 5;
    //console.log("MRP: "+mrp+" Discount: "+discount+" FinalAmt: "+amount+" Remainder: "+remainder);      
    //$(objfinalamount).val(amount-remainder);    
    $(objfinalamount).val(mrp-discount);
    calculateAmtPayable();
  });  
  calculateAmtPayable();
}

function createTr(){
  return $("<tr></tr>");
}

function createTh(){
  return $("<th></th>");
}

function createTd(){
  return $("<td></td>");
}

function createInput(type, placeholder, name){ 
  var attr;
  if(name == "discount"){   
    attr = {
      "type":type,
      "placeholder":placeholder,
      "class":"form-control",
      "name" : name,
      "onblur" : "calculate(this)",
      "onkeydown" : "calculate(this)"      
    }
  }else{
    attr = {
      "type":type,
      "placeholder":placeholder,
      "class":"form-control",
      "name" : name,
      "readonly":"true"
    }
  }
  return $("<input/>").attr(attr);
}

function calculateAmtPayable(){
  parent = $("#billinfo").children();
  total = 0;
  $.each(parent,function(i,obj){
    temp = $($($($(obj).children()[1]).children()[2]).children()[0]).val();
    //console.log(i+"-"+ parseInt($($($($(obj).children()[1]).children()[2]).children()[0]).val()));    
    total += parseInt( temp =="" ? 0 : temp );
    //console.log(i+"-"+ total);
  });
  $("#amt").html(total);  
  $("#tamt").val(total); // for setting tendered amount  
  setForBillEdit();
}

function setForBillEdit(){   
  eamt = parseInt(document.getElementById("eamt").innerHTML);
  //eamt = $("eamt").html();
  if(eamt > 0){
    //console.log(parseInt(eamt) + parseInt($("#amt").html()));
    tamt = parseInt(eamt) + parseInt($("#amt").html());
    $("#famt").html(tamt); 
    $("#tamt").val(tamt);
  }  
}

function setrate(obj){  
  val = parseInt($(obj).val());   
  parent = $(obj).parent().parent().parent();
  if(!val == ""){
    $($($($(parent).children()[1]).children()[0]).children()[0]).val(rate[(val-1)]);
    $($($($(parent).children()[1]).children()[1]).children()[0]).val("0");
    $($($($(parent).children()[1]).children()[2]).children()[0]).val(rate[(val-1)]);
  }else{
    $($($($(parent).children()[1]).children()[0]).children()[0]).val("");
    $($($($(parent).children()[1]).children()[1]).children()[0]).val("");
    $($($($(parent).children()[1]).children()[2]).children()[0]).val("");
  }
  calculateAmtPayable();
}

function createSelect(arrayObj, placeholder, name){ 
  var select; 
  if(name == "serviceid"){
    select = $("<select></select>").attr({
      "name" : name,
      "class" : "form-control",
      "onchange" : "setrate(this)"
    });
  }else if(name == "paymode"){
    select = $("<select></select>").attr({
      "id" : "paymode",
      "class" : "form-control",      
    });
  }else{
    select = $("<select></select>").attr({
      "name" : name,
      "class" : "form-control"
    });
  }    
  select.append($("<option></option>").text(placeholder).val(""));
  $.each(arrayObj,function(i,obj){      
    select.append($("<option></option>").text(obj.name).val(obj.id));  //"<option value='"+obj.id+"'>"+obj.name+"</option>";      
  });
  return select;  //$("#billinfo").append(select);  
}

function getDivXs4(){
  return $("<div></div>").attr({
    "class" : "form-group col-xs-4"
  });
}

function getDivXs3(){
  return $("<div></div>").attr({
    "class" : "form-group col-xs-3"
  });
}

function getDivXs1(){
  return $("<div></div>").attr({
    "class" : "form-group col-xs-1"
  });
}

function deleteMe(obj){    
  $(obj).parent().parent().parent().remove();
  calculateAmtPayable();
}

function numberOnly(obj){
  val = obj.value;
  //console.log("number only : "+val);
  obj.value = val.replace(/[^0-9]/g,"");
}

function setChangeAmt(){
  tamt = parseInt($("#tamt").val());
  amt = parseInt($("#amt").html());
  $("#bamt").html(tamt - amt);                
}

function autocomplete(inp, arr) {
    /*the autocomplete function takes two arguments,
    the text field element and an array of possible autocompleted values:*/
    var currentFocus;
    /*execute a function when someone writes in the text field:*/
    inp.addEventListener("input", function(e) {
        var a, b, i, val = this.value;
        /*close any already open lists of autocompleted values*/
        closeAllLists();
        if (!val) { return false;}
        currentFocus = -1;
        /*create a DIV element that will contain the items (values):*/
        a = document.createElement("DIV");
        a.setAttribute("id", this.id + "autocomplete-list");
        a.setAttribute("class", "autocomplete-items");
        /*append the DIV element as a child of the autocomplete container:*/
        this.parentNode.appendChild(a);
        /*for each item in the array...*/
        for (i = 0; i < arr.length; i++) {
          /*check if the item starts with the same letters as the text field value:*/
          if (arr[i].substr(0, val.length).toUpperCase() == val.toUpperCase()) {
            /*create a DIV element for each matching element:*/
            b = document.createElement("DIV");
            /*make the matching letters bold:*/
            b.innerHTML = "<strong>" + arr[i].substr(0, val.length) + "</strong>";
            b.innerHTML += arr[i].substr(val.length);
            /*insert a input field that will hold the current array item's value:*/
            b.innerHTML += "<input type='hidden' value='" + arr[i] + "'>";
            /*execute a function when someone clicks on the item value (DIV element):*/
                b.addEventListener("click", function(e) {
                /*insert the value for the autocomplete text field:*/
                inp.value = this.getElementsByTagName("input")[0].value;
                //console.log("Phone : "+inp.value);
                getInfo($("#myInput").val());                                                
                /*close the list of autocompleted values,
                (or any other open lists of autocompleted values:*/
                closeAllLists();
            });
            b.addEventListener("keydown", function(e){
              if(e.keyCode == 13){ // if enter pressed
                getInfo($("#myInput").val());                
              }
            });
            a.appendChild(b);
          }
        }
    });
    /*

    /*execute a function presses a key on the keyboard:*/
    inp.addEventListener("keydown", function(e) {
        var x = document.getElementById(this.id + "autocomplete-list");
        if (x) x = x.getElementsByTagName("div");
        if (e.keyCode == 40) {
          /*If the arrow DOWN key is pressed,
          increase the currentFocus variable:*/
          currentFocus++;
          /*and and make the current item more visible:*/
          addActive(x);
        } else if (e.keyCode == 38) { //up
          /*If the arrow UP key is pressed,
          decrease the currentFocus variable:*/          
          currentFocus--;
          /*and and make the current item more visible:*/
          addActive(x);
        } else if (e.keyCode == 13) {          
          /*If the ENTER key is pressed, prevent the form from being submitted,*/  
          //console.log("event occured");        
          e.preventDefault();
          if (currentFocus > -1) {
            /*and simulate a click on the "active" item:*/
            if (x) x[currentFocus].click();
          }else{
            getInfo(inp.value);
            closeAllLists();
          }
        }else if(e.keyCode == 9){
            getInfo(inp.value);
            closeAllLists();
        }
    });
    function addActive(x) {
      /*a function to classify an item as "active":*/
      if (!x) return false;
      /*start by removing the "active" class on all items:*/
      removeActive(x);
      if (currentFocus >= x.length) currentFocus = 0;
      if (currentFocus < 0) currentFocus = (x.length - 1);
      /*add class "autocomplete-active":*/
      x[currentFocus].classList.add("autocomplete-active");
    }
    function removeActive(x) {
      /*a function to remove the "active" class from all autocomplete items:*/
      for (var i = 0; i < x.length; i++) {
        x[i].classList.remove("autocomplete-active");
      }
    }
    function closeAllLists(elmnt) {
      /*close all autocomplete lists in the document,
      except the one passed as an argument:*/
      var x = document.getElementsByClassName("autocomplete-items");
      for (var i = 0; i < x.length; i++) {
        if (elmnt != x[i] && elmnt != inp) {
        x[i].parentNode.removeChild(x[i]);
      }
    }
  }
  /*execute a function when someone clicks in the document:*/
  document.addEventListener("click", function (e) {
      closeAllLists(e.target);
  });
  }
/** 
   * var validation = {
    isEmailAddress:function(str) {
        var pattern =/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/;
        return pattern.test(str);  // returns a boolean
    },
    isNotEmpty:function (str) {
        var pattern =/\S+/;
        return pattern.test(str);  // returns a boolean
    },
    isNumber:function(str) {
        var pattern = /^\d+$/;
        return pattern.test(str);  // returns a boolean
    },
    isSame:function(str1,str2){
        return str1 === str2;
    }
};   

alert(validation.isNotEmpty("dff"));
alert(validation.isNumber(44));
alert(validation.isEmailAddress("mf@tl.ff"));
alert(validation.isSame("sf","sf"));
   */