layui.use(['layer','form','jquery','element','upload'],function(){
	layer = layui.layer;
	upload = layui.upload;		// layui 上传
	var element = layui.element;
	var form = layui.form;
	$ = layui.jquery;
})

function pl_img_addd(url){
	$.post(url,$('#formp').serialize(),function(res){
		if(res.code==200){
			layer.msg(res.msg,{'icon':1});
			window.location.reload();
		}else{
			layer.msg(res.msg,{'icon':2});
		}
	},'json');
}
// 编辑
function pl_img_edit(url){
	layer.open({
		type: 2,
		title: '重新上传',
		shade: 0.3,
		area: ['100%','100%'],
		content: url
	});
}
// 操作
function pl_img_make(url,id,act){
	$.post(url,{'id':id,'act':act},function(res){
		if(res.code==200){
			layer.msg(res.msg,{icon:1});
			setTimeout(function(){window.location.reload();},500);
		}else{
			layer.alert(res.msg,{icon:2});
		}
	},'json');
}
function submit_pl(url){
	$.post(url,$('#formu').serialize(),function(res){
		if(res.code==200){
			layer.msg(res.msg,{icon:1});
			setTimeout(function(){window.location.reload();},500);
		}else{
			layer.alert(res.msg,{icon:2});
		}
	},'json');
}


function closeLayer(){
	parent.layer.closeAll();
}

function pl_state(url,type,param){
	form.on('switch('+type+')', function(data){
		var act = type;
		var id = $(data.elem).data(param);
		if(id == undefined){
			id = data.value
		}
		$.post(url,{'id':id,'act':act},function(res){
			if(res.code==200){
				layer.msg(res.msg,{icon:1});
				setTimeout(function(){window.location.reload();},500);
			}else{
				layer.msg(res.msg,{icon:2});
			}
		},'json');
	});
}
function pl_save(url){
	$.post(url,$('form').serialize(),function(res){
		if(res.code==200){
			layer.msg(res.msg,{'icon':1});
			parent.layer.closeAll();
			parent.window.location.reload();
		}else{
			layer.msg(res.msg,{'icon':2});
		}
	},'json');
}
function pl_add(url){
	parent.layer.open({
		type: 2,
		title: '添加相关信息',
		shade: 0.3,
		area: ['90%','80%'],
		fixed: false, //不固定
		maxmin: true,
		content: url
	});
}
// 编辑
function pl_edit(url){
	parent.layer.open({
		type: 2,
		title: '编辑相关信息',
		shade: 0.3,
		area: ['90%','80%'],
		fixed: false, //不固定
		maxmin: true,
		content: url
	});
}
// 删除
function pl_del(url,id){
	layer.confirm('确定要删除吗？', {
		icon:3,
		btn: ['确定','取消']
	}, function(){
		$.post(url,{'id':id},function(res){
			if(res.code==200){
				layer.msg(res.msg,{icon:1});
				setTimeout(function(){window.location.reload();},500);
			}else{
				layer.alert(res.msg,{icon:2});
			}
		},'json');
	});
}
// 操作
function pl_make(url){
	$.post(url,$('#formu').serialize(),function(res){
		if(res.code==200){
			layer.msg(res.msg,{icon:1});
			setTimeout(function(){window.location.reload();},500);
		}else{
			layer.alert(res.msg,{icon:2});
		}
	},'json');
}