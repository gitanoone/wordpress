function search(){
    jQuery.ajax({
        url: myajax.url,
        type: 'post',
        data: { action: 'data_fetch',
                title: jQuery('#title').val(),
                date:  jQuery('#date').val(),
                num:   jQuery('#num').val()
        },
        success: function(data) {
                jQuery('#datafetch').html( data );
        }
    });
}