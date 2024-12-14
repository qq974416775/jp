<?php
require 'conn/conn.php';
require 'conn/function.php';

$type=$_GET["type"];
$id=intval($_GET["id"]);
$page=$_GET["page"];
$address=intval($_POST["A_address"]);

if($_SESSION["M_id"]==""){
	$M_id=1;
}else{
	$M_id=intval($_SESSION["M_id"]);
}
$num=intval($_POST["no"]);
if($num<1){
    $num=1;
}
if(isMobile()){
	$client="手机端";
}else{
	$client="电脑端";
}

$no = date("YmdHis");
$genkey=t($_REQUEST["genkey"]);
if($genkey=="" || $C_cache==1){
	$genkey=date("YmdHis");
}
$sql="Select * from sl_member where M_id=".intval($M_id);
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($result);
$M_id=$row["M_id"];
$M_email=$row["M_email"];
$M_money=$row["M_money"];
$M_viptime=$row["M_viptime"];
$M_viplong=$row["M_viplong"];

if($M_viplong-(time()-strtotime($M_viptime))/86400>0){
    $M_vip=1;
    if($M_viplong>30000){
        $N_discount=$C_n_discount2/10;
        $P_discount=$C_p_discount2/10;
        $C_discount=$C_c_discount2/10;
        $M_svip=1;
    }else{
        $N_discount=$C_n_discount/10;
        $P_discount=$C_p_discount/10;
        $C_discount=$C_c_discount/10;
        $M_svip=0;
    }
}else{
    $M_vip=0;
    $M_svip=0;
    $N_discount=1;
    $P_discount=1;
    $C_discount=1;
}

if($C_buylimit>0){
	if($C_viplimit==1){
		if($M_vip==1){
			if(time()-intval($_SESSION["buylimit"])<60*$C_buylimit){
				box("管理员设定了VIP购买间隔，请".(60*$C_buylimit+intval($_SESSION["buylimit"])-time())."秒后再试！","back","error");
			}
		}
	}else{
		if(time()-intval($_SESSION["buylimit"])<60*$C_buylimit){
			box("管理员设定了购买间隔，请".(60*$C_buylimit+intval($_SESSION["buylimit"])-time())."秒后再试！","back","error");
		}
	}
}

if($type=="courseinfo"){
	if($_SESSION["M_id"]!=""){
		$sql="select * from sl_course where C_id=".$id;
	    $result = mysqli_query($conn, $sql);
	    $row = mysqli_fetch_assoc($result);

	    if($page=="all"){
	    	$money=$row["C_price"]*$C_discount;
	    	$C_title=$row["C_title"]."-全套课程";
	    }else{
		    $lession=explode("||",$row["C_lesson"]);
			for($i=0;$i<count($lession);$i++){
				if(strpos($lession[$i],"_")!==false){
					$l=$l.$lession[$i]."||";
				}
			}

	    	$money=splitx(splitx($l,"||",($page-1)),"__",1)*$C_discount;
	    	$C_title=$row["C_title"]."-".splitx(splitx($l,"||",($page-1)),"__",0);;
	    }
	    
	    $C_pic=$row["C_pic"];
	    $C_mid=$row["C_mid"];

		if(getrs("select O_id from sl_orders where O_genkey='$genkey'","O_id")==""){//判断订单是否已存在
			mysqli_query($conn, "insert into sl_orders(O_cid,O_mid,O_time,O_type,O_price,O_num,O_title,O_pic,O_state,O_address,O_content,O_genkey,O_sellmid,O_ip,O_client) values($id,$M_id,'".date('Y-m-d H:i:s')."',2,$money,1,'$C_title','$C_pic',0,'','$page','$genkey',$C_mid,'".getip()."','".$client."')");
		}
		if($C_buylimit>0){
			$_SESSION["buylimit"]=time();
		}
		die("<script>window.location.href='member/unlogin.php?genkey=$genkey';</script>");
	}else{
		box("请先登录会员帐号！","member/login.php?from=".urlencode("../?type=".$type."&id=".$id."&genkey=".$genkey."&page=".$page),"error");
	}
}

if($type=="newsinfo"){
	$sql="select * from sl_news where N_id=".$id;
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($result);

    if($row["N_vip"]==1){
        $money=p($row["N_price"]*$N_discount);
    }else{
        $money=p($row["N_price"]);
    }
    
    $N_title=$row["N_title"];
    $N_pic=$row["N_pic"];
    $N_mid=$row["N_mid"];
    $N_unlogin=$row["N_unlogin"];

    if($N_unlogin==0 && $_SESSION["M_id"]==""){
    	box("请先登录会员帐号！","member/login.php?from=".urlencode("../?type=".$type."&id=".$id),"error");
    }else{
	    if($N_mid>0 && $N_mid==$_SESSION["M_id"]){
	    	box("无法购买自己的文章！","back","error");
	    }
		if(getrs("select O_id from sl_orders where O_genkey='$genkey'","O_id")==""){//判断订单是否已存在
			mysqli_query($conn, "insert into sl_orders(O_nid,O_mid,O_time,O_type,O_price,O_num,O_title,O_pic,O_state,O_address,O_content,O_genkey,O_sellmid,O_ip,O_client) values($id,$M_id,'".date('Y-m-d H:i:s')."',1,$money,1,'$N_title','$N_pic',0,'','$genkey','$genkey',$N_mid,'".getip()."','".$client."')");
		}
		if($C_buylimit>0){
			$_SESSION["buylimit"]=time();
		}
		die("<script>window.location.href='member/unlogin.php?genkey=$genkey';</script>");
    }
}

if($type=="productinfo"){
	$P_taobao=getrs("select * from sl_product where P_id=".$id,"P_taobao");
	if(splitx($P_taobao,"|",0)!=""){
		die("<script>window.location.href='".splitx($P_taobao,"|",0)."';</script>");
	}else{
	    $sql="select * from sl_product where P_id=".$id;
        $result = mysqli_query($conn, $sql);
        $row = mysqli_fetch_assoc($result);
        $subject=mb_substr($row["P_title"],0,10,"utf-8")."...-购买";
        $P_title=t($row["P_title"]);
        $P_pic=splitx($row["P_pic"],"|",0);
        $P_mid=$row["P_mid"];
        $P_gg=$row["P_gg"];
        $P_ggsell=$row["P_ggsell"];
        $P_rest=$row["P_rest"];
        $P_unlogin=$row["P_unlogin"];
        if($P_unlogin==1 || $_SESSION["M_id"]!=""){

	        if($row["P_limit"]>0 and $row["P_limitlong"]>0 and $row["P_limitlong"]-(time()-strtotime($row["P_limittime"]))/3600>0){
              $P_price=$row["P_limit"];
            }else{
              $P_price=$row["P_price"];
            }

	        $P_sell=$row["P_sell"];
	        $P_selltype=$row["P_selltype"];
	        $P_viponly=$row["P_viponly"];
	        $P_viponly2=$row["P_viponly2"];

			if($P_viponly2==1 && $M_svip==0){
	        	box("本商品仅限永久VIP用户购买！","back","error");
	        }
	        if($P_viponly==1 && $M_vip==0){
	        	box("本商品仅限VIP用户购买！","back","error");
	        }

	        if($P_mid>0 && $P_mid==$_SESSION["M_id"]){
	        	box("无法购买自己的商品！","back","error");
	        }

		    $sp=0;
		    $sq=1;
			foreach ($_POST as $x=>$value) {
				if(splitx($x,"_",0)=="scvvvvv"){
					$sc=$sc.splitx(splitx(splitx($P_gg,"@",splitx($x,"_",1)),"_",1),"|",$_POST[$x])."|";
					if(substr(splitx(splitx(splitx($P_gg,"@",splitx($x,"_",1)),"_",2),"|",$_POST[$x]),0,1)=="*"){
						$sq=$sq*substr(splitx(splitx(splitx($P_gg,"@",splitx($x,"_",1)),"_",2),"|",$_POST[$x]),1);
					}else{
						$sp=$sp+splitx(splitx(splitx($P_gg,"@",splitx($x,"_",1)),"_",2),"|",$_POST[$x]);
					}
				}
			}
			$sc=substr($sc,0,strlen($sc)-1);
			if($sc==""){
				$sc="标配";
			}

			$price=($P_price+$sp)*$sq;

	        if($row["P_vip"]==1){
	            $money=p($price*$P_discount);
	        }else{
	            $money=p($price);
	        }

	        if($P_ggsell!=""){
            	$O_gg=splitx($sc,"|",0);
				$gg=splitx(splitx($P_gg,"@",0),"_",1);
				$gg=explode("|",$gg);
				for($z=0;$z<count($gg);$z++){
					if($O_gg==$gg[$z]){
						switch(splitx(splitx($P_ggsell,"\n",$z),"|",0)){
						  	case 0:
						  		$gg_rest=999999999;
						  	break;
						  	case 1:
						  		$gg_rest=getrs("select count(C_id) as C_count from sl_card where C_sort=".intval(splitx(splitx($P_ggsell,"\n",$z),"|",1))." and C_use=0 and C_del=0","C_count");
						  	break;
						  	case 2:
						  		$gg_rest=$P_rest;
						  	break;
						}
					}
				}
        	}else{
			    switch ($P_selltype) {
					case 0:
					$gg_rest=999999999;
					break;
					case 1:
					$gg_rest=getrs("select count(C_id) as C_count from sl_card where C_sort=".intval($P_sell)." and C_use=0 and C_del=0","C_count");
					break;
					case 2:
					$gg_rest=$P_rest;
					break;
				}
        	}
        	if($gg_rest>0){
        		if(getrs("select O_id from sl_orders where O_genkey='$genkey'","O_id")==""){//判断订单是否已存在
        			mysqli_query($conn, "insert into sl_orders(O_pid,O_mid,O_time,O_type,O_price,O_num,O_content,O_title,O_pic,O_address,O_state,O_genkey,O_sellmid,O_gg,O_ip,O_client) values($id,$M_id,'".date('Y-m-d H:i:s')."',0,$money,$num,'','$P_title','$P_pic','',0,'$genkey',$P_mid,'$sc','".getip()."','".$client."')");
        		}else{
        			mysqli_query($conn, "update sl_orders set O_pid=$id,O_mid=$M_id,O_price=$money,O_num=$num,O_title='$P_title',O_pic='$P_pic',O_state=0,O_sellmid=$P_mid,O_gg='$sc' where O_genkey='$genkey'");
        		}
        		if($C_buylimit>0){
        			$_SESSION["buylimit"]=time();
        		}
				die("<script>window.location.href='member/unlogin.php?genkey=$genkey&address=$address';</script>");
        	}else{
        		box("库存不足，请联系客服补货！","back","error");
        	}
		}else{
			box("请先登录会员帐号！","member/login.php?from=".urlencode("../?type=".$type."&id=".$id),"error");
		}
	}
}
?>