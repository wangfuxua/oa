<?
function getDiaryTypeName($typeid=1){
	switch ($typeid){
		case 1:
			return "工作日志";
		    break;
		case 2:
			return "个人日志";
			break;
		case 3:
			return "工作周报";
			break;
		case 4:		    	
		    return "工件月报";
		    break;
		case 5:
			return "年度总结";
			break;    
	}
}

?>