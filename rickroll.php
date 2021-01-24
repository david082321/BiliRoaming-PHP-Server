<?php
$url = "/x/player/playurl?cid=137649199&qn=0&type=&otype=json&fourk=0&bvid=BV1GJ411x7h7&fnver=0&fnval=80";
$host = "https://api.bilibili.com";

$ch = curl_init();
curl_setopt($ch,CURLOPT_URL,$host.$url);
curl_setopt($ch,CURLOPT_FOLLOWLOCATION,true); 
curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
curl_setopt($ch,CURLOPT_HTTPHEADER, array(
    'User-Agent: '.@$_SERVER["HTTP_USER_AGENT"]
));
$output = curl_exec($ch);
curl_close($ch);
//exit($output);

if (@$_GET['type']=="web"){
    $array = json_decode($output, true);
	$array2['code'] =  $array['code'];
	$array2['message'] =  $array['message'];
	$array2['result'] =  $array['data'];
    // 发送内容
    header('Content-Type: application/json; charset=utf-8');
    $output = json_encode($array2);
    echo $output;
}else{
    // 分析 output
    $array = json_decode($output, true);
    $array2['timelength'] = $array['data']['timelength'];
    // 分析 output(视频)
    $video = $array['data']['dash']['video'][0];
    $array2['v_base_url'] = $video['base_url'];
    $array2['v_bandwidth'] = $video['bandwidth'];
    $array2['v_backup_url'] =  $video['backup_url'];
    // 分析 output(音频)
    $audio = $array['data']['dash']['audio'][0];
    $array2['a_base_url'] = $audio['base_url'];
    $array2['a_bandwidth'] = $audio['bandwidth'];
    $array2['a_backup_url'] =  $audio['backup_url'];
    // 发送内容
    header('Content-Type: application/json; charset=utf-8');
    $output = json_encode($array2);
    echo $output;
}

?>