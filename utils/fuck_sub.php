<?php
	$array = json_decode($output, true);
	$code = $array['code'];
	if ($code == "0" || $code == 0) {
		// 替换 aid
		$episodes_items = $array['result']['modules'][0]['data']['episodes'];
		for ($i=0; $i<count($episodes_items); $i++) {
			$array['result']['modules'][0]['data']['episodes'][$i]['aid'] = TH_AID;
		}
		// 判断 season 内容
		if (count($array['result']) > 0) {
			$ss_type = "result";
		} elseif (count($array['data']) > 0) {
			$ss_type = "data";
		} else {
			$ss_type = "";
		}
		// 下载要替换的字幕
		if (SUBTITLE_API != 'https://example.com/path?season_id=') {
			if ($ss_type == "result") {
				$ss_id = $array['result']['season_id'];
			} else {
				$ss_id = $array['data']['season_id'];
			}
			$url = SUBTITLE_API.$ss_id;
			$replace_json = get_webpage($url);
			$replace_array = json_decode($replace_json, true);
			$code = $replace_array['code'];
			// 替换字幕
			if ($code == "0") {
				$replace = $replace_array['data'];
				if ($ss_type == "result") {
				    $items = $array['result']['modules'][0]['data']['episodes'];
				} else {
					$items = $array['data']['sections']['section'][0]['ep_details'];
				}
				$count = count($items);
				for ($i=0; $i<count($replace); $i++) {
					$ep = $replace[$i]['ep'];
					$key = $replace[$i]['key'];
					$lang = $replace[$i]['lang'];
					$url = $replace[$i]['url'];
					if ($ep < $count) {
						if ($ss_type == "result") {
							$sub_arr = $array['result']['modules'][0]['data']['episodes'][$ep]['subtitles'];
						} else {
							$sub_arr = $array['data']['sections']['section'][0]['ep_details'][$ep]['subtitles'];				    
						}
						$sub_count = count($sub_arr);
						$add_arr = array(
							"id"=>1,
							"key"=>$key,
							"title"=>"[非官方]".$lang."(".SUBTITLE_TEAM_NAME.")",
							"url"=>"https://".$url,
							"is_machine"=>false
						);
						array_unshift($sub_arr,$add_arr); //这个会放在前面
						//array_push($sub_arr,$add_arr); //这个会放在后面
						if ($ss_type == "result") {
							$array['result']['modules'][0]['data']['episodes'][$ep]['subtitles'] = $sub_arr;
						} else {
							$array['data']['sections']['section'][0]['ep_details'][$ep]['subtitles'] = $sub_arr;				    
						}
						
					}
				}
			}
		}
		$output = json_encode($array, JSON_UNESCAPED_UNICODE);
	}
?>
