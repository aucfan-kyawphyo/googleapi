<?php

$email_map = array(
  'makise@aucfan.com'        => array('name' => '牧瀬', 'user_id' => 88),
  'ida@aucfan.com'           => array('name' => '井田', 'user_id' => 16),
  'yotsuya@aucfan.com'       => array('name' => '四谷', 'user_id' => 57),
  'takaya@aucfan.com'        => array('name' => '高屋', 'user_id' => 95),
  'che@aucfan.com'           => array('name' => 'チェ', 'user_id' => 67),
  'tanikado@aucfan.com'      => array('name' => '谷門', 'user_id' => 96),
  'horikoshi@aucfan.com'     => array('name' => '堀越', 'user_id' => 100),
  'kyawphyonaing@aucfan.com' => array('name' => 'ちょーぴょー', 'user_id' => 97),
);


$name_list = array();
foreach ($email_map as $member) {
  $name_list[] = $member['name'];
}


// 読み込むファイル名の指定
$file_name = "./task.csv";
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

foreach ($email_map as $email => $member) {
  echo '#' . $member['name'] . "\n";
  echo '本日の作業内容' . "\n";
  if (!isset($result[$email])) {
    echo 'なし' . "\n\n";
  }
  else {
    $task_list = $result[$email];
    foreach ($task_list as $project_name => $project_list) {
      echo ' ' . $project_name . "\n";
      foreach ($project_list as $task) {
        echo "  " . $task['title'] . ':' . $task['time'] . "時間\n";
        echo "  * " . $task['url'] . "\n";
      }
      echo "\n";
    }
  }
  echo '明日の作業予定内容' . "\n";
  if (!isset($next_task[$member['name']])) {
    echo "未定\n";
  }
  else {
    $task_list = $next_task[$member['name']];
    foreach ($task_list as $task) {
      echo " " . $task . "\n";
    }
  }
  echo "\n";
}


