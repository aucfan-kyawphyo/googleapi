<?php
	//ライブラリの読み込み
	require_once "./Feed.php";

	//取得するフィードのURLを指定
    $person_url = "https://dsol:dsolredmine@dsolmine.aucfan.com/time_entries.atom?c%5B%5D=project&c%5B%5D=spent_on&c%5B%5D=user&c%5B%5D=activity&c%5B%5D=issue&c%5B%5D=comments&c%5B%5D=hours&f%5B%5D=spent_on&f%5B%5D=user_id&f%5B%5D=&key=44508922c468f805fef688447e31054779ac467a&op%5Bspent_on%5D=w&op%5Buser_id%5D=%3D&utf8=%E2%9C%93&v%5Buser_id%5D%5B%5D=";

    $name_map = array(
      'makise@aucfan.com' => array('name' => '牧瀬', 'user_id' => 88),
      'ida@aucfan.com' => array('name' => '井田', 'user_id' => 16),
      'yotsuya@aucfan.com' => array('name' => '四谷', 'user_id' => 57),
      'takaya@aucfan.com' => array('name' => '高屋', 'user_id' => 95),
      'che@aucfan.com' => array('name' => 'チェ', 'user_id' => 67),
      'tanikado@aucfan.com' => array('name' => '谷門', 'user_id' => 96),
      'horikoshi@aucfan.com'     => array('name' => '堀越', 'user_id' => 100),
      'kyawphyonaing@aucfan.com' => array('name' => 'ちょーぴょー', 'user_id' => 97),
    );

   $result = array();
   foreach ($name_map as $member) {
     //インスタンスの作成
     $feed = new Feed;
     //RSSを読み込む
     $atom = $feed->loadAtom($person_url .$member['user_id']);
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

foreach($name_map as $email => $member) {
  echo '#' .$member['name'] . "\n";
  if (! isset($result[$email])) {
    echo 'なし' . "\n\n";
    continue;
  }
  $task_list = $result[$email];
  foreach ($task_list as $project_name => $project_list) {
    echo ' ' .$project_name . "\n";
    foreach ($project_list as $task) {
      echo "  " . $task['title'] . ':' . $task['time'] . "時間\n";
      echo "  * " . $task['url'] . "\n";
    }
    echo "\n";
  }
}

