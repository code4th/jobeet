$(document).ready(function()
{
 
  $('#search_keywords').keyup(function(key)
  {
    if (this.value.length >= 3 || this.value == '')
    {
      $('.search input[type="submit"]').hide();
      $('#loader').show();
      $('#jobs').load(
        $(this).parents('form').attr('action'),
        { query: this.value + '*' },
        function() { $('#loader').hide(); }
      );
    }else{
        $('.search input[type="submit"]').show();
    }
  });
});