<?php
	require('../../global.php');
	
	//验证是否登入
	if(!checkLogin()){
		redirect('../error.php?code='.$errorcode['noLogin']);
	}
	//验证是否为管理员
	else if(!checkAdmin()){
		redirect('../error.php?code='.$errorcode['noAdmin']);
	}
	//验证是否有权限
	else if(!checkPermissions(1)){
		redirect('../error.php?code='.$errorcode['noPermissions']);
	}
?>
<!DOCTYPE HTML>
<html>
<head>
<meta charset="utf-8">
<title>应用管理</title>
<?php include('sysapp/global_css.php'); ?>
<link rel="stylesheet" href="../../img/ui/sys.css">
<style>
body{margin:10px 10px 0}
.appicon{width:32px;height:32px}
.appname{margin-left:10px}
</style>
</head>

<body>
<div class="well well-small" style="margin-bottom:10px">
	<div class="form-inline">
		<label>名称：</label>
		<input type="text" name="search_1" id="search_1" class="span2">
		<label style="margin-left:10px">分类：</label>
		<select name="search_2" id="search_2" style="width:150px">
			<option value="">全部</option>
			<?php
				foreach($apptype as $at){
					echo '<option value="'.$at['id'].'">'.$at['name'].'</option>';
				}
			?>
		</select>
		<a class="btn" menu="search" href="javascript:;" style="margin-left:10px"><i class="icon-search"></i> 搜索</a>
		<a class="btn btn-primary fr" href="javascript:openDetailIframe('detail.php');"><i class="icon-white icon-plus"></i> 添加新应用</a>
	</div>
</div>
<table class="list-table">
	<thead>
		<tr class="col-name">
			<th>应用名称</th>
			<th style="width:100px">类型</th>
			<th style="width:100px">分类</th>
			<th style="width:100px">使用人数</th>
			<th style="width:200px">操作</th>
		</tr>
		<tr class="sep-row"><td colspan="100"></td></tr>
		<tr class="toolbar">
			<td colspan="100">
				<b style="margin:0 10px">符合条件的记录</b>有<font class="list-count">0</font>条
			</td>
		</tr>
		<tr class="sep-row"><td colspan="100"></td></tr>
	</thead>
	<tbody class="list-con"></tbody>
	<tfoot><tr><td colspan="100">
		<div class="pagination pagination-centered">
			<div id="pagination"></div>
		</div>
		<input id="pagination_setting" type="hidden" per="10">
	</td></tr></tfoot>
</table>
<?php include('sysapp/global_module_detailIframe.php'); ?>
<?php include('sysapp/global_js.php'); ?>
<script>
$(function(){
	//加载列表
	getPageList(0);
	//删除，推荐
	$('.list-con').on('click', '.do-del', function(){
		var appid = $(this).attr('appid');
		var appname = $(this).parents('tr').children('td:first-child').text();
		$.dialog({
			id : 'del',
			content : '确定要删除 “' + appname + '” 该应用么？',
			ok : function(){
				$.ajax({
					type : 'POST',
					url : 'index.ajax.php',
					data : 'ac=del&appid=' + appid
				}).done(function(){
					$('#pagination').trigger('currentPage');
				});
			},
			cancel: true
		});
	}).on('click', '.do-recommend', function(){
		var appid = $(this).attr('appid');
		$.ajax({
			type : 'POST',
			url : 'index.ajax.php',
			data : 'ac=recommend&appid=' + appid
		}).done(function(){
			$('#pagination').trigger('currentPage');
		});
	});
	//搜索
	$('a[menu=search]').click(function(){
		getPageList(0);
	});
});
function initPagination(current_page){
	$('#pagination').pagination(parseInt($('#pagination_setting').attr('count')), {
		current_page : current_page,
		items_per_page : parseInt($('#pagination_setting').attr('per')),
		num_display_entries : 9,
		num_edge_entries : 2,
		callback : getPageList,
		prev_text : '上一页',
		next_text : '下一页'
	});
}
function getPageList(current_page){
	ZENG.msgbox.show('正在加载中，请稍后...', 6, 100000);
	var from = current_page * parseInt($('#pagination_setting').attr('per')), to = parseInt($('#pagination_setting').attr('per'));
	$.ajax({
		type : 'POST', 
		url : 'index.ajax.php', 
		data : 'ac=getList&from=' + from + '&to=' + to + '&search_1=' + $('#search_1').val() + '&search_2=' + $('#search_2').val(),
		success : function(msg){
			var arr = msg.split('<{|*|}>');
			$('#pagination_setting').attr('count', arr[0]);
			$('.list-count').text(arr[0]);
			$('.list-con').html(arr[1]);
			initPagination(current_page);
			ZENG.msgbox._hide();
		}
	}); 
}
</script>
</body>
</html>