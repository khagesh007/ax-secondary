<script>
    $(document).on('click', '.rmActive', function() {
    var spanClasses = $(this).find('span').attr('class').split(' ');
    var badgeSoftClass = spanClasses.filter(function(className) {
        return className.includes('color-outline');
    })[0];
    console.log(badgeSoftClass);
    $('.rmDark').removeClass(badgeSoftClass);
    badgeClass = badgeSoftClass.split('-');
    console.log(badgeClass)

    $(this).find('span').addClass('color-' + 'bg-' + badgeClass[2]);
    $('#lead_status_id').val($(this).data('status_id'));
    $('#lead_sub_status_id').val($(this).data('sub_id'));
    $('.rmActive').removeClass('active');
    console.log('SUB ' + $('#lead_sub_status_id').val() + ' Status ' + $('#lead_status_id')
        .val());
    });

</script>
