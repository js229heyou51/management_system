var menu = {
	el: '#wide-menu',
	mainMenu: '#ns-header-menu',
	firstClass: '.firstClass',
	secondClass: '.secondClass',
	menuDetailClass: '.wide-menu-content',
	mobileMenuBtnClass: '.ns-header-menu-mb',
	isMobile: navigator.userAgent.toLocaleLowerCase().indexOf('mobile') >= 0 || window.innerWidth <= 992,
	init: function () {
		var _this = this;
		$(_this.firstClass).on('click', '[data-menuItem]', function () {
			if ($(this).hasClass('active')) return;
			$(_this.firstClass).find('[data-menuItem]').removeClass('active');
			$(this).addClass('active');
			$($(this).find(_this.secondClass + ' li')).removeClass('active');
			$($(this).find(_this.secondClass + ' li')[0]).addClass('active');
			_this.showDetail($($(this).find(_this.secondClass + ' li')[0]));
			setMenuMinHeight ()
		})
		$(_this.secondClass).on('click', '>li', function () {
			if ($(this).hasClass('viewAll')) return;
			$(_this.secondClass + ' li').removeClass('active');
			$(this).addClass('active');
			_this.showDetail($(this));
			if (_this.isMobile) {
				$(_this.menuDetailClass).addClass('active')
			}
			setMenuMinHeight ()
		})
		$(_this.mainMenu).on('click', 'button, li', function() {
			var _that = this
			var _clickedMenuName = $(_that).attr('data-menuName') === 'all' ? 'products' : $(_that).attr('data-menuName')
			var _clickedMenu = $(_this.firstClass).find('[data-menuItem="' + _clickedMenuName + '"]')
			if ($(_that).attr('data-menuName') === 'all' && $(_this.el).hasClass('expanded')) {
				$(_this.el).removeClass('expanded');
				$(_this.mainMenu).find('button > div').removeClass('open')
				return
			}
			$('.ns-search-layer').hide()
			$(_this.firstClass).find('[data-menuItem]').removeClass('active');
			_clickedMenu.addClass('active')
			$($(_clickedMenu).find(_this.secondClass + ' li')).removeClass('active');
			$($(_clickedMenu).find(_this.secondClass + ' li')[0]).addClass('active');
			_this.showDetail($($(_clickedMenu).find(_this.secondClass + ' li')[0]));
			$(_this.el).addClass('expanded');
			$(_this.mainMenu).find('button > div').addClass('open')
			$('html,body').scrollTop(0)
			setMenuMinHeight ()
		})
		$(_this.mobileMenuBtnClass).on('click', function () {
			if ($(this).hasClass('open')) {
				$(this).removeClass('open')
				$(_this.el).removeClass('expanded');
				$(_this.el).find('.active').removeClass('active');
			} else {
				$(this).addClass('open')
				$(_this.el).addClass('expanded');
				$('html,body').scrollTop(0)
			}
		})
		$(_this.secondClass + ' > span').on('click', function() {
			$(this).parents('li.active').removeClass('active')
			return false;
		})
		$(_this.menuDetailClass).on('click', '> span', function () {
			console.log($(this).parent())
			$(this).parent().removeClass('active')
			return false;
		})
		// Mobile footer
		$('.ns-menu-items').on('click', 'h5', function () {
			var _parent = $(this).parent()
			if ($(_parent).hasClass('expanded')) {
				$(_parent).removeClass('expanded')
			} else {
				$(_parent).addClass('expanded')
			}
		})
		// _this.open()

	},
	open: function (menuName) {
		var activeMenu = menuName ? $(this.el + ' .firstClass>li[data-menuItem="' + menuName + '"]')[0] : $('#wide-menu .firstClass>li.hasChild')[0]
		var firstOpen = $(activeMenu)
		var secondOpen = $($(activeMenu).find(this.secondClass + ' li')[0])
		firstOpen.addClass('active')
		secondOpen.addClass('active')
		this.showDetail(secondOpen)
	},
	close: function () {},
	showDetail: function (item) {
		var html = item.find('.wide-menu-item-content').clone();
		$(this.menuDetailClass).html('').append('<span>&lt; 返回</span>' + (html.html() ? html.html() : ''));
	}
}
var socialMenuInit = function () {
	$('.ns-social-items').on('click', 'a', function (e) {
		e.preventDefault();
		console.log(this)
	})
}
function setMenuMinHeight () {
	if ($(window).width() > 992) {
		setTimeout(function () {
			var oneMenuList = $('.firstClass').children()
			var oneMenuHeight = 40
			var twoMenuLsit = $('.firstClass li.active').find('.secondClass').children()
			var twoMenuHeight = 40
			var threeMenuList = $('.wide-menu-content').children()
			var threeMenuHeight = 40
			$.each(threeMenuList, function (i, e) {
				threeMenuHeight += $(e).outerHeight(true)
			})
			$.each(twoMenuLsit, function (i, e) {
				twoMenuHeight += $(e).outerHeight(true)
			})
			$.each(oneMenuList, function (i, e) {
				oneMenuHeight += $(e).outerHeight(true)
			})
			var menuHeightArr = [oneMenuHeight, twoMenuHeight, threeMenuHeight]
			menuHeightArr.sort(function (a, b) {
				return b - a
			})
			if (menuHeightArr[0] > $(window).height()) {
				$('#wide-menu').css('height', menuHeightArr[0])
			} else {
				$('#wide-menu').css('height', $(window).height())
			}
		}, 200)
	}
}
$(function () {
	menu.init()
	socialMenuInit()

	if ($('.ns-header-user ul > li').length > 0) {
		$('.ns-header-user ul > li').eq(0).addClass('active')
	}
	$('.ns-header-user ul > li').click(function () {
		if (!$(this).hasClass('active')) {
			$('.ns-header-user ul > li').removeClass('active')
			$(this).addClass('active')
		}
	})
	$('#ns-float-contact-us .ns-btn').click(function () {
		$('#ns-float-contact-us').addClass('active')
	})
	$('.ns-close-float-contact-us').click(function () {
		$('#ns-float-contact-us').removeClass('active')
	})
	$('.ns-mb-language-region h2').click(function () {
		if ($(this).parents('.ns-mb-language-region').hasClass('open-active')) {
			$(this).parents('.ns-mb-language-region').removeClass('open-active')
		} else {
			$(this).parents('.ns-mb-language-region').addClass('open-active')
		}
	})
	$(window).resize(function () {
		setMenuMinHeight ()
	})

	$('.ns-header-search').on('click', function () {
		if ($('.ns-search-layer').css('display') === 'none') {
			$('.ns-search-layer').slideDown()
			$('#wide-menu').removeClass('expanded')
			$('#ns-header-menu button>div').removeClass('open')
		} else {
			$('.ns-search-layer').slideUp()
		}
	})
})
