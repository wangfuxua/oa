// Node object
function Node(id, pid, name, url, title, target, icon, iconOpen, open, cls, obj) {
    this.id = id; 			//表示当前节点的ID       
    this.pid = pid; 			//表示当前节点的父节点的ID,根节点的值为 -1 
    this.name = name; 		//节点要显示的文字
    this.url = url; 			//节点的Url
    this.title = title; 		//鼠标移至该节点时节点的Title
    this.target = target; 	//节点的target  
    this.icon = icon; 		//用做节点的图标,节点没有指定图标时使用默认值
    this.iconOpen = iconOpen; //用做节点打开的图标,节点没有指定图标时使用默认值 
    this._io = open || false; //判断节点是否打开 
    this._is = false; 		//节点选中标识;
    this._ls = false; 		//同级最后节点标识;
    this._hc = false; 		//包含子节点标识;
    this._ai = 0; 			//节点在节点数组中的索引值，初始值为0
    this._p; 				//保存父节点对象;
    this.cls = cls; 			//自定义class
    this.oo = obj;
    if (this.oo) {
        this._userState = this.oo.UserState.Labor + '&' + this.oo.UserState.Spell + '&' + this.oo.UserState.State + '&' + this.oo.UserState.Sex + '&' + name;
    }
};

// Tree object
function sTree(objName) {
	this.config = {
		target				: null,		//所有节点的target   
		folderLinks			: false,	//文件夹可链接
		useSelection		: true,		//节点可被选择(高亮)
		useCookies			: true,		//树可以使用cookies记住状态
		useLines			: false,	//创建带线的树
		useIcons			: true,		//创建带有图标的树 
		useStatusText		: false,	//用节点名替代显示在状态栏的节点url   
		closeSameLevel		: true,		//只有一个有父级的节点可以被展开,当这个函数可用时openAll() 和 closeAll() 函数将不可用
		inOrder				: true		//如果父级节点总是添加在子级节点之前,使用这个参数可以加速菜单显示.   

	}
	this.icon = {
		root				: '/oa/Tpl/default/Public/img/empty.gif',				// 根节点图标
		folder				: '/oa/Tpl/default/Public/img/folder.gif',				// 枝节点文件夹图标
		folderOpen			: '/oa/Tpl/default/Public/img/folderopen.gif',			// 枝节点打开状态文件夹图标
		node				: '/oa/Tpl/default/Public/images/bg_4x7.gif',			// 叶节点图标
		empty				: '/oa/Tpl/default/Public/img/empty.gif',				// 空白图标
		line				: '/oa/Tpl/default/Public/img/line.gif',				// 竖线图标
		join				: '/oa/Tpl/default/Public/img/join.gif',				// 丁字线图标
		joinBottom			: '/oa/Tpl/default/Public/img/joinbottom.gif',			// L线图标
		plus				: '/oa/Tpl/default/Public/img/plus.gif',				// 丁字折叠图标
		plusBottom			: '/oa/Tpl/default/Public/img/plusbottom.gif',			// L折叠图标
		minus				: '/oa/Tpl/default/Public/img/minus.gif',				// 丁字展开图标
		minusBottom			: '/oa/Tpl/default/Public/img/minusbottom.gif',		// L展开图标
		nlPlus				: '/oa/Tpl/default/Public/img/nolines_plus.gif',		// 无线折叠图标
		nlMinus				: '/oa/Tpl/default/Public/img/nolines_minus.gif'		// 无线展开图标
	};
	this.obj = objName;				// 树对象名称(必须一致) 
	this.aNodes = [];				// 节点数组
	this.aIndent = [];				// 当前节点到根节点次级节点(pid==-1)，所有父节点是否是同级节点中的最后一个，如果_ls==true则数组对应元素之为0，反之为1
	this.root = new Node(-1);		// 默认根节点
	this.selectedNode = null;		// 选中节点的id(tree初始化之前)或它在字节数组中的索引值_ai(tree初始化之后)
	this.selectedFound = false;		// true存在选中的节点;false反之
	this.completed = false;			// tree html 文本构造完成

};

// 添加节点到节点数组
sTree.prototype.add = function(id, pid, name, url, title, target, icon, iconOpen, open, cls, obj) {
    this.aNodes[this.aNodes.length] = new Node(id, pid, name, url, title, target, icon, iconOpen, open, cls, obj);
};

// 展开树上所有节点
sTree.prototype.openAll = function() {
	this.oAll(true);
};
//折叠树上所有节点
sTree.prototype.closeAll = function() {
	this.oAll(false);
};

// 生成tree的html字符串
sTree.prototype.toString = function() {

	var str = '<div class="sTree">\n';
	if (document.getElementById) {
		if (this.config.useCookies) this.selectedNode = this.getSelected();
		str += this.addNode(this.root);
	} else str += '浏览器不支持Cookies.';
	str += '</div>';
	if (!this.selectedFound) this.selectedNode = null;
	this.completed = true;
	return str;
};

// 生成节点及其子节点的html字符串
sTree.prototype.addNode = function(pNode) {
	var str = '';
	var n=0;
	
	//默认在整个数组中搜索子节点
	if (this.config.inOrder) n = pNode._ai;		
	
	// 遍历节点数组
	for (n; n<this.aNodes.length; n++) {
		
		// 只处理直接下级节点 
		if (this.aNodes[n].pid == pNode.id) {
			
			// 临时变量
			var cn = this.aNodes[n];
			
			// 设置节点的父节点属性
			cn._p = pNode;
			
			// 设置节点的数组索引属性
			cn._ai = n;
			
			// 设置节点包含子节点标识_hc和同级最后节点标识_ls
			this.setCS(cn);
			
			// 设置节点target 属性 
			if (!cn.target && this.config.target) cn.target = this.config.target;
			
			// 判断一个包含子节点的节点在Cookie中是否是展开状态
			if (cn._hc && !cn._io && this.config.useCookies) cn._io = this.isOpen(cn.id);
			
			// 判断是否允许包含子节点的节点带有超链接地址
			if (!this.config.folderLinks && cn._hc) cn.url = null;
			
			// 判断节点是否被选中
			if (this.config.useSelection && cn.id == this.selectedNode && !this.selectedFound) {
				
					// 初始化节点选中标志
					cn._is = true;
					
					// 从这里开始this.selectedNode值由id变为_ai(节点数组索引)
					this.selectedNode = n;
					
					// 初始化tree的选中标志
					this.selectedFound = true;
			}
			str += this.node(cn, n);
			
			// 判断本级最后一个节点，结束循环
			if (cn._ls) break;
		}
	}
	return str;
};

/* 
 * 生成节点的html字符串
 * @param node 节点对象;
 * @param nodeId 节点在节点数组中的索引值;
 */

sTree.prototype.node = function(node, nodeId) {
	var onCls,menuCls,onID;
	if(node.url){
		onCls=((this.config.useSelection) ? ((node._is ? 'menuOn' : '')) : '');
	}else{
		onCls='';
	}
	if(node.cls){
		menuCls=node.cls;
	}else{
		menuCls='';
	}
	
	// 节点前的线条或空白图标 
	var str = '<div class="sTreeNode ' + onCls + ' ' + menuCls + '">' + this.indent(node, nodeId);
	
	// 根据节点类型和状态确定节点的默认图标
	if (this.config.useIcons) {
		if (!node.icon) node.icon = (this.root.id == node.pid) ? this.icon.root : ((node._hc) ? this.icon.folder : this.icon.node);
		if (!node.iconOpen) node.iconOpen = (node._hc) ? this.icon.folderOpen : this.icon.node;
		if (this.root.id == node.pid) {
			node.icon = this.icon.root;
			node.iconOpen = this.icon.root;
		}
        str += '<img id="i' + this.obj + nodeId + '" src="' + ((node._io) ? node.iconOpen : node.icon) + '" alt="" name="'+node.id+'" />';
	}
	
	// 节点文本及动作方法(带超链接、不带超链接) 
	if (node.url) {
		str += '<a id="s' + this.obj + nodeId + '" class="' + ((this.config.useSelection) ? ((node._is ? 'nodeSel' : 'node')) : 'node') + '" href="' + node.url + '"';
		if (node.title) str += ' title="' + node.title + '"';
		if (node.target) str += ' target="' + node.target + '"';
		if (this.config.useStatusText) str += ' onmouseover="window.status=\'' + node.name + '\';return true;" onmouseout="window.status=\'\';return true;" ';
		if (this.config.useSelection && ((node._hc && this.config.folderLinks) || !node._hc))
			str += ' onclick="javascript: ' + this.obj + '.s(' + nodeId + ');"';
		str += '>';
		
	}
	else if ((!this.config.folderLinks || !node.url) && node._hc && node.pid != this.root.id)
		str += '<a href="javascript: ' + this.obj + '.o(' + nodeId + ');" class="node">';
    // 2009-02-17
    str += node.name;
    if (node.url || ((!this.config.folderLinks || !node.url) && node._hc)) str += '</a>';
    if (node.oo) {
        str += '<p id="' + node.id + '" title="'+node.name+'" name="' + node._userState + '" class="onlinebar"><a href="javascript:">聊天</a> <a href="javascript:">消息</a> <a href="javascript:">邮件</a> </p>';
    } 
	str += '</div>';
	// --------- 以上是节点面板 --------
	
	// --------- 以下是包含子节点的面板 --------
	if (node._hc) {
		str += '<div id="d' + this.obj + nodeId + '" class="clip" style="display:' + ((this.root.id == node.pid || node._io) ? 'block' : 'none') + ';">';
		str += this.addNode(node);
		str += '</div>';
	}
	this.aIndent.pop();
	return str;
};

/*
 * 根据当前节点到次级根节点的所有父节点是否是同级最后一个节点的属性<br>
 * 确定节点前面显示图标的数量和种类<br>
 * @param node 节点对象;
 * @param nodeId 节点在节点数组中的索引值;
 */
sTree.prototype.indent = function(node, nodeId) {
	var str = '';
	if (this.root.id != node.pid) {
		for (var n=0; n<this.aIndent.length; n++)
			str += '<img src="' + ( (this.aIndent[n] == 1 && this.config.useLines) ? this.icon.line : this.icon.empty ) + '" alt="" />';
		(node._ls) ? this.aIndent.push(0) : this.aIndent.push(1);
		if (node._hc) {
			str += '<a href="javascript: ' + this.obj + '.o(' + nodeId + ');"><img id="j' + this.obj + nodeId + '" src="';
			if (!this.config.useLines) str += (node._io) ? this.icon.nlMinus : this.icon.nlPlus;
			else str += ( (node._io) ? ((node._ls && this.config.useLines) ? this.icon.minusBottom : this.icon.minus) : ((node._ls && this.config.useLines) ? this.icon.plusBottom : this.icon.plus ) );
			str += '" alt="" /></a>';
		} else str += '<img src="' + ( (this.config.useLines) ? ((node._ls) ? this.icon.joinBottom : this.icon.join ) : this.icon.empty) + '" alt="" />';
	}
	return str;
};

/*
 * 设置节点包含子节点标识_hc和同级最后节点标识_ls
 */
sTree.prototype.setCS = function(node) {
	var lastId;
	for (var n=0; n<this.aNodes.length; n++) {
		if (this.aNodes[n].pid == node.id) node._hc = true;
		if (this.aNodes[n].pid == node.pid) lastId = this.aNodes[n].id;
	}
	if (lastId==node.id) node._ls = true;
};

/*
 * 从Cookie中取得被选中节点在节点数组中的索引
 */
sTree.prototype.getSelected = function() {
	var sn = this.getCookie('cs' + this.obj);
	return (sn) ? sn : null;
};

/*
 * 使选中的节点高亮显示
 * @param id 节点在节点数组中的索引值;
 */

sTree.prototype.s = function(id) {
	
	// 判断是否允许选中节点
	if (!this.config.useSelection) return;
	
	// 根据索引值从节点数组中取出节点对象
	var cn = this.aNodes[id];
	
	// 判断包含子节点的节点是否允许选中
	if (cn._hc && !this.config.folderLinks) return;
	
	// 交换新旧节点的选中状态，改变css
	if (this.selectedNode != id) {
		if (this.selectedNode || this.selectedNode==0) {
			eOld = document.getElementById("s" + this.obj + this.selectedNode);
			eOld.className = "node";
			eOld.parentNode.className="sTreeNode";
		}
		eNew = document.getElementById("s" + this.obj + id);
		eNew.className = "nodeSel";
		eNew.parentNode.className="sTreeNode menuOn";
		//alert(eNew.parentNode.className);
		this.selectedNode = id;
		if (this.config.useCookies) this.setCookie('cs' + this.obj, cn.id);
	}
};



/*
 * 展开或折叠包某个含子节点的节点
 * @param id 节点在节点数组中的索引值;
 */
sTree.prototype.o = function(id) {
	var cn = this.aNodes[id];
	this.nodeStatus(!cn._io, id, cn._ls);
	cn._io = !cn._io;
	if (this.config.closeSameLevel) this.closeLevel(cn);
	if (this.config.useCookies) this.updateCookie();
};

/*
 * 展开或折叠包全部含子节点的节点
 * @param status true展开，false折叠;
 */

sTree.prototype.oAll = function(status) {
	for (var n=0; n<this.aNodes.length; n++) {
		if (this.aNodes[n]._hc && this.aNodes[n].pid != this.root.id) {
			this.nodeStatus(status, n, this.aNodes[n]._ls)
			this.aNodes[n]._io = status;
		}
	}
	if (this.config.useCookies) this.updateCookie();
};

// 为选中或看到某一节点而展开其所有父节点
sTree.prototype.openTo = function(nId, bSelect, bFirst) {
	if (!bFirst) {
		for (var n=0; n<this.aNodes.length; n++) {
			if (this.aNodes[n].id == nId) {
				nId=n;
				break;
			}
		}
	}
	var cn=this.aNodes[nId];
	if (cn.pid==this.root.id || !cn._p) return;
	cn._io = true;
	cn._is = bSelect;
	if (this.completed && cn._hc) this.nodeStatus(true, cn._ai, cn._ls);
	if (this.completed && bSelect) this.s(cn._ai);
	else if (bSelect) this._sn=cn._ai;
	this.openTo(cn._p._ai, false, true);
};



/*
 * 折叠同级的其他包含有子节点的节点，使得只有自己处于展开状态
 * @param node 节点对象;
 */

sTree.prototype.closeLevel = function(node) {
	for (var n=0; n<this.aNodes.length; n++) {
		if (this.aNodes[n].pid == node.pid && this.aNodes[n].id != node.id && this.aNodes[n]._hc) {
			this.nodeStatus(false, n, this.aNodes[n]._ls);
			this.aNodes[n]._io = false;
			this.closeAllChildren(this.aNodes[n]);
		}
	}
}

/*
 * 折叠同级的其他包含有子节点的节点，使得只有当前节点处于展开状态
 * @param node 节点对象;
 */
sTree.prototype.closeAllChildren = function(node) {
	for (var n=0; n<this.aNodes.length; n++) {
		if (this.aNodes[n].pid == node.id && this.aNodes[n]._hc) {
			if (this.aNodes[n]._io) this.nodeStatus(false, n, this.aNodes[n]._ls);
			this.aNodes[n]._io = false;
			this.closeAllChildren(this.aNodes[n]);		
		}
	}
}

/*
 * 改变节点的状态(展开或折叠)
 * @param status true展开，false折叠;
 * @param id 节点的数组索引值(_ai);
 * @param bottom 是否是本级最后一个节点(_ls);
 */

sTree.prototype.nodeStatus = function(status, id, bottom) {
	eDiv	= document.getElementById('d' + this.obj + id);
	eJoin	= document.getElementById('j' + this.obj + id);
	if (this.config.useIcons) {
		eIcon	= document.getElementById('i' + this.obj + id);
		eIcon.src = (status) ? this.aNodes[id].iconOpen : this.aNodes[id].icon;
	}
	eJoin.src = (this.config.useLines)?
	((status)?((bottom)?this.icon.minusBottom:this.icon.minus):((bottom)?this.icon.plusBottom:this.icon.plus)):
	((status)?this.icon.nlMinus:this.icon.nlPlus);
	eDiv.style.display = (status) ? 'block': 'none';
};

/*
 * 清空Cookie中保存的展开状态节点id集合、选中的节点id(不是节点的字节数组索引_ai)
 */

sTree.prototype.clearCookie = function() {
	var now = new Date();
	var yesterday = new Date(now.getTime() - 1000 * 60 * 60 * 24);
	this.setCookie('co'+this.obj, 'cookieValue', yesterday);
	this.setCookie('cs'+this.obj, 'cookieValue', yesterday);
};

/*
 * 在Cookie中保存一个键值对
 */
sTree.prototype.setCookie = function(cookieName, cookieValue, expires, path, domain, secure) {
	document.cookie =
		escape(cookieName) + '=' + escape(cookieValue)
		+ (expires ? '; expires=' + expires.toGMTString() : '')
		+ (path ? '; path=' + path : '')
		+ (domain ? '; domain=' + domain : '')
		+ (secure ? '; secure' : '');
};

/*
 * 从Cookie中获取一个键名的值
 */

sTree.prototype.getCookie = function(cookieName) {
	var cookieValue = '';
	var posName = document.cookie.indexOf(escape(cookieName) + '=');
	if (posName != -1) {
		var posValue = posName + (escape(cookieName) + '=').length;
		var endPos = document.cookie.indexOf(';', posValue);
		if (endPos != -1) cookieValue = unescape(document.cookie.substring(posValue, endPos));
		else cookieValue = unescape(document.cookie.substring(posValue));
	}
	return (cookieValue);
};

/*
 * 保存展开状态节点的ID到Cookie中
 */

sTree.prototype.updateCookie = function() {
	var str = '';
	for (var n=0; n<this.aNodes.length; n++) {
		if (this.aNodes[n]._io && this.aNodes[n].pid != this.root.id) {
			if (str) str += '.';
			str += this.aNodes[n].id;
		}
	}
	this.setCookie('co' + this.obj, str);
};

/*
 * 检查一个节点的id是否保存在Cookie中，以判断节点展开或折叠
 */

sTree.prototype.isOpen = function(id) {
	var aOpen = this.getCookie('co' + this.obj).split('.');
	for (var n=0; n<aOpen.length; n++)
		if (aOpen[n] == id) return true;
	return false;
};

// 如果数组类型没有定义 Push 和 pop 方法，就使用自定义的Push 和 pop实现
if (!Array.prototype.push) {
	Array.prototype.push = function array_push() {
		for(var i=0;i<arguments.length;i++)
			this[this.length]=arguments[i];
		return this.length;
	}
};

if (!Array.prototype.pop) {
	Array.prototype.pop = function array_pop() {
		lastElement = this[this.length-1];
		this.length = Math.max(this.length-1,0);
		return lastElement;
	}
};