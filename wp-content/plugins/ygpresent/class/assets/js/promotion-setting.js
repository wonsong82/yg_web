jQuery(document).ready(function($){

	var curCheckCnt = $('#curCheckCnt').val();
	var requiredCnt = $('#required_cnt').val();

	$('.btn-submit.promotion').click(function(e){
		e.preventDefault();

		// -1 means no need to check current checkCnt
		if(requiredCnt != -1){

			if(requiredCnt != curCheckCnt){
				alert('You have to check ' + requiredCnt + ' items to proceed. Currently ' + curCheckCnt + ' checked.');
				return;
			}
		}
		$('#form').submit();
	});


	$('[id*="enable_"]').each(function(){
		$(this).on('change', checkBoxChanged);
	});


	function checkBoxChanged(){
		var $this = $(this);
		var curId = $this.attr('id').split('_')[1];

		var $orderInput = $('#order_'+curId);

		//Checkbox selected - Order input field should be enabled
		if($this.is(':checked')){
			$orderInput.removeAttr('disabled').focus();
			curCheckCnt++;
		}else{
			$orderInput.attr('disabled','disabled').val("");
			curCheckCnt--;
		}

	}

});










 