<?php
require 'conn/conn.php';
require 'conn/function.php';
$type=$_GET["type"];
$id=intval($_GET["id"]);
$no=intval($_POST["no"]);
if($no<1){
	die("购买数量有误！");
}
$address=intval($_POST["A_address"]);

if($_SESSION["M_id"]==""){
	die("请登录会员帐号后继续购买！");
}else{
	$sql="Select * from sl_member where M_id=".intval($_SESSION["M_id"]);
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
        }else{
            $N_discount=$C_n_discount/10;
            $P_discount=$C_p_discount/10;
        }
    }else{
        $M_vip=0;
        $N_discount=1;
        $P_discount=1;
    }
}

if($type=="addcart"){
	$sql="select * from sl_product where P_del=0 and P_id=".$id;
	$result = mysqli_query($conn, $sql);
	  $row = mysqli_fetch_assoc($result);
	  if (mysqli_num_rows($result) > 0) {
	    $P_title=$row["P_title"];
	    $P_pic=splitx($row["P_pic"],"|",0);
	    $P_sell=$row["P_sell"];
	    $P_selltype=$row["P_selltype"];
	    $P_mid=$row["P_mid"];
	    $P_vip=$row["P_vip"];
	    $P_price=$row["P_price"];
	    $P_rest=$row["P_rest"];
	    $P_gg=$row["P_gg"];
	    $P_ggsell=$row["P_ggsell"];

	    $P_viponly=$row["P_viponly"];
	    
    	if($row["P_limit"]>0 and $row["P_limitlong"]>0 and $row["P_limitlong"]-(time()-strtotime($row["P_limittime"]))/3600>0){
          $P_price=$row["P_limit"];
        }else{
          $P_price=$row["P_price"];
        }
            
        if($P_viponly==1 && $M_vip==0){
        	die("本商品仅限VIP用户购买！");
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
            $P_price=p($price*$P_discount);
        }else{
            $P_price=p($price);
        }

	    if($P_mid==$M_id){
	    	die("无法购买自己的商品");
	    }

	    if($P_price==0){
	    	die("免费商品不支持加入购物车，请点击立即购买");
	    }

		if($no>1 && $P_selltype==0){
		 	die("该商品每次只可买一件！");
		}

	  }else{
	  	die("该商品未找到！");
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
				  		$O_address=getrs("select * from sl_member where M_id=".intval($M_id),"M_email");
				  	break;
				  	case 1:
				  		$gg_rest=getrs("select count(C_id) as C_count from sl_card where C_sort=".intval(splitx(splitx($P_ggsell,"\n",$z),"|",1))." and C_use=0 and C_del=0","C_count");
				  		$O_address=getrs("select * from sl_member where M_id=".intval($M_id),"M_email");
				  	break;
				  	case 2:
				  		$gg_rest=$P_rest;
						if($address==0){
					  		$A_id=getrs("select * from sl_address where A_mid=".intval($M_id)." order by A_default desc","A_id");
					  		if($A_id!=""){
					  			$O_address=getrs("select * from sl_address where A_id=".$A_id,"A_address")." ".getrs("select * from sl_address where A_id=".$A_id,"A_name")." ".getrs("select * from sl_address where A_id=".$A_id,"A_phone");
					  		}else{
					  			die("请先完善您的收货信息");
					  		}
					  	}else{
					  		$O_address=getrs("select * from sl_address where A_id=".$address,"A_address")." ".getrs("select * from sl_address where A_id=".$address,"A_name")." ".getrs("select * from sl_address where A_id=".$address,"A_phone");
					  	}
				  	break;
				}
			}
		}
	}else{

	    switch ($P_selltype) {
			case 0:
			$gg_rest=999999999;
			$O_address=getrs("select * from sl_member where M_id=".intval($M_id),"M_email");
			break;
			case 1:
			$gg_rest=getrs("select count(C_id) as C_count from sl_card where C_sort=".intval($P_sell)." and C_use=0 and C_del=0","C_count");
			$O_address=getrs("select * from sl_member where M_id=".intval($M_id),"M_email");
			break;
			case 2:
			$gg_rest=$P_rest;
			if($address==0){
		  		$A_id=getrs("select * from sl_address where A_mid=".intval($M_id)." order by A_default desc","A_id");
		  		if($A_id!=""){
		  			$O_address=getrs("select * from sl_address where A_id=".$A_id,"A_address")." ".getrs("select * from sl_address where A_id=".$A_id,"A_name")." ".getrs("select * from sl_address where A_id=".$A_id,"A_phone");
		  		}else{
		  			die("请先完善您的收货信息");
		  		}
		  	}else{
		  		$O_address=getrs("select * from sl_address where A_id=".$address,"A_address")." ".getrs("select * from sl_address where A_id=".$address,"A_name")." ".getrs("select * from sl_address where A_id=".$address,"A_phone");
		  	}
			break;
		}
	}
	if($gg_rest>0){
		  mysqli_query($conn, "insert into sl_orders(O_pid,O_mid,O_time,O_type,O_price,O_num,O_content,O_title,O_pic,O_address,O_state,O_genkey,O_sellmid,O_gg) values($id,$M_id,'".date('Y-m-d H:i:s')."',0,".$P_price.",$no,'','$P_title','$P_pic','$O_address',0,'".gen_key(20)."',$P_mid,'$sc')");
		die("success");
	}else{
		die("库存不足，请联系客服补货！");
	}
}
?>