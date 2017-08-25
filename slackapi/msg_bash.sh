#!/bin/bash
# 国民の祝日に関する法律に基づく休みかどうかをAPIにてチェックする
today=`date +%Y%m%d`
holiday=`wget -q -O - "http://s-proj.com/utils/checkHoliday.php?kind=h&date=${today}"`

# 平日の場合
if [ $holiday = 'else' ] ; then
# aucmine
#php /deploy/googleapi/slackapi/aucmine.php > /deploy/googleapi/slackapi/message.txt 2>> /deploy/googleapi/slackapi/error.log
# calendar and aucmine
php /deploy/googleapi/news.php > /deploy/googleapi/slackapi/message.txt 2>> /deploy/googleapi/slackapi/error.log
fi
