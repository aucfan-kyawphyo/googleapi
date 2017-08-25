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
/*
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

// Print the next 10 events on the user's calendar.
$calendarId = 'primary';
$optParams = array(
  'maxResults' => 10,
  'orderBy' => 'startTime',
  'singleEvents' => TRUE,
  'timeMin' => date('c'),
);
$results = $service->events->listEvents($calendarId, $optParams);

if (count($results->getItems()) == 0) {
  print "No upcoming events found.\n";
} else {
  print "Upcoming events:\n";
  foreach ($results->getItems() as $event) {
    $start = $event->start->dateTime;
    if (empty($start)) {
      $start = $event->start->date;
    }
    printf("%s (%s)\n", $event->getSummary(), $start);
  }
}

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
  'fujinaga@aucfan.com'       => array('name' => '藤永', 'user_id' => 105),
  'manabe@aucfan.com'         => array('name' => '真鍋', 'user_id' => 36),
  'makise@aucfan.com'         => array('name' => '牧瀬', 'user_id' => 88),
  'yotsuya@aucfan.com'        => array('name' => '四谷', 'user_id' => 57),
  'tani@aucfan.com'           => array('name' => '谷', 'user_id' => 117),
  'n_tanaka@aucfan.com'       => array('name' => '田中', 'user_id' => 137),
  'yoriko.yamada@aucfan.com'  => array('name' => '山田', 'user_id' => 154),
  'nobuyuki.honma@aucfan.com' => array('name' => '本間', 'user_id' => 166),
  'kyawphyonaing@aucfan.com'  => array('name' => 'チョー', 'user_id' => 97),
  'kakehi@aucfan.com'         => array('name' => '筧', 'user_id' => 98),
  'lai@aucfan.com'            => array('name' => 'ライ', 'user_id' => 132),
  'wu@aucfan.com'             => array('name' => 'ウ', 'user_id' => 138),
);
///////////////////////////////////////

//redmine prepare

$name_list = array();
foreach ($email_map as $member) {
  $name_list[] = $member['name'];
}
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
echo "各位\n本日の作業内容を共有いたします。\n";
// Print calendar and redmine
foreach($email_map as $email => $member) {
  $calendarId = $email;
  echo  "\n" .'#' . $member['name'] . "\n";
    //Today for redmine
    echo '本日の作業内容' . "\n\n";
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
    echo '今日のカレンダー予定' . "\n\n";
    echo "なし\n";
  } else {
    echo "今日のカレンダー予定\n\n";
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
    echo '明日のカレンダー予定' . "\n\n";
    echo "  なし\n";
  } else {
    echo "明日のカレンダー予定\n\n";
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
echo "==END==";
