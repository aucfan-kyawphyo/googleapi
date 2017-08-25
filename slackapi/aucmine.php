<?php

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


$name_list = array();
foreach ($email_map as $member) {
  $name_list[] = $member['name'];
}

//ライブラリの読み込み
require_once "/deploy/googleapi/slackapi/Feed.php";

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
foreach ($email_map as $email => $member) {
  echo '#' . $member['name'] . "\n\n";
  echo '・本日の作業内容' . "\n\n";
  if (!isset($result[$email])) {
    echo 'なし' . "\n\n\n";
  }
  else {
    $task_list = $result[$email];
    foreach ($task_list as $project_name => $project_list) {
      echo ' ' . $project_name . "\n";
      foreach ($project_list as $task) {
        echo "  " . $task['title'] . ' : ' . $task['time'] . "時間\n";
        echo "  * " . $task['url'] . "\n";
      }
      echo "\n\n";
    }
  }
}
