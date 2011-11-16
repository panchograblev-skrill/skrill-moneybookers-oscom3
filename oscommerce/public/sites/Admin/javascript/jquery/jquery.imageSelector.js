/**
 * Image Selector jQuery Plugin
 *
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license GNU General Public License 2; http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 *
 * Based on Image JSON Pagination v1.0
 * Copyright (c) 2009 Jaydson Gomes; http://code.google.com/p/json-image-pagination/
 */

(function( $ ){
  $.fn.imageSelector = function(settings) {
    var defaults = {
      multipleSel: false,
      images: null
    }

    var core = {
      targetElementId: '',
      elementsToAppend: null,
      imagesJSON: null,
      totalPages: 0,
      currentPage: 1,
      size: null,
      placeholderImage: $('#' + settings.selector).css('backgroundImage'),

      CreateImages: function() {
        var totalImages = core.elementsToAppend.length;

        for ( var i=0; i<totalImages; i++ ) {
          if ( core.imagesJSON.images[i] ) {
            var elem = document.createElement('IMG');
            elem.src = settings.imagePath + '/' + core.imagesJSON.images[i];
            elem.imageName = core.imagesJSON.images[i];
            elem.alt = elem.title = elem.imageName;

            $(elem).click(function() {
              if ( $('#' + settings.selector + ' img:first').attr('src') == this.src ) {
                $('#' + settings.selector + ' img:first').remove();

                if ( settings.images != null ) {
                  $('#' + settings.selector).css('backgroundImage', 'none');
                  $('#' + settings.selector).html('<img src="' + settings.images + '" alt="' + settings.images + '" title="' + settings.images + '" onclick="window.open(this.src);" />');
                } else {
                  $('#' + settings.selector).css('backgroundImage', core.placeholderImage);
                }
              } else {
                $('#' + settings.selector).css('backgroundImage', 'none');
                $('#' + settings.selector).html('<img src="' + this.src + '" alt="' + this.alt + '" title="' + this.title + '" onclick="window.open(this.src);" /><input type="hidden" name="' + settings.selector + 'Selected" value="' + this.alt + '" />');
              }
            });

            $(core.elementsToAppend[i]).append(elem);
          }
        }

        core.size = core.elementsToAppend.length;

        core.CreatePagination();
      },

      Pager: function() {
        var newImages = new Array();

        for ( var i=core.size-core.elementsToAppend.length; i<core.size; i++ ) {
          newImages.push(core.imagesJSON.images[i]);
        }

        for ( var x=0; x<newImages.length; x++ ) {
          $(core.elementsToAppend[x]).children().hide();

          if ( newImages[x] ) {
            $(core.elementsToAppend[x]).children().attr('imageName', newImages[x]);
            $(core.elementsToAppend[x]).children().attr('src', settings.imagePath + '/' + newImages[x]);
            $(core.elementsToAppend[x]).children().attr('alt', newImages[x]);
            $(core.elementsToAppend[x]).children().attr('title', newImages[x]);

            $(core.elementsToAppend[x]).children().show();
          }
        }

        delete newImages;

        $('#igReload').remove();
        $('#igPBack').remove();
        $('#igPForward').remove();

        core.CreatePagination();
      },

      CreatePagination: function() {
        var igReload = '<div id="igReload" style="float: left; padding-top: 12px;">' + batchIconNavigationReload + '</div>';

        $('#' + core.targetElementId).before(igReload);

        var igPBack = '<div id="igPBack" style="float: left; padding-top: 12px;">';

        if ( core.currentPage > 1 ) {
          igPBack += batchIconNavigationBack;
        } else {
          igPBack += batchIconNavigationBackGrey;
        }

        igPBack += '</div>';

        var igPForward = '<div id="igPForward" style="float: left; padding-top: 12px; padding-left: 7px;">';

        if ( core.currentPage >= core.totalPages) {
          igPForward += batchIconNavigationForwardGrey;
        } else {
          igPForward += batchIconNavigationForward;
        }

        igPForward += '</div>';

        $('#' + core.targetElementId).after(igPBack + igPForward);

        $('#igReload img').click(function() {
          core.Init();
        });

        $('#igPBack img').click(function() {
          core.PreviousPage();
        });

        $('#igPForward img').click(function() {
          core.NextPage();
        });
      },

      NextPage: function() {
        if ( core.currentPage >= core.totalPages) {
          return false;
        };

        core.currentPage++;
        core.size = core.size + core.elementsToAppend.length;
        core.Pager();
      },

      PreviousPage: function() {
        if ( core.currentPage <= 1 ) {
          return false;
        };

        core.currentPage--;
        core.size = core.size - core.elementsToAppend.length;
        core.Pager();
      },

      Init: function() {
        $.getJSON(settings.json, function(jsonObject) {
          core.imagesJSON = jsonObject;

          $('#igReload, #igPBack, #' + core.targetElementId + ' li, #igPForward').remove();

          for (var i=0; i<settings.show; i++) {
            $('#' + core.targetElementId).append('<li></li>');
          }

          core.elementsToAppend = $('#' + core.targetElementId + ' li');

          core.currentPage = 1;

          core.totalPages = Math.ceil(parseInt(core.imagesJSON.images.length) / (parseInt(core.elementsToAppend.length)));

          core.CreateImages();
        });
      }
    }

    settings = $.extend(defaults, settings);

    return this.each(function() {
      core.targetElementId = $(this).attr('id');

      if ( settings.images != null ) {
        $('#' + settings.selector).css('backgroundImage', 'none');
        $('#' + settings.selector).html('<img src="' + settings.images + '" alt="' + settings.images + '" title="' + settings.images + '" style="max-width: 90px; max-height: 90px; vertical-align: middle;" onclick="window.open(this.src);" />');
      }
        
      core.Init();
    });
  }
})( jQuery );
