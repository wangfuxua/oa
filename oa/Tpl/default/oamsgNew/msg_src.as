// ActionScript file

import flash.external.*;

import org.jivesoftware.xiff.core.*;
import org.jivesoftware.xiff.data.*;
import org.jivesoftware.xiff.events.*;

private var connection:XMPPSocketConnection= new XMPPSocketConnection();
private var keepAlive:Timer;
private var presence:Presence;
private var serverName:String;

private function init():void
{
	//接口函数
	ExternalInterface.addCallback("sendToHim", sendToHim);
	ExternalInterface.addCallback("imLogin", onCreationComplete);	
	ExternalInterface.addCallback("regUser", regUser);
	ExternalInterface.addCallback("isLogin", isLogin);
	
}

//对别人说
internal function sendToHim(_to:String,_msg:String):void
{
	if(!isLogin())
	{
		showHtmlAlert("与聊天服务器连接已断开，请重新连接！");
		return;
	}
	if(txt_toUsername.text==null||txt_toUsername.text=="")
	{
		showHtmlAlert("请输入要对话的用户名！");
		txt_toUsername.focusEnabled=true;
		return;
	}
	//trace(_to+
	var ms:Message=new Message(new JID(_to+"@"+serverName),null,_msg,null,null,null);
	connection.send(ms);
}

//登陆
private function onCreationComplete(parm_serverIP:String,parm_serverName:String,parm_user:String,parm_pass:String):void
{
	if(connection.isLoggedIn())
	{
		connection.disconnect();
	}
	serverName=parm_serverName;
	
	connection.addEventListener(XIFFErrorEvent.XIFF_ERROR,onError);
	connection.addEventListener(LoginEvent.LOGIN, onLogin);
	connection.addEventListener(MessageEvent.MESSAGE,onMessages);
	connection.addEventListener(PresenceEvent.PRESENCE, onPresence);
	presence = new Presence(null,connection.jid, Presence.SHOW_CHAT, Presence.SHOW_CHAT, null, 1);
	
	connection.server = parm_serverIP;
	connection.port = 5222;
	
	connection.username = parm_user;
	connection.password = parm_pass;
	
	connection.connect("flash");
	
	keepAlive = new Timer(100000);
    keepAlive.addEventListener(TimerEvent.TIMER, onKeepAliveLoop);
    keepAlive.start(); 

}

//登陆是否成功
private function isLogin():Boolean
{
	return connection.isLoggedIn();
}

//注册帐户　
private function regUser(parm_userName:String):void
{
	if(!connection.isLoggedIn()|| connection.username!="admin")
	{
		showHtmlAlert("请先以管理员身份登陆！");
		return;
	}
	if(parm_userName==null || parm_userName=="")
	{
		showHtmlAlert("用户名不能为空！");
		return;
	}
	var regs:Object={};
	var defaultPass:String="123";
	regs.username=parm_userName;
	regs.email="test@7e73.com";
	regs.password=defaultPass;
	connection.sendRegistrationFields(regs,null);
}


private function onPresence(evt:PresenceEvent):void 
{
    trace("onPresence " + getTimer());
}

private function onKeepAliveLoop(evt:TimerEvent):void 
 {
    connection.sendKeepAlive();
 }

private function onMessages(e:MessageEvent):void
{
	trace(e.data.from+"  says: "+e.data.body);
	//ExternalInterface.call("alert",e.data.from+"  says: "+e.data.body);
	ExternalInterface.call("sendToMe",e.data.from.toString(),e.data.body);
}

private function onLogin(e:LoginEvent):void
{
	trace("登陆成功!"+connection.jid);
	//showHtmlAlert("登陆成功！欢迎："+connection.username);
	ExternalInterface.call("loginOK",connection.username);
	connection.send(presence);
}


private function onError(e:XIFFErrorEvent):void
{
	trace(e.errorCode+e.errorMessage);
	switch(e.errorCode)
	{
		case 503:
		trace("连接聊天服务器失败!");
		showHtmlAlert("连接聊天服务器失败!");
		case 400:
		trace("登陆失败！请检查帐号密码是否正确!");
		showHtmlAlert("登陆失败！请检查帐号密码是否正确!");
	}
}

private function showHtmlAlert(str:String):void
{
	ExternalInterface.call("alert",str);
}