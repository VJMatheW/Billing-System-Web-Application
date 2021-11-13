<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta http-equiv="Cache-Control" content="no-store" />
    <!-- <link rel="icon" type="image/x-icon" href="/favicon.ico" />-->
    <link rel="shortcut icon" href="/favicon.ico" /> 
    <title>Login</title>
    <style type="text/css">
        *{
            font-family: sans-serif;
        }
        body{
            width: 100%;
            height: 100vh;
            padding: 0px;
            margin: 0px;        
        }
        .container{
            width: 100%;
            height: 100%;
            display: flex;
            justify-content: center; 
            align-items: center;
            background: url("resource/bg2.jpeg");
            background-repeat: no-repeat;
            background-size: cover;            
        }
        .form{
            width: 300px;
            height: 400px;
            background-color: rgba(255,255,255,1);
            text-align: center;
            padding: 30px 15px;            
        }
        .form img{
            margin-top: 30px;
        }
        .wrapper{
            margin: 20px 27px;
            position: relative;
        }
        input{
            color: black;
            padding: 3px 1px;
            font-family: sans-serif;
            font-size: 17px;
            width: 100%;
            height: 30px;
            border: none;
            background-color: transparent;
            outline: none;
            border-bottom: 2px solid rgb(0, 0, 0);            
        }        
        .wrapper label{
            font-family: sans-serif;
            font-weight: 100;
            color: rgba(0, 0, 0,0.5);            
            position: absolute;
            top: 12px;
            left: 0;
            pointer-events: none;
            transition: .5s;
        }
        input:focus ~ label, input:valid ~ label{
            top: -10px;  
            font-weight: bold;
            font-size: 11px;              
            color: rgba(0, 0, 0,1);        
        }
        input[type="submit"]{                       
            margin-top: 15px;
            font-size: 16px;
            text-transform: uppercase;
            letter-spacing: 2px;
            font-weight: bold;
            border-bottom: none;            
            border:1px solid  rgb(0, 0, 0);
            background-color: transparent;
        }
        input[type="submit"]:active{
            background-color: rgb(0, 0, 0);
            color:white;
        }
        .error{
            padding:15px 5px;
            background-color:rgb(225,0,0);
            color:white;
            font-weight: 100;
        }
        img{
            transform: scale(1.5);
            width:100px; 
            height:100px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="form">
        <img src="/resource/logo-whitebg.jpg" alt="DK Profile Logo">
            <form action="/authenticate" method="POST">
                
                <div class="wrapper">
                    <input type="text" name="uname" id="uname" autofocus="true" autocomplete="off" required >
                    <label>UserName</label>
                </div>
                <div class="wrapper">
                    <input type="password" name="password" id="password" required>
                    <label>Password</label>
                </div>
                <div class="wrapper">
                    <input type="submit" value="Login">
                </div>                                
                <?php 
                    session_start();
                    if(isset($_SESSION['error'])){
                        echo "<div class='error'>Invalid Login-Id / Password</div>";
                    }
                ?>                
            </form>
        </div>
    </div>
</body>
</html>