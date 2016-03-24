# googleapi
Setting include_path in PHP.ini

date.timezone = Asia/Tokyo
include_path = ".:/usr/local/lib/php:/vagrant/google-api/src"

php index.php | ./json.php > hogehoge.json
cat hogehoge.json
#192.168.101.195
curl -XPOST -H 'Authorization: Bearer 796ddbc73e10ee1f1e64c4f621a0cfff1839bcec' -H 'Content-Type: application/json' https://afdsol.qiita.com/api/v2/items/ -d @/deploy/qiita/google-api/hogehoge.json
#local test
curl -XPOST -H 'Authorization: Bearer 796ddbc73e10ee1f1e64c4f621a0cfff1839bcec' -H 'Content-Type: application/json' https://afdsol.qiita.com/api/v2/items/ -d @/Users/kyawphyonaing/Desktop/aucfan_data/vagrant/work/google-api/hogehoge.json

#192.168.101.195
#for monday to friday
#for qiita
00 22 * * 1,2,3,4,5 sh /deploy/qiita/google-api/daily_post.sh 2>>/tmp/google-api.log
#for mail
00 22 * * 1,2,3,4,5 php /deploy/qiita/google-api/mail_index.php > /deploy/qiita/google-api/message.txt
00 09 * * 1,2,3,4,5 php /deploy/qiita/google-api/mail.php 2>>/tmp/google-mail.log

