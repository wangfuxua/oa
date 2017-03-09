<?php
/**
* *
* xml.class.php 
* 作 用： xml读写类,暂只支持三级节点 
* author： 贺辽平 
* date: 2009-02-27
*/
define('XML_FILE_SAVE',APP_PATH."/Xml/");

class xml{
	var $file; // 要读取的XML文件 
	var $root_item; // 顶层元素
	var $child_item; // 节点 
	var $parser; // 剖析器 
	var $vals; // 属性 
	var $index; // 索引 
	var $child_item_array;// 节点数组 
	var $array; // 下级节点的数组 
	var $result; // 返回的结果 
	var $querys;
	
	function xml($file,$child_item){
		if (!is_dir(XML_FILE_SAVE)) {
			mkdir(APP_PATH . '/Xml/', 0777);
		}

		$this->file=XML_FILE_SAVE.$file;
		$this->root_item="document";
		$this->child_item=$child_item;
		$data=$this->ReadXml($this->file);
		if(!$data) die( "无法读取 $this->file");
		
		$this->parser = xml_parser_create();
		xml_parser_set_option($this->parser,XML_OPTION_CASE_FOLDING,0);
		xml_parser_set_option($this->parser,XML_OPTION_SKIP_WHITE,1);
		xml_parse_into_struct($this->parser,$data,$this->vals,$this->index); 
		xml_parser_free($this->parser);
		//遍历索引，筛选出要取值的节点 节点名:$child_item 
		foreach ($this->index as $key=>$val) {
			if ($key == $this->child_item) $this->child_item_array = $val; 
			else continue; 
		} 
		for ($i=0; $i < count($this->child_item_array); $i+=2) {
			$offset = $this->child_item_array[$i] + 1; 
			$len = $this->child_item_array[$i + 1] - $offset;
			//array_slice() 返回根据 offset 和 length 参数所指定的 array 数组中的一段序列。 
			//所取节点下级数组 
			$value=array_slice($this->vals,$offset,$len);
			//取得有效数组，合并为结果数组 
			$this->array[]=$this->parseEFF($value);
		} 
		return true; 
	} 
	//将XML文件读入并返回字符串 
	function ReadXml($file){ 
		// 文件不存在则创建新文件
		if(!file_exists($file)){
			$xml.= "<?xml version=\"1.0\" encoding=\"utf-8\"?>\r\n";
			$xml.= "<$this->root_item>\r\n";
			$xml.= "</$this->root_item>";
			$fp=@fopen($this->file,"w");
			chmod($file, 0777);
			if(flock($fp, LOCK_EX)){ 
				rewind($fp); 
				fputs($fp,$xml); 
				flock($fp,LOCK_UN);
			}
			fclose($fp); 
		}
		return file_get_contents($file); 
	} 
	//取得有效数组 
	function parseEFF($effective) { 
		for ($i=0; $i < count($effective); $i++){
			$effect[$effective[$i][ "tag"]] = $effective[$i]["value"];
		} 
		return $effect; 
	} 
	//xml_query(方法,条件,多条件时逻辑运算符and or or,总数据数组,插入或更新的数组) 
	function xml_query($method,$condition,$if='and',$array=array()){ 
		if(($method=='select')||($method=='count')){ 
			return $this->xml_select($method,$condition,$if);
		}elseif($method=='insert') { 
			return $this->xml_insert($condition,$if,$array);
		}elseif($method=='update') { 
			return $this->xml_update($condition,$if,$array);
		} 
	} 
	//取得xml数组 
	function xml_fetch_array($condition,$if){ 
		//$this->querys++;
		$row = $this->array; //初始化数据数组
		if($condition) { 
			//是否有条件,如有条件则生成符合条件的数组 
			//生成条件数组,条件格式 field,operator,match 
			$condition=explode( ",",$condition);//条件数组
			$cs=count($condition)/3; //条件数 
			for($i=0;$i <$cs;$i++){
				$conditions[]=array( "field"=>$condition[$i*3],"operator"=>$condition[$i*3+1],"match"=>$condition[$i*3+2]);
			} 
			//echo count($row); 
			for($r=0;$r <count($row);$r++){
				for($c=0;$c <$cs;$c++){
					//$i++; 
					$condition=$conditions[$c]; //当前条件 
					$field=$condition['field']; //字段 
					$operator=$condition[ "operator"];//运算符
					$match=$condition['match']; //匹配 
					if(($operator=='=') &&($row[$r][$field]==$match)){
						$true++;//若条件符合,符合数加1 
					} elseif(($operator=='!=') &&($row[$r][$field]!=$match)){
						$true++;//若条件符合,符合数加1 
					} elseif(($operator==' <')&&($row[$r][$field]<$match)){
						$true++;//若条件符合,符合数加1 
					} elseif(($operator==' <=')&&($row[$r][$field]<=$match)){
						$true++;//若条件符合,符合数加1 
					} elseif(($operator==' >')&&($row[$r][$field]>$match)){
						$true++;//若条件符合,符合数加1 
					} elseif(($operator==' >')&&($row[$r][$field]>=$match)){
						$true++;//若条件符合,符合数加1 
					} 
				} 
				//根据条件取值 
				if($if=='and'){ 
					if($true==$cs) $result[]=$row[$r]; 
				} else { 
					//如果多条件为or,当有符合纪录时,生成数组 
					if($true!=0) $result[]=$row[$r]; 
				} 
				$true=0;//符合条件数归零,进入下一轮循环 
			} 
		} else { 
			$result=$this->array;
		} 
		return $result; 
	} 
	//筛选或统计 
	function xml_select($method,$condition,$if) 
	{ 
		$result=$this->xml_fetch_array($condition,$if);
		if($method=='select') return $result; 
		else return count($result); 
	
	} 
	//插入数据 
	function xml_insert($condition,$if,$array) 
	{ 
		$data=$this->xml_fetch_array($condition,$if);//总数据数组
		$data[]=$array; //插入后的总数据数组 
		$this->array=$data; //更新总数组
		$this->WriteXml($data);
	} 
	//得到更新的XML并改写 
	function xml_update($condition,$if,$array){ 
		$datas=$this->array; //总数据数组
		$subtract=$this->xml_fetch_array($condition,$if);//要更新的数组
		for($i=0;$i <count($datas);$i++){
			$data=$datas[$i]; 
			foreach($data as $k=>$v){
				if($v==$subtract[0][$k]) $is++; 
			} 
			if($is==count($data)) {
				if(empty($array)) unset($datas[$i]);
				else $datas[$i]=$array; 
			}
			$is=0; 
		} 
		$this->array=array_reverse(array_reverse($datas));
		$this->WriteXml(array_reverse(array_reverse($datas)));
	
	} 
	//写入XML文件(全部写入) 
	function WriteXml($array) 
	{ 
		if(!is_writeable($this->file))die( "无法写入".$this->file);
		$xml.= "<?xml version=\"1.0\" encoding=\"utf-8\"?>\r\n";
		$xml.= "<$this->root_item>\r\n";
		for($i=0;$i <count($array);$i++){
			$xml.= "<$this->child_item>\r\n";
			foreach($array[$i] as $k=>$s){
				$xml.= "<$k>$s</$k>\r\n";
			} 
			$xml.= "</$this->child_item>\r\n";
		} 
		$xml.= "</$this->root_item>";
		//file,"w'>
		$fp=@fopen($this->file,"w");
		if(flock($fp, LOCK_EX)){
			rewind($fp); 
			fputs($fp,$xml); 
			flock($fp, LOCK_UN);
		}
		fclose($fp); 
	} 
	//逐行写入xml 
	function WriteLine($array) 
	{ 
		if(!is_writeable($this->file)){
			die( "无法写入".$this->root_item.".xml");
		} 
		//file,"w'>
		$fp=@fopen($this->file,"w");
		rewind($fp); 
		if(flock($fp, LOCK_EX)){ 
			fputs($fp, "<?xml version=\"1.0\" encoding=\"utf-8\"?>\r\n");
			fputs($fp, "<$this->root_item>\r\n");
			for($i=0;$i <count($array);$i++){
				fputs($fp, "<$this->child_item>\r\n");
				$xml.= "<$this->child_item>\r\n";
				foreach($array[$i] as $k=>$s){
					fputs($fp, "<$k>$s</$k>\r\n");
				} 
				fputs($fp, "</$this->child_item>\r\n");
			} 
			fputs($fp, "</$this->root_item>");
			flock($fp, LOCK_UN);
		}
		fclose($fp); 
	} 
}
?>
