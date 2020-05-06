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
                if(response.value == "followed"){
                    $(".nfc-category-list li a[data-cat-id="+catid+"]").text(response.value) 
                    $(".nfc-category-list li a[data-cat-id="+catid+"]").attr('data-value',response.value) 
                    let li = $(".nfc-category-list li a[data-cat-id="+catid+"]").closest('li')[0].outerHTML
                    $('.nfc-my-category-list').append(li)
                    setInterval(function(){
                        $(".nfc-all-category-list li a[data-cat-id="+catid+"]").parent('li').remove()
                     },500)
                     console.log(response);
                     
                }else{
                    $(".nfc-category-list li a[data-cat-id="+catid+"]").attr('data-value',response.value) 
                    $(".nfc-category-list li a[data-cat-id="+catid+"]").text(response.value) 

                    let li = $(".nfc-category-list li a[data-cat-id="+catid+"]").closest('li')[0].outerHTML
                    $('.nfc-all-category-list').append(li)
                    setInterval(function(){
                        $(".nfc-my-category-list li a[data-cat-id="+catid+"]").parent('li').remove()
                     },500)
                     

                }
               
            });
            e.preventDefault();
        });
        
        
        $('.nfc-tag-list li a').on('click', function (e) {
            let tag_id = $(this).data('tag-id');
            
            $.ajax({
                url:nfc_data.ajax_url,
                type: 'post',
                data:{ 
                    action: 'nfc_tag_ajax_get_id',
                    data: tag_id,
                }
            }).done (function (response) {
                if(response.value == "followed"){
                    $(".nfc-tag-list li a[data-tag-id="+tag_id+"]").text(response.value) 
                    $(".nfc-tag-list li a[data-tag-id="+tag_id+"]").attr('data-value',response.value) 
                    
                    let li = $(".nfc-tag-list li a[data-tag-id="+tag_id+"]").closest('li')[0].outerHTML
                    $('.nfc-my-tag-list').append(li)
                     
                    setInterval(function(){
                       $(".nfc-all-tags-list li a[data-tag-id="+tag_id+"]").parent('li').remove()
                    },500)
                    
                    console.log(li)
                }else{
                    $(".nfc-tag-list li a[data-tag-id="+tag_id+"]").attr('data-value',response.value) 
                    $(".nfc-tag-list li a[data-tag-id="+tag_id+"]").text(response.value) 
                    let li = $(".nfc-tag-list li a[data-tag-id="+tag_id+"]").closest('li')[0].outerHTML
                    $('.nfc-all-tags-list').append(li)
                    setInterval(function(){
                        $(".nfc-my-tag-list li a[data-tag-id="+tag_id+"]").parent('li').remove()
                     },500)
                }
                
                
               
            });
            e.preventDefault();
        });  
    });

})(jQuery);
