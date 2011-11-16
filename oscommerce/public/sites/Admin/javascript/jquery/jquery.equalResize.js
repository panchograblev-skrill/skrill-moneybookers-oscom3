/**
 * Equal Resize jQuery Plugin
 *
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

(function( $ ){
  $.fn.equalResize = function() {
    return this.each(function() {
      var widest = 0;
      var highest = 0;

      $(this).children().each(function() {
        widest = Math.max(widest, $(this).width());
        highest = Math.max(highest, $(this).height());
      }).width(widest).height(highest);
    });
  };
})( jQuery );
