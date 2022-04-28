(function($){

	function resetAll(e){
		e.preventDefault();
		const allinput = $('#papst_title .papst_input input');
		allinput.val('');
		$('.color_check>span').css({'color':''});
		$('input[type="checkbox"]:checked').prop('checked', false);
	}

	function colorValue(){
		var valofcol = $('.papst-second-title-color input[type="color"]').val();
		console.log(valofcol);
		$('.color_check>span').css({'color':valofcol});
	}

	function controls(){
		$(document).on('input','#Changecolor_Sec',colorValue);
		$(document).on('click','.reset',resetAll);
	}
	controls();
})(jQuery);