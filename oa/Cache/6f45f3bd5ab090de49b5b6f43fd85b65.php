<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="Content-Language" content="zh-CN" />
				<table width="225" cellpadding="0" cellspacing="1">
					<tr>
						<td width="50" height="24"><p class="p7"><a href="/index.php/Email/inbox">收件箱：</a></p></td>
						<td width="155" class="td1"><a href="/index.php/WebMail/index"><?php echo ($mails['INBOX_COUNT']); ?></a> 封邮件 <a href="#"><?php echo ($mails['NEW_LETER_COUNT']); ?></a> 封新邮件</td>
					</tr>
					<tr>
						<td height="24"><p class="p7 p8"><a href="/index.php/WebMail/index/to/outbox">发件箱：</a></p></td>
						<td class="td1"><a href="/index.php/WebMail/index/to/outbox"><?php echo ($mails['OUTBOX_COUNT']); ?></a> 封邮件</td>
					</tr>
				</table>