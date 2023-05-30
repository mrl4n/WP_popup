jQuery(document).ready(function($) {
  // Your JavaScript code here

  // Example: Toggle a class on a button click
  $('#my-button').on('click', function() {
    $(this).toggleClass('active');
  });

  // Example: Perform an AJAX request
  $('#my-form').on('submit', function(event) {
    event.preventDefault();
    var formData = $(this).serialize();

    $.ajax({
      url: ajaxurl, // Replace with the appropriate AJAX URL
      method: 'POST',
      data: formData,
      success: function(response) {
        // Handle the AJAX response
      },
      error: function(xhr, status, error) {
        // Handle AJAX error
      }
    });
  });
});
