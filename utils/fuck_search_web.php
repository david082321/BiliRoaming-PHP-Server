<?php
	//标题
	$set_title = "哔哩漫游 [<em class='keyword'>禁止在B站宣传漫游相关内容</em>]";
	//头像
	$set_cover = "//i0.hdslb.com/bfs/album/887ff772ba48558c82e21772f8a8d81cbf94ea1e.png";
	//风格
	$set_style = "赛博朋克";
	//地区
	$set_area = "三体宇宙";
	//cv
	$set_cv = "陈睿";
	//描述
	$set_desc = "切勿宣扬，发现拉黑！切勿宣扬，发现拉黑！<br>切勿宣扬，发现拉黑！切勿宣扬，发现拉黑！";
	//投票分数
	$set_rating = "114.514";
	//投票人数
	$set_users = "1919810";
	//显示方式：grid 显示为一行    horizontal 列表显示
	$set_selection_style = "horizontal";
	//选集内容
	$set_episodes = '{"id":114514,"cover":"","title":"EP1","url":"","release_date":"","badges":null,"index_title":"","long_title":"EP1"},{"id":114514,"cover":"","title":"EP2","url":"","release_date":"","badges":null,"index_title":"","long_title":"EP2"}';

	//开始替换
	$array = json_decode($output, true);
	@$seid = $array['data']['seid'];
	@$page = $array['data']['page'];
	@$pagesize = $array['data']['pagesize'];
	@$numResults = $array['data']['numResults'];
	@$numPages = $array['data']['numPages'];
	$result_old = $array['data']['result'];
	$result_new= trim(json_encode($result_old, 320),'[]');
	$result0 = '{"type":"media_bangumi","media_id":114514,"title":"'.$set_title.'","org_title":"'.$set_title.'","media_type":1,"cv":"'.$set_cv.'","staff":"无","season_id":114514,"is_avid":false,"hit_columns":["title"],"hit_epids":"","season_type":1,"season_type_name":"","selection_style":"'.$set_selection_style.'","ep_size":1,"url":"","button_text":"立即观看","is_follow":1,"is_selection":1,"eps":['.$set_episodes.'],"badges":"'.$set_badges.'","cover":"'.$set_cover.'","areas":"'.$set_area.'","styles":"'.$set_style.'","goto_url":"","desc":"'.$set_desc.'","pubtime":253402271999,"media_mode":1,"fix_pubtime_str":"","media_score":{"score":'.$set_rating.',"user_count":'.$set_users.'},"display_info":"","pgc_season_id":114514,"corner":2},';
	$output = '{"code":0,"message":"0","ttl":1,"data":{"seid":"'.$seid.'","page":'.$page.',"pagesize":'.$pagesize.',"numResults":"'.$numResults.'","numPages":"'.$numPages.'","suggest_keyword": "","rqt_type": "search","cost_time":"","exp_list": null,"egg_hit": 0,"result": ['.$result0.$result_new.'],"show_column": 0}}';
?>
