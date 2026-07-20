layui.use(['layer'],function(){
	$ = layui.jquery;

	$('input').keydown(function(e){
		if(e.keyCode == 13){
			login()
		}
	})

	login = function(){
		var flag = true;
		$('#pform input[required=true]').each(function(){
			var val = $(this).val();
			if(val == ''){
				layer.msg($(this).attr('error-msg'),{'icon':2});
				flag = false;
				return false;
			}
		})
		if(flag){
			$.post('/Login/login',$('#pform').serialize(),function(res){
				if(res.code==200){
					layer.msg(res.msg,{'icon':1});
					setTimeout(function(){window.location.href=''+res.url+'';},500);
				}else{
					layer.msg(res.msg,{'icon':2});
					// setTimeout(function(){window.location.href='/person/';},500);
				}
			},'json');
		}
	}

	
	

	//发送短信计时
	let wait=60;//时间 
	dxtime = function (btn){
		//btn为按钮的对象，str为可选，这里是60秒过后，提示文字的改变 
		if (wait == 0){ 
			btn.removeClass("cur");//取消样式
			btn.removeAttr("disabled"); 
			btn.html("获取验证码");//改变按钮中value的值
			wait = 60; 
		} else { 
			btn.addClass("cur");//添加样式
			btn.attr("disabled",'disabled');//倒计时过程中禁止点击按钮 
			btn.html("重新发送 (<font>" + wait + "</font>)");//改变按钮中value的值
			wait--;
			setTimeout(function(){dxtime(btn);},1000) //循环调用
		} 
	}


	//点击发送短信
	$('body').on('click','.js-sendcode',function(){
		var btn = $(this);
		var username = $('#username');
		// var phonereg = /^0?1[3|4|5|7|8][0-9]\d{8}$/;
		
		//判断
		if(username.val()==''){
			layer.msg('请输入手机号',{anim:6});
			username.focus();
			return false;	
		}
		// if(!phonereg.test(username.val())){
		// 	layer.msg('手机号格式有误',{anim:6});
		// 	username.focus();
		// 	return false;	
		// }
		$.post('/Login/sendSms',{username:username.val()},function(res){
			if(res.code == 200){
				btn.css("pointer-events", "none");
				layer.msg(res.msg,{'icon':1});
				setTimeout(function(){
					dxtime(btn);
				},300)
			}else{
				layer.msg(res.msg,{'icon':2});
				// setTimeout(function(){window.location.href='/person/';},500);
			}
		},'json');
	})
	

	register = function(){
		$.post('/Login/register',$('#rform').serialize(),function(res){
			if(res.code==200){
				layer.msg(res.msg,{'icon':1});
				setTimeout(function(){window.location.href='/login/';},500);
			}else{
				layer.msg(res.msg,{'icon':2});
				// setTimeout(function(){window.location.href='/person/';},500);
			}
		},'json');
	}
})