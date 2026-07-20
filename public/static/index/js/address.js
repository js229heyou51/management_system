layui.use(['layer','form'],function(){
	$ = layui.jquery;
	var form = layui.form;
	layer = layui.layer;

	addAddress = function(type = ''){
		var param = '';
		if(type){
			param = '?type=' + type;
		}
		layer.open({
			type: 2,
			title: '添加收货地址',
			content : '/person/editAddress' + param,
			area: ['800px', '430px'],
		})
		init('cart-address-main','');
		form.render();
	}
	editAddress = function(data,type = ''){
		var param = '';
		if(type){
			param = '&type=' + type;
		}
		layer.open({
			type: 2,
			title: '编辑收货地址',
			content: '/person/editAddress?id='+data + param,
			area: ['800px', '430px'],
		})
		form.render();
		init('cart-address-main','');
	}

	selectAddress = function(id){
		$.post('/Cart/editAddress',{id:id},function(res){
			if(res.code==200){
				$('#addressId').val(res.data.id);
				var html = '<p>寄送至：' + res.data.province + res.data.city + res.data.district + res.data.address + '收货人：' + res.data.rename + ' ' + res.data.phone +'</p>'
				$('#confirmAddress').html(html);
			}else{
				layer.msg(res.msg,{icon:2})
			}
		})
		
	}



	saveAddress = function(type = ''){
		var data = $('#addForm').serialize();
		$.post('/Cart/addAddress',data,function(res){
			if(res.code==200){
				parent.layer.msg(res.msg,{icon:1})
				if(type == 'ajax'){
					var html = getAddress(res.data);
					parent.$('#addressBox').html(html);
					parent.layer.closeAll('iframe');
				}else{
					parent.window.location.reload()
				}
			}else{
				layer.msg(res.msg,{icon:2})
			}
		},'json');
	}
	getAddress = function(data){
		var html = '';
		for (var i = 0; i < data.length; i++) {
			html += '<li index="'+ i +'">';
			html += '<div class="name line1">'+data[i].rename+'</div>';
			html += '<div class="phone">'+data[i].phone+'</div> ';
			html += '<div class="text line4">'+data[i].province+data[i].city+data[i].district+data[i].address+'</div> ';
			html += '<div class="edit-box">';
			html += '<span onclick="editAddress('+data[i].id+',\'ajax\')">修改</span>';
			html += '<span onclick="delAddress('+data[i].id+',\'ajax\')">删除</span>';
			html += '</div>';
			html += ''+(data[i].type==1?'<div class="moren">默认</div>':'')+'';
			html += '</li>';
		}
		html += '<li class="addbox" onclick="addAddress(\'ajax\')"><div class="box"><img src="/static/index/images/add.02a167a.png" alt=""> <p>添加新地址</p></div></li>';
		return html;
	}
})