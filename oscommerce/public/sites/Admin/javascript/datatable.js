/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

var batchCurrentPage = 1;
var batchPages = 1;

var osC_DataTable = function() {
  var initialized = false;

  if ( moduleParams[dataTablePageSetName] == null ) {
    moduleParams[dataTablePageSetName] = 1;
  }

  batchCurrentPage = moduleParams[dataTablePageSetName];
  var batchTotalRecords = 0;
  var batchFrom = 0;
  var batchTo = 0;

  return {
    initialize: function() {
      $('#batchTotalPages').html(batchTotalPagesText.sprintf(0, 0, 0));
      $('#batchPageLinks').html(batchIconNavigationBackGrey + '&nbsp;' + batchIconNavigationForwardGrey);
      $('#batchPullDownMenu').html(batchCurrentPageset.sprintf(1, 1));

      $('#liveSearchField').val(moduleParams['search']);

      $('#liveSearchField').keyup( function(e) {
        var t = this;

        if ( e.keyCode == 13 ) {
          osC_DataTable.load(1, t.value);
        } else if ( this.value != this.lastValue ) {
          if ( this.timer ) {
            clearTimeout(this.timer);
          }

          this.timer = setTimeout( function() {
            osC_DataTable.load(1, t.value);
          }, 800);

          this.lastValue = this.value;
        }
      });

      $('#liveSearchForm').submit(function() {
        $('#liveSearchField').trigger('keyup');
        return false;
      });

      initialized = true;
    },

    reset: function() {
      $('#liveSearchField').val('').blur();
      this.load(1, '');
    },

    load: function(page, search) {
      if ( initialized == false ) {
        this.initialize();
      }

      if (page == undefined) {
        page = moduleParams[dataTablePageSetName];
      }

      if (search == undefined) {
        search = moduleParams['search'];
      }

      page = parseInt(page);
      search = String(search);

      moduleParams[dataTablePageSetName] = page;
      batchCurrentPage = page;
      moduleParams['search'] = search;

      $('#batchTotalPages').html(batchIconProgress + '&nbsp;Loading&hellip;');

      $.getJSON(dataTableDataURL + '&' + $.param(moduleParams),
        function (response) {
          if ( response.rpcStatus == 1 ) {
            $('#' + dataTableName + ' tbody tr').remove();

            if ( response.entries.length > batchSize ) {
              batchSize = response.entries.length;
            }

            batchTotalRecords = response.total;
            batchFrom = batchSize * (batchCurrentPage - 1);
            batchTo = batchSize * batchCurrentPage;
            batchPages = Math.ceil(batchTotalRecords / batchSize);

            if ( batchTo > batchTotalRecords ) {
              batchTo = batchTotalRecords;
            }

            if ( batchTo == 0 ) {
              batchFrom = 0;
            } else {
              batchFrom++;
            }

            feedDataTable(response);

// Add alternating row background colours
            $('#' + dataTableName + ' tbody tr:odd').addClass('alt');

// Check if table listing is jQuery.sortable()
            if ( $('#' + dataTableName + ' tbody:data(sortable)').length > 0 ) {
// Attach a helper function to properly resize the dragged item
              $('#' + dataTableName + ' tbody').sortable({
                helper: function(e, tr) {
                  var $originals = tr.children();
                  var $helper = tr.clone();

                  $helper.children().each(function(index) {
// Set helper cell sizes to match the original sizes
                    $(this).width($originals.eq(index).width());
                  });

                  return $helper;
                }
              });

// Automatically refresh alternating row background colours on change
              $('#' + dataTableName + ' tbody').bind('sortupdate', function(event, ui) {
                $('#' + dataTableName + ' tbody tr').removeClass('alt');
                $('#' + dataTableName + ' tbody tr:odd').addClass('alt');
              });
            }

            $('#batchTotalPages').html(batchTotalPagesText.sprintf(batchFrom, batchTo, batchTotalRecords));

            if ( batchPages > 1 ) {
              var form = '<form action="#" onsubmit="return false;">';

              var select = '<select name="page" onchange="osC_DataTable.load(this.form.page.options[this.form.page.selectedIndex].value);">';

              for ( var i = 1; i <= batchPages; i++ ) {
                var option = '<option value="' + i + '"';

                if ( i == batchCurrentPage ) {
                  option += ' selected="selected"';
                }

                option += '>' + i + '</option>';

                select += option;
              }

              select += '</select>';

              form += batchCurrentPageset.sprintf(select, batchPages) + '</form>';

              $('#batchPullDownMenu').html(form);
            } else {
              $('#batchPullDownMenu').html(batchCurrentPageset.sprintf(1, 1));
            }

            var batchPageLinks;

            if ( batchCurrentPage > 1 ) {
              batchPageLinks = '<a id="batchPrevLink" href="#" onclick="osC_DataTable.load(' + (batchCurrentPage - 1) + '); return false;">' + batchIconNavigationBack + '</a>';
            } else {
              batchPageLinks = batchIconNavigationBackGrey;
            }

            batchPageLinks += '&nbsp;';

            if ( batchCurrentPage < batchPages ) {
              batchPageLinks += '<a id="batchNextLink" href="#" onclick="osC_DataTable.load(' + (batchCurrentPage + 1) + '); return false;">' + batchIconNavigationForward + '</a>';
            } else {
              batchPageLinks += batchIconNavigationForwardGrey;
            }

            $('#batchPageLinks').html(batchPageLinks);

            $.cookie(moduleParamsCookieName, $.toJSON(moduleParams));
          } else {
            var errorMsg = null;

            switch (response.rpcStatus) {
              case -10:
                document.location.href = pageURL;
                break;

              case -20:
                errorMsg = 'Error: No module defined on RPC call.';
                break;

              case -50:
                errorMsg = 'Error: No access allowed to RPC call.';
                break;

              case -60:
                errorMsg = 'Error: Class not found on RPC call.';
                break;

              case -70:
                errorMsg = 'Error: No action defined on RPC call.';
                break;

              case -71:
                errorMsg = 'Error: Action not found on RPC call.';
                break;

              default:
                errorMsg = 'A general error has occurred with an RPC call. Please refresh the page and try again.';
                break;
            }

            if ( errorMsg != null ) {
              $('#batchTotalPages').html('An error has occurred with an RPC call. Please refresh the page and try again.');

              errorMsg += '\n\n' + dataTableDataURL + '&' + $.param(moduleParams);
              alert(errorMsg);
            }
          }
        }
      );
    }
  };
};

$(document.documentElement).keyup(function (event) {
  if ( event.altKey && (event.keyCode == 37) ) { // alt + left arrow key; go to previous page set
    if ( batchCurrentPage > 1 ) {
      $('#batchPrevLink').click();
    }
  } else if ( event.altKey && (event.keyCode == 39) ) { // alt + right arrow key; go to next page set
    if ( batchCurrentPage < batchPages ) {
      $('#batchNextLink').click();
    }
  }
});
