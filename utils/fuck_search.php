<?php
	// 标题
	$set_title = "公告：<em class=\\\"keyword\\\">禁止宣传</em>";
	// 头像
	$set_cover = "https://i0.hdslb.com/bfs/face/046974d6dde4af386f7eb4f231b84ec08bad693b.jpg";
	// URI（似乎无用）
	$set_uri = "https://github.com/david082321/BiliRoaming-PHP-Server";
	// 副标题1
	$set_style = "切勿宣扬，发现拉黑！";
	// 副标题2
	$set_label = "勿谓言之不预也";
	// 投票分数
	$set_rating = "114.514";
	// 投票数
	$set_vote = "1919.810";
	// 观看按钮
	$set_watch_button_title = "？";
	$set_watch_button_link = "https://www.bilibili.com/video/av928861104";
	// 追番按钮
	$set_follow_button_title = "别点";
	$set_unfollow_button_title = "谢邀";
	// 头像右上角标签
	$set_badges = "萨日朗";
	// 显示方式：grid 显示为一行    horizontal 列表显示
	$set_selection_style = "horizontal";
	// 选集内容
	$set_episodes = '{"title":"教程","uri":"https://github.com/yujincheng08/BiliRoaming/wiki#使用方法"},{"title":"官方反馈群","uri":"https://t.me/biliroaming","badges":[{"text":"官方","text_color":"#FFFFFF","text_color_night":"#E5E5E5","bg_color":"#FB7299","bg_color_night":"#BB5B76","border_color":"#FB7299","border_color_night":"#BB5B76","bg_style":1}]},{"title":"这里没东西","uri":"https://www.bilibili.com/video/av928861104","badges":[{"text":"愿者上勾","text_color":"#FFFFFF","text_color_night":"#E5E5E5","bg_color":"#FB7299","bg_color_night":"#BB5B76","border_color":"#FB7299","border_color_night":"#BB5B76","bg_style":1}]}';

	// 开始替换
	$array = json_decode($output, true);
	if (is_array($array)) {
		@$trackid = $array['data']['trackid'];
		@$exp_str = $array['data']['exp_str'];
		@$total = $array['data']['total'];
		@$pages = $array['data']['pages'];
		@$items_old = $array['data']['items'];
		$items_new= trim(json_encode($items_old, 320), '[]');
		$items = str_replace('["追番","已追番"]', '{"0":"追番","1":"已追番"}', $items_new);
	} else {
		$trackid = "";
		$exp_str = "";
		$total = "";
		$pages = "";
		$items_old = "";
		$items = "";
	}
	$items0 = '{"title":"'.$set_title.'","cover":"'.$set_cover.'","uri":"'.$set_uri.'","param":"1","goto":"bangumi","ptime":1500000000,"season_id":1,"season_type":1,"season_type_name":"番剧","media_type":1,"style":"'.$set_style.'","styles":"'.$set_style.'","cv":"","rating":'.$set_rating.',"vote":'.$set_vote.',"area":"漫游","staff":"无","is_selection":1,"badge":"公告","episodes":[{"position":1,"uri":"https://www.bilibili.com/video/av928861104","param":"1","index":"1"}],"label":"'.$set_label.'","watch_button":{"title":"'.$set_watch_button_title.'","link":"'.$set_watch_button_link.'"},"follow_button":{"icon":"http://i0.hdslb.com/bfs/bangumi/154b6898d2b2c20c21ccef9e41fcf809b518ebb4.png","texts":{"0":"'.$set_follow_button_title.'","1":"'.$set_unfollow_button_title.'"},"status_report":"bangumi"},"selection_style":"'.$set_selection_style.'","episodes_new":['.$set_episodes.'],"badges":[{"text":"'.$set_badges.'","text_color":"#FFFFFF","text_color_night":"#E5E5E5","bg_color":"#00C0FF","bg_color_night":"#0B91BE","bg_style":1}]}';
	if (!$items_old) {
		$items = '';
	} else {
		$items = ','.$items;
	}
	$output = '{"code":0,"message":"0","ttl":1,"data":{"trackid":"'.$trackid.'","pages":'.$pages.',"total":'.$total.',"exp_str":"'.$exp_str.'","keyword":"","items": ['.$items0.$items.']}}';
?>
