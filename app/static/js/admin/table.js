// JavaScript Document
var table = {};
table.init = function () {
	table.line();	
	$("table tr").bind("mouseover", table.tr_mouseover); //表格TR移动上去效果
	$("table tr").bind("mouseout", table.tr_mouseout); //表格TR移开效果
	$(".input").bind("focus", table.input_focus); //表单选中效果
	$(".input").bind("blur", table.input_blur); //表单移开效果
	$("table tr td textarea").bind("focus", table.input_focus); //表单选中效果
	$("table tr td textarea").bind("blur", table.input_blur); //表单移开效
	$(".content_tab ul li").bind("mouseover", table.title_mouseover); //表单移开效
	$(".content_tab ul li").bind("mouseout", table.title_mouseout); //表单移开效
	$(".content_tab ul li").bind("click", table.title_click); //表单移开效
	$('.Wdate').bind('click', table.date);
	table.html();
}
/* 初始化表格 表格隔行变色*/
table.line = function () {
	$("table").each(function(j) {
		$("table").eq(j).find('tr').each(function(i) {	
			if ((i%2) == 0) {
				//$(this).addClass('tbalt');
			} else {
				$(this).find('th').css('background', '#FFFFFF');	
			}
		})
	});
}

table.tr_mouseover = function () {
	$(this).addClass('tr_mouseover');
}

table.tr_mouseout = function () {
	$(this).removeClass('tr_mouseover');
}

table.input_focus = function () {
	$(this).addClass('input_fouce');	
}

table.input_blur = function () {
	$(this).removeClass('input_fouce');	
}
table.title_mouseover = function () {
	$(this).addClass('over');	
}
table.title_mouseout = function () {
	$(this).removeClass('over');	
}
table.title_click = function () {
	var url = $(this).attr('name');
	location.href = url;
}

table.openAll = function () {
	$(".left", parent.document).hide();
	$(".header", parent.document).hide();
	$(".footer", parent.document).hide();	
	$(".right", parent.document).css("margin-left", '15px');	
	$(".right", parent.document).addClass('right_all');
	$(".content_tab").eq(0).find('a').eq(1).html('缩小');
	$(".content_tab").eq(0).find('a').eq(1).attr('href','javascript:table.closeAll()');
	parent.open_i = 1;
}
table.closeAll = function () {
	$(".left", parent.document).show();
	$(".header", parent.document).show();
	$(".footer", parent.document).show();	
	$(".right", parent.document).removeClass('right_all');
	$(".content_tab").eq(0).find('a').eq(1).html('全屏');
	$(".content_tab").eq(0).find('a').eq(1).attr('href','javascript:table.openAll()');
	parent.open_i = 0;
}
table.date = function () {
	WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss'});
}
table.html = function () {
	if (parent.open_i == 0) {
		$(".content_tab").eq(0).append('<div class="append_tab"><a href="javascript:location.href=location.href;">刷新</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="javascript:table.openAll()">全屏</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="javascript:history.back();">返回上一页</a></div>');
	} else {
		$(".content_tab").eq(0).append('<div class="append_tab"><a href="javascript:location.href=location.href;">刷新</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="javascript:table.closeAll()">缩小</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="javascript:history.back();">返回上一页</a></div>');
	}
}
table.init();
