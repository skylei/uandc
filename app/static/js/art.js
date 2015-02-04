$(function(){				
	$(".dscomment").toggle(function(){
			$(this).html("收起评论框");
			$(".usercomment").slideDown();	
			
			//var y = window.pageYOffset;
			$('#nickname').focus();
	},function(){
			$(this).html("发表评论");
			$(".usercomment").slideUp();
			
	});
	$('.reply').toggle(
		function(){
			$(".usercomment").slideDown();	
			
			$id = $(this).attr('id');
			var add = '<p><input type=hidden name=replyTo value='+$id+'/></p>';
			$('#nickname').parent().next().append(add);
		},
		function(){
			$(".usercomment").slideUp();
		}
	);
	
	$("#zan").bind('click',function(){
		$val = $(this).attr('value');
		
		var url = 'http://www.crab.cc/?m=Home&c=index&a=good';
		var _id = $('.reply').attr('id');
		$.post(url,{id:_id, value: $val},function(data){
			if(data == true){
				$(this).attr('value', ++$val);
			}
		});
	})
	var url = 'http://www.crab.cc/?m=Home&c=art&a=loadComment';
	$.post(url, function(data){
		if(data != '')	$('.comment').append(data);
	});
	var num = Math.ceil(Math.random()*3);
	$('.sidebar2').css({background:'url(../images/site/'+num+'.jpg) no-repeat'});
	
});
		