(function ($) {

    //alert(nfc_data.ajax_url);

    $(document).ready(function ($) {
        $('.nfc-category-list li a').on('click', function (e) {
            let catid = $(this).data('cat-id');
            $.ajax({
                url:nfc_data.ajax_url,
                type: 'post',
                data:{ 
                    action: 'nfc_ajax_get_id',
                    data: catid
                }
            }).done (function (response) {
                if(response){
                    $(this).text('unfollow');
                    console.log(response);
                }else{
                    alert(response);
                }
               
            });
            e.preventDefault();
        });
    });

})(jQuery);
