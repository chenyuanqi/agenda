<?php
error_reporting(0);
include_once './config.php';
if ( ACCESS_TOKEN === $_POST['pwd'] )
{
    setcookie("CYQ", ACCESS_TOKEN, time() + 1800);
    header("location:index.php");
}
?>
<html>

<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>访问受到密码保护 [401]</title>
<style type="text/css">
body{
  background: #F2F2F2; 
  text-align: center; 
  margin: 0; 
  padding: 0;
}

#login {
    float: none;
    text-align: left;
    width: 410px;
    margin: 0px auto;
    margin-top: 134px;
    background: #171717;
    border: 4px solid #222222;
    font-family: arial;
    color: white;
    padding: 0 0 15px 25px;
    opacity: .85;
    filter: alpha(opacity=85);
}

#title {
    font-size: 24px;
    font-weight: bold;
    display: block;
    width: 385px;
    border-bottom: 1px solid #888;
    margin-bottom: 30px;
}

#submit {
    background: #E9E9E9;
    color: #161616;
    font-size: 18px;
    font-weight: bold;
    padding: 4px;
    margin-left: 5px;
}

#pwd {
    border: 2px solid #959595;
    font-size: 18px;
    padding: 5px;
    width: 305px;
}

.error-message {
    font-size: 14px;
    color: red;
}
</style>
<!--[if IE]>
<style type="text/css">

#login {
  padding: 25px 25px 15px 25px;

}

#pwd {
  width: 270px;
  height: 35px;
}

#submit {
  padding: 0px;
  margin-left: 5px;
  height: 38px;
  position: relative;
  top: 2px;
}

</style>
<! [endif]-->
</head>

<body>
    <div id="login">
        <p id="title">访问受到密码保护</p>
        <form method="POST">
            <p style="font-size: 14px;">请在下面输入密码</p>
            <input type="password" name="pwd" id="pwd" autofocus />
            <input type="submit" id="submit" value="Enter" />
        </form>
    </div>
</body>

</html>
<script type='text/javascript'>
window.onload=function(){
  document.getElementById('pwd').focus();
};
</script>

