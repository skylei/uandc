var ajaxLoad = {};
ajaxLoad.start = function () {
	$("#ajaxloading").show();	
}
ajaxLoad.end = function () {
	$("#ajaxloading").hide();		
}
/* 表单AJAX提交 */

var submitForm = function (id, url) {
	$('#' + id).submit(function(){
		 ajaxLoad.start();
		 $(".btn2").attr("disabled",true);  
		 $('#' + id).ajaxSubmit(function(result) {
				ajaxLoad.end();
				if (url == '') url = location.href;
				result = eval('(' + result + ')');
				if (result.status == 0) {
					$(".btn2").attr("disabled",false);  
					$("#ajax_msg").html('&nbsp;&nbsp;<img src="static/images/admin/no.gif">&nbsp;&nbsp;' + result.message);
				} else {
					$("#ajax_msg").html('&nbsp;&nbsp;<img src="static/images/admin/yes.gif">&nbsp;&nbsp;' + result.message);
					setInterval(function(){
						location.href = url;
						return;
					},1000);
				}
				return false;
		 });
	 return false;
	 });
}

/* del */
var del = function (msg, url, data) {
	if (confirm(msg) == true) { 
		ajaxLoad.start();
		$.ajax({
     		type: "POST",
     		url: url,
			dataType: 'json',
     		data: data,
     		success: function(result){ 
				ajaxLoad.end();
				if (result.status == 0) {
					$("#ajax_msg").html('&nbsp;&nbsp;<img src="static/images/admin/no.gif">&nbsp;&nbsp;' + result.message);
				} else {
					location.href = location.href;	
				}
			}   
		}); 
	}	
}

//弹出层
function sAlert(strTitle,strContent){
    var msgw,msgh,bordercolor;
    msgw=350;//提示窗口的宽度
    msgh=100;//提示窗口的高度
    titleheight=25 //提示窗口标题高度
    bordercolor="#000000";//提示窗口的边框颜色
    titlecolor="#99CCFF";//提示窗口的标题颜色

    var sWidth,sHeight;
    sWidth=document.body.offsetWidth;
    sHeight=screen.height;
    var bgObj=document.createElement("div");
    bgObj.setAttribute('id','bgDiv');
    bgObj.style.position="absolute";
    bgObj.style.top="0";
    bgObj.style.background="#FFFFFF";
    bgObj.style.filter="progid:DXImageTransform.Microsoft.Alpha(style=3,opacity=25,finishOpacity=20";
    bgObj.style.opacity="0.6";
    bgObj.style.left="0";
    bgObj.style.width="100%";
    bgObj.style.height="100%";
    bgObj.style.zIndex = "10000";
    document.body.appendChild(bgObj);
	


    var msgObj=document.createElement("div")
    msgObj.setAttribute("id","msgDiv");
    msgObj.setAttribute("align","center");
    msgObj.style.background="white";
    msgObj.style.border="1px solid " + bordercolor;
    msgObj.style.position = "absolute";
    msgObj.style.left = "50%";
    msgObj.style.top = "50%";
    msgObj.style.font="12px/1.6em Verdana, Geneva, Arial, Helvetica, sans-serif";
    msgObj.style.marginLeft = "-225px" ;
    
    msgObj.style.width = msgw + "px";
    msgObj.style.height =msgh + "px";
    msgObj.style.textAlign = "center";
    msgObj.style.lineHeight ="25px";
    msgObj.style.zIndex = "10001";

	if (parent.document.documentElement.scrollTop > 0) {
		msgObj.style.marginTop = (parent.document.documentElement.scrollTop - document.body.offsetHeight/2 + 141) + 'px';
	} else {
		msgObj.style.marginTop = (parent.document.documentElement.scrollTop - document.body.offsetHeight/2 + 141) + 'px';
	}


    var title=document.createElement("h4");
    title.setAttribute("id","msgTitle");
    title.setAttribute("align","right");
    title.style.margin="0";
    title.style.padding="3px";
    title.style.background=bordercolor;
    title.style.filter="progid:DXImageTransform.Microsoft.Alpha(startX=20, startY=20, finishX=100, finishY=100,style=1,opacity=75,finishOpacity=100);";
    title.style.opacity="0.75";
    title.style.border="1px solid " + bordercolor;
    title.style.height="18px";
    title.style.font="12px Verdana, Geneva, Arial, Helvetica, sans-serif";
    title.style.color="white";
    title.style.cursor="pointer";
    title.title = "点击关闭";
    title.innerHTML="<table border='0' width='100%'><tr><td align='left'><b>"+ strTitle +"</b></td><td align='right'>[关闭]</td></tr></table></div>";
    title.onclick=function(){
    document.body.removeChild(bgObj);
    document.getElementById("msgDiv").removeChild(title);
    document.body.removeChild(msgObj);
    }
    document.body.appendChild(msgObj);
    document.getElementById("msgDiv").appendChild(title);
    var txt=document.createElement("p");
    txt.style.margin="1em 0"
    txt.setAttribute("id","msgTxt");
    txt.innerHTML=strContent;
    document.getElementById("msgDiv").appendChild(txt);
	
	$("#msg_close").click(function () {
		document.body.removeChild(bgObj);
    	document.getElementById("msgDiv").removeChild(title);
    	document.body.removeChild(msgObj);								
	});
} 
