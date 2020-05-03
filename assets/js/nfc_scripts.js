(function ($) {

    //alert(nfc_data.ajax_url);

    $(document).ready(function () {
        $('.nfc-category-list li a').on('click', function (e) {
            let catid = $(this).data('cat-id');
            
            $.ajax({
                url:nfc_data.ajax_url,
                type: 'post',
                data:{ 
                    action: 'nfc_ajax_get_id',
                    data: catid,
                }
            }).done (function (response) {
                if(response == catid){
                    $(".nfc-category-list li a[data-cat-id="+catid+"]").text('unfollow') 
                }
                
                //console.log(response);
               
            });
            e.preventDefault();
        });
    });

})(jQuery);
