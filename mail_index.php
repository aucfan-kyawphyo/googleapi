<?php
chdir(dirname(__FILE__));
require __DIR__ . '/vendor/autoload.php';
//holiday checking
require './holiday.php';
$tomorrow = holiday($tomorrow);

define('APPLICATION_NAME', 'Google Calendar API PHP Quickstart');
define('CREDENTIALS_PATH', '~/.credentials/calendar-php-quickstart.json');
define('CLIENT_SECRET_PATH', __DIR__ . '/client_secret.json');
define('SCOPES', implode(' ', array(
  Google_Service_Calendar::CALENDAR_READONLY)
));

if (php_sapi_name() != 'cli') {
  throw new Exception('This application must be run on the command line.');
}

/**
 * Returns an authorized API client.
 * @return Google_Client the authorized client object
 */
function getClient() {
  $client = new Google_Client();
  $client->setApplicationName(APPLICATION_NAME);
  $client->setScopes(SCOPES);
  $client->setAuthConfigFile(CLIENT_SECRET_PATH);
  $client->setAccessType('offline');

  // Load previously authorized credentials from a file.
  $credentialsPath = expandHomeDirectory(CREDENTIALS_PATH);
  if (file_exists($credentialsPath)) {
    $accessToken = file_get_contents($credentialsPath);
  } else {
    // Request authorization from the user.
    $authUrl = $client->createAuthUrl();
    printf("Open the following link in your browser:\n%s\n", $authUrl);
    print 'Enter verification code: ';
    $authCode = trim(fgets(STDIN));

    // Exchange authorization code for an access token.
    $accessToken = $client->authenticate($authCode);

    // Store the credentials to disk.
    if(!file_exists(dirname($credentialsPath))) {
      mkdir(dirname($credentialsPath), 0700, true);
    }
    file_put_contents($credentialsPath, $accessToken);
    printf("Credentials saved to %s\n", $credentialsPath);
  }
  $client->setAccessToken($accessToken);

  // Refresh the token if it's expired.
  if ($client->isAccessTokenExpired()) {
    $client->refreshToken($client->getRefreshToken());
    file_put_contents($credentialsPath, $client->getAccessToken());
  }
  return $client;
}

/**
 * Expands the home directory alias '~' to the full path.
 * @param string $path the path to expand.
 * @return string the expanded path.
 */
function expandHomeDirectory($path) {
  $homeDirectory = getenv('HOME');
  if (empty($homeDirectory)) {
    $homeDirectory = getenv("HOMEDRIVE") . getenv("HOMEPATH");
  }
  return str_replace('~', realpath($homeDirectory), $path);
}

// Get the API client and construct the service object.
$client = getClient();
$service = new Google_Service_Calendar($client);
///////////////////////////////////////

//calendar ID setting
$email_map = array(
    'tokugami@aucfan.com' => array('name' => '得上竜一', 'user_id' => 10),
    'ida@aucfan.com'           => array('name' => '井田', 'user_id' => 16),
    't_watanabe@aucfan.com'    => array('name' => '渡辺', 'user_id' => 45),
    'yotsuya@aucfan.com'       => array('name' => '四谷', 'user_id' => 57),
    'che@aucfan.com'           => array('name' => 'チェ', 'user_id' => 67),
    'makise@aucfan.com'        => array('name' => '牧瀬', 'user_id' => 88),
    'takaya@aucfan.com'        => array('name' => '高屋', 'user_id' => 95),
    'fujinaga@aucfan.com'     => array('name' => '藤永', 'user_id' => 105),
    'fukuda@aucfan.com'     => array('name' => '福田', 'user_id' => 109),
    'ohira@aucfan.com'     => array('name' => '大平', 'user_id' => 112),
    'tani@aucfan.com'     => array('name' => '谷', 'user_id' => 117),
    'kyawphyonaing@aucfan.com' => array('name' => 'チョー', 'user_id' => 97),
);
///////////////////////////////////////

//redmine prepare

$name_list = array();
foreach ($email_map as $member) {
  $name_list[] = $member['name'];
}


// 読み込むファイル名の指定
foreach(glob('./{*.csv}',GLOB_BRACE) as $file){
  if(is_file($file)){
    $file_name = "./{$file}";
    break;
  }
}

// ファイルポインタを開く
$fp = fopen($file_name, 'r');

$next_task = array();
// データが無くなるまでファイル(CSV)を１行ずつ読み込む
$key_line_list = array();
$line_number = 0;
while ($ret_csv = fgetcsv($fp, 256)) {
  $line_number++;
  if (empty($ret_csv[0])) {
    continue;
  }
  // 読み込んだ行(CSV)を表示する
  if (in_array($ret_csv[0], $name_list)) {
    $key_line_list[$line_number] = $ret_csv[0];
  }
  else {
    if (empty($key_line_list)) {
      continue;
    }
    $max_line_number = max(array_keys($key_line_list));
    if (is_bool($max_line_number)) {
      continue;
    }
    $next_task[$key_line_list[$max_line_number]][] = $ret_csv[0] . ' ' .$ret_csv[1] . $ret_csv[2];
  }
}

// 開いたファイルポインタを閉じる
fclose($fp);
//ライブラリの読み込み
require_once "./Feed.php";

$target_day = NULL;
//$target_day = '2015-10-02';
$now = new DateTime($target_day);
$day = $now->format("Y-m-d");
//取得するフィードのURLを指定
$person_url = "https://dsol:dsolredmine@dsolmine.aucfan.com/time_entries.atom?f%5B%5D=spent_on&f%5B%5D=user_id&key=44508922c468f805fef688447e31054779ac467a&op%5Bspent_on%5D=%3E%3C&op%5Buser_id%5D=%3D&utf8=%E2%9C%93&v%5Bspent_on%5D%5B%5D={$day}&v%5Bspent_on%5D%5B%5D={$day}&v%5Buser_id%5D%5B%5D=";


$result = array();
foreach ($email_map as $member) {
  //インスタンスの作成
  $feed = new Feed;
  //RSSを読み込む
  $atom = $feed->loadAtom($person_url . $member['user_id']);
  $ticket_no_pattern = '/\/(\d+)\/time_entries$/';
  $time_title_pattern = '/(.+)時間 \((.+)\)/';
  foreach ($atom->{'entry'} as $item) {
    $author_name = (string) $item->author->name;
    $email = (string) $item->author->email;
    $org_title = (string) $item->title;
    $day = (string) $item->updated;
    $url = (string) $item->id;
    preg_match($ticket_no_pattern, $url, $matches);
    $ticket_no = $matches[1];
    if (!array_key_exists($email, $result)) {
      $result[$email] = array();
    }

    $title_array = explode(' - ', $org_title);
    $project_name = $title_array[0];
    $time_title = $title_array[1];
    preg_match($time_title_pattern, $time_title, $matches);
    $time = floatval($matches[1]);
    $title = $matches[2];
    if (!isset($result[$email][$project_name])) {
      $result[$email][$project_name] = array();
    }

    if (!isset($result[$email][$project_name][$title])) {
      $result[$email][$project_name][$title] = array(
          'project_name' => $project_name,
          'title'        => $title,
          'time'         => $time,
          'url'          => 'https://dsolmine.aucfan.com/issues/' . $ticket_no,
      );
    }
    else {
      $result[$email][$project_name][$title]['time'] += $time;
    }
  }
}
echo "各位\n本日の得上竜一さんと開発Gの作業内容を共有いたします。\n";
// Print calendar and redmine
foreach($email_map as $email => $member) {
  $calendarId = $email;
  echo  "\n" .'#' . $member['name'] . "\n";
  //得上さん以外の人だけ
  if($calendarId != 'tokugami@aucfan.com') {
    //Today for redmine
    echo '前日の作業内容' . "\n\n";
    if (!isset($result[$email])) {
      echo "なし\n\n";
    } else {
      $task_list = $result[$email];
      foreach ($task_list as $project_name => $project_list) {
        echo ' ' . $project_name . "\n";
        foreach ($project_list as $task) {
          echo "  " . $task['title'] . ':' . $task['time'] . "時間\n";
          echo "  * " . $task['url'] . "\n";
        }
        echo "\n\n";
      }
    }
  }
  //Today for calendar
  $optParams = array(
      'maxResults' => 20,
      'orderBy' => 'startTime',
      'singleEvents' => TRUE,
      'timeMin' => date(DATE_ATOM, mktime(0, 0, 0, date("m"), date("d"), date("Y"))),
      'timeMax' => date(DATE_ATOM, mktime(0, 0, 0, date("m"), date("d") + 1, date("Y")))
  );
  $results = $service->events->listEvents($calendarId, $optParams);
  if (count($results->getItems()) == 0) {
    echo '前日のカレンダー予定' . "\n\n";
    echo "なし\n";
  } else {
    echo "前日のカレンダー予定\n\n";
    foreach ($results->getItems() as $event) {
      $start = $event->start->dateTime;
      $end = $event->end->dateTime;
      if (empty($start)) {
        // $startdate = $event->start->date;
        printf("%s \n", $event->getSummary());
      }else{
        $startdate = date("m/d H:i", strtotime($start));
        $enddate = date("H:i", strtotime($end));
        printf("%s (%s-%s)\n", $event->getSummary(), $startdate, $enddate);
      }
    }
  }
  //Tommorrow for redmine
  //得上さん以外の人だけ
  if($calendarId != 'tokugami@aucfan.com') {
    echo "\n" . '今日の作業予定内容' . "\n\n";
    if (!isset($next_task[$member['name']])) {
      echo "未定\n";
    } else {
      $task_list = $next_task[$member['name']];
      foreach ($task_list as $task) {
        echo $task . "\n";
      }
    }
  }
  echo "\n\n";
  //Tommorrow for calendar
    $optParams = array(
        'maxResults' => 20,
        'orderBy' => 'startTime',
        'singleEvents' => TRUE,
        'timeMin' => date(DATE_ATOM, strtotime($tomorrow)),
        'timeMax' => date(DATE_ATOM, strtotime($tomorrow . ' +1 day'))
    );
  $results = $service->events->listEvents($calendarId, $optParams);
  if (count($results->getItems()) == 0) {
    echo '本日のカレンダー予定' . "\n\n";
    echo "  なし\n";
  } else {
    echo "本日のカレンダー予定\n\n";
    foreach ($results->getItems() as $event) {
      $start = $event->start->dateTime;
      $end = $event->end->dateTime;
      if (empty($start)) {
        //$startdate = $event->start->date;
        printf("%s \n", $event->getSummary());
      }else{
        $startdate = date("m/d H:i", strtotime($start));
        $enddate = date("H:i", strtotime($end));
        printf("%s (%s-%s)\n", $event->getSummary(), $startdate, $enddate);
      }
    }
  }
  print "\n";
}
echo "以上。\n";
