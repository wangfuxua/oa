<?
class BgsbModel extends Model {
	
	var $_validate=array(
	    array('MC','require','资产不能为空！')

	);
	
	
	/**
	 * 	    array('SL','is_number','数量错误',1,'function'),
	    array('DJ','is_number','单价错误',1,'function'),
	    array('YJSYNX','is_number','预计使用年限错误',1,'function'),
	    array('KSSYRQ','is_date','开始使用日期格式不对，应形如 1999-1-2',1,'function')
	 * */
}

?>