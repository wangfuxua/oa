<?php

// +----------------------------------------------------------------------
// | 十里年华学生社区                                                         
// +----------------------------------------------------------------------
// | Copyright (c) 2008 http://www.freestu.net All rights reserved.      
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: solosky <solosky772@qq.com>                                  
// +----------------------------------------------------------------------
// $Id$
/**
 +------------------------------------------------------------------------------
 * 分类管理
 +------------------------------------------------------------------------------
 * @category   Com
 * @package  Com
 * @subpackage  API
 * @author    solosky <solosky772@qq.com>
 * @version   $Id$
 +------------------------------------------------------------------------------
 */

/*
分类表的结构，可以根据需要添加其他字段
+--------+---------------------+------+-----+---------+----------------+----------+-------------------+
| Field  | Type                | Null | Key | Default | Extra          | Required | Intro             |
+--------+---------------------+------+-----+---------+----------------+----------+-------------------+
| id     | tinyint(4) unsigned | NO   | PRI | NULL    | auto_increment | YES      | 分类的主键        |
| title  | varchar(255)        | NO   |     |         |                | YES      | 分类的标题        |
| pid    | tinyint(4)          | NO   |     |         |                | YES      | 上级分类ID        |
| level  | tinyint(4)          | NO   |     |         |                | YES      | 分类的级别        |
| intro  | varchar(500)        | YES  |     | NULL    |                | NO       | 分类的详细介绍    |
| status | tinyint(1)          | NO   |     | 1       |                | NO       | 分类的状态        |
+--------+---------------------+------+-----+---------+----------------+----------+-------------------+
*/

class Sort extends Base
{	
	//填充标题前的字符，方便显示是那个级别的分类
	protected $padding=array(
						0=>"※",
						1=>"&nbsp;&nbsp;■",
						2=>"&nbsp;&nbsp;&nbsp;&nbsp;◆",
						3=>"&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;▲",
						4=>"&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;★",
						5=>"&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;◎");

	//分类的数据表模型
	private $model;
	//分类的列表
	private $list=array();
	//错误信息  
	private $error="";
	

	 /**
     +----------------------------------------------------------
     * 架构函数
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param mixed $model 分类表的数据模型实例或者模型名
     +----------------------------------------------------------
     */
	 public function __construct($model)
	{
		//判断参数类型
		if(is_string($model)){
			if(!$this->model=D($model))
				throw_exception($model."模型不存在！");
		}
		elseif(is_object($model))
			$this->model=&$model;
		else{
			throw_exception("参数错误！");
		}
	}


	 /**
     +----------------------------------------------------------
     * 返回对应的模型
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param void
     +----------------------------------------------------------
     * @return Model Object 当前分类表的数据模型
     +----------------------------------------------------------
     */
	public function getModel()
	{
		return $this->model;
	}
	
     /**
     +----------------------------------------------------------
     * 查询路径
     +----------------------------------------------------------
     * @access private
     +----------------------------------------------------------
     * @param integer $sid  分类的ID
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     */
	 private function _searchPath($sid)
	{
		
		//检查参数
		if(!$sid){
			return false;
		}
		//初始化对象，查找上级Id；
		$rs=$this->model->find($sid);
		//填充分类前的字符
		$rs["fulltitle"]=$this->padding[$rs["level"]].$rs["title"];
		//保存结果
		$this->list[]=$rs;

		$this->_searchPath($rs['pid']);
	}

	 /**
     +----------------------------------------------------------
     * 根据给定的分类id，查询分类路径
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param integer $sid  分类的ID
     +----------------------------------------------------------
     * @return array 分类的路径
     +----------------------------------------------------------
     */

	public function getPath($sid)
	{
		//检查参数
		if(!$this->_checkSID($sid))
			return false;
		//检查模型
		if(!$this->_checkModel())
			return false;
		
		//查询分类路径
		$this->_searchPath($sid);

		return array_reverse($this->list);
	}

     /**
     +----------------------------------------------------------
     * 查询所有的分类列表
     +----------------------------------------------------------
     * @access private
     +----------------------------------------------------------
     * @param integer $sid  分类的ID
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     */
	private function _searchList($sid)
	{	//查询条件为上级分类ID为给定的ID
		$map=array("pid"=>$sid);
		$sorts=$this->model->findAll($map);

		if(!$sorts)	//如果结果为空，退出递归
			return;
		else{
			foreach ($sorts as $sort){
				//设置含有填充的标题
				$sort["fulltitle"]=$this->padding[$sort["level"]].$sort["title"];
				//保存当前结果
				$this->list[]=$sort;
				//递归继续查询下级分类
				$this->_searchList($sort["id"]);
			}
		}
	}
     /**
     +----------------------------------------------------------
     * 返回所有分类
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param void
     +----------------------------------------------------------
     * @return array 所有分类
     +----------------------------------------------------------
     */
	public function getList()
	{	if(!$list=S($this->_getCacheName())){
			//检查模型
			if(!$this->_checkModel())
				return false;
			
			//查询所有分类，给定参数0为第一级分类的上级ID（PID）
			$this->_searchList(0);
			//返回结果
			$list=&$this->list;
			//缓存结果
			S($this->_getCacheName(),$list);
		}
		else
			$this->list=$list;

		return $list;

	}
	

     /**
     +----------------------------------------------------------
     * 返回给定分类ID的所有子分类
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param integer $sid  分类的ID
     +----------------------------------------------------------
     * @return array 子分类的数组
     +----------------------------------------------------------
     */
	public function getSubList($sid)
	{		
		//检查参数
		if(!$this->_checkSID($sid))
			return false;
		//查询下级分类
		$this->_searchList($sid);
		//返回结果
		return $this->list;
	}

     /**
     +----------------------------------------------------------
     * 返回给定分类ID的所有子分类的ID
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param integer $sid  分类的ID
     +----------------------------------------------------------
     * @return array 子分类ID的数组
     +----------------------------------------------------------
     */
	public function getSubListID($sid)
	{	
		//检查参数
		if(!$this->_checkSID($sid))
			return false;
		//查询下级分类
		$this->_searchList($sid);
		//处理结果，返回一个数组，下标和值均为分类的ID
		$sids=$this->getCols('id,id');
	
		return array_keys($sids);

	}

     /**
     +----------------------------------------------------------
     * 返回结果集的两行
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param string $cols 字段的名字
     +----------------------------------------------------------
     * @return array 结果集的两行
     +----------------------------------------------------------
     */
	public function getCols($cols)
	{
		if(!$this->_checkList())
			return false;

		return $this->model->getCols($this->list,$cols);
	}
	
     /**
     +----------------------------------------------------------
     * 根据一个字段查询分类
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param string $field 字段的名字
     * @param string $value 字段的值
     +----------------------------------------------------------
     * @return array 查询结果
     +----------------------------------------------------------
     */
	public function getBy($field,$value)
	{
		return $this->model->getBy($field,$value);
	}

     /**
     +----------------------------------------------------------
     * 清除缓存
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param  void
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     */
	public function clearCache()
	{
		S($this->_getCacheName(),null);
	}


     /**
     +----------------------------------------------------------
     * 数据管理::添加分类
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param  void
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     */
	public function add()
	{
		//检查模型
		if(!$this->_checkModel())
			return false;
		//上级分类ID
		$level=1;
		$pid=empty($_REQUEST['pid'])?0:$_REQUEST['pid'];
		//查询上级ID
		if($pid&&$ps=$this->model->find($_REQUEST['pid'])){
			//如果查询成功,设置上级ID和分类级别
			$level=intval($ps['level'])+1;
			//如果查询失败，默认为顶级分类
		}
		//创建数据
		if(!$this->model->create()){
			$this->error=$this->model->getError();
			return false;
		}
		//设置上级分类ID和级别
		$this->model->pid = $pid;
		$this->model->level = $level;

		//调用前置函数
		if(!$this->_before_add($model))
			return false;

		//添加数据
		if(!$sid=$this->model->add()){
			$this->error=$this->model->getError();
			return false;
		}

		//调用后置函数
		return $this->_after_add($model,$sid);
	}
	//新增的前置和后置函数
	protected function _before_add(&$model){return true;}
	protected function _after_add(&$model,$sid){return true;}

     /**
     +----------------------------------------------------------
     * 数据管理::读取分类
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param integer $sid  分类的ID
     +----------------------------------------------------------
     * @return array 结果集数组
     +----------------------------------------------------------
     */
	public function read($sid)
	{
		return $this->model->find($sid);
	}

     /**
     +----------------------------------------------------------
     * 数据管理::更新分类
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param  void
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     */
	public function update()
	{
		//检查模型
		if(!$this->_checkModel())
			return false;
		//上级分类ID
		$level=1;
		$pid=empty($_REQUEST['pid'])?0:$_REQUEST['pid'];
		//查询上级ID
		if($pid&&($ps=$this->model->find($_REQUEST['pid']))){
			//如果查询成功,设置上级ID和分类级别
			$level=intval($ps['level'])+1;
			//如果查询失败，默认为顶级分类
		}
		//创建数据
		if(!$this->model->create()){
			$this->error=$this->model->getError();
			return false;
		}
		//设置上级分类ID和级别
		$this->model->pid = $pid;
		$this->model->level = $level;

		//调用前置函数
		if(!$this->_before_update($model))
			return false;

		//修改当前的数据
		if(!$this->model->save()){
			$this->error=$this->model->getError();
			return false;
		}

		//调用后置函数
		return $this->_after_update($model);
	}
//更新的前置和后置函数
	protected function _before_update(&$model){return true;}
	protected function _after_update(&$model){return true;}


     /**
     +----------------------------------------------------------
     * 数据管理::删除分类
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param integer $sid    分类的ID
     * @param integer $delSub 是否删除下级分类
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     */
	public function delete($sid,$delSub=true)
	{
		if(!$this->_checkModel()||!$this->_checkSID($sid))
			return false;
		
		if(!$this->_before_delete($model))
			return false;

		if($delSub){
			//先删除下级分类
			$ids=$this->getSubListID($sid);
			if($ids)
				$this->model->deleteByIds(implode(',',$ids));
		}

		//删除当前分类
		if(!$this->model->deleteById($sid)){
			$this->error=$this->model->getError();
			return false;
		}

		return $this->_after_delete($model);
	}
//更新的前置和后置函数
	protected function _before_delete(&$model){return true;}
	protected function _after_delete(&$model){return true;}
	
     /**
     +----------------------------------------------------------
     * 返回错误
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param void
     +----------------------------------------------------------
     * @return string 错误信息
     +----------------------------------------------------------
     */
	public function getError()
	{
		return $this->error;
	}


     /**
     +----------------------------------------------------------
     * 检查模型是否为空
     +----------------------------------------------------------
     * @access private
     +----------------------------------------------------------
     * @param void
     +----------------------------------------------------------
     * @return boolean
     +----------------------------------------------------------
     */
	private function _checkModel()
	{
		if(!is_object($this->model)){
			$this->error="数据模型为空";
			return false;
		}
		else
			return true;
	}


     /**
     +----------------------------------------------------------
     * 检查结果是否为空
     +----------------------------------------------------------
     * @access private
     +----------------------------------------------------------
     * @param void
     +----------------------------------------------------------
     * @return boolean
     +----------------------------------------------------------
     */
	private function _checkList()
	{
		if(empty($this->list)){
			$this->error="查询结果为空！";
			return false;
		}
		else
			return true;
	}

     /**
     +----------------------------------------------------------
     * 检查分类参数是否为空
     +----------------------------------------------------------
     * @access private
     +----------------------------------------------------------
     * @param void
     +----------------------------------------------------------
     * @return boolean
     +----------------------------------------------------------
     */
	private function _checkSID($sid)
	{
		if(intval($sid))
			return true;
		else{
			$this->error="参数分类ID为空或者无效！";
			return false;
		}
	}

     /**
     +----------------------------------------------------------
     * 返回缓存的名
     +----------------------------------------------------------
     * @access private
     +----------------------------------------------------------
     * @param void
     +----------------------------------------------------------
     * @return string 缓存名
     +----------------------------------------------------------
     */
	private function _getCacheName()
	{
		$module=defined('C_MODULE_NAME')?C_MODULE_NAME:MODULE_NAME;
		return $module."SortList";
	}
}

?>