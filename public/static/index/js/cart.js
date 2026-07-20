layui.use(['layer'],function(){
	$ = layui.jquery;

	$('.ny2-sxl>ul>li').on('click',function(){
		var lm = $('#lm').val();
		$(this).toggleClass('active').siblings().removeClass('active');
		var idArr = [];
		$('.ny2-sxl>ul>li').each(function(){
			if($(this).hasClass('active') == true){
				var id = $(this).data('id');
				idArr.push(id)
			}
		})
		$.post('/Product/listAjax',{'lm':lm,'idArr':idArr},function(res){
			if(res.code == 200){
				getProduct(res);
			}else{
				layer.msg('参数错误！',{icon:2});
			}
		},'json');
	})

	getProduct = function(res){
		var html = '';
		for (var i = 0; i < res.data.length; i++) {
			html += '<ul>';
			html += '<li><div class="ny2-list-box flex"><div class="ny2-list-img">';
			html += '<a href="/product/show/'+res.data[i].id+'"><img src="'+(res.data[i].gallery_list[0]?.path??'')+'"></a>';
			html += '</div><div class="ny2-list-text">';
			html += '<a href="/product/show/'+res.data[i].id+'"><h2>'+res.data[i].title+'</h2></a>';
			html += '<p>';
			if(res.data[i].paramList.length > 0){
				for (var j = 0; j < res.data[i].paramList.length; j++) {
					if(res.data[i].paramList[j]?.profile?.title_lm){
						html += '<span>'+(res.data[i].paramList[j]?.profile?.title_lm)+'：';
						html += res.data[i].paramList[j]?.title;
					}
					html += '</span>';
				}
			}
			html += '</p>';
			if(res.data[i]?.fil_sl){
				html += '<div class="ny2-list-pdf">';
				html += '<a href="'+res.data[i]?.fil_sl+'"><img src="/static/index/images/ny2_03.png"> 数据手册</a>';
				html += '</div>';
			}
			html += '</div></div></li>';
			html += '<li><div class="ny2-list-price">';
			for (var j = 0; j < res.data[i].priceList.length; j++) {
				html += '<p>'+res.data[i].priceList[j].num+'+：￥'+res.data[i].priceList[j].price+'</p>';
			}
			html += '<p>2000+： <a href="javascript:;" data-title="'+res.data[i].title+'" class="js-bj">报价</a></p>';
			html += '</div></li>';

			html += '<li><div class="ny2-list-num">';
			html += '<button type="button" class="num-less" onclick="less(this)">-</button>';
			html += '<input type="text" name="num" placeholder="01" value="1">';
			html += '<button type="button" class="num-plus" onclick="plus(this)">+</button>';
			html += '</div>';
			html += '<div class="ny2-list-stock">';
			html += '<p>库存量:'+res.data[i].stock+'</p>';
			html += '</div></li>';
			html += '<li><div class="ny2-list-buy">';
			html += '<a href="javascript:;">立即购买</a>';
			html += '<a href="javascript:;">加入购物车</a>';
			html += '</div></li>';
			html += '</ul>';
		}
		var pageHtml = '';
		if(res.page.last_page > 1){
			pageHtml += '<ul>';
			if(res.page.current_page == 1){
				pageHtml += '<li class="disabled"><span>&laquo;</span></li>';
			}else{
				prevPage = res.page.current_page - 1;
				pageHtml += '<li><a href="javascript:;" onclick="goPage('+prevPage+')">&laquo;</a></li>';
			}
			for (var i = 1; i <= res.page.last_page; i++) {
				if(res.page.current_page == i){
					pageHtml += '<li class="active"><span>'+i+'</span></li>';
				}else{
					pageHtml += '<li><a href="javascript:;" onclick="goPage('+i+')">'+i+'</a></li>';
				}
			}
			if(res.page.current_page == res.page.last_page){
				pageHtml += '<li class="disabled"><span>&raquo;</span></li>';
			}else{
				nextPage = res.page.current_page + 1;
				pageHtml += '<li><a href="javascript:;" onclick="goPage('+nextPage+')">&raquo;</a></li>';
			}
			pageHtml += '</ul>';
		}
		$('#proResultList').html(html);
		$('#resultCount').text(res.count);
		$('#proResultPages').html(pageHtml);
	}

	less = function(self){
		const package = $('#package').val();
		var num = $(self).siblings('input[name=num]').val();
		var resNum = Number(num)-1;
		if(resNum <= 0){
			resNum = 1;
			$(self).val(resNum);
			layer.msg('已是最少数量',{icon:2});
		}
		$(self).siblings('input[name=num]').val(resNum);
		packagenum = Math.floor(resNum/package);
		$('input[name=packagenum]').val(packagenum);
		const price = getPrcie(resNum);
		$('#priceText').text(price);
		const total = resNum * price;
		$('#totalText').text(total.toFixed(4));
	}
	plus = function(self){
		const package = $('#package').val();
		var num = $(self).siblings('input[name=num]').val();
		var resNum = Number(num)+1;
		const stock = $('#stock').val();
		if(parseInt(resNum) > parseInt(stock)){
			resNum = stock;
			$(self).val(resNum);
			layer.msg('已超出库存数量',{icon:2});
		}
		$(self).siblings('input[name=num]').val(resNum);
		packagenum = Math.floor(resNum/package);
		$('input[name=packagenum]').val(packagenum);
		const price = getPrcie(resNum);
		$('#priceText').text(price);
		const total = resNum * price;
		$('#totalText').text(total.toFixed(4));
	}
	$('#num').on('keyup',function(){
		const package = $('#package').val();
		var num = $(this).val();
		const stock = $('#stock').val();
		if(parseInt(num) > parseInt(stock)){
			num = stock;
			$(this).val(num);
			layer.msg('已超出库存数量',{icon:2});
		}
		if(num <= 0){
			num = 1;
			$(this).val(num);
			layer.msg('数量不能少于0',{icon:2});
		}
		packagenum = Math.floor(num/package);
		$('input[name=packagenum]').val(packagenum);
		const price = getPrcie(num);
		$('#priceText').text(price);
		const total = num * price;
		$('#totalText').text(total.toFixed(4));
	})

	packageLess = function(self){
		const package = $('#package').val();
		var packagenum = $(self).siblings('input[name=packagenum]').val();
		if(packagenum <= 0){
			layer.msg('已是最少数量',{icon:2});
		}else{
			$(self).siblings('input[name=packagenum]').val(Number(packagenum)-1);
			var num = $('input[name=num]').val();
			const resNum = Number(num)-Number(package)
			$('input[name=num]').val(resNum);
			const price = getPrcie(resNum);
			$('#priceText').text(price);
			const total = resNum * price;
			$('#totalText').text(total.toFixed(4));
		}
	}
	packagePlus = function(self){
		const package = $('#package').val();
		var packagenum = $(self).siblings('input[name=packagenum]').val();
		$(self).siblings('input[name=packagenum]').val(Number(packagenum)+1);
		var num = $('input[name=num]').val();
		const resNum = Number(num)+Number(package)
		$('input[name=num]').val(resNum);
		const price = getPrcie(resNum);
		$('#priceText').text(price);
		const total = resNum * price;
		$('#totalText').text(total.toFixed(4));
	}

	$('input[name=packagenum]').on('keyup',function(){
		const package = $('#package').val();
		var packagenum = $(this).val();
		const num = packagenum*Number(package)
		$('input[name=num]').val(num);
		const price = getPrcie(num,$(this));
		$('#priceText').text(price);
		const total = num * price;
		$('#totalText').text(total.toFixed(4));
	})

	goPage = function(page){
		var lm = $('#lm').val();
		var idArr = [];
		$('.ny2-sxl>ul>li').each(function(){
			if($(this).hasClass('active') == true){
				var id = $(this).data('id');
				idArr.push(id);
			}
		})
		$.post('/Product/listAjax',{'lm':lm,'idArr':idArr,'page':page},function(res){
			if(res.code == 200){
				getProduct(res);
			}else{
				layer.msg('参数错误！',{icon:2});
			}
		},'json');
	}

	getPrcie = function(num,self=''){
		const _priceLists = $('#priceLists').val();
		if(_priceLists == undefined){
			priceLists = $(self).siblings('input[name=priceLists]').val();
		}else{
			priceLists = _priceLists;
		}
		priceJSON = $.parseJSON(priceLists);
		if(priceJSON){
			for (var i = 0; i < priceJSON.length; i++) {
				if(parseInt(num) >= priceJSON[i].num){
					price = priceJSON[i].price;
				}
			}
			$('#price').val(price);
			return price;
		}
		return false
	}

	addCartList = function(self){
		const data = $(self).parents('form').serialize();
		$.post('/Cart/addCart',data,function(res){
			if(res.code == 200){
				$('#cartCount').text(res.cartCount);
				layer.msg(res.msg,{icon:1});
			}else{
				layer.msg(res.msg,{icon:2});
				setTimeout(function(){window.location.href='/login/';},500);
			}
		},'json');
	}


	addCart = function(){
		const data = $('#cartForm').serialize();
		$.post('/Cart/addCart',data,function(res){
			if(res.code==200){
				$('#cartCount').text(res.cartCount);
				layer.msg(res.msg,{icon:1});
			}else{
				layer.msg(res.msg,{icon:2});
			}
		},'json');
	}

	cartLess = function(self){
		const pid = $(self).parents('.cartItem').find('input[name=pid]').val();
		const package = $('#package').val();
		var num = $(self).siblings('input[name=num]').val();
		if(num <= 1){
			layer.msg('已是最少数量',{icon:2});
		}else{
			var resNum = Number(num)-1;
			$(self).parents('.cartItem').find('input[name=num]').val(resNum);
			const price = getPrcie(resNum,self);

			$.post('/Cart/makeCart',{'pid':pid,'num':resNum,'price':price,'act':'addCart'},function(res){
				if(res.code==200){
					$(self).parents('.cartItem').find('.cart-cprice h3 span').text(price);
					const total = resNum * price;
					$(self).parents('.cartItem').find('.cart_aprice span').text(total.toFixed(4));
					$(self).parents('.cartItem').find('input[name=totalPrice]').val(total.toFixed(4));
					console.log($(self).parents('.cartItem').find('input[name="cartSelect[]"]').is(':checked'))
					if($(self).parents('.cartItem').find('input[name="cartSelect[]"]').is(':checked') == true){
						var totalPrice = 0;
						$('input[name=totalPrice]').each(function(){
							if($(this).parents('.cartItem').find('input[name="cartSelect[]"]').is(':checked') == true){
								totalPrice = totalPrice + parseFloat($(this).val());
							}
						})
						$('#totalPriceText').text(totalPrice.toFixed(4));
						$('#totalPrice').val(totalPrice.toFixed(4));
					}
				}else{
					layer.msg(res.msg,{icon:2});
				}
			},'json')

			
		}
	}
	cartPlus = function(self){
		const pid = $(self).parents('.cartItem').find('input[name=pid]').val();
		var num = $(self).siblings('input[name=num]').val();
		var resNum = Number(num)+1;
		const stock = $(self).parents('.cartItem').find('input[name=stock]').val();
		if(parseInt(resNum) > parseInt(stock)){
			resNum = stock;
			$(self).val(resNum);
			layer.msg('已超出库存数量',{icon:2});
		}
		$(self).siblings('input[name=num]').val(resNum);
		const price = getPrcie(resNum,self);
		$.post('/Cart/makeCart',{'pid':pid,'num':resNum,'price':price,'act':'addCart'},function(res){
			if(res.code==200){
				$(self).parents('.cartItem').find('.cart-cprice h3 span').text(price);
				const total = resNum * price;
				$(self).parents('.cartItem').find('.cart_aprice span').text(total.toFixed(4));
				$(self).parents('.cartItem').find('input[name=totalPrice]').val(total.toFixed(4));
				if($(self).parents('.cartItem').find('input[name="cartSelect[]"]').is(':checked') == true){
					var totalPrice = 0;
					$('input[name=totalPrice]').each(function(){
						if($(this).parents('.cartItem').find('input[name="cartSelect[]"]').is(':checked') == true){
							totalPrice = totalPrice + parseFloat($(this).val());
						}
					})
					$('#totalPriceText').text(totalPrice.toFixed(4));
					$('#totalPrice').val(totalPrice.toFixed(4));
				}
			}else{
				layer.msg(res.msg,{icon:2});
			}
		},'json')
	}
	$('.cartItem input[name=num]').on('change',function(){
		self = this;
		const pid = $(self).parents('.cartItem').find('input[name=pid]').val();
		var resNum = $(self).val();
		const stock = $(self).siblings('input[name=stock]').val();
		if(parseInt(resNum) > parseInt(stock)){
			resNum = stock;
			$(self).val(resNum);
			layer.msg('已超出库存数量',{icon:2});
		}
		if(resNum <= 0){
			resNum = 1;
			$(self).val(resNum);
			layer.msg('数量不能少于0',{icon:2});
		}
		const price = getPrcie(resNum,self);
		$.post('/Cart/makeCart',{'pid':pid,'num':resNum,'price':price,'act':'addCart'},function(res){
			if(res.code==200){
				$(self).parents('.cartItem').find('.cart-cprice h3 span').text(price);
				const total = resNum * price;
				$(self).parents('.cartItem').find('.cart_aprice span').text(total.toFixed(4));
				$(self).parents('.cartItem').find('input[name=totalPrice]').val(total.toFixed(4));
				if($(self).parents('.cartItem').find('input[name="cartSelect[]"]').is(':checked') == true){
					var totalPrice = 0;
					$('input[name=totalPrice]').each(function(){
						if($(this).parents('.cartItem').find('input[name="cartSelect[]"]').is(':checked') == true){
							totalPrice = totalPrice + parseFloat($(this).val());
						}
					})
					$('#totalPriceText').text(totalPrice.toFixed(4));
					$('#totalPrice').val(totalPrice.toFixed(4));
				}
			}else{
				layer.msg(res.msg,{icon:2});
			}
		},'json')
	})

	selectAll = function(self){
		if($(self).is(':checked') == true){
			$('input[name=selectAll').attr('checked',true);
			$('input[name="cartSelect[]"]').attr('checked',true);
			const totalNum = $('input[name="cartSelect[]"]').length;
			$('#totalNumText').text(totalNum);
			$('#totalNum').val(totalNum);
			var totalPrice = 0;
			$('input[name=totalPrice]').each(function(){
				totalPrice = totalPrice + parseFloat($(this).val());
			})
			$('#totalPriceText').text(totalPrice.toFixed(4));
			$('#totalPrice').val(totalPrice.toFixed(4));
		}else{
			$('input[name=selectAll').attr('checked',false);
			$('input[name="cartSelect[]"]').attr('checked',false);
			$('#totalNumText').text(0)
			$('#totalNum').val(0);
			$('#totalPriceText').text(0);
			$('#totalPrice').val(0);
		}
	}

	selectOne = function(self){
		var count = 0;
		const length = $('input[name="cartSelect[]"]').length;
		var sumPrice = 0;
		$('input[name="cartSelect[]"]').each(function(){
			if($(this).is(':checked') == true){
				count ++;
				var price = $(this).parents('.cartItem').find('input[name=totalPrice]').val();
				sumPrice = sumPrice + parseFloat(price);
			}
		});
		if(count === parseInt(length)){
			$('input[name=selectAll').attr('checked',true);
		}else{
			$('input[name=selectAll').attr('checked',false);
		}
		$('#totalNumText').text(count);
		$('#totalNum').val(count);
		var totalPrice = parseFloat(sumPrice);
		$('#totalPriceText').text(totalPrice.toFixed(4));
		$('#totalPrice').val(totalPrice.toFixed(4));
	}

	selectDel = function(){
		var count = 0;
		var idStr = '';
		$('input[name="cartSelect[]"]').each(function(){
			if($(this).is(':checked') == true){
				count ++;
				val = $(this).val();
				idStr += val+',';
			}
		});
		if(idStr!=''){
			idStr = idStr.slice(0,-1);
		}
		if(count <= 0){
			layer.msg('请选择数据',{icon:2});
			return false;
		}

		$.post('/Cart/makeCart',{'idStr':idStr,'act':'selectDel'},function(res){
			if(res.code>0){
				layer.msg(res.msg,{icon:2});
			}else{
				layer.msg(res.msg,{icon:1});
				setTimeout(function(){window.location.reload();},1000);
			}
		},'json')
	}

	delCart = function(id){
		layer.confirm('确定要删除吗？', {
			icon:3,
			btn: ['确定','取消']
		}, function(){
			$.post("/Cart/makeCart",{'id':id,'act':'delCart'},function(res){
				if(res.code==200){
					layer.msg(res.msg,{icon:1});
					setTimeout(function(){window.location.reload();},500);
				}else{
					layer.alert(res.msg,{icon:2});
				}
			},'json');
		});
	}
	settlement = function(){
		var count = 0;
		var idStr = '';
		$('input[name="cartSelect[]"]').each(function(){
			if($(this).is(':checked') == true){
				count ++;
				val = $(this).val();
				idStr += val+',';
			}
		});
		if(idStr!=''){
			idStr = idStr.slice(0,-1);
		}
		if(count <= 0){
			layer.msg('请选择数据',{icon:2});
			return false;
		}

		// 创建一个新的form元素
		var cform = $('<form action="/cart/settlement/" method="post"></form>');

		// 将input添加到form中
		// 创建一个新的隐藏输入字段并添加到表单中
		var input = $('<input>').attr({
			type: 'hidden',
			name: 'idStr',
			value: idStr
		})
		cform.append(input);
		$('body').append(cform);
		setTimeout(function(){cform.submit();},500);
	}


	selectFreight = function(self){
		var text = $(self).text();
		var id = $(self).data('id');
		$('#freightId').val(id);
		$('#freightType').text(text);
	}

	selectPayType = function(self){
		var url = $(self).data('url');
		$('#payUrl').val(url);
	}

	submitOrder = function(){
		var orderForm = $('#orderForm');
		var remarks = $('#remarks').val();
		$('#orderRemarks').remove();
		var textarea = $('<textarea></textarea>').attr({
			id: 'orderRemarks',
			name: 'remarks',
			value: remarks,
			style:'display:none'
		})
		orderForm.append(textarea);
		var data = $('#orderForm').serialize();
		$.post("/Cart/addOrder",data,function(res){
			if(res.code == 200){
				layer.msg(res.msg,{icon:1});
				setTimeout(function(){window.location.href='/cart/payOrder/'+res.orderId;},500);
				// setTimeout(function(){window.location.reload();},1000);
			}else{
				layer.alert(res.msg,{icon:2});
			}
		},'json');
	}


	collect = function(self,type=''){
		var id = $(self).data('id');
		$.post("/Cart/collect",{'id':id},function(res){
			if(res.code==200){
				layer.msg(res.msg,{icon:1});
				$(self).text(res.text);
				if(type == 'collect'){
					setTimeout(function(){window.location.reload();},1000);
				}
			}else{
				layer.alert(res.msg,{icon:2});
			}
		},'json');
	}

	buyNow = function(self){
		var id = $(self).data('id');
		var num = $(self).parents('form').find('input[name=num]').val();
		// 创建一个新的form元素
		var cform = $('<form action="/cart/settlement/" method="post"></form>');

		// 将input添加到form中
		// 创建一个新的隐藏输入字段并添加到表单中
		var input = $('<input>').attr({
			type: 'hidden',
			name: 'pid',
			value: id
		})
		cform.append(input);
		if(num !== undefined){
			var input2 = $('<input>').attr({
				type: 'hidden',
				name: 'num',
				value: num
			})
			cform.append(input2);
		}
		$('body').append(cform);
		setTimeout(function(){cform.submit();},500);
	}
})