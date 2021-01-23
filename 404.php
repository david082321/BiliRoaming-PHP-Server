<?php
if (@$_GET['type']=="web"){
    exit('{"code":-10403,"message":"抱歉您所在地区不可观看！"}');
}
// 必须要用户更改UPOS的默认设置，改成不替换
exit('{"timelength":16740,"v_base_url":"https://s1.hdslb.com/bfs/static/player/media/error.mp4","v_bandwidth":172775,"v_backup_url":["https://s2.hdslb.com/bfs/static/player/media/error.mp4"],"a_base_url":"https://s1.hdslb.com/bfs/static/player/media/error.mp4","a_bandwidth":172775,"a_backup_url":["https://s2.hdslb.com/bfs/static/player/media/error.mp4"]}');
?>