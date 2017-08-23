# 国民の祝日に関する法律に基づく休みかどうかをAPIにてチェックする
today=`date +%Y%m%d`
holiday=`wget -q -O - "http://s-proj.com/utils/checkHoliday.php?kind=h&date=${today}"`

# 平日の場合
if [ $holiday = 'else' ] ; then

php /var/www/html/googleapi/slackapi/aucmine.php > /var/www/html/googleapi/slackapi/message.txt

fi
