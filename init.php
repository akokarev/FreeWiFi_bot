<?php
define ('API_KEY', '123456789:ABCDEFGHIJKLMNO_PQRSTUVWXYZ1234_5-6');
define ('LOG_FILE', 'bot.log');
define ('BOT_USER_AGENT', 'FreeWiFi_Bot/1.0');

define ('HELP', "Вы можете использовать следующие комманды:\n
/help [cmd] - Справка по коммандам бота
/find [key] - Поиск в базе
/ping [msg] - Проверить бота");

define ('START_MESSAGE', 
"Пользуясь этим ботом Вы принимаете с Соглашение http://3wifi.stascorp.com/rules. 
Администрация не несет ответственности за Ваши действия с полученной информацией!\n\n".HELP);

define ('HELP_FIND', "Найти информацию о WiFi точке в базе.
/find [key]\n
В кчестве параметра key укажите имя интересующей сети (ESSID).

Возвращает информацию в формате:
Дата_Комментарий
Диапазон IP
BSSID|ESSID
(Тип шифрования)Пароль[WPS]
Широта Долгота");

define ('HELP_PING',"Проверить что бот работает.
/ping [msg]
Необязательный параметр msg будет возвращен в ответном сообщении.

Формат ответа:
PONG msg");

define ('HELP_HELP',"Cправка по командам бота.
/help [cmd]
Без параметров возвращает список доступных команд.
С параметром cmd отображает детальную справку по команде.");

define ('HELP_UNKNOWN',"Я таким командам не обучен!");


require 'vendor/autoload.php';
use Telegram\Bot\Api;

$telegram = new Api(API_KEY);
$bot = $telegram->getMe();
$botId = $bot->getId();
$botUserName = $bot->getUserName();
$botFirstName = $bot->getFirstName();
	
function logs($msg)
{
	file_put_contents(LOG_FILE, $msg."\n", FILE_APPEND);
}

function logs_var($name, $var)
{
	logs($name." = ".var_export($var, TRUE));
}

function checkToken()
{
	if ( API_KEY != (isset($_GET["token"])? $_GET["token"]:false) ) 
	{
		logs("ERROR TOKEN!");
		logs_var("_GET",$_GET);
		logs_var("_POST",$_POST);
		logs_var("_SERVER",$_SERVER);
		exit();
	}
}

function startWith($needle, $haystack) {
    return (strcasecmp($needle, substr($haystack, 0, strlen($needle))) == 0);
}



if ($curl = curl_init())
{ 
	curl_setopt($curl, CURLOPT_RETURNTRANSFER,true);
	curl_setopt($curl, CURLOPT_POST, true);
	curl_setopt($curl, CURLOPT_USERAGENT, BOT_USER_AGENT);
	curl_setopt($curl, CURLOPT_COOKIEFILE, $_SERVER['DOCUMENT_ROOT'].'cookie.txt');
	curl_setopt($curl, CURLOPT_COOKIEJAR, $_SERVER['DOCUMENT_ROOT'].'cookie.txt');
	
}else exit();

function sendPost($url, $request='')
{
	global $curl;
	if( $curl ) {
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $request);
		return curl_exec($curl);
	}else return false;
}
