<?php

function japan_holiday()
{
    $year = date('Y');
    $apiKey = 'AIzaSyCcjxqywYYuCtq9s6o5MYXfzIAkCEtU8eg';  // 手順4で作成したAPIキー
    $holidays = array();
 
    // カレンダーID 
    $calendar_id = urlencode('japanese__ja@holiday.calendar.google.com');
 
    // 取得期間
    $start  = date($year."-01-01\T00:00:00\Z");
    $finish = date($year."-12-31\T00:00:00\Z");
 
    $url = "https://www.googleapis.com/calendar/v3/calendars/{$calendar_id}/events?key={$apiKey}&timeMin={$start}&timeMax={$finish}&maxResults=50&orderBy=startTime&singleEvents=true";
//pr($url);
 
    if ($results = file_get_contents($url, true)) {
        // JSON形式で取得した情報を配列に格納
        $results = json_decode($results);
 
        // 年月日をキー、祝日名を配列に格納
        foreach ($results->items as $item) {
            $date = strtotime((string) $item->start->date);
            $title = (string) $item->summary;
            $holidays[date('Y-m-d', $date)] = $title;
        }
 
        // 祝日の配列を並び替え
        ksort($holidays);
        //get only holiday
        foreach ($holidays as $key => $value) {
            $hol[] = $key;
        }
        //get saturday and sunday
        $dayNR = date('N');         //monday = 1, tuesday = 2, etc.
        $satDiff = 6-$dayNR;        //for monday we need to add 5 days -> 6 - 1
        $sunDiff = $satDiff+1;      //sunday is one day more
        $satDate = date("Y/m/d", strtotime(" +".$satDiff." days"));
        $sunDate = date("Y/m/d", strtotime(" +".$sunDiff." days"));
        //holiday and weekend
        array_push($hol, $satDate, $sunDate);
    }
    return $hol; 
}
//$holiday = japan_holiday();
//var_dump($holiday);
$today = date('Y/m/d');
$tomorrow = get_tomorrow($today);
//check for tomorrow
/*
function holiday($tomorrow){
    $holiday = japan_holiday();
    //var_dump($holiday);
    if(in_array($tomorrow, $holiday)){
        $tomorrow = get_tomorrow($tomorrow);
        holiday($tomorrow);
    }else {
        return $tomorrow;
    }
}*/
//check for tomorrow
function holiday($tomorrow)
{
    $holiday = japan_holiday();
    $max = sizeof($holiday);
    for($i=1; $i<=$max; $i++) {
        if (in_array($tomorrow, $holiday)) {
            $tomorrow = get_tomorrow($tomorrow);
        }
    }
    return $tomorrow;
}
//get tomorrow
function get_tomorrow($today){
    $today = strtotime($today);
    $tomorrow = date('Y/m/d', $today +60*60*24);
    return $tomorrow;
}

