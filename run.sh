#!/bin/bash

chmod a+w ./www/inc
chmod a+w ./rec

if [ $# -eq 0 ]; then
	echo "Asterisk callback-viewer by feedme"
   	echo "Usage:"
	echo "	start - запустить приложение"
	echo "	stop - остановить приложение"
fi

if [ "$1" == "start" ]; then
docker-compose up -d 
echo "Приложение запущено"
elif [ "$1" == "stop" ]; then
docker-compose down
echo "Приложение остановлено"
fi
