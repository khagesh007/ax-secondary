$(window).scroll(function (event) {

    if($(window).scrollTop() <= 0) {
        $(".pkh-logo").attr("src", $("#base_url").val()+"/pkh/images/logo-light.png");
    } else if($(window).scrollTop() > 75) {
        $(".pkh-logo").attr("src", $("#base_url").val()+"/pkh/images/logo-dark.png");
    }
});


$(document).on('click', '#btn_property-enquiry-popup', function(){

	$('#to_email').val($(this).data('enq_email'));
	$('#enquiry_for').val($(this).data('enquiry_for'));
	$('#enquiry_property_id').val($(this).data('enquiry_property_id'));
	$('#enquiry_agent_id').val($(this).data('enquiry_agent_id'));
	$('#prop_url').val($(this).data('enq_url'));
	$('#enquiry_message').val($(this).data('enq_msg'));
    $('#agent_form_name').text($(this).data('enquiry_agent_name'));
    

 });


 $(document).on('click', '#btn_property-booking-popup', function(){

	$('#booking_property_id').val($(this).data('enquiry_property_id'));
	$('#booking_property_url').val($(this).data('enq_url'));
    

 });





  $(document).on('submit', '.submit_form_load', function(){

	//$('.submit_form_load.submit_form_btn').html('<i class="fa fa-spinner fa-spin"></i> Sending');
	 $(this).closest('div').find('.submit_form_btn').prop('disabled',true).html('<span class="icon-spinner fa-spin"> </span> &nbsp;Sending');


 });

