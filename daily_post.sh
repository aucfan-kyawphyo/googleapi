# 国民の祝日に関する法律に基づく休みかどうかをAPIにてチェックする
today=`date +%Y%m%d`
holiday=`wget -q -O - "http://s-proj.com/utils/checkHoliday.php?kind=h&date=${today}"`

# 平日の場合
if [ $holiday = 'else' ] ; then

#php /vagrant/work/google-api/index.php

#192.168.101.195
php /deploy/qiita/googleapi/index.php | php /deploy/qiita/googleapi/json.php > /deploy/qiita/googleapi/hogehoge.json
curl -XPOST -H 'Authorization: Bearer 796ddbc73e10ee1f1e64c4f621a0cfff1839bcec' -H 'Content-Type: application/json' https://afdsol.qiita.com/api/v2/items/ -d @/deploy/qiita/googleapi/hogehoge.json

#vagrant test
#php /vagrant/work/google-api/index.php | php /vagrant/work/googleapi/json.php > /vagrant/work/googleapi/hogehoge.json
#curl -XPOST -H 'Authorization: Bearer 796ddbc73e10ee1f1e64c4f621a0cfff1839bcec' -H 'Content-Type: application/json' https://afdsol.qiita.com/api/v2/items/ -d @/vagrant/work/google-api/hogehoge.json

#local test
#php /Users/kyawphyonaing/Desktop/google-api/index.php | php /Users/kyawphyonaing/Desktop/googleapi/json.php > /Users/kyawphyonaing/Desktop/googleapi/hogehoge.json
#curl -XPOST -H 'Authorization: Bearer 796ddbc73e10ee1f1e64c4f621a0cfff1839bcec' -H 'Content-Type: application/json' https://afdsol.qiita.com/api/v2/items/ -d @/Users/kyawphyonaing/Desktop/googleapi/hogehoge.json

fi
