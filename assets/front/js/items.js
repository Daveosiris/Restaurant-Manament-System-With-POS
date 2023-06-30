(function ($) {
    "use strict";
    let maxprice = 0;
    let minprice = 0;
    let typeSort = '';
    let category = '';
    let subcategory = '';
    let tag = '';
    let review = '';
    let search = '';

    $("#slider-range").slider({
        range: true,
        min: 0,
        max: sliderInitMax,
        values: [sliderMinPrice, sliderMaxPrice],
        slide: function (event, ui) {
            $("#amount").val((position == 'left' ? symbol : '') + ui.values[0] + (position == 'right' ? symbol : '') + " - " + (position == 'left' ? symbol : '') + ui.values[1] + (position == 'right' ? symbol : ''));
        }
    });

    $("#amount").val((position == 'left' ? symbol : '') + $("#slider-range").slider("values", 0) + (position == 'right' ? symbol : '') + " - " + (position == 'left' ? symbol : '') + $("#slider-range").slider("values", 1) + (position == 'right' ? symbol : ''));

    $(document).on('click', '.filter-button', function () {
        let filterval = $('#amount').val();
        filterval = filterval.split('-');
        maxprice = filterval[1].replace('$', '');
        minprice = filterval[0].replace('$', '');
        maxprice = parseInt(maxprice);
        minprice = parseInt(minprice);
        $('#maxprice').val(maxprice);
        $('#minprice').val(minprice);
        $('#search-button').trigger('click');
    });

    $(document).on('change', '#type_sort', function () {
        typeSort = $(this).val();
        $('#type').val(typeSort);
        $('#search-button').trigger('click');
    })
    $(document).ready(function () {
        typeSort = $('#type_sort').val();
        $('#type').val(typeSort);
    })

    $(document).on('click', '.tag-id', function () {
        tag = '';
        if ($(this).attr('data-href') != 0) {
            tag = $(this).attr('data-href');
        }
        $('#tag').val(tag);
        $('#search-button').trigger('click');
    })

    $(document).on('click', '.review_val', function () {
        review = $(".review_val:checked").val();
        $('#review').val(review);
        $('#search-button').trigger('click');
    })

    $(document).on('keypress','.input-search',function(e){
        var key = e.which;
        if(key == 13)  // the enter key code
        {
            search = $('.input-search').val();
            $('#search').val(search);
            $('#search-button').click();
            return false;  
        }
    
    })
})(jQuery);