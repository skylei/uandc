// JavaScript Document
/* 导航JS */
var sidebar = {};

sidebar.open_bar = null; //用户记录已经打开的左侧sidebar列表

//初始化函数
sidebar.init = function() {
	$("#nav li").bind("click", sidebar.openSidebar); //主导航绑定click事件
	$(".left .title").bind("click", sidebar.openOption); //侧导航绑定click事件
	$(".sidebar_list li").bind("mouseover", sidebar.optionOver); //侧导航选项绑定鼠标移上去事件
	$(".sidebar_list li").bind("mouseout", sidebar.optionOut); //侧导航绑定鼠标移开事件
	$(".sidebar_list li").bind("click", sidebar.optionClick); //侧导航选项绑定鼠标点击事件
	sidebar.openDefaultOption(''); //展开默认进入的侧导航选项页面
}

/* 展开侧导航 */
sidebar.openSidebar = function () {
	$("#nav li").each(function (j) {
		$(this).removeClass('checked');
	})
	$(this).addClass('checked');
	var idName = $(this).attr("title");
	$('.sidebar').each(function(i) {
		$(this).hide();
	});
	sidebar._closeAllSidebar();
	$("#" + idName).show();
	sidebar.openDefaultOption(idName); //默认打开第一个
} 

/* 显示Sidebar列表中 具体的列表 */
sidebar.openOption = function () {
	var idName = $(this).attr("title");
	sidebar._closeAllSidebar();
	if (sidebar.open_bar != idName) {	
		$(this).addClass('open');
		$("#" + idName).show();
		sidebar.open_bar = idName;
	} else {
		sidebar.open_bar = null;
	}
} 

/* 打开默认选项 */
sidebar.openDefaultOption = function (val) {
	if (val == '') {
		$("#nav li").eq(0).addClass('checked');
		var idName = $("#nav li").eq(0).attr('title');
		$("#" + idName).show();
	} else {
		var idName = val;	
	}
	var ulFirst = $("#" + idName + " ul").eq(0);
	ulFirst.show(); //默认展开第一组Sidebar
	$("#" + idName + " .title").eq(0).addClass('open');
	sidebar.open_bar = ulFirst.attr('id');	
}

/* 鼠标移过效果 */
sidebar.optionOver = function () {
	$(this).addClass('over');
}

/* 鼠标移开效果 */
sidebar.optionOut = function () {
	$(this).removeClass('over');
}

/* 鼠标选中效果 */
sidebar.optionClick = function () {
	$('.selected').eq(0).removeClass('selected');
	$(this).removeClass('over');
	$(this).addClass('selected');
}

/* 关闭所有Sidebar列表函数 */
sidebar._closeAllSidebar = function () {
	$('.sidebar_list').each(function(i) {
		$(this).hide();
	});
	$('.title').each(function (j) {
		$(this).removeClass('open');
	})
}

/* 初始化 执行程序*/
sidebar.init();

/* Iframe 高度自适应 */
var iframHeight = function(idName) { 
	var ifm = document.getElementById(idName); 
	var subWeb = document.frames ? document.frames[idName].document : ifm.contentDocument; 
	if(ifm != null && subWeb != null) { 
		var height = subWeb.body.offsetHeight + 14;
		ifm.height = subWeb.body.offsetHeight;
		$('.main .left').eq(0).css('height', height);
	} 
} 


var iframeOpen = function (url, title, sign) {
	if ($("#"+sign).attr('name') != sign) {
		var iframe = '<iframe name="'+sign+'" id="'+sign+'" onload="iframHeight(\''+sign+'\')" class="iframepage" src="'+url+'" style="border:0px; width:100%;*width:99%;" frameborder="no" border="0" marginwidth="0" marginheight="0"  allowtransparency="yes"></iframe>';
		$(".iframepage").each(function(i){ 
			$(".iframepage").eq(i).hide();			   
		}); 
		$(".right").eq(0).append(iframe);	
	} else {
		$(".iframepage").each(function(i){ 
			$(".iframepage").eq(i).hide();			   
		}); 
		$("#" + sign).show();
		iframHeight(sign);
	}
	
}

var open_i = 0;