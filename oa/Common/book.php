<?
function getTypename($typeid){
	$dao=D("bookType");
	$row=$dao->where("TYPE_ID='$typeid'")->find();
	return $row[TYPE_NAME];
}

?>