jQuery(document).ready(function($) {
    $('.add-to-compare').click(function(e) {
        e.preventDefault();
        
        var productId = $(this).data('product-id');

        // Check if productId exists
        if (!productId) {
            alert('Product ID is missing!');
            return;
        }

        // Make the AJAX request to add the product to the comparison
        $.ajax({
            url: product_compare_ajax_object.ajax_url,  // The localized AJAX URL
            type: 'POST',
            data: {
                action: 'add_to_compare',    // Action defined in PHP
                product_id: productId,       // Product ID from data attribute
                nonce: product_compare_ajax_object.ajax_nonce // Nonce for security
            },
            success: function(response) {
                if (response.success) {
                    alert(response.data);  // Success message
                } else {
                    console.error('Error Response: ', response);
                    alert(response.data);  // Error message if something went wrong
                }
            },
            error: function() {
                alert('An error occurred while processing your request. Check console for details.');
            }
        });
    });
});
