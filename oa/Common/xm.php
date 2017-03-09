<?php

function xmsh_userlist($wh_id,$sh,$login_user_id,$return=""){//项目审核时用到
	$op="";
	$dao=new Model();
    $listuser=$dao->table("xmss_lc a")
                  ->join("user b on a.USER_ID=b.USER_ID")
                  ->where("b.WH_ID='$wh_id'")
                  ->findall();
	if ($listuser) {
		foreach ($listuser as $row){
			if(!in_array($row["user_id"],$sh) && $row["user_id"] != $login_user_id)
			   $op .= "<option value=\"".$row["user_id"]."\">".$row["user_name"]."</option>";
		}
	}
	
	if ($return) {
		return $op;
	}else{
        echo $op;
	} 
	
}

?>