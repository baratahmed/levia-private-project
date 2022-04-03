$(document).ready(function() {
	$('#sidebarCollapse').on('click', function () {
        $('#sidebar').toggleClass('active');
    });

	$('.close-notification').click(function() {
		$(this).parent().parent().parent().fadeOut('slow');
	});

    $('#view-rating-restaurant').css('display', 'block');

	$('input:radio[name="ratings"]').change(function() {
		if ($(this).is(':checked')) {
			$('.rating').css('display', 'none');
			$('#view-' + this.id).css('display', 'block');
		}
	});

	$('tr').click(function() {
		$(this).find('button').trigger('click');
    });
});