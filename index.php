<?php
ini_set("display_errors", "On");//打开错误提示
ini_set("error_reporting",E_ALL);//显示所有错误

$first=json_decode(file_get_contents("conn/config.json"))->first;

if($first=="1"){
    Header("Location: install");
    die();
}

require 'conn/conn.php';
require 'conn/function.php';

checkauth();

if($C_indexon==0 && $_SESSION["M_login"]==""){
	Header("Location: member");
    die();
}

$type=$_GET["type"];
$id=intval($_GET["id"]);
$s=$_GET["s"];

if($s!=""){
  if(substr($s,0,1)=="p"){
    $type="productinfo";
  }
  if(substr($s,0,1)=="n"){
    $type="newsinfo";
  }
  if(substr($s,0,1)=="c"){
    $type="courseinfo";
  }
  $id=intval(substr($s,1));
  $_GET["id"]=intval(substr($s,1));
}

if($type==""){
	$type="index";
}

if(isMobile()){
  $t=$C_wap;
}else{
  $t=$C_template;
}

$md5=md5(json_encode($H_data).json_encode(getlist("select * from sl_slide")).json_encode(getlist("select * from sl_link")).json_encode(getlist("select * from sl_menu")));
$html_path="conn/cache/".$_SERVER["SERVER_NAME"]."_".$t."_".t($_GET["type"])."_".intval($_GET["id"])."_".intval($_GET["page"])."_".intval($_SESSION["M_id"]).".cache";

if(is_file($html_path) && $C_cache==1 && $_GET["type"]!="search" && $md5==substr(file_get_contents($html_path),0,32)){
	die(ycode(substr(file_get_contents($html_path),32)));
}else{
	switch($type){
	  case "index":
	  $html=tpl("template/".$t."/index.tpl");
	  break;

	  case "text":
	  if(getrs("select * from sl_text where T_id=$id and T_del=0","T_id")==""){
	    box("该单页已删除！","back","error");
	  }else{
	    $html=text(tpl("template/".$t."/text.tpl"));
	  }
	  break;

	  case "news":
	  if(getrs("select * from sl_nsort where S_id=$id and S_del=0","S_id")=="" && $id!=0){
	    box("该新闻分类已删除！","back","error");
	  }else{
	    $html=news(tpl("template/".$t."/news.tpl"));
	  }
	  break;

	  case "newsinfo":
	  if(getrs("select * from sl_news where N_id=$id and N_del=0","N_id")==""){
	    box("该新闻已删除！","back","error");
	  }else{
	    if(getrs("select * from sl_news where N_id=$id and N_sh=1","N_id")==""){
	      box("该新闻尚未通过审核，请稍候再试","back","error");
	    }else{
	      $html=newsinfo(tpl("template/".$t."/newsinfo.tpl"));
	    }
	  }
	  break;

	  case "course":
	  if(getrs("select * from sl_usort where S_id=$id and S_del=0","S_id")=="" && $id!=0){
	    box("该课程分类已删除！","back","error");
	  }else{
	    $html=course(tpl("template/".$t."/course.tpl"));
	  }
	  break;

	  case "courseintro":
	  if(getrs("select * from sl_course where C_id=$id and C_del=0","C_id")==""){
	    box("该课程已删除！","back","error");
	  }else{
	    if(getrs("select * from sl_course where C_id=$id and C_sh=1","C_id")==""){
	      box("该课程尚未通过审核，请稍候再试","back","error");
	    }else{
	      $html=courseintro(tpl("template/".$t."/courseintro.tpl"));
	    }
	  }
	  break;

	  case "courseinfo":
	  if(getrs("select * from sl_course where C_id=$id and C_del=0","C_id")==""){
	    box("该课程已删除！","back","error");
	  }else{
	    if(getrs("select * from sl_course where C_id=$id and C_sh=1","C_id")==""){
	      box("该课程尚未通过审核，请稍候再试","back","error");
	    }else{
	      $html=courseinfo(tpl("template/".$t."/courseinfo.tpl"));
	    }
	  }
	  break;

	  case "product":

	  if(getrs("select * from sl_psort where S_id=$id and S_del=0","S_id")=="" && $id!=0){
	    box("该产品分类已删除！","back","error");
	  }else{
	    $html=product(tpl("template/".$t."/product.tpl"));
	  }
	  break;

	  case "productinfo":
	  if(getrs("select * from sl_product where P_id=$id and P_del=0","P_id")==""){
	    box("该商品已删除！","back","error");
	  }else{
	    if(getrs("select * from sl_product where P_id=$id and P_sh=1","P_id")==""){
	      box("该商品尚未通过审核，请稍候再试","back","error");
	    }else{
	      $html=productinfo(tpl("template/".$t."/productinfo.tpl"));
	    }
	  }
	  break;

	  case "p":
	  if(getrs("select * from sl_product where P_id=$id and P_del=0","P_id")==""){
	    box("该商品已删除！","back","error");
	  }else{
	    if(getrs("select * from sl_product where P_id=$id and P_sh=1","P_id")==""){
	      box("该商品尚未通过审核，请稍候再试","back","error");
	    }else{
	      $html=productinfo(tpl("template/".$t."/p.tpl"));
	    }
	  }
	  break;

	  case "shop":
	  Header("Location: ./shop.php?M_id=".$id);
	  die();
	  break;

	  case "query":
	  Header("Location: ./?type=text&id=".getrs("select * from sl_text where T_type=4 and T_del=0","T_id"));
	  die();
	  break;

	  case "lisence":
	  Header("Location: ./?type=text&id=".getrs("select * from sl_text where T_type=5 and T_del=0","T_id"));
	  die();
	  break;

	  case "member":
	  Header("Location: ./member");
	  die();
	  break;

	  case "seller":
	  Header("Location: ./member/seller.php");
	  die();
	  break;

	  case "login":
	  Header("Location: ./member/login.php");
	  die();
	  break;

	  case "reg":
	  Header("Location: ./member/reg.php");
	  die();
	  break;

	  case "search":
	  $html=tpl("template/".$t."/search.tpl");
	  break;

	  case "contact":
	  Header("Location: ./?type=text&id=".getrs("select * from sl_text where T_type=1 and T_del=0","T_id"));
	  die();
	  break;

	  case "guestbook":
	  Header("Location: ./?type=text&id=".getrs("select * from sl_text where T_type=2 and T_del=0","T_id"));
	  die();
	  break;

	  case "quan":
	  Header("Location: ./?type=text&id=".getrs("select * from sl_text where T_type=3 and T_del=0","T_id"));
	  die();
	  break;

	  default:
	  $html=tpl("template/".$t."/".$type.".tpl");
	  break;
	}

	$html=d($html);
	if($C_cht==1){
		$html=CnFont($html,"f");
	}

	if($C_html==1){
		$html=html($html);
	}
	$html=str_replace("?type=index&id=1","./",$html);
	$html=str_replace("?type=index&id=0","./",$html);
	if($C_cache==1){
		mkdirs_2(dirname($html_path));
		file_put_contents($html_path,$md5.zcode($html));
	}
	die($html);
}
?>