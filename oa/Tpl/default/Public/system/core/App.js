Ext.app.App = function(config){
    Ext.apply(this, config);
    
    this.addEvents({
        'ready' : true,
        'beforeunload' : true
    });

    Ext.onReady(this.initApp, this);
};

Ext.extend(Ext.app.App, Ext.util.Observable, {
    
    isReady : false,				/* 只读. 应用程序准备状态 				* @type boolean */
    launchers : null,				/* 只读. 应用程序发送状态				* @type object  */
    modules : null,					/* 只读. 应用程序组件/模块	    		* @type array   */
    styles : null,					/* 只读. 应用程序样式     				* @type object */
    startConfig : null,				/* 只读. “开始菜单”的设置     			* @type object     */
    startItemsConfig : null,		/* 只读. 开始菜单和工具菜单条目设置.    	* @type object     */
    logoutButtonConfig : null,		/* 只读. “退出”按钮设置    			* @type object     */
    /**
	 * Read-only. The url of this app's server connection
	 * 
	 * Allows a module to connect to its server script without knowing the path.
	 * Example ajax call:
	 * 
	 * Ext.Ajax.request({
	 * 		url: this.app.connection,
	 * 		// Could also pass moduleId and fileName in querystring like this,
	 * 		// instead of in the params config option.
	 *      
	 * 		// url: this.app.connection+'?moduleId='+this.id+'&fileName=Preferences.php',
	 *      params: {
	 *			moduleId: this.id,
	 *			fileName: 'Preferences.php',
	 *
	 *			...
	 *		},
	 *		success: function(){
	 *			...
	 *		},
	 *		failure: function(){
	 *			...
	 *		},
	 *		scope: this
	 *	});
	 */
	connection : 'connect',
	/**
     * 只读. The url of this app's loader script
     * @type string
     * loading时处理组件
     */
	loader : 'loadmodule',
	requestQueue : [],	/* 只读。队列的请求运行一次模块加载	 */

	
	init : Ext.emptyFn,
	startMenuSortFn : Ext.emptyFn,
	getModules : Ext.emptyFn,
	getLaunchers : Ext.emptyFn,
	getStyles : Ext.emtpyFn,
	getStartConfig : Ext.emptyFn,
    getLogoutConfig : Ext.emptyFn,
    getMkConfig : Ext.emptyFn,
    
	initApp : function(){
		Ext.BLANK_IMAGE_URL = '/oa/Tpl/default/Public/Base/images/default/s.gif';

    	this.preventBackspace();
    	
    	this.privileges = this.privileges || this.getPrivileges();
    	
    	this.modules = this.modules || this.getModules();
    	this.initModules();
		
		this.startConfig = this.startConfig || this.getStartConfig();
		this.startItemsConfig = this.startItemsConfig || this.getStartItemsConfig();
		Ext.apply(this.startConfig, this.startItemsConfig);
		
		this.desktop = new Ext.Desktop(this);
		
		this.styles = this.styles || this.getStyles();
    	this.initStyles();
		
		this.launchers = this.launchers || this.getLaunchers();
		this.initLaunchers();
		
		this.logoutConfig = this.logoutConfig || this.getLogoutConfig();
		this.initLogout();

		this.mkConfig = this.mkConfig || this.getMkConfig();
		this.initMk();
		
		this.init();
	
		Ext.EventManager.on(window, 'beforeunload', this.onBeforeUnload, this);
		this.fireEvent('ready', this);
		this.isReady = true;
    },
    
    initLogout : function(){
		if(this.logoutConfig){
			this.desktop.taskbar.startMenu.addTool(this.logoutConfig);
		}
	},
    initMk : function(){
		if(this.mkConfig){
			this.desktop.taskbar.startMenu.addTool(this.mkConfig);
		}
	},
	
	initStyles : function(){
    	var s = this.styles;
    	if(!s){
    		return false;
    	}
    	
    	this.desktop.setBackgroundColor(s.backgroundcolor);
    	this.desktop.setFontColor(s.fontcolor);
    	this.desktop.setTheme(s.theme);
    	this.desktop.setTransparency(s.transparency);
    	this.desktop.setWallpaper(s.wallpaper);
    	this.desktop.setWallpaperPosition(s.wallpaperposition);
    	
    	return true;
    },

    initModules : function(){
    	var ms = this.modules;
    	if(!ms){ return false; }
    	
		for(var i = 0, len = ms.length; i < len; i++){			
			if(ms[i].loaded === true){
				//ms[i].app = this;
    		}else{
    			// 组件没有被加载, set the handler for its launcher
    			ms[i].launcher.handler = this.createWindow.createDelegate(this, [ms[i].moduleId]);
    		}
    		ms[i].app = this;
        }
        
        return true;
    },
    
    initLaunchers : function(){
    	var l = this.launchers;
    	if(!l){
    		return false;
    	}
    	
    	if(l.contextmenu){
			this.initContextMenu(l.contextmenu);
		}
		if(l.quickstart){
			this.initQuickStart(l.quickstart);
		}
		if(l.shortcut){
			this.initShortcut(l.shortcut);
		}
		if(l.autorun){
			this.onReady(this.initAutoRun.createDelegate(this, [l.autorun]), this);
		}
		
		return true;
    },
    
    /**
	 * @param {array} mIds An array of the module ids to run when this app is ready
	 */
    initAutoRun : function(mIds){
    	if(mIds){
    		for(var i = 0, len = mIds.length; i < len; i++){
	            var m = this.getModule(mIds[i]);
	            if(m){
	            	m.autorun = true;
	            	this.createWindow(mIds[i]);
	            }
			}
		}
    },

	/**
	 * @param {array} mIds An array of the module ids to add to the Desktop Context Menu
	 */
    initContextMenu : function(mIds){
    	if(mIds){
    		for(var i = 0, len = mIds.length; i < len; i++){
    			this.desktop.addContextMenuItem(mIds[i]);
	        }
    	}
    },

	/**
	 * @param {array} mIds An array of the module ids to add to the Desktop Shortcuts
	 */
    initShortcut : function(mIds){
		if(mIds){
			for(var i = 0, len = mIds.length; i < len; i++){
	            this.desktop.addShortcut(mIds[i], false);
	        }
		}
    },

	/**
	 * @param {array} mIds An array of the modulId's to add to the Quick Start panel
	 */
	initQuickStart : function(mIds){
		if(mIds){
			for(var i = 0, len = mIds.length; i < len; i++){
	            this.desktop.addQuickStartButton(mIds[i], false);
	        }
		}
    },
    
    /**
	 * Returns the Start Menu items and toolItems configs
	 * @param {array} ms An array of the modules.
	 */
	getStartItemsConfig : function(){
		var ms = this.modules;
		var sortFn = this.startMenuSortFn;
		
    	if(ms){
    		var paths;
    		var root;
    		var sm = { menu: { items: [] } }; // Start Menu
    		var smi = sm.menu.items;
    		
    		smi.push({text: 'StartMenu', menu: { items: [] } });
    		smi.push({text: 'ToolMenu', menu: { items: [] } });
    		
			for(var i = 0, iLen = ms.length; i < iLen; i++){ // loop through modules
				if(ms[i].menuPath){	
					paths = ms[i].menuPath.split('/');
					root = paths[0];
					
					if(paths.length > 0){
						if(root === 'StartMenu'){
							simplify(smi[0].menu, paths, ms[i].launcher);
							sort(smi[0].menu);
						}else if(root === 'ToolMenu'){
							simplify(smi[1].menu, paths, ms[i].launcher);
							sort(smi[1].menu);
						}
					}
				}
			}
			
			return {
				items: smi[0].menu.items,
				toolItems: smi[1].menu.items
			};
    	}
    	
    	return null;
		
		/**
		 * Creates nested arrays that represent the Start Menu.
		 * 
		 * @param {array} pMenu The Start Menu
		 * @param {array} texts The menu texts
		 * @param {object} launcher The launcher config
		 */
		function simplify(pMenu, paths, launcher){
			var newMenu;
			var foundMenu;
			
			for(var i = 1, len = paths.length; i < len; i++){ // ignore the root (StartMenu, ToolMenu)
				foundMenu = findMenu(pMenu.items, paths[i]); // text exists?
				
				if(!foundMenu){
					newMenu = {
						iconCls: 'ux-start-menu-submenu',
						handler: function(){ return false; },
						menu: { items: [] },
						text: paths[i]
					};
					pMenu.items.push(newMenu);
					pMenu = newMenu.menu;
				}else{
					pMenu = foundMenu;
				}
			}
			
			pMenu.items.push(launcher);
		}
		
		/**
		 * Returns the menu if found.
		 * 
		 * @param {array} pMenu The parent menu to search
		 * @param {string} text
		 */
		function findMenu(pMenu, text){
			for(var j = 0, jlen = pMenu.length; j < jlen; j++){
				if(pMenu[j].text === text){
					return pMenu[j].menu; // found the menu, return it
				}
			}
			return null;
		}
		
		/**
		 * @param {array} menu The nested array to sort
		 */
		function sort(menu){
			var items = menu.items;
			for(var i = 0, ilen = items.length; i < ilen; i++){
				if(items[i].menu){
					sort(items[i].menu); // use recursion to iterate nested arrays
				}
				bubbleSort(items, 0, items.length); // sort the menu items
			}
		}
		
		/**
		 * @param {array} items Menu items to sort
		 * @param {integer} start The start index
		 * @param {integer} stop The stop index
		 */
		function bubbleSort(items, start, stop){
			for(var i = stop - 1; i >= start;  i--){
				for(var j = start; j <= i; j++){
					if(items[j+1] && items[j]){
						if(sortFn(items[j], items[j+1])){
							var tempValue = items[j];
							items[j] = items[j+1];
							items[j+1] = tempValue;
						}

					}
				}
			}
			return items;
		}
	},
    
    /**
     * @param {string} moduleId
     * 
     * Provides the handler to the placeholder's launcher until the module it is loaded.
     * Requests the module.  Passes in the callback and scope as params.
     */
    createWindow : function(moduleId){
    	var m = this.requestModule(moduleId, function(m){
    		if(m){
	    		m.createWindow();
	    	}
    	}, this);
    },
    
    /** 
     * @param {string} v The moduleId or moduleType you want returned
     * @param {Function} cb The Function to call when the module is ready/loaded
     * @param {object} scope The scope in which to execute the function
     */
	requestModule : function(v, cb, scope){
    	var m = this.getModule(v);
        
        if(m){
	        if(m.loaded === true){
	        	cb.call(scope, m);
	        }else{
	        	if(cb && scope){
		        	this.requestQueue.push({
			        	moduleId: m.moduleId,
			        	callback: cb,
			        	scope: scope
			        });
			        this.loadModule(m.moduleId, m.launcher.text);
	        	}
	        }
        }
    },
    
    loadModule : function(moduleId, moduleName){
    	var notifyWin = this.desktop.showNotification({
			html: '加载 ' + moduleName + '...'
			, title: '请等待'
		});
    	
    	Ext.Ajax.request({
    		url: this.loader,
    		//url: '/index.php/newoa/load',
    		params: {
    			moduleId: moduleId
    		},
    		success: function(o){
    			notifyWin.setIconClass('x-icon-done');
				notifyWin.setTitle('完成');
				notifyWin.setMessage(moduleName + ' 已加载.');
				this.desktop.hideNotification(notifyWin);
				notifyWin = null;
		
    			if(o.responseText !== ''){
    				eval(o.responseText);
    				this.loadModuleComplete(true, moduleId);
    			}else{
    				alert('服务器端错误.'+moduleId+o.responseText);
    			}
    		},
    		failure: function(){
    			alert('链接服务器错误!');
    		},
    		scope: this
    	});
    },
    
    /**
     * @param {boolean} success
     * @param {string} moduleId
     * 
     * Will be called when a module is loaded.
     * If a request for this module is waiting in the
     * queue, it as executed and removed from the queue.
     */
    loadModuleComplete : function(success, moduleId){    	
    	if(success === true && moduleId){
    		var m = this.getModule(moduleId);
    		m.loaded = true;
    		m.init();
    		
	    	var q = this.requestQueue;
	    	var nq = [];
	    	for(var i = 0, len = q.length; i < len; i++){
	    		if(q[i].moduleId === moduleId){
	    			q[i].callback.call(q[i].scope, m);
	    		}else{
	    			nq.push(q[i]);
	    		}
	    	}
	    	this.requestQueue = nq;
    	}
    },

    /**
     * @param {string} v The moduleId or moduleType you want returned
     */
    getModule : function(v){
    	var ms = this.modules;
    	
        for(var i = 0, len = ms.length; i < len; i++){
    		if(ms[i].moduleId == v || ms[i].moduleType == v){
    			return ms[i];
			}
        }
        
        return null;
    },
    
    
    /**
     * @param {Ext.app.Module} m The module to register
     */
    registerModule: function(m){
    	if(!m){ return false; }
		this.modules.push(m);
		m.launcher.handler = this.createWindow.createDelegate(this, [m.moduleId]);
		m.app = this;
	},

    /**
     * @param {string} moduleId or moduleType 
     * @param {array} requests An array of request objects
     * 
     * Example:
     * this.app.makeRequest('module-id', {
	 *    requests: [
	 *       {
	 *          action: 'createWindow',
	 *          params: '',
	 *          callback: this.myCallbackFunction,
	 *          scope: this
	 *       },
	 *       { ... }
	 *    ]
	 * });
     */
    makeRequest : function(moduleId, requests){
    	if(moduleId !== '' && Ext.isArray(requests)){
	    	var m = this.requestModule(moduleId, function(m){
	    		if(m){
		    		m.handleRequest(requests);
		    	}
	    	}, this);
    	}
    },
    
    /**
     * @param {string} action The module action
     * @param {string} moduleId The moduleId property
     */
    isAllowedTo : function(action, moduleId){
    	var p = this.privileges,
    		a = p[action];
    	
    	if(p && a){
    		for(var i = 0, len = a.length; i < len; i++){
    			if(a[i] === moduleId){
    				return true;
    			}
    		}
    	}
    	
    	return false;
    },
    
    getDesktop : function(){
        return this.desktop;
    },
    
    /**
     * @param {Function} fn The function to call after the app is ready
     * @param {object} scope The scope in which to execute the function
     */
    onReady : function(fn, scope){
        if(!this.isReady){
            this.on('ready', fn, scope);
        }else{
            fn.call(scope, this);
        }
    },
    
    onBeforeUnload : function(e){
        if(this.fireEvent('beforeunload', this) === false){
            e.stopEvent();
        }
    },
    
    /**
     * Prevent the backspace (history -1) shortcut
     */
    preventBackspace : function(){
    	var map = new Ext.KeyMap(document, [{
			key: Ext.EventObject.BACKSPACE,
			fn: function(key, e){
				var t = e.target.tagName;
				if(t != "INPUT" && t != "TEXTAREA"){
					e.stopEvent();
				}
			}
		}]);
    }
});