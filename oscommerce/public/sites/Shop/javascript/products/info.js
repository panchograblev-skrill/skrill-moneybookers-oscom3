/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

function refreshVariants() {
  var price = null;
  var availability = null;
  var model = null;

  for (c in combos) {
    id = null;

    variants_loop:
    for (group_id in combos[c]['values']) {
      for (value_id in combos[c]['values'][group_id]) {
        if (document.getElementById('variants_' + group_id) != undefined) {
          if (document.getElementById('variants_' + group_id).type == 'select-one') {
            if (value_id == document.getElementById('variants_' + group_id).value) {
              id = c;
            } else {
              id = null;

              break variants_loop;
            }
          }
        } else if (document.getElementById('variants_' + group_id + '_1') != undefined) {
          j = 0;

          while (true) {
            j++;

            if (document.getElementById('variants_' + group_id + '_' + j).type == 'radio') {
              if (document.getElementById('variants_' + group_id + '_' + j).checked) {
                if (value_id == document.getElementById('variants_' + group_id + '_' + j).value) {
                  id = c;
                } else {
                  id = null;

                  break variants_loop;
                }
              }
            }

            if (document.getElementById('variants_' + group_id + '_' + (j+1)) == undefined) {
              break;
            }
          }
        }
      }
    }

    if (id != null) {
      break;
    }
  }

  if (id != null) {
    price = combos[id]['price'];
    availability = productInfoAvailability;
    model = combos[id]['model'];
  } else {
    price = originalPrice;
    availability = productInfoNotAvailable;
    model = '';
  }

  document.getElementById('productInfoPrice').innerHTML = price;
  if(document.getElementById('productInfoAvailability') != undefined) {
    document.getElementById('productInfoAvailability').innerHTML = availability;
  }
  document.getElementById('productInfoModel').innerHTML = model;
}
