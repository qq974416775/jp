<?php
require 'conn/conn.php';
require 'conn/function.php';
$M_id=intval($_GET["M_id"]);
$sql="Select * from sl_member where M_id=$M_id";
	$result = mysqli_query($conn, $sql);
	if (mysqli_num_rows($result) > 0) {
		$row = mysqli_fetch_assoc($result);
		$M_login=$row["M_login"];
		$M_shop=$row["M_shop"];
		$M_head=$row["M_head"];
		
		$M_qq=$row["M_qq"];
		$M_mobile=$row["M_mobile"];
		$M_notice=$row["M_notice"];

		$M_type=$row["M_type"];
		$M_sellertime=$row["M_sellertime"];
		$M_sellerlong=$row["M_sellerlong"];

		if($M_type==0 || time()-strtotime($M_sellertime)>$M_sellerlong*86400){//商家到期
			box("商户已到期！","back","error");
			die();
		}
	}else{
		box("未发现该店铺，请检查！","back","error");
	}
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8"> 
	<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
	<title><?php echo $M_shop?>-<?php echo $C_title?></title>
	<link href="media/<?php echo $C_ico?>" rel="shortcut icon" />
	<meta name="description" content="<?php echo $C_description?>" />
	<meta name="keywords" content="<?php echo $C_keyword?>" />
	<link rel="stylesheet" href="https://cdn.staticfile.org/twitter-bootstrap/3.3.7/css/bootstrap.min.css">  
	<script src="https://cdn.staticfile.org/jquery/2.1.1/jquery.min.js"></script>
	<script src="https://cdn.staticfile.org/twitter-bootstrap/3.3.7/js/bootstrap.min.js"></script>
	<style type="text/css">
.left_p{color:#333333;text-decoration:none;display: block;padding-top: 10px;}
.left_p:hover{background: #f7f7f7;text-decoration:none;}
.left_p img{width:55px;height:55px;border:solid #DDDDDD 1px;padding:5px;margin-bottom:10px;}
.left_p .P_title{display:inline-block;vertical-align:top;width:calc(100% - 70px);font-size:12px;margin-left:5px;}

.right_p{color:#333333;text-decoration:none;}
.scms-pic:hover{background: #f7f7f7;text-decoration:none;}
</style>
</head>
<body>
	<nav class="navbar navbar-default navbar-fixed-top" role="navigation">
	<div class="container-fluid">
	<div class="navbar-header">
		<button type="button" class="navbar-toggle" data-toggle="collapse"
				data-target="#example-navbar-collapse">
			<span class="sr-only">切换导航</span>
			<span class="icon-bar"></span>
			<span class="icon-bar"></span>
			<span class="icon-bar"></span>
		</button>
		<a href="./"><img src="media/<?php echo $C_logo?>" style="height: 50px"></a>
	</div>
	<div>
	<div class="collapse navbar-collapse" id="example-navbar-collapse">
	<ul class="nav navbar-nav navbar-right">
<?php
			if($_SESSION["M_login"]==""){
				echo "<li><a href=\"member/reg.php\"><span class=\"glyphicon glyphicon-user\"></span> 注册</a></li><li><a href=\"member/login.php\"><span class=\"glyphicon glyphicon-log-in\"></span> 登录</a></li>";
			}else{
				echo "
				<li><a href=\"member\"><span class=\"glyphicon glyphicon-user\"></span> ".$_SESSION["M_login"]."</a></li>
				<li><a href=\"member/cart.php\"><span class=\"glyphicon glyphicon-shopping-cart\"></span> 购物车</a></li>
				<li><a href=\"member/login.php?action=unlogin\"><span class=\"glyphicon glyphicon-log-out\"></span> 退出</a></li>";
      }

?>


    </ul>
	<ul class="nav navbar-nav">
<?php
if(ismobile()){
	$sql="select * from sl_menu where U_del=0 and U_sub=0  order by U_order,U_id desc";
$result = mysqli_query($conn,  $sql);
if (mysqli_num_rows($result) > 0) {
while($row = mysqli_fetch_assoc($result)) {

  if($row["U_type"]=="link"){
    $link=$row["U_link"];
    $target="_blank";
  }else{
    $link="./?type=".$row["U_type"]."&id=".$row["U_typeid"];
    $target="_self";
  }

if(getrs("select count(*) as U_count from sl_menu where U_sub=".$row["U_id"],"U_count")>0){
	echo "<li class=\"dropdown\"><a class=\"dropdown-toggle\" data-toggle=\"dropdown\" href=\"".$link."\" target=\"".$target."\" >".$row["U_title"]." <b class=\"caret\"></b></a>";
}else{
	echo "<li class=\"dropdown\"><a  href=\"".$link."\" target=\"".$target."\" >".$row["U_title"]."</a>";
}
  

$sql2="select * from sl_menu where U_del=0 and U_sub=".$row["U_id"]." order by U_order,U_id desc";
$result2 = mysqli_query($conn,  $sql2);
if (mysqli_num_rows($result2) > 0) {
echo "<ul class=\"dropdown-menu\">";

while($row2 = mysqli_fetch_assoc($result2)) {
  if($row2["U_type"]=="link"){
    $link2=$row2["U_link"];
    $target2="_blank";
  }else{
    $link2="./?type=".$row2["U_type"]."&id=".$row2["U_typeid"];
    $target2="_self";
  }

    echo "<li><a href=\"".$link2."\" target=\"".$target2."\">".$row2["U_title"]."</a></li>";
  }
  echo "</ul>";
}
      echo "</li>";
    }
}
}else{
	$sql="select * from sl_menu where U_del=0 and U_sub=0  order by U_order,U_id desc";
$result = mysqli_query($conn,  $sql);
if (mysqli_num_rows($result) > 0) {
while($row = mysqli_fetch_assoc($result)) {

  if($row["U_type"]=="link"){
    $link=$row["U_link"];
    $target="_blank";
  }else{
    $link="./?type=".$row["U_type"]."&id=".$row["U_typeid"];
    $target="_self";
  }

if(getrs("select count(*) as U_count from sl_menu where U_sub=".$row["U_id"],"U_count")>0){
	echo "<li class=\"dropdown\"><a href=\"".$link."\" target=\"".$target."\" >".$row["U_title"]." <b class=\"caret\"></b></a>";
}else{
	echo "<li class=\"dropdown\"><a href=\"".$link."\" target=\"".$target."\" >".$row["U_title"]."</a>";
}

$sql2="select * from sl_menu where U_del=0 and U_sub=".$row["U_id"]." order by U_order,U_id desc";
$result2 = mysqli_query($conn,  $sql2);
if (mysqli_num_rows($result2) > 0) {
echo "<ul class=\"dropdown-menu\">";

while($row2 = mysqli_fetch_assoc($result2)) {

  if($row2["U_type"]=="link"){
    $link2=$row2["U_link"];
    $target2="_blank";
  }else{
    $link2="./?type=".$row2["U_type"]."&id=".$row2["U_typeid"];
    $target2="_self";
  }

    echo "<li><a href=\"".$link2."\" target=\"".$target2."\">".$row2["U_title"]."</a></li>";
  }
  echo "</ul>";
}
      echo "</li>";
    }
}
}

?>
		</ul>
	</div>
	</div>
	</div>
</nav>
<div id="myCarousel" class="carousel slide" style="margin-top: 50px;max-height: 450px;overflow: hidden">
	<!-- 轮播（Carousel）指标 -->
	<ol class="carousel-indicators">

<?php 
			$i=0;
          $sql="select * from sl_slide where S_del=0 and S_mid=$M_id order by S_order,S_id desc";
                $result = mysqli_query($conn,  $sql);
if (mysqli_num_rows($result) > 0) {
while($row = mysqli_fetch_assoc($result)) {
                        echo "<li data-target=\"#myCarousel\" data-slide-to=\"".$i."\"></li>";
                        $i+=1;
                    }
                }

       ?>

	</ol>   
	<!-- 轮播（Carousel）项目 -->
	<div class="carousel-inner">

		<?php
          $sql="select * from sl_slide where S_del=0 and S_mid=$M_id order by S_order,S_id desc";
                $result = mysqli_query($conn,  $sql);
if (mysqli_num_rows($result) > 0) {
while($row = mysqli_fetch_assoc($result)) {
                        echo "<div class=\"item\" onClick=\"window.location.href='".$row["S_link"]."'\">
			<img src=\"".pic($row["S_pic"])."\" alt=\"".$row["S_title"]."\">
			<div class=\"carousel-caption\">".$row["S_title"]."</div>
		</div>";

                    }
                }

        ?>
	</div>
	<!-- 轮播（Carousel）导航 -->
	<a class="left carousel-control" href="#myCarousel" role="button" data-slide="prev">
	    <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
	    <span class="sr-only">Previous</span>
	</a>
	<a class="right carousel-control" href="#myCarousel" role="button" data-slide="next">
	    <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
	    <span class="sr-only">Next</span>
	</a>
</div>
<div class="container" style="margin-top: 20px">
	<div class="row">
<div class="col-md-3">


<img class="img-responsive" src="media/<?php echo $M_head?>"  style="margin-bottom: 20px;width: 100%;border-radius: 10px;">

<div class="panel panel-info">
			    <div class="panel-heading">
			        <h3 class="panel-title">
			            店铺信息
			        </h3>
			    </div>
			    <div class="panel-body" >
<?php
  echo "<div>
  <div style=\"margin-bottom:10px\">
  <h4 style=\"display: inline-block;margin-bottom:-45px;\">".$M_shop."</h4></div>
  <p>信誉：<img title=\"信用值10000\" src=\"images/s-crown-5.gif\" /></p>
  <p>掌柜：".$M_login."</p>
  <p>宝贝：".getrs("select count(P_id) as P_count from sl_product where P_mid=$M_id and P_del=0","P_count")."件</p>
  <p>创店：".date("Y-m-d",strtotime($M_sellertime))."</p>
  <p>QQ：<a href=\"http://wpa.qq.com/msgrd?v=3&uin=".$M_qq."&site=qq&menu=yes\">".$M_qq."</a></p>
  <p><a class=\"btn btn-sm btn-default\" href=\"\" target=\"_blank\">进入店铺</a> <button class=\"btn btn-sm btn-default\"><span id=\"collection_shop\" mid=\"".$M_id."\"></span></button></p>
  </div>";


?>
			    </div>
			</div>

<div class="panel panel-info">
			    <div class="panel-heading">
			        <h3 class="panel-title">
			            商品销量榜
			        </h3>
			    </div>
			    <div class="panel-body" >
			    	<?php


$sql="select * from sl_product,sl_psort where S_del=0 and P_del=0 and P_sort=S_id and P_mid=$M_id  order by P_top desc,P_order,P_id desc limit 12";
$result = mysqli_query($conn,  $sql);
if (mysqli_num_rows($result) > 0) {
while($row = mysqli_fetch_assoc($result)) {

	echo "<a href=\"./?type=productinfo&id=".$row["P_id"]."\" title=\"".$row["P_title"]."\" class=\"left_p\"><img src=\"".pic(splitx($row["P_pic"],"|",0))."\">
	<div class=\"P_title\"><div style=\"height:35px;overflow:hidden;font-weight:bold\">".$row["P_title"]."</div><b style=\"color:#ff0000\">￥".p($row["P_price"])."</b> 已售：".$row["P_sold"]."件</div></a>";
                }
            }else{
            	echo "暂未发布商品";
            }
?>
			    </div>

</div>

</div>
<div class="col-md-9">
<div class="panel panel-info">
			    <div class="panel-heading">
			        <span class="panel-title">
			            最新商品
			        </span>
			        <a href="./?type=product&M_id=<?php echo $M_id?>" class="btn btn-primary btn-xs pull-right">查看更多</a>
			    </div>
			    <div class="panel-body" >
			    	<?php

$sql="select * from sl_product,sl_psort where S_del=0 and P_del=0 and P_sort=S_id and P_mid=$M_id  order by P_top desc,P_order,P_id desc limit 12";
$result = mysqli_query($conn,  $sql);
if (mysqli_num_rows($result) > 0) {
while($row = mysqli_fetch_assoc($result)) {

echo "<a href=\"./?type=productinfo&id=".$row["P_id"]."\" title=\"".$row["P_title"]."\" class=\"right_p\"><div class=\"col-md-3 col-xs-6 scms-pic\">

<img src=\"".pic(splitx($row["P_pic"],"|",0))."\" width=\"100%\" >
<p style=\"margin:10px 0;\"><b>".$row["P_title"]."</b></p><p><span style=\"font-weight:bold;text-align:left;color:#ff0000\">￥".p($row["P_price"])."</span>  <span style=\"color: #999999;float:right;\">已售：".$row["P_sold"]."件</span></p>
</div></a>";
                }
            }else{
            	echo "暂未发布商品";
            }

?>
			    </div>

</div>


<div class="panel panel-info">
			    <div class="panel-heading">
			        <span class="panel-title">
			            最新文章
			        </span>
			        <a href="./?type=news&M_id=<?php echo $M_id?>" class="btn btn-primary btn-xs pull-right">查看更多</a>
			    </div>
			    <?php

$sql="select * from sl_news,sl_nsort where S_del=0 and N_del=0 and N_sort=S_id and N_mid=$M_id order by N_top desc,N_order,N_id desc limit 10";

			$result = mysqli_query($conn,  $sql);
if (mysqli_num_rows($result) > 0) {
while($row = mysqli_fetch_assoc($result)) {

			    echo "<a href=\"./?type=newsinfo&id=".$row["N_id"]."\" class=\"list-group-item\"><span class=\"badge\">".date("Y-m-d",strtotime($row["N_date"]))."</span>".$row["N_title"]."</a>";
			    }
			}else{
			    	echo "<li class=\"list-group-item\">暂未发表文章</li>";
			    }


			    ?>

</div>


<div class="panel panel-info">
			    <div class="panel-heading">
			        <span class="panel-title">
			            最新课程
			        </span>
			        <a href="./?type=course&M_id=<?php echo $M_id?>" class="btn btn-primary btn-xs pull-right">查看更多</a>
			    </div>
			    <div class="panel-body" >
			    	<?php

$sql="select * from sl_course,sl_usort where S_del=0 and C_del=0 and C_sort=S_id and C_mid=$M_id  order by C_top desc,C_order,C_id desc limit 12";
$result = mysqli_query($conn,  $sql);
if (mysqli_num_rows($result) > 0) {
while($row = mysqli_fetch_assoc($result)) {

echo "<a href=\"./?type=courseinfo&id=".$row["C_id"]."\" title=\"".$row["C_title"]."\" class=\"right_p\"><div class=\"col-md-3 col-xs-6 scms-pic\">

<img src=\"".pic($row["C_pic"])."\" width=\"100%\" >
<p style=\"margin:10px 0;\"><b>".$row["C_title"]."</b></p><p><span style=\"font-weight:bold;text-align:left;color:#ff0000\">￥".p($row["C_price"])."</span>  <span style=\"color: #999999;float:right;\">已售：".$row["C_sold"]."件</span></p>
</div></a>";
                }
            }else{
            	echo "暂未发布课程";
            }

?>
			    </div>

</div>

	</div>


</div>
<div class="panel panel-info">
			    <div class="panel-heading">
			        <h3 class="panel-title">
			            版权文字
			        </h3>
			    </div>
			    <div class="panel-body" >
			    	<?php
			    	echo $C_copyright." ".$C_beian." ".$C_code;
			    	?>
			    </div>

</div>
</div>
<script type="text/javascript">

<?php
if(!ismobile()){
	echo "\$(document).ready(function(){dropdownOpen();});";
}
?>

function dropdownOpen() {
 var $dropdownLi = $('li.dropdown');
 $dropdownLi.mouseover(function() {
 $(this).addClass('open');
 }).mouseout(function() {
 $(this).removeClass('open');
 });
}

$(".carousel-indicators").find("li:first").attr("class","active");
$(".carousel-inner").find("div:first").attr("class","item active");
$(".scms-pic").attr("style","float: none;display:inline-block;vertical-align:top;");
</script>
<script src="conn/f.php?action=collection"></script>
</body>
</html>