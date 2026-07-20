layui.use(['layer','form'],function(){

	$ = layui.jquery;
	var form = layui.form;

	addInvoice = function(self){
		$('#addInvoiceBox').toggle();
		if($('#addInvoiceBox').css('display') == 'none'){
			$(self).text('新增发票');
		}else{
			$(self).text('关闭新增');
			$('#addInvoiceBox input').val('');
			$('#addInvoiceBox select').val('');
			form.render();
		}
	}

	editInvoice = function(id){
		$('#addInvoiceText').text('关闭编辑');
		$.post('/Cart/editInvoice',{id:id},function(res){
			if(res.code==200){
				var district = {};
				var province = '';
				var city = '';
				var county = '';
				$.each(res.data, function(index, value) {
					if(index == 'spcounty'){
						index = 'spdistrict';
					}
					$('#addInvoiceBox').find('input[name='+index+']').val(value);
					$('#addInvoiceBox').find('select[name='+index+']').val(value);
					$('#addInvoiceBox').find('select[name='+index+']').data(index,value);
					form.render('select');
				})
				form.render();
				init('cart-fpaddress-main','sp');
				$('#addInvoiceBox').show();
			}else{
				layer.msg(res.msg,{icon:2})
			}
		},'json');
	}

	selectInvoice = function(id){
		var valueAdded = $('#valueAdded').val();
		var totalPrice = $('#totalPrice').val();
		var freightPrice = $('#freightPrice').val();

		var allPrice = parseFloat(totalPrice*valueAdded) + parseFloat(totalPrice) + parseFloat(freightPrice)

		$('#allPrice').val(allPrice.toFixed(4));
		$('#allPriceText').text(allPrice.toFixed(4));
		$('#invoiceId').val(id);
	}

	saveInvoice = function(){
		var data = $('#invoiceForm').serialize();
		$.post('/Cart/invoice',data,function(res){
			if(res.code==200){
				$('#addInvoiceBox').hide();
				var html = getInvoice(res.data)
				$('#invoiceBox').html(html);
			}else{
				layer.msg(res.msg,{icon:2})
			}
		},'json');
	}
	getInvoice = function(data){
		var html = '<div class="confirm-fpmore">'
		html += '<a href="javascript:;" onclick="addInvoice(this)">新增发票</a>';
		html += '</div>';
		html += '<div class="confirm-fplist">';
		for (var i = 0; i < data.length; i++) {
			html += '<div class="confirm-add '+(i==0?'add-on':'')+'">';
			html += '<div class="confirm-addcon">';
			html += '<div class="confirm-addbox" onclick="selectInvoice('+data[i].id+')">'+data[i].spname+'</div>';
			html += '<ul class="flex">';
			html += '<li>'+data[i].spname+'</li>';
			html += '<li>'+data[i].spphone+'</li>';
			html += '<li>'+data[i].spname+'</li>';
			html += '<li>'+data[i].spprovince+data[i].spcity+data[i].spdistrict+data[i].spaddress+'</li>';
			// html += ''+(i==0?'<li class="confirm-moren">默认发票</li>':'')+'';
			html += '</ul>';
			html += '</div>';
			html += '<div class="confirm-addcz">';
			html += '<a href="javascript:;" onclick="editInvoice('+data[i].id+')">编辑</a>';
			html += '<a href="javascript:;" onclick="delInvoice('+data[i].id+')">删除</a>';
			html += '</div>';
			html += '</div>';
		}
		html += '</div>';
		return html;
	}
})