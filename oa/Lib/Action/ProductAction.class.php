<?php
class ProductAction extends PublicAction {
/**实物产品操作开始*/
	public function create(){
		$this->display();
	}
	
	public function save(){
		$product = D('Product');
		
		// 设置图片上传验证的大小，后缀类型以及图片类型
		$condition = array('maxSize' => 1024*1024*2, 
							'allowExts' => array('gif', 'jpg', 'jpeg', 'png', 'bmp'), 
							'allowTypes' => array('image/pjpeg', 'image/gif', 'image/x-png', 'image/bmp'));
							
		// 图片上传
		$pic_res = $product->fileinfo_save($condition);
		if(is_string($pic_res) && $pic_res != '没有选择上传文件'){
			$this->error($pic_res);
		}elseif(is_array($pic_res)){
			$_POST['pic_url'] = $pic_res['attachment'];
		}else{
			if($_POST['pic_url']){
				$pic_url = explode("Uploads/", $_POST['pic_url']);
				$_POST['pic_url'] = $pic_url[count($pic_url)-1];
			}else $_POST['pic_url'] = '';
		}
		$_POST['type'] = 1;
		$product->create($_POST);
		$id = $product->add();
		if($id) $this->redirect("view/id/$id");
		else $this->error("创建失败!");
	}
	
	public function view(){
		/*if (!C('FILE_UPLOAD_PATH')) {
			C('FILE_UPLOAD_PATH', '/oa/Uploads');
		}*/
		$id = $_GET['id'];
		if(!$id) $this->redirect('lists');
		$product = D('Product');
		$product_res = $product->find($id);
		if($product_res['pic_url']) $product_res['pic_url'] = FILE_UPLOAD_PATH . '/' .$product_res['pic_url'];
		$this->assign('product_res', $product_res);
		$this->display();
	}

	public function edit(){
		$id = isset($_GET['id']) ? $_GET['id'] : null; 
		$update = isset($_GET['update']) ? $_GET['update'] : null; 
		if (!$id)
			$this->redirect('lists');
		
		$product = D('Product');
		$product_res = $product->find($id);
		if($product_res['pic_url']) $product_res['pic_url'] = FILE_UPLOAD_PATH.'/'.$product_res['pic_url'];
		
		$this->assign('update', $update);
		$this->assign('product_res',$product_res);
		$this->display('create');		
	}
	
	public function update(){
		$id = isset($_POST['product_id']) ? $_POST['product_id'] : null;
		if (null == $id)
			$this->redirect('edit');
		
		/**根据$_POST对指定id数据进行更新*/
		$product = D('Product');
		// 设置图片上传验证的大小，后缀类型以及图片类型
		$condition = array('maxSize' => 1024*1024*2, 
							'allowExts' => array('gif', 'jpg', 'jpeg', 'png', 'bmp'), 
							'allowTypes' => array('image/pjpeg', 'image/gif', 'image/x-png', 'image/bmp'));
							
		// 图片上传
		$pic_res = $product->fileinfo_save($condition);
		if(is_string($pic_res) && $pic_res != '没有选择上传文件'){
			$this->error($pic_res);
		}elseif(is_array($pic_res)){
			$_POST['pic_url'] = $pic_res['attachment'];
		}else{
			if($_POST['pic_url']){
				$pic_url = explode("Uploads/", $_POST['pic_url']);
				$_POST['pic_url'] = $pic_url[count($pic_url)-1];
			}else $_POST['pic_url'] = '';
		}
		if ($product->create()){
			$product->where("id='$id'")->save();
		}
		$this->redirect("view/id/$id");
	}
	
	public function lists(){
		/**字段排序*/
		if($_GET['field']){
			$sort = $_GET['field']." ".$_GET['sort'];
			$def_sort = ($_GET['sort'] == 'asc') ? 'desc' : 'asc';
		}else{
			$sort = 'id desc';
			$def_sort = 'desc';
		}
		$product = D('Product');
		$condition = "type=1";
		$count	= $product->count($condition);
		$rows	=	10;
		$p		=	new Page($count,$rows);
		$list	=	$product->findAll($condition,'*',$sort,$p->firstRow.','.$p->listRows);
		$page	=	$p->show();
		$xml_type = new xml("product_type.xml", 'item');
		
		$this->assign('xml_type',$xml_type->array); // 产品类型记录集
		$this->assign('product_res',$list);
		$this->assign('page',$page);
		$this->assign('sort_url',__URL__."/lists/?&p=".$p->nowPage."&sort=".$def_sort); // 排序操作路径
		if($_GET['to_id']){
			$this->assign('to_id',$_GET['to_id']);
			$this->assign('to_name',$_GET['to_name']);
			$this->display('prolists');	
		}else{
			$this->display();
		}
	}
	
	function search(){
		$product = D('Product');
		$condition = "type=1";
		$cond = $param = '';
		if($_POST){
			/**组合post过来的查询条件$condition和分页url参数$param*/
			foreach($_POST as $key => $value){
				if($value){
					$cond .= $key.' like "%'.$value.'%" and ';
					$param .= $key."=".urlencode($value)."&";
				}
			}
		}
		else if($_GET){
			/**组合get过来的查询条件$condition和分页url参数$param*/
			foreach($_GET as $key => $value){
				if($value && $key != 'a' && $key != 'm' && $key != 'p' && $key != 'field' && $key != 'sort'){
					$cond .= $key.' like "%'.$value.'%" and ';
					$param .= $key."=".urlencode($value)."&";
				}
			}
		}
		$cond = substr($cond,0,-4);
		$param = substr($param,0,-1);
		if($cond)$condition .= " and ".$cond;
		/**字段排序*/
		if($_GET['field']){
			$sort = $_GET['field']." ".$_GET['sort'];
			$def_sort = ($_GET['sort'] == 'asc') ? 'desc' : 'asc';
		}else{
			$sort = 'id desc';
			$def_sort = 'desc';
		}
		
		$count	= $product->count($condition);
		$rows	=	10;
		$p		=	new Page($count,$rows,$param);
		$list	=	$product->findAll($condition,'*',$sort,$p->firstRow.','.$p->listRows);
		$page	=	$p->show();
		$xml_type = new xml("product_type.xml", 'item');
		
		$this->assign('xml_type',$xml_type->array); // 来源记录集
		$this->assign('product_res',$list);
		$this->assign('page',$page);
		$this->assign('sort_url',__URL__."/search/?&p=".$p->nowPage."&".$param."&sort=".$def_sort); // 排序操作路径
		if($_GET['to_id']){
			$this->assign('to_id',$_GET['to_id']);
			$this->assign('to_name',$_GET['to_name']);
			$this->display('prolists');	
		}else{
			$this->display('lists');
		}	
	}
	
	public function del(){
		//获取传递过来要删除的id数组
		$ids_array = isset($_POST["mass"]) ? $_POST["mass"] : null;
		$id = isset($_POST["id"]) ? $_POST["id"] : null;
		if (null == $ids_array && null == $id){
			$this->redirect('lists');
		}
		//将id数组组合成字符串
		if ($id)
			$ids_string = $id;
		else
			$ids_string = join(',', $ids_array);
		$product = D('Product');
		$product->delete("id in($ids_string)");
		$this->redirect('lists');
	}
/**实物产品操作结束*/

/**服务产品操作开始*/
	public function screate(){
		$this->display();
	}
	
	public function ssave(){
		$product = D('Product');
		$_POST['type'] = 2;
		$product->create($_POST);
		$id = $product->add();
		if($id) $this->redirect("sview/id/$id");
		else $this->error("创建失败!");
	}
	
	public function sview(){
		$id = $_GET['id'];
		if(!$id) $this->redirect('slists');
		$product = D('Product');
		$product_res = $product->find($id);
		$this->assign('product_res', $product_res);
		$this->display();
	}

	public function sedit(){
		$id = isset($_GET['id']) ? $_GET['id'] : null; 
		$update = isset($_GET['update']) ? $_GET['update'] : null; 
		if (!$id)
			$this->redirect('slists');
		
		$product = D('Product');
		$product_res = $product->find($id);
		
		$this->assign('update', $update);
		$this->assign('product_res',$product_res);
		$this->display('screate');		
	}
	
	public function supdate(){
		$id = isset($_POST['product_id']) ? $_POST['product_id'] : null;
		if (null == $id)
			$this->redirect('sedit');
		
		/**根据$_POST对指定id数据进行更新*/
		$product = D('Product');
		if ($product->create()){
			$product->where("id='$id'")->save();
		}
		$this->redirect("sview/id/$id");
	}
	
	public function slists(){
		/**字段排序*/
		if($_GET['field']){
			$sort = $_GET['field']." ".$_GET['sort'];
			$def_sort = ($_GET['sort'] == 'asc') ? 'desc' : 'asc';
		}else{
			$sort = 'id desc';
			$def_sort = 'desc';
		}
		$product = D('Product');
		$condition = "type=2";
		$count	= $product->count($condition);
		$rows	=	10;
		$p		=	new Page($count,$rows);
		$list	=	$product->findAll($condition,'*',$sort,$p->firstRow.','.$p->listRows);
		$page	=	$p->show();
		
		$this->assign('product_res',$list);
		$this->assign('page',$page);
		$this->assign('sort_url',__URL__."/slists/?&p=".$p->nowPage."&sort=".$def_sort); // 排序操作路径
		if($_GET['to_id']){
			$this->assign('to_id',$_GET['to_id']);
			$this->assign('to_name',$_GET['to_name']);
			$this->display('serlists');	
		}else{
			$this->display();
		}	
	}
	
	function ssearch(){
		$product = D('Product');
		$condition = "type=2";
		$cond = $param = '';
		if($_POST){
			/**组合post过来的查询条件$condition和分页url参数$param*/
			foreach($_POST as $key => $value){
				if($value){
					$cond .= $key.' like "%'.$value.'%" and ';
					$param .= $key."=".urlencode($value)."&";
				}
			}
		}
		else if($_GET){
			/**组合get过来的查询条件$condition和分页url参数$param*/
			foreach($_GET as $key => $value){
				if($value && $key != 'a' && $key != 'm' && $key != 'p' && $key != 'field' && $key != 'sort'){
					$cond .= $key.' like "%'.$value.'%" and ';
					$param .= $key."=".urlencode($value)."&";
				}
			}
		}
		$cond = substr($cond,0,-4);
		$param = substr($param,0,-1);
		if($cond)$condition .= " and ".$cond;
		/**字段排序*/
		if($_GET['field']){
			$sort = $_GET['field']." ".$_GET['sort'];
			$def_sort = ($_GET['sort'] == 'asc') ? 'desc' : 'asc';
		}else{
			$sort = 'id desc';
			$def_sort = 'desc';
		}
		
		$count	= $product->count($condition);
		$rows	=	10;
		$p		=	new Page($count,$rows,$param);
		$list	=	$product->findAll($condition,'*',$sort,$p->firstRow.','.$p->listRows);
		$page	=	$p->show();
		
		$this->assign('product_res',$list);
		$this->assign('page',$page);
		$this->assign('sort_url',__URL__."/ssearch/?&p=".$p->nowPage."&".$param."&sort=".$def_sort); // 排序操作路径
		if($_GET['to_id']){
			$this->assign('to_id',$_GET['to_id']);
			$this->assign('to_name',$_GET['to_name']);
			$this->display('serlists');	
		}else{
			$this->display('slists');
		}	
	}
	
	public function sdel(){
		//获取传递过来要删除的id数组
		$ids_array = isset($_POST["mass"]) ? $_POST["mass"] : null;
		$id = isset($_POST["id"]) ? $_POST["id"] : null;
		if (null == $ids_array && null == $id){
			$this->redirect('slists');
		}
		//将id数组组合成字符串
		if ($id)
			$ids_string = $id;
		else
			$ids_string = join(',', $ids_array);
		$product = D('Product');
		$product->delete("id in($ids_string)");
		$this->redirect('slists');
	}
/**服务产品操作结束*/
}
?>