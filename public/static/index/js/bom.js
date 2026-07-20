layui.use(['layer','form'],function(){
	$ = layui.jquery;
	var form = layui.form;
	$('#selAll').click(function(){
		var btn = $(this);
		var total_num = 0;
		var total_price = 0;
		$('#matchResult input[lay-skin=primary]').each(function(index,item){
			if(item.checked == true){
				item.checked = false;
				total_num = 0;
				total_price = 0;
			}else{
				item.checked = true;
				var num = $(this).parents('tr').find('input.bomnum').val();
				total_num = parseInt(total_num) + parseInt(num);
				var price = $(this).siblings('input.bomprice').val();
				total_price = parseFloat(total_price) + parseFloat(price*num);
			}
		})
		$('#total-num').val(parseInt(total_num));
		$('#total-price').val(parseFloat(total_price));
		$('#matchAmount').text(parseFloat(total_price).toFixed(2,''))
		form.render('checkbox');
	})
	$('#addToCart').on('click',function(){
		var btn = $(this);
		var str = '';
		var a   = 1;
		var data = $('#bomForm').serialize();
		$.post('/Bom/make',data,function(res){
			if(res.code>0){
				layer.msg(res.msg,{icon:2});
				if(res.code == 2){
					setTimeout(function(){window.location.href='/login/';},500);
				}
			}else{
				setTimeout(function(){window.location.href='/cart/';},500);
			}
		},'json')
	})

	form.on('checkbox(select)',function(data){
		var total_num = 0;
		var total_price = 0;
		$('#matchResult input[lay-skin=primary]').each(function(index,item){
			if(item.checked == true){
				var num = $(this).parents('tr').find('input.bomnum').val();
				total_num = parseInt(total_num) + parseInt(num);
				var price = $(this).siblings('input.bomprice').val();
				total_price = parseFloat(total_price) + parseFloat(price*num);
			}
		})
		$('#total-num').val(parseInt(total_num));
		$('#total-price').val(parseFloat(total_price));
		$('#matchAmount').text(parseFloat(total_price).toFixed(2,''))
		form.render('checkbox');
	})
	
	$('.bomnum').change(function(){
		var numrow = $(this);
		var row    = $(this).parents("tr");//产品栏
		var pid    = row.data('pid');//产品ID
		var num    = $(this).val();//数量
		if(num==''){
			num = 0;	
		}
		if(parseInt(num)!=num){
			num = parseInt(num);
		}
		total_num = 0;
		total_price = 0;
		$.ajax({
			type:"post",
			url:"bommake.php",
			dataType:"json",
			data:{act:"bomchange",num:num,pid:pid},
			success:function(data){
				if(data.login){
					loginMsgBox(data.login);
				}
				if(data.mesg){
					layer.msg(data.mesg,{anim:6});
				}
				if(data.success){
					//更新单个产品信息
					numrow.parents('td').siblings('.td-hover').find('span').data('value',data.price);
					numrow.parents('td').siblings('.td-hover').find('span').text(data.price);
					$('#matchResult>tbody>tr').each(function(){
						if($(this).find('.icheckbox_minimal-orange').hasClass('checked')==true){
							var num = $(this).find('.bomnum').val();
							var price = $(this).find('.td-hover span').data('value');
							total_num = parseInt(total_num) + parseInt(num);
							total_price = parseFloat(total_price) + parseFloat(price*num);
						}else{
							total_num = 0;
							total_price = 0.00;
						}
					})
					$('#total-num').val(parseInt(total_num));
					$('#total-price').val(parseFloat(total_price));
					$('#matchAmount').text(parseFloat(total_price).formatMoney(2,''))
					// if(numrow.parents('tr').find('.icheckbox_minimal-orange').hasClass('checked')==true){
					// 	$()
					// 	$('#total-num').val(parseInt(total_num));
					// 	$('#total-price').val(parseFloat(total_price));
					// 	$('#matchAmount').text(parseFloat(total_price).formatMoney(2,''))
					// 	$('#matchAmount').text(parseFloat(data.price*num).formatMoney(2,''))
					// }
				}
			}
		})
	})
})