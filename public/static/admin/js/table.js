/*
	colsType cols类型 true时 colsParam = [] ,false时 colsparam = [];
 */
function tableInit(config = {})
{
	var initData = {
		table: {}, 
		data:[], 
		elem:'dataTable', 
		langText:[],
		colsInit : [],
		recycleMake: 'recycle_make',
		toolbarElem: 'toolbarDemo',
		lineStyle : '',
		colsType: true,
		isTool: true,
		isToolBar: true,
		isEdit: true,
		isPage: true,
		isCheckbox: true,
		isPx: true,
		isId: true,
		isTitle: true,
		isTitleEdit: true,
		isTime: true,
		isState: true,
		recycleType: false,
		toolbarConfig: {},
		height: 720,
		width: '',
		...config
	}
	var colsParam = buildCols(initData)
	var page = initData.isPage ? buildPage(initData.pageInit) : false;

	initData.table.init(initData.elem, {
		elem: '#'+initData.elem // 指定原始表格元素选择器（推荐 id 选择器）
		,page: page
		,cellMinWidth: 80
		,toolbar: '#' + initData.toolbarElem
		,defaultToolbar: ['filter', 'exports', 'print',{
				title: '刷新',
				layEvent: 'refresh',
				icon: 'layui-icon-refresh'
			}]
		,cols: colsParam
		,data: initData.data
		,lineStyle: initData.lineStyle
		,height: initData.height
		,width: initData.width
	})

	if(initData.isTool){
		toolInit(initData);
	}
	if(initData.isToolBar){
		toolbarInit(initData);
	}
	if(initData.isEdit){
		editInit(initData);
	}

}

/*
初始化分页参数
 */
function buildPage(pageOptions){
	var defaultPage = { // 支持传入 laypage 组件的所有参数（某些参数除外，如：jump/elem） - 详见文档
			layout: [ 'prev', 'page', 'next', 'skip', 'count', 'limit'] //自定义分页布局
			,limit: 15
			,limits: [10,15,20,25,50,100]
			,curr: 1
			,groups: 3 //只显示 3 个连续页码
			,first: 1 //不显示首页 false
			,last: '' //不显示尾页
			,prev: '<icon class="layui-icon layui-icon-left"></icon>'
			,next: '<icon class="layui-icon layui-icon-right"></icon>'
		};
	if(pageOptions){
		return $.extend(true, defaultPage, pageOptions);
	}

	return defaultPage;
}

/*
初始化列数
 */
function buildCols(initData){
	if(!initData.colsType){
		return [initData.colsInit];
	}
	var colsParam = [];
	colsParam = [
		colsParam.concat(initData.isCheckbox ? [{type: 'checkbox', fixed: 'left'}] : [])
		.concat(initData.isPx ? [{field: 'px', title: ''+initData.langText.sort+'', width: 60, align: 'center',sort:'desc', fixed: 'left',edit: 'text'}] : [])
		.concat(initData.isId ? [{field: 'id', title: 'ID', width: 60,  fixed: 'left', align: 'center',sort:'desc', fixed: 'left'}] : [])
		.concat(initData.isTitle ? [
			{field: 'title', title: '' + initData.langText.title + '', minwidth:300, templet: function(d){
				var html = '<div >';
				if(d.profile?.title_lm){
					html += '<b>【' + (d.profile.title_lm) + '】</b>';
				}
				var event = '';
				if(initData.isTitleEdit){
					event = 'lay-event="edit"';
				}
				html += '<span class="cursor" '+ event +'>' + (d.title) + '</span>';
				if(typeof d?.gallery_list !== 'undefined'){
					if(d?.gallery_list[0]?.path) {
						html += ' <i class="layui-icon layui-icon-picture" onmouseover="show_img(this)" onmouseleave="hide_img()" src="' + d?.gallery_list[0]?.path + '"></i>';
					}
				}
				html += '</div>';
				return html;
			}}
		] : [])
		.concat(initData.colsInit && initData.colsInit.length > 0 ? initData.colsInit : [])
		.concat(initData.isTime ? [{field: 'wtime', title: '' + initData.langText.time + '', width: 160, sort:'desc',align: 'center'}] : [])
		.concat(initData.isState ? [{field: '', title: '' + initData.langText.state + '', width: initData.stateWidth??270 , maxWidth:300, align: 'center',templet: '#state'}] : [])
		.concat(initData.recycleType ? [] : [{field: '', title: '' + initData.langText.operate + '', width: initData.operateWidth??200, align: 'center',templet: '#operateDemo',fixed: 'right'}])
	];
	return colsParam;
}

/*
//行内操作
 */
function toolInit(initData, operate = {del: 'del', edit: 'edit'}){
	initData.table.on('tool('+initData.elem+')', function(obj){ // 双击 toolDouble
		var data = obj.data; // 获得当前行数据
		if(obj.event === 'del'){
			del(initData.url + '' + operate.del + '?id='+data.id+'',''+initData.langText.delete+'')
		}
		if(obj.event === 'edit'){
			edit(initData.url + '' + operate.edit + '?id='+data.id+'',''+initData.langText.edit+'')
		}
		if(obj.event === 'create'){
			create(initData.url + 'createWeb?id='+data.id+'')
		}
		if(obj.event === 'delRecord') {
			del(initData.url + 'recordDel?id='+data.id+'',''+initData.langText.delete+'')
		}

		if(obj.event === 'cart'){
			edit(initData.url + 'orderDetail?id='+data.id+'',''+ initData.langText.orderDetail +'', initData.toolbarConfig)
		}
	})
}

/*
// 表格工具栏操作
 */
function toolbarInit(initData, elem = 'listForm', operate = {add:'add', recycle:'recycle', recycleRecord: 'recycleRecord', makeRecord: 'makeRecord', make: 'make'}){
	initData.table.on('toolbar(' + initData.elem + ')', function(obj){
		if(obj.event === 'refresh'){
			window.location.reload();
			return false
		}
		if(obj.event === 'add'){
			add(initData.url + '' + operate.add + '',''+initData.langText.add+'');
			return false
		}
		if(obj.event === 'recycle'){
			recycle(initData.url + '' + operate.recycle + '',''+initData.langText.recycle+'')
			return false
		}

		if(obj.event === 'recycleRecord'){
			recycle(initData.url + '' + operate.recycleRecord + '',''+initData.langText.recycle+'')
			return false
		}
		var event = obj.event
		var operateArr = ['ding1','ding2','tj1','tj2','hot1','hot2','pass1','pass2','del','recovery','remove','makeRecord'];
		if(operateArr.includes(event)){
			var id = obj.config.id;
			var checkStatus = initData.table.checkStatus(id);
			var data = checkStatus.data; // 获取选中的数据
			if(data.length === 0){
				return layer.msg('请选择一行');
			}
			var idArr = [];
			for(var i = 0; i < data.length; i ++){
				const formId = data[i]['id']??data[i]['id_lm'];
				$('#' + elem).append('<input type="hidden" name="checkbox['+formId+']" value="on">')
				$('#' + elem).append('<input type="hidden" name="id[]" value='+formId+'>');
			}

			if(event === 'makeRecord'){
				make(initData.url + '' + operate.makeRecord + '?act=del')
				return false
			}

			if(event === 'recovery'){
				batchRecovery(initData.url + '' + initData.recycleMake + '?act=recovery')
				return false
			}else if(event === 'remove'){
				batchRemove(initData.url + '' + initData.recycleMake + '?act=remove')
				return false
			}else{
				make(initData.url + '' + operate.make + '?act=' + event)
			}
			return false
		}
		
	})
}

/*
// 单元格编辑事件
 */
function editInit(initData,elem = 'listForm',operate = {make: 'make'}){
	initData.table.on('edit('+initData.elem+')', function(obj){
		var field = obj.field; // 得到字段
		var value = obj.value; // 得到修改后的值
		var data = obj.data; // 得到所在行所有键值
		$('#' + elem).append('<input type="hidden" name="id" value='+data['id']+'>');
		$('#' + elem).append('<input type="hidden" name="'+field+'" value='+value+'>');
		make(initData.url + '' + operate.make + '?act=sort')
	})
}


/*
	treetable
 */
function treeTableInit(config = {}){
	let treeTableInit = {
		elem: 'dataTable',
		langText:[],
		method: 'post',
		toolbar: 'power-toolbar',
		tree: {
			customName: {name : 'title_lm'},
			view: {icon: '',showIcon: false, expandAllDefault: true,}
		},
		isPage: true,
		cols: [],
		isCols: true,
		recycleType: true,
		isState: true,
		isRecycle: true,
		isEdit: true,
		isToolBar: true,
		height: 830,
		...config
	}
	var colsParam = treeColsInit(treeTableInit)
	treeTableInit.cols = colsParam

	var inst = treeTableInit.treeTable.render({
		elem: '#' + treeTableInit.elem ?? 'dataTable',
		url: treeTableInit.url + 'default',
		method: treeTableInit.method ?? 'post',
		tree: {
			customName: {name: treeTableInit.tree.customName.name ?? 'title_lm'},
			view: {
				icon: treeTableInit.tree.view.icon ?? ' ', 
				showIcon: treeTableInit.tree.view.showIcon ?? true,
				expandAllDefault: treeTableInit.tree.view.expandAllDefault ?? false
			},
		},
		toolbar: '#' + treeTableInit.toolbar ?? 'power-toolbar',
		cols: colsParam,
		height: treeTableInit.height,
	})


	if(treeTableInit.isEdit){
		treeEditInit(treeTableInit)
	}
	if(treeTableInit.isToolBar){
		treeToolbarInit(treeTableInit)
	}
}


/*
	treetable 的cols初始化列数
 */
function treeColsInit(treeTableInit){
	var colsParam = [];
	if(treeTableInit.isCols){
		colsParam = [[ // 表头
			{field: 'px', title: ''+treeTableInit.langText.sort+'', width: 80, align: 'center', edit: 'text',fixed: 'left'}
			,{field: 'id_lm', title: 'ID', width: 60, align: 'center',sort:'desc',fixed: 'left'}
			,{field: 'title_lm', title: '' + treeTableInit.langText.category + '', minwidth:300}]
		.concat(treeTableInit.colsInit && treeTableInit.colsInit.length > 0 ? treeTableInit.colsInit : [])
		.concat(treeTableInit.isState ? [{field: '', title: '' + treeTableInit.langText.state + '', width: treeTableInit.stateWidth??225, maxWidth:300, align: 'center',templet: '#state'}] : [])
		.concat(treeTableInit.isRecycle ? [{field: '', title: '' + treeTableInit.langText.operate + '', width: 200, align: 'center',templet: '#operateDemo',fixed: 'right'}] : [])
		];
	}else{
		colsParam = [treeTableInit.colsInit];
	}
	return colsParam;
}

/*
treetable toolbar
 */
function treeToolbarInit(treeTableInit){
	treeTableInit.treeTable.on('toolbar(' + treeTableInit.elem + ')', function(obj){
		if(obj.event === 'expandAll'){
			treeTableInit.treeTable.expandAll(treeTableInit.elem,true);
		}
		if(obj.event === 'foldAll'){
			treeTableInit.treeTable.expandAll(treeTableInit.elem,false);
		}
	})
}

/*
treetable edit
 */
function treeEditInit(treeTableInit){
	treeTableInit.treeTable.on('edit(' + treeTableInit.elem + ')', function(obj){
		var px = obj.value //得到修改后的值
		,data = obj.data //得到所在行所有键值
		,field = obj.field; //得到字段
		$.post(treeTableInit.url + 'make',{'act':'px','id':data.id_lm,'px':px},function(res){
			if(res.code==200){
				layer.msg(res.msg,{icon:1});
				setTimeout(function(){window.location.reload();},1000);
			}else{
				layer.alert(res.msg,{icon:2});
			}
		},'json')
	})
}

function save(url,type=0){
	$.post(url,$('form').serialize(),function(res){
		if(res.code==200){
			layer.msg(res.msg,{'icon':1});
			if(type == 1){
				setTimeout(function(){window.location.reload();},1000);
			}else{
				setTimeout(function(){parent.window.location.reload();},1000);
			}
		}else{
			layer.msg(res.msg,{'icon':2});
		}
	},'json');
}
//search
function search(url){
	$.post(url,$('#sform').serialize(),function(res){
		if(res.code==200){
			// layer.msg(res.msg,{icon:7});
			setTimeout(function(){
				window.location.hash = '#'+res.where;
				window.location.reload();
			},500);
		}else{
			layer.alert(res.msg,{icon:2});
		}
	},'json');
}
// 状态搜索
function allState(url,type='zt_val'){
	form.on('select('+type+')', function(data){
		$.post(url,$('#sform').serialize(),function(res){
			if(res.code==200){
				setTimeout(function(){window.location.href=url+'?'+res.where;},500);
			}else{
				layer.alert(res.msg,{icon:2});
			}
		},'json');
	});
}

// 状态操作 pass
function state(url,type,param){
	form.on('switch('+type+')', function(data){
		var act = type;
		var id = $(data.elem).data(param);
		if(id == undefined){
			id = data.value
		}
		$.post(url,{id:id,'act':act},function(res){
			if(res.code==200){
				layer.msg(res.msg,{icon:1});
				setTimeout(function(){window.location.reload();},1000);
			}else{
				layer.alert(res.msg,{icon:2});
			}
		},'json');
	});
}
function refresh(){
	window.location.reload();
}
function recycle(url,title='回收站'){
	layer.open({
		type: 2,
		title: title,
		shade: 0.3,
		area: ['80%','80%'],
		fixed: false, //不固定
		maxmin: true,
		content: url,
		end :function(){
			window.location.reload();
		}
	});
}

function batchRecovery(url){
	$.post(url,$('#listForm').serialize(),function(res){
		if(res.code==200){
			layer.msg(res.msg,{icon:1});
			setTimeout(function(){window.location.reload();},1000);
		}else{
			layer.alert(res.msg,{icon:2});
		}
	},'json');
}
function batchRemove(url){
	layer.confirm('确定要删除吗？', {
		icon:3,
		btn: ['确定','取消']
	}, function(){
		$.post(url,$('#listForm').serialize(),function(res){
			if(res.code==200){
				layer.msg(res.msg,{icon:1});
				setTimeout(function(){window.location.reload();},1000);
			}else{
				layer.alert(res.msg,{icon:2});
			}
		},'json');
	});
}


// 添加
function add(url,title="添加"){
	layer.open({
		type: 2,
		title: title,
		shade: 0.3,
		area: ['100%','100%'],
		fixed: false, //不固定
		maxmin: true,
		content: url
	});
}
// 编辑
function edit(url,title="编辑",config={}){
	layer.open({
		type: 2,
		title: title,
		shade: 0.3,
		area: ['100%','100%'],
		fixed: false, //不固定
		maxmin: true,
		content: url,
		...config
	});
}
// 删除
function del(url,title="删除"){
	layer.confirm('确定要删除吗？', {
		icon:3,
		title: title,
		btn: ['确定','取消']
	}, function(){
	  $.post(url,function(res){
			if(res.code==200){
				layer.msg(res.msg,{icon:1});
				setTimeout(function(){window.location.reload();},1000);
			}else{
				layer.alert(res.msg,{icon:2});
			}
	  },'json');
	});
}

// 
function create(url,title="投喂"){
	layer.confirm('确定要投喂吗？', {
		icon:3,
		title: title,
		btn: ['确定','取消']
	}, function(){
		var  loadIndex = ''
		$.ajax({
			type: 'POST',
			url: url,
			dataType: 'json',
			beforeSend: function() {
				
				loadIndex = layer.load(2, {shade: false}); //0代表加载的风格，支持0-2  
				// 在发送请求之前执行的操作
			},
			success: function(res) {
				if(res.code==200){
					layer.msg(res.msg,{icon:1});
					setTimeout(function(){window.location.reload();},1000);
				}else{
					layer.alert(res.msg,{icon:2});
					layer.close(loadIndex)
				}
			}
		});
	});
}

// 操作
function make(url){
	layer.confirm('确定要操作吗？', {
		icon:3,
		title: '操作',
		btn: ['确定','取消']
	}, function(){
		$.post(url,$('#listForm').serialize(),function(res){
			if(res.code==200){
				layer.msg(res.msg,{icon:1});
				setTimeout(function(){window.location.reload();},1000);
			}else{
				layer.alert(res.msg,{icon:2});
			}
		},'json');
	});
	
}