(function ($) {

    //alert(nfc_data.ajax_url);

    $(document).ready(function () {
        $('.nfc-category-list li a').on('click', function (e) {
            e.preventDefault();

            let catid = $(this).parent('li').data('cat-id');
            //alert(id);
            //$(this).text('unfollow');
            $.ajax({
                url: nfc_data.ajax_url,
                type: 'post',
                data: {
                    action: 'nfc_ajax_get_id',
                    'data': 'nice',
                },
                dataType: 'json',
                success: function (data) {
                    alert(data);
                },
                error: function (errorThrown) {
                    alert(errorThrown);
                }
            });

            // $.post(nfc_data.ajax_url, {action:'nfc_get_id', data: catid},function(data){
            //     console.log(data);
            // });


        });
    });

})(jQuery);