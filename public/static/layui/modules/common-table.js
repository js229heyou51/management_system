layui.define(['table'], function(exports) {
	const table = layui.table;

	function renderTable(options = {}){
		var initData = {
			table: options.table ?? table, 
			data: options.data ?? [], 
			elem: options.elem ?? 'dataTable', 
			langText: options.langText ?? [],
			colsInit : options.colsInit ?? [],
			colsType: options.colsType ?? true,
			recycleMake: options.recycleMake ?? 'recycle_make',
			toolbarElem: options.toolbarElem ?? 'toolbarDemo',
			lineStyle : options.lineStyle ?? '',
			isTool: options.isTool ?? true,
			isToolBar: options.isToolBar ?? true,
			isEdit: options.isEdit ?? true,
			isPage: options.isPage ?? true,
			isCheckbox: options.isCheckbox ?? true,
			isPx: options.isPx ?? true,
			isId: options.isId ?? true,
			isTitle: options.isTitle ?? true,
			isTime: options.isTime ?? true,
			isState: options.isState ?? true,
			recycleType: options.recycleType ?? false,
			toolbarConfig: options.toolbarConfig ?? {},
			height: options.height ?? 720,
			width: options.width ?? '',
			...options
		}
		var colsParam = buildCols(initData);
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
					html += '<span class="cursor" lay-event="edit">' + (d.title) + '</span>';
					if(d?.gallery_list[0]?.path) {
						html += ' <i class="layui-icon layui-icon-picture" onmouseover="show_img(this)" onmouseleave="hide_img()" src="' + d?.gallery_list[0]?.path + '"></i>';
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

			if(obj.event === 'cart'){
				edit(initData.url + 'orderDetail?id='+data.id+'',''+ initData.langText.orderDetail +'', initData.toolbarConfig)
			}
		})
	}

	/*
	// 表格工具栏操作
	 */
	function toolbarInit(initData, elem = 'listForm', operate = {add:'add', recycle:'recycle', recycleRecord: 'recycleRecord', make: 'make'}){
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
			var operateArr = ['ding1','ding2','tj1','tj2','hot1','hot2','pass1','pass2','del','recovery','remove'];
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

	// 暴露接口
    exports('commonTable', {
        render: renderTable
    });
})