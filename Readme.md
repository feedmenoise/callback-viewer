Asterisk callback-viewer by feedme.
------------------------------------

Трекинг пропущенных звонков, со статусом обработки звонка.<br>
Определение номеров абонентов, через API UserSide (3.14 и 3.11).<br>
Определение номера агента, через AMI.<br>
Мониторинг и управление модемами через AMI в риалтайме.<br>
Генерация через tts API Yandex аварийных сообщений, установка в диалпан на ходу, через AMI.<br>
Генерация отчетов по пропущенным звонкам в Telegram. <br>
<br>

Деплой приложения через Docker. 
-------------------------------------
<pre>./run.sh start</pre>

Конфигурация Asterisk
-------------------------------------
<pre>
[контекст исходящих]
exten => h,1,NoOp(Outgoing-check start)
	same => n,Set(call_type=out)
	same => n,Set(id=${UNIQUEID})
	same => n,Set(number=${receiver})
	same => n,Set(agent=${CALLERID(num)})
	same => n,Set(time=${STRFTIME(${EPOCH},,%d-%m-%y %H:%M:%S)})
	same => n,Set(status=${DIALSTATUS})
	same => n,AGI(/path/to/script/outgoing_update.php,${call_type},${id},${number},${agent},${time},${status}) ;; указать путь к скрипту
	same => n,Hangup()


[контекст входящих]
exten => h,1,NoOp(Incoming-check start)
	same => 2,Set(call_type=in)
	same => 3,Set(id=${UNIQUEID})
	same => 4,Set(number=${CALLERID(num)})
	same => 5,Set(queuename=support) 									;; название очереди
	same => 6,Set(time=${STRFTIME(${EPOCH},,%d-%m-%y %H:%M:%S)})
	same => 7,GotoIf($["${ABANDONED}" == "TRUE"]?h,8:h,10)
	same => 8,Set(status=${ABANDONED})
	same => 9,Goto(11)
	same => 10,Set(status=FALSE)
	same => n,AGI(/path/to/script/outgoing_update.php,${call_type},${id},${number},${queuename},${time},${status}) ;; указать путь к скрипту
</pre>

Отчеты в телеграм
-------------------------------------

В <b>tg-bot.php</b> необходимо подставить токен бота
<pre>
$token = "token_here";
</pre>

В <b>telegram-notification.php</b> необходимо указать креды для подключения к БД и указать путь к <b>tg-bot.php</b>

<pre>
$servername = "";
$database = "";
$username = "";
$password = "";


include("/path/to/script/tg-bot.php");
</pre>

В <b>crontab</b> добавить на каждую интресующую очередь обращение к скрипту :

<pre>
0 09-18 * * * /path/to/script/telegram-notification.php chat_id queue_name 1(за какой период отправлять отчет, в часах)
</pre>


<br>
<br>
<Br>

<b>feedme</b><br>
<i> 2019-2020 </i>