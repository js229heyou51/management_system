var ICON_SHRINK = 'layui-icon-shrink-right',
ICON_SPREAD = 'layui-icon-spread-left',
APP_SPREAD_SM = 'layadmin-side-spread-sm',
SIDE_SHRINK = 'layadmin-side-shrink',
full = 1,
status = '';
var form;


layui.use(function(){
	layer = layui.layer;
	upload = layui.upload;      // layui 上传
	var element = layui.element;
	form = layui.form;
	$ = layui.jquery;
	laydate = layui.laydate;

	laydate.render({
		elem: '#wtime',
		type: 'datetime'
	});

	laydate.render({
		elem: '#searchDate',
		range: ['#start-date', '#end-date'],
		rangeLinked: true, // 开启日期范围选择时的区间联动标注模式 ---  2.8+ 新增
		// calendar: true
	});

	selectAll = function (id){
		$('#'+id+' input[lay-skin=primary]').each(function(index,item){
			item.checked = true;
		})
		form.render('checkbox');
	}
	invert = function (id){
		$('#'+id+' input[lay-skin=primary]').each(function(index,item){
			// var status = item.checked
			item.checked = !item.checked;
		})
		form.render('checkbox');
	}
	selectList = function(id,active,url){
		var str = '';
		$('#'+id+' input[lay-skin=primary]').each(function(index,item){
			// console.log($(item).data('id'));
			if(item.checked){
				str += ',' + $(item).data('id');
			}
		})
		str = str.replace(',','');
		if(str == ''){
			layer.alert('至少选择一条数据',{icon:2});
			return false;
		}
		$.post(url,{'act':active,'idStr':str},function(res){
			if(res.code>0){
				layer.alert(res.msg,{icon:2});
			}else{
				layer.msg(res.msg,{icon:1});
				setTimeout(function(){window.location.reload();},1000);
			}
		},'json')
	}

	$('.select-color-item').on('each',function(){
		var color = localStorage.getItem("theme-color");
		if($(this).attr("color") == color){
			$(".select-color-item").removeClass("layui-icon").removeClass("layui-icon-ok");
			$(this).addClass("layui-icon").addClass("layui-icon-ok");
		}
	})

	form.on('radio(info)', function(data){
		if(data.elem.value == 'true'){
			$('#infoDiv').show();
		}else{
			$('#infoDiv').hide();
		}
	})

	personState = function(state,langText){
		var html = '';
		if(state == '1'){
			html = '<span class="layui-badge layui-bg-blue">' + langText.unreviewed + '</span>';
		}else if(state == '2'){
			html = '<span class="layui-badge layui-bg-gray">' + langText.notPassed + '</span>';
		}else if(state == '3'){
			html = '<span class="layui-badge layui-bg-green">' + langText.approved + '</span>';
		}else if(state == '4'){
			html = '<span class="layui-badge">' + langText.shield + '</span>';
		}
		return html;
	}

	delImg = function(self,elem){
		var id = $(self).parent().attr('data-id');
		var idJson = JSON.parse($('#'+elem).val());
		idArr = idJson.filter(item => item !== Number(id));
		$('#'+elem).val(JSON.stringify(idArr));
		$(self).parent().remove();
	}

	window.parentSpecificFunction = function(elem,idData = {}){
		var idarr = idData.ids;
		var idListHtml = '';
		for (var i = 0; i < idData.idList.length; i++) {
			idarr.push(idData.idList[i].id);
			idListHtml += '<div class="upload_pic_li" data-id="'+idData.idList[i].id+'"><span onclick="delImg(this,\''+ elem +'\')"></span><img src="'+ idData.idList[i].src +'" alt="" class="layui-upload-img"></div>';
		}
		$('#'+elem+'').val(JSON.stringify(idarr));
		$('#'+elem+'_list').append(idListHtml);
	}

	uploadImg = function(elem = '', url, params = {type: 'image'},title=''){
		parent.layer.open({
			type: 2,
			title: title ?? '选择图片',
			shade: 0.3,
			area: ['940px','630px'],
			fixed: false, //不固定
			maxmin: true,
			content: url ,
			success:function(layero, childIndex){
				var childWindow = layero.find('iframe')[0].contentWindow;
				if(elem != ''){
					var ids = $('#'+elem+'').val();
					childWindow.parentFunctionBridge = window.parentSpecificFunction;
					childWindow.someDataFromParent = {
						elem: elem,
						ids: ids,
						...params
					};
				}
			}
		})
	}

	$(document).on('dragstart','.upload_pic_li', function(e) {
		draggedElement = $(this);
		console.log(draggedElement)
		var originalEvent = e.originalEvent;
		originalEvent.dataTransfer.effectAllowed = 'move';
		console.log('开始拖拽:', $(this).text());
	})

	// 拖拽结束
	$(document).on('dragend','.upload_pic_li', function() {
		$(this).removeClass('dragging');
		draggedElement = null;
		console.log('结束拖拽:', $(this).text());
	});

})

function getCookie(name) {
		// 使用正则表达式查找名为name的Cookie的值
	let matches = document.cookie.match(new RegExp(
		"(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"
	));
	return matches ? decodeURIComponent(matches[1]) : undefined;
}

let think_lang = getCookie('think_lang') ?? 'zh-CN';
if(think_lang == 'zh-cn'){
	think_lang = 'zh-CN';
}

//图片上传
function imgUpload(elemSelector,uploadUrl){
	layui.use(['upload','layer'],function(){
		layer = layui.layer;
		upload = layui.upload;
		var defaultOptions = {
			elem: "#"+elemSelector+"",
			url: uploadUrl,
			multiple: true,
			done: function(res){
				if(res.code == 200){
					$("#add_"+elemSelector+"").html('<div class="upload_pic_li" ><span></span><img src="'+ res.url +'" class="layui-upload-img" onmouseover="show_img(this)" onmouseleave="hide_img()"><input type="hidden" name="'+elemSelector+'" value="'+res.data+'"/></div>');
				}else{
					return layer.msg("上传失败，"+res.msg);
				}
			}
		} 
		upload.render(defaultOptions);
	})
}
//文件上传
function fileUpload(elemSelector,uploadUrl){
	layui.use(['upload','layer'],function(){
		layer = layui.layer;
		upload = layui.upload;
		var defaultOptions = {
			elem: "#"+elemSelector+"",
			url: uploadUrl,
			multiple: true,
			accept: 'file',
			done: function(res){
				if(res.code == 200){
					$("#add_"+elemSelector+"").html('<div class="upload_file_li"><input type="text" class="layui-input" name="'+elemSelector+'" value="'+ res.url +'" ></div>');
				}else{
					return layer.msg("上传失败，"+res.msg);
				}
			}
		} 
		upload.render(defaultOptions);
	})
}
//全屏
function fullScreen(){
	if(full==1){
		var ele = document.documentElement;
		var reqFullScreen = ele.requestFullScreen || ele.webkitRequestFullScreen || ele.mozRequestFullScreen || ele.msRequestFullscreen;
		if(typeof reqFullScreen !== 'undefined' && reqFullScreen) {
			reqFullScreen.call(ele);
		};
		full = 2;
	}else{
		if (document.exitFullscreen) {
			document.exitFullscreen();
		} else if (document.mozCancelFullScreen) {
			document.mozCancelFullScreen();
		} else if (document.webkitCancelFullScreen) {
			document.webkitCancelFullScreen();
		} else if (document.msExitFullscreen) {
			document.msExitFullscreen();
		}
		full = 1;
	}
}
// 侧边伸缩
function shrink(){
	var app = $('#LAY_app'),
	iconElem=$('#LAY_app_flexible');
	//设置状态，PC：默认展开、移动：默认收缩
	if(status === 'spread'){
		//切换到展开状态的 icon，箭头：←
		iconElem.removeClass(ICON_SPREAD).addClass(ICON_SHRINK);
		//移动：从左到右位移；PC：清除多余选择器恢复默认
		if(screen() < 2){
			app.addClass(APP_SPREAD_SM);
		} else {
			app.removeClass(APP_SPREAD_SM);
		}
		app.removeClass(SIDE_SHRINK);
		status = '';
	} else {
		//切换到搜索状态的 icon，箭头：→
		iconElem.removeClass(ICON_SHRINK).addClass(ICON_SPREAD);
		//移动：清除多余选择器恢复默认；PC：从右往左收缩
		if(screen() < 2){
			app.removeClass(SIDE_SHRINK);
		} else {
			app.addClass(SIDE_SHRINK);
		}
		app.removeClass(APP_SPREAD_SM)
		status = 'spread';
	}
}
//屏幕类型
function screen(){
	var width = $(window).width();
	if(width > 1200){
		return 3; //大屏幕
	} else if(width > 992){
		return 2; //中屏幕
	} else if(width > 768){
		return 1; //小屏幕
	} else {
		return 0; //超小屏幕
	}
}
//xss 转义
function escape(html){
	return String(html || '').replace(/&(?!#?[a-zA-Z0-9]+;)/g, '&amp;')
	.replace(/</g, '&lt;').replace(/>/g, '&gt;')
	.replace(/'/g, '&#39;').replace(/"/g, '&quot;');
}



// 显示图片
function show_img(obj){
	var imgurl = $(obj).attr('src');
	var res = getMousePos();
	var html = '<div style="background:#fff;position:absolute;width:200px;border:solid 1px #cdcdcd;border-radius:6px;padding:2px;left:'+res.x+'px;top:'+res.y+'px;z-index:1000" id="preview">\
			<img style="width:100%;border-radius:6px;" src="'+imgurl+'">\
		</div>';
	$('body').append(html);
}
// 隐藏图片
function hide_img(){
	$('#preview').remove();
}
// 图片位置计算
function getMousePos(event) {
	var e = event || window.event;
	var scrollX = document.documentElement.scrollLeft || document.body.scrollLeft;
	var scrollY = document.documentElement.scrollTop || document.body.scrollTop;
	var x = e.pageX || e.clientX + scrollX;
	var y = e.pageY || e.clientY + scrollY;
	return { 'x': x, 'y': y };
}
// 删除图片
function deleteImage(path,obj){
	$(obj).closest('.upload_pic_li').remove();
}

function tab(self,id){
	var that = $('#'+ id);
	$(self).find('span').addClass('this');
	$(self).siblings().find('span').removeClass('this');
	that.show().siblings().hide();
}
function step(type = '',className = '.layui-step'){
	var ind = 0;
	var that = $('.layui-step-header a');
	that.each(function(){
		if($(this).find('span').hasClass('this')){
			ind = $(this).index();
		}
	})
	if(type == 'next'){
		var maxInd = that.length - 1;
		var next = ind + 1;
		if(maxInd - 1 == ind){
			next = maxInd;
		}
		$(className).eq(next).show().siblings().hide();
		that.eq(next).find('span').addClass('this');
		that.eq(next).siblings().find('span').removeClass('this');
	}
	if(type == 'prev'){
		var prev = ind - 1;
		if(ind <= 0){
			prev = 0;
		}
		$(className).eq(prev).show().siblings().hide();
		that.eq(prev).find('span').addClass('this');
		that.eq(prev).siblings().find('span').removeClass('this');
	}
	
}
function del_img(id){
	$('#'+id).html(' ');
	$('#'+id).find('input').val('');
}
function uploadImgSkin (url,editor){
	parent.layer.open({
		type: 2,
		title: '选择图片',
		shade: 0.3,
		area: ['940px','75%'],
		fixed: false, //不固定
		maxmin: true,
		content: url,
		success:function(layero, childIndex){
			var childWindow = layero.find('iframe')[0].contentWindow;
			childWindow.currentActiveEditorData = {
				editor:editor
			};
		}
	})
}
function tinymceEditor(id){
	tinymce.init({
		selector: '#'+id,
		api_key: 'u11beud5itdws9zrd9efn2lteqkt6mr0ru85zazdk07urysf',
		setup: function (editor) {
			var editorId = editor.id;
			// 添加自定义图片按钮
			editor.ui.registry.addButton(
				'customimage', {
					icon: 'gallery', // 使用相同的图标
					tooltip: '插入图片', // 提示文本
					onAction: function() {
						// 点击按钮时执行您的自定义函数
						uploadImgSkin('/admin.php/Gallery/index',editor);
					}
				}
			);
			editor.ui.registry.addButton(
				'custommedia', {
					icon: 'embed', // 使用相同的图标
					tooltip: '插入媒体',
					onAction: function() {
						// 点击按钮时执行您的自定义函数
						uploadImgSkin('/admin.php/Video/index',editor);
					}
				}
			);
			editor.on('change', function () {
				tinymce.triggerSave();
				var content = editor.getContent();
				$('#'+id).val(content);
			});
		},
		language: think_lang,
		menubar:false,
		plugins: 'preview searchreplace autolink directionality visualblocks visualchars fullscreen image link media code codesample table charmap pagebreak nonbreaking anchor insertdatetime advlist lists wordcount help emoticons autosave autoresize ',
		toolbar: 'undo redo | styles | fontsize | bold italic underline strikethrough align lineheight forecolor backcolor | customimage custommedia table | link outdent indent bullist numlist | subscript superscript removeformat pagebreak preview code fullscreen',
		height: 500, //编辑器高度
		max_height: 500,
		min_height: 500,
	});
}
// console.log(window.tinymceEditors['ym_bot'])
// 全局函数：从图片库接收图片并插入编辑器
window.insertImageFromGallery = function(editor, image = []) {
	// console.log(window.tinymceEditors)
	// var editor = window.tinymceEditors[editorId];
	if (editor && !editor.isHidden()) {
		// 在光标位置插入图片
		for (var i = 0; i < image.length; i++) {
			editor.insertContent('<p><img src="' + image[i].src + '" alt="' + (image[i].title || '') + '" style="max-width:100%;"></p>');
		}
		return true;
	}else{
		return false;
	}
}

// 全局函数：从图片库接收图片并插入编辑器
window.insertMediaFromGallery = function(editor, media = []) {
	// var editor = window.tinymceEditors[editorId];
	if (editor && !editor.isHidden()) {
		// 在光标位置插入图片
		for (var i = 0; i < media.length; i++) {
			editor.insertContent('<p><video src="' + media[i].src + '" controls="" style="max-width:100%;min-height:500rpx"></video></p>');
		}
		return true;
	}else{
		return false;
	}
}