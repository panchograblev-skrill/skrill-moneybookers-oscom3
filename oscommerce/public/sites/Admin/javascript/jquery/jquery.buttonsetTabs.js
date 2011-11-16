/**
 * Buttonset Tabs jQuery Plugin
 *
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

(function( $ ){
  $.fn.buttonsetTabs = function() {
    return this.each(function() {
      var $this = $(this);
      var sectionsId = $this.attr('id');
      var checkedId = $('#' + sectionsId + ' input[type="radio"]:checked').val();

      if ( typeof checkedId === 'undefined' ) {
        $('#' + sectionsId + ' input[type="radio"]:first').attr('checked', 'checked');
        checkedId = $('#' + sectionsId + ' input[type="radio"]:first').val();
      }

      $this.buttonset();

      $('div[id^="' + sectionsId + '_"]').each(function() {
        if ( $(this).attr('id') != sectionsId + '_' + checkedId ) {
          $(this).hide(); 
        }
      });

      $('#' + sectionsId + ' input[type="radio"]').each(function() {
        $(this).click(function() {
          if ( $(this).val() != checkedId ) {
            $('#' + sectionsId + '_' + checkedId).hide();
            $('#' + sectionsId + '_' + $(this).val()).show();
            checkedId = $(this).val();
          }
        });
      });
    });
  };
})( jQuery );
