<?php
require 'conn/conn.php';
require 'conn/function.php';

$action=$_GET["action"];
if($action=="save"){
	$G_title = htmlspecialchars($_POST["G_title"]);
	$G_name = htmlspecialchars($_POST["G_name"]);
	$G_mail = htmlspecialchars($_POST["G_mail"]);
	$G_phone = htmlspecialchars($_POST["G_phone"]);
	$G_msg = htmlspecialchars($_POST["G_msg"]);

	if(strpos($G_mail,"@")===false || strpos($G_mail,".")===false){
		box("请填写一个正确的邮箱！","back","error");
	}

	if(strlen($G_phone)!=11 || !is_numeric($G_phone)){
		box("请填写一个正确的手机号码！","back","error");
	}

	if(($_POST["G_code"]!=$_SESSION["CmsCode"] || $_POST["G_code"]=="" || $_SESSION["CmsCode"]=="") && $C_slide==1){
        box("验证码错误!".$_SESSION["CmsCode"]."|".xcode($_POST["G_code"],'DECODE',$_SESSION["CmsCode"],0), "back", "error");
    } else {
        mysqli_query($conn, "insert into sl_guestbook(G_title,G_name,G_mail,G_phone,G_msg,G_time,G_reply) values('$G_title','$G_name','$G_mail','$G_phone','$G_msg','".date('Y-m-d H:i:s')."','')");
        $info=sendmail("您的网站有用户留言","<p>您的网站《".$C_title."》有用户留言，请登录后台->其他设置->留言管理查看<p><p>留言标题：$G_title</p>",$C_email);
        if($info=="success"){
            box("留言成功，请等待管理员回复！","index.php","success");
        }else{
        	box($info,"index.php","success");
        }
    }
}
?>