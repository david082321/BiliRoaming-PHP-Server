<?php
// 防止外部破解
if(!defined('SYSTEM')) {exit();}

function replace_playurl() {
	global $host;
	global $path;
	// 判断来源
	switch ($path) {
		case "/pgc/player/api/playurl": //APP playurl
			$type = "main";
			break;
		case "/intl/gateway/v2/ogv/playurl": //东南亚APP playurl
			$type = "intl";
			break;
		case "/pgc/player/web/playurl": //WEB playurl
			$type = "web";
			break;
		default:
			$type = "web";
	}

	$url = 'https://bili.tuturu.top/cid_rand.php?type='.$type;
	$output = get_webpage($url);

	// 分析 output
	$array = json_decode($output, true);
	$timelength = $array['timelength'];
	$base_url = $array['v_base_url'];
	$bandwidth = $array['v_bandwidth'];
	$backup_url =  $array['v_backup_url'];
	$a_base_url = $array['a_base_url'];
	$a_bandwidth = $array['a_bandwidth'];
	$a_backup_url =  $array['a_backup_url'];

	if ($type == "web") {
		exit($output);
	}

	// 转发到指定服务器
	$url = $host.$path."?".$_SERVER['QUERY_STRING'];
	$output2 = get_webpage($url);
	if ($type == "intl") {
		$array2 = json_decode($output2, true);
		$array2['data']['video_info']['timelength'] = $timelength;

		// 替换视频
		// 好像是count不到正确数量，不晓得有没有人会改的
		//$v_count = count($array2['data']['video_info']['stream_list']);
		for($j=0;$j<5;$j++) {
			$array2['data']['video_info']['stream_list'][$j]['dash_video']['base_url'] = $base_url;
			$array2['data']['video_info']['stream_list'][$j]['dash_video']['backup_url'] = $backup_url;
			$array2['data']['video_info']['stream_list'][$j]['dash_video']['bandwidth'] = $bandwidth;
		}

		// 替换音频
		//$a_count = count($array2['data']['video_info']['dash_audio']);
		for($j=0;$j<3;$j++) {
			$array2['data']['video_info']['dash_audio'][$j]['base_url'] = $a_base_url;
			$array2['data']['video_info']['dash_audio'][$j]['backup_url'] = $a_backup_url;
			$array2['data']['video_info']['dash_audio'][$j]['bandwidth'] = $a_bandwidth;
		}
	} elseif ($type == "main") {
		$array2 = json_decode($output2, true);
		$array2['timelength'] = $timelength;

		// 替换视频
		$video = $array2['dash']['video'];
		$count3 = count($video);
		for($j=0;$j<$count3;$j++) {
			$array2['dash']['video'][$j]['base_url'] = $base_url;
			$array2['dash']['video'][$j]['backup_url'] = $backup_url;
			$array2['dash']['video'][$j]['bandwidth'] = $bandwidth;
		}

		// 替换音频
		$audio = $array2['dash']['audio'];
		$count4 = count($audio);
		for($j=0;$j<$count4;$j++) {
			$array2['dash']['audio'][$j]['base_url'] = $a_base_url;
			$array2['dash']['audio'][$j]['backup_url'] = $a_backup_url;
			$array2['dash']['audio'][$j]['bandwidth'] = $a_bandwidth;
		}
	}

	// 发送内容
	$output3 = json_encode($array2);
	$output3 = str_replace("\/","/",$output3);
	exit($output3);
}
?>
