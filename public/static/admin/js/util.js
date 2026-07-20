layui.use(function(){
	const $ = layui.jquery;
	const layer = layui.layer;
	const util = layui.util;

	uploadTool = function(params = {}){
		parent.layer.open({
			type: 2,
			title: params.title ?? '选择',
			shade: 0.3,
			area: ['940px','630px'],
			fixed: false, //不固定
			maxmin: true,
			content: params.url ,
			success:function(layero, childIndex){
				var childWindow = layero.find('iframe')[0].contentWindow;
				params.isSingle = params.isSingle ?? (params.type === 'video' ? true : false);
				if(params.elem != ''){
					const val = $('#'+params.elem+'').val();
					if(val !== ''){
						params.ids = JSON.parse(val);
					}
					childWindow.parentFunctionBridge = window.parentToolFunction;
					childWindow.dataFromParent = {
						...params
					};
				}
			}
		})
	}

	window.parentToolFunction = function(params = {}){
		let idarr = params.ids ?? [];
		var idListHtml = '';
		for (var i = 0; i < params.idList.length; i++) {
			idarr.push(params.idList[i].id);
			if(params?.type === 'image'){
				idListHtml += '<div class="upload_pic_li" data-id="'+params.idList[i].id+'"><span onclick="delImg(this,\''+ params.elem +'\')"></span><img src="'+ params.idList[i].src +'" alt="" class="layui-upload-img"></div>';
			}else if(params?.type === 'video'){
				idListHtml += '<div class="box-video-style" data-id="'+params.idList[i].id+'">';
				idListHtml += '<video src="'+ params.idList[i].src +'" controls="controls" style="width: 100%; height: 100%;">您的浏览器不支持 video 标签。</video>';
				idListHtml += '<div class="mark"></div>';
				idListHtml += '<i class="iconv layui-icon layui-icon-delete" lay-on="delTool" data-type="'+ params.type +'" data-elem="'+ params.elem +'"></i>';
				idListHtml += '</div>';
			}
		}
		$('#'+params.elem+'').val(JSON.stringify(idarr));
		$('#'+params.elem+'_list').append(idListHtml);
		$('#'+params.elem+'_list').find('div.layui-inline').hide();
	}

	util.on({
		'delTool':function(obj){
			const self = obj[0];
			const id = $(self).parent().attr('data-id');
			const type = $(self).data('type');
			const elem = $(self).data('elem');
			const idJson = JSON.parse($('#' + elem).val());
			idArr = idJson.filter(item => item !== Number(id));
			$('#' + elem).val(JSON.stringify(idArr));
			$(self).parent().remove();
			if(type === 'video'){
				$('#'+elem+'_list').find('div.layui-inline').show();
				$('#' + elem).val('');
			}
		}
	})
})