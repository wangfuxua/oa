<?php

function getSysl($DZYH_ID,$SL=0){
	
	$dao=D("Dzyhply");
	$map="DZYH_ID = ".$DZYH_ID." AND SH = 2";
	$LYSL=$dao->count($map);
	
    if($LYSL >= $SL)$color="red";
    else $color="black";
    	
	echo "<font color=\"$color\">".($SL-$LYSL)."</font>";

}

function getSyslNum($DZYH_ID,$SL=0){
	
	$dao=D("Dzyhply");
	$map="DZYH_ID = ".$DZYH_ID." AND SH = 2";
	$LYSL=$dao->count($map);
		
	echo ($SL-$LYSL);

}

/*--------已领用数量----------*/
function check_ylysl($DZYH_ID){
	$dao=D("Dzyhply");
	$row=$dao->where("DZYH_ID = ".$DZYH_ID." AND SH = 2")->field("SUM(LYSL) as YLYSL")->find();
	return intval($row[YLYSL]);
}


function check_sysl($DZYH_ID){
	$dao=D("Dzyh");
	$row1=$dao->where("DZYH_ID = ".$DZYH_ID)->field("SL")->find();
		
	$dao=D("Dzyhply");
	$row2=$dao->where("DZYH_ID = ".$DZYH_ID." AND SH = 2")->field("SUM(LYSL) as YLYSL")->find();
	
	return intval($row1[SL]-$row2[YLYSL]);
}

?>