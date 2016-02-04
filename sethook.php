<?php
include 'init.php';
checkToken();

$response = $telegram->getMe();

$param = ['url' => 'https://example.com/hook.php?token='.API_KEY];
echo "setWebHook=".$telegram->setWebHook($param);

echo "\n<br>ID=$botId<br>FIRST NAME=$botFirstName<br>USERNAME=$botUserName";
