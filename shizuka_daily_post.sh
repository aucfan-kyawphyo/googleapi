# 国民の祝日に関する法律に基づく休みかどうかをAPIにてチェックする
today=`date +%Y%m%d`
holiday=`wget -q -O - "http://s-proj.com/utils/checkHoliday.php?kind=h&date=${today}"`

# 平日の場合
if [ $holiday = 'else' ] ; then

#php /vagrant/work/google-api/index.php
php /deploy/qiita/google-api/shizuka.php > /deploy/qiita/google-api/shizuka_message.txt
php /deploy/qiita/google-api/shizuka_mail.php
#192.168.101.36
#php /deploy/qiita/google-api/index.php | php /deploy/qiita/google-api/json.php > /deploy/qiita/google-api/hogehoge.json
#curl -XPOST -H 'Authorization: Bearer 796ddbc73e10ee1f1e64c4f621a0cfff1839bcec' -H 'Content-Type: application/json' https://afdsol.qiita.com/api/v2/items/ -d @/deploy/qiita/google-api/hogehoge.json

#vagrant test
#php /vagrant/work/google-api/index.php | php /vagrant/work/google-api/json.php > /vagrant/work/google-api/hogehoge.json
#curl -XPOST -H 'Authorization: Bearer 796ddbc73e10ee1f1e64c4f621a0cfff1839bcec' -H 'Content-Type: application/json' https://afdsol.qiita.com/api/v2/items/ -d @/vagrant/work/google-api/hogehoge.json

#local test
#php /Users/kyawphyonaing/Desktop/aucfan_data/vagrant/work/google-api/index.php | php /Users/kyawphyonaing/Desktop/aucfan_data/vagrant/work/google-api/json.php > /Users/kyawphyonaing/Desktop/aucfan_data/vagrant/work/google-api/hogehoge.json
#curl -XPOST -H 'Authorization: Bearer 796ddbc73e10ee1f1e64c4f621a0cfff1839bcec' -H 'Content-Type: application/json' https://afdsol.qiita.com/api/v2/items/ -d @/Users/kyawphyonaing/Desktop/aucfan_data/vagrant/work/google-api/hogehoge.json

fi
