layui.use(['layer'],function(){
	$ = layui.jquery;

	logout = function(){
		layer.confirm('确认要退出吗？',{
			icon:3,
			btn: ['确定','取消']
		},function(){
			$.post('/Login/logout',function(res){
				if(res.code==200){
					layer.msg(res.msg,{'icon':1});
					setTimeout(function(){window.location.href="/login/";},100);
				}else{
					layer.msg(res.msg,{'icon':2});
				}
			},'json')
		})
	}

	var swiper = new Swiper('.banner .swiper-container', {
		slidesPerView: 1,
		spaceBetween: 0,
		loop: true,
		// autoplay: true,
		pagination: {
			el: '.banner .swiper-pagination',
			clickable: true,
		},
		// navigation: {
		// 	nextEl: '.banner .swiper-button-next',
		// 	prevEl: '.banner .swiper-button-prev',
		// },

	});
	$('.js-bj').click(function(){
		var title = $(this).data('title');
		$('#pro_title').val(title);
		$('.ny2-tcbjbg').show();
		$('.ny2-tcbj').show();
	})
	$('.ny2-tcbj-close').click(function(){
		$(this).parents('.ny2-tcbj').hide();
		$(this).parents('.ny2-tcbj').siblings('.ny2-tcbjbg').hide();
	})
	$('.js-xq-bj').click(function(){
		$('.ny2-tcbjbg').show();
		$('.ny2-tcbj').show();
	})
	$('.cart-cprice a').hover(function(){
		$(this).parents('.cart-cprice').find('.cart-jgjt').toggle();
	})
	$('.address-item').click(function(){
		$(this).addClass('on').siblings().removeClass('on');
	})
	$('.confirm-paybox').click(function(){
		$(this).addClass('pay-on').siblings().removeClass('pay-on');
	})
	$('.confirm-kdbox').click(function(){
		$(this).addClass('kd-on').siblings().removeClass('kd-on');
	})
	$('.confirm-fpbox').click(function(){
		$(this).addClass('fp-on').siblings().removeClass('fp-on');
		var type=$(this).data('type');
		var totalPrice = $('#totalPrice').val();
		var freightPrice = $('#freightPrice').val();
		var allPrice = parseFloat(totalPrice) + parseFloat(freightPrice)
		if(type==0){
			$('#needInvoice').val(0)
			$('.confirm-needfp').hide();
			$('#invoiceId').val('');
			$('#valueAddedText').hide();
			$('.confirm-fplist .confirm-add').removeClass('add-on');
			$('#allPrice').val(allPrice.toFixed(2));
			$('#allPriceText').text(allPrice.toFixed(2));
		}else{
			$('#needInvoice').val(1);
			$('#valueAddedText').show();
			var valueAdded = $('#valueAdded').val();
			allPrice = allPrice + allPrice * valueAdded;
			$('#allPrice').val(allPrice.toFixed(2));
			$('#allPriceText').text(allPrice.toFixed(2));
			$('.confirm-needfp').show();
		}
	})
})