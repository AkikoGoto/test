$(function() {
	//クリックしたときのファンクションをまとめて指定
	$('.change_tab li').click(function() {

		//.index()を使いクリックされたタブが何番目かを調べ、
		//indexという変数に代入します。
		var index = $('.change_tab li').index(this);

		//コンテンツを一度すべて非表示にし、
		$('.tab_content li').css('display','none');

		//クリックされたタブと同じ順番のコンテンツを表示します。
		$('.tab_content li').eq(index).css('display','block');

		//一度タブについているクラスselectを消し、
		$('.change_tab li').removeClass('select');

		//クリックされたタブのみにクラスselectをつけます。
		$(this).addClass('select')
	});
});