<?php
include 'init.php';
checkToken();

$upd = $telegram->getWebhookUpdates();
//$updId = $upd->getUpdateId();
$msg = $upd->getMessage();
$chat = $msg->getChat();
$chatId = $chat->getId();
$text = $msg->getText();
$from = $msg->getFrom();
$fromUserName = $from->getUsername();

logs("@$fromUserName: $text");

$command = explode(" ",$text,2);
$command[0] = explode("@",$command[0])[0];

if ($command[0][0] != '/') {exit();}

switch (strtoupper($command[0]))
{
	case "/PING":
		$sendMsg = [ 'chat_id' => $chatId, 'text' => 'PONG '.(isset($command[1])?$command[1]:'')];
		$telegram->sendMessage($sendMsg);
		break;
	case "/START":
		//command[1] содержит hash переданный /start hash
		$sendMsg = [ 'chat_id' => $chatId, 'text' => START_MESSAGE ];
		$telegram->sendMessage($sendMsg);
		break;
	case "/STOP":
		break;
	case "/HELP":
		switch(strtoupper($command[1]))
		{
			case "":
				$sendMsg = [ 'chat_id' => $chatId, 'text' => HELP ];
				break;
			case "FIND":
				$sendMsg = [ 'chat_id' => $chatId, 'text' => HELP_FIND ];
				break;
			case "HELP":
				$sendMsg = [ 'chat_id' => $chatId, 'text' => HELP_HELP ];
				break;
			case "PING":
				$sendMsg = [ 'chat_id' => $chatId, 'text' => HELP_PING ];
				break;
			default:
				$sendMsg = [ 'chat_id' => $chatId, 'text' => HELP_UNKNOWN ];
		}
		$telegram->sendMessage($sendMsg);
		break;
	case "/FIND":
		if ($command[1] != "") 
		{
			$res1 = sendPost('http://3wifi.stascorp.com/user.php?a=login','login=antichat&password=antichat');
			$res2 = sendPost('http://3wifi.stascorp.com/3wifi.php?a=find','essid='.$command[1]);
			$json = json_decode($res2, true);
			
			if (count($json['data']) > 0) 
			{
				foreach ($json['data'] as $d) 
				{
					$sendMsg = [ 'chat_id' => $chatId, 'text' => 
						substr($d['time'],0,strpos(trim($d['time']),' '))." ".$d['comment']."\n".
						$d['range']."\n".
						$d['bssid']."|".$d['essid']."\n".
						"(".$d['sec'].")".$d['key']."[".$d['wps']."]\n".
						$d['lat']." ".$d['lon']
						];
					$telegram->sendMessage($sendMsg);
				}
				unset($d);
				
				$sendMsg = [ 'chat_id' => $chatId, 'text' => "Страница ".$json['page']['current']."|".$json['page']['count'] ];
				$telegram->sendMessage($sendMsg);
			}else{
				$sendMsg = [ 'chat_id' => $chatId, 'text' => "Совпадений не найдено" ];
				$telegram->sendMessage($sendMsg);
			}
		}else{
			$sendMsg = [ 'chat_id' => $chatId, 'text' => HELP_FIND ];
			$telegram->sendMessage($sendMsg);
		}
		break;
		default:
			$sendMsg = [ 'chat_id' => $chatId, 'text' => HELP_UNKNOWN ];
			$telegram->sendMessage($sendMsg);
}
