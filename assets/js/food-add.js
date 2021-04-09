jQuery(document).ready(function($){
    $('.food-add-btn').click(function(e){
        e.preventDefault();
        console.log('Form Submitted');

        let formSelected = e.currentTarget.parentElement;
        
        let values = [];
        var product_id = $('input[type=hidden]').val();
        values = Array.from( document.querySelectorAll( 'input[type=checkbox]:checked' )).map(item=>item.value);
        console.log(values);
        $.ajax({
            url: ajax_object.ajax_url,
            data: {
                'action': 'food_ajax_add_to_cart',
                'product_id': product_id,
                'variation': values
            },
            type: 'post',
            success: function(res){
                console.log('success');
            },
            error: function(err){
                console.log(err);
            },
        });

        //formSelected.reset();
        //window.location.href = '/';

        // console.log(formSelected);
        // console.log(values);
        
    });
});