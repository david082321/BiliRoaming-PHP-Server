<?php
	$array = json_decode($output, true);
	$code = $array['code'];
	if ($code == "0" || $code == 0) {
		$ss_id = $array['result']['season_id'];
		$url = SUBTITLE_API.$ss_id;
		$replace_json = get_webpage($url);
		$replace_array = json_decode($replace_json, true);
		$code = $replace_array['code'];
		if ($code == "0") {
			$replace = $replace_array['data'];
			$items = $array['result']['modules'][0]['data']['episodes'];
			$count = count($items);
			for ($i=0; $i<count($replace); $i++) {
				$ep = $replace[$i]['ep'];
				$key = $replace[$i]['key'];
				$lang = $replace[$i]['lang'];
				$url = $replace[$i]['url'];
				if ($ep < $count) {
					$sub_arr = $array['result']['modules'][0]['data']['episodes'][$ep]['subtitles'];
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
					$array['result']['modules'][0]['data']['episodes'][$ep]['subtitles'] = $sub_arr;
				}
			}
			$output = json_encode($array, JSON_UNESCAPED_UNICODE);
		}
	}
?>
