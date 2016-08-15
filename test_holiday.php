<?php
//$today = date('Y/m/d', mktime(0,0,0,9,18,2015));
$today = date('Y/m/d');
//echo $today;
$tom = get_tom($today);
holiday($tom);




//////
/*

$start = $now = 1280293200; // starting time
$end = 1283014799; // ending time
$day = intval(date("N", $now));
$weekends = array();
if ($day < 6) {
  $now += (6 - $day) * 86400;
}
while ($now <= $end) {
  $day = intval(date("N", $now));
  if ($day == 6) {
    $weekends[] += $now;
    $now += 86400;
  }
  elseif ($day == 7) {
    $weekends[] += $now;
    $now += 518400;
  }
}
echo "Weekends from " . date("r", $start) . " to " . date("r", $end) . ":\n";
foreach ($weekends as $timestamp) {
  echo date('Y/m/d', $timestamp) . "\n";
}

*/
//var_dump(mktime(0,0,0,10,2015));
function holiday($tom){
	//$holiday = array('Sun', 'Sat', 'Tue');
	$holiday = japan_holiday();
	//var_dump($holiday);
	if(in_array($tom, $holiday)){
		$tom = get_tom($tom);
		holiday($tom);
	}else {
		echo $tom ."\n";
	}
}

function get_tom($today){
	$today = strtotime($today);
	$tom = date('Y/m/d', $today +60*60*24);
	return $tom;
}

function japan_holiday() {
    // カレンダーID
    $calendar_id = urlencode('japanese__ja@holiday.calendar.google.com');
    // 取得期間
    $start  = date("Y-10-01\T00:00:00\Z");
    $end = date("Y-12-31\T00:00:00\Z");
    $url = 'https://www.google.com/calendar/feeds/'.$calendar_id.'/public/basic';
    $url .= '?start-min='.$start;
    $url .= '&start-max='.$end;
    $url .= '&max-results=30';
    $url .= '&alt=json';
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $result = curl_exec($ch);
    curl_close($ch);
    if (!empty($result)) {
        $json = json_decode($result, true);
        if (!empty($json['feed']['entry'])) {
            $datas = array();
            foreach ($json['feed']['entry'] as $val) {
                $date = preg_replace('#\A.*?(2\d{7})[^/]*\z#i', '$1', $val['id']['$t']);
                ////var_dump($date);
                $datas[$date] = array(
                    'date' => preg_replace('/\A(\d{4})(\d{2})(\d{2})/', '$1/$2/$3', $date),
                    'title' => $val['title']['$t'],
                );
            }
            $dayNR = date('N');         //monday = 1, tuesday = 2, etc.
            $satDiff = 6-$dayNR;        //for monday we need to add 5 days -> 6 - 1
            $sunDiff = $satDiff+1;      //sunday is one day more
            $satDate = date("Y/m/d", strtotime(" +".$satDiff." days"));
            $sunDate = date("Y/m/d", strtotime(" +".$sunDiff." days"));

//            echo $satDate."\n";
//            echo $sunDate."\n";

            ksort($datas);
			foreach ($datas as $key => $value) {
			//$hol[] = strtotime($value["date"]);
			$hol[] = ($value["date"]);
			//$day[] = date("D", $hol);
			}
            array_push($hol, $satDate, $sunDate);
//var_dump($hol);
            return $hol;
        }
    }
}
