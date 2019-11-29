function init() {

  let widget = document.querySelector('#lootly-widget');
  if (widget) {
    let iframe_url = (widget.getAttribute('data-provider') || 'https://lootly.io').replace(new RegExp('\/$'), '') + '/widget'
    let api_key = widget.getAttribute('data-api-key') || ''
    let query_params = [
      { key: '_cid', value: widget.getAttribute('data-customer-id') || '' },
      { key: '_cs', value: widget.getAttribute('data-customer-signature') || '' },
      { key: '_shd', value: widget.getAttribute('data-shop-domain') || '' },
      { key: '_shs', value: widget.getAttribute('data-shop-id') || '' },
    ]
    iframe_url += '?' + query_params.filter((item) => {
      return item.value.trim() !== ''
    }).map((item) => {
      return encodeURIComponent(item.key) + '=' + encodeURIComponent(item.value)
    }).join('&')

    let iframe = document.createElement('iframe')
    iframe.setAttribute('src', iframe_url)
    iframe.setAttribute('id', 'lootly_iframe')
    widget.classList.add('widget-closed')
    widget.appendChild(iframe)

    // Listen to message from iframe
    bindEvent(window, 'message', function(e) {
      let type = e.data
      let data = {}
      try {
        let eventBody = JSON.parse(e.data)
        type = eventBody.action
        data = eventBody.data
      } catch (error) {}

      switch (type) {
        case 'widget-init':
          break
        case 'widget-ready':
          let widgetPosition = data.position == 'left' ? 'widget-left' : 'widget-right';
          widget.classList.add(widgetPosition);

          widget.setAttribute('display-on', data.display_on);

          let styles = document.createElement('style')
          styles.innerHTML = data.styles || ''
          widget.appendChild(styles)

          widget.classList.add('widget-ready');

          afterWidgetInit();
          break
        case 'account-login':
          window.location.href = (typeof Lootly !== 'undefined' && Lootly.config && Lootly.config.loginUrl) ? Lootly.config.loginUrl : '/account/login'
          break
        case 'account-register':
          window.location.href = (typeof Lootly !== 'undefined' && Lootly.config && Lootly.config.registerUrl) ? Lootly.config.registerUrl : '/account/register'
          break
        case 'close-widget':
          document.body.classList.remove('lootly-widget-open');
          widget.classList.add('widget-closed')
          break
        case 'open-widget':
          document.body.classList.add('lootly-widget-open');
          widget.classList.remove('widget-closed')
          break
        case 'add-current-page':
          widget.setAttribute('data-current-page', data)
          break
        case 'toggle-rewards-tab':
          widget.classList.toggle('widget-has-rewards')
          break
        case 'reward-redeemed-tab':
          widget.classList.add('widget-reward-redeemed')
          break
      }
    })
  }
}

function afterWidgetInit() {

  fillUpIframe();
  window.addEventListener('resize', sendWindowWidth);
  sendWindowWidth();
}

function fillUpIframe() {

  let iframe = document.querySelector('#lootly-widget #lootly_iframe')
  if (iframe) {
    let widget = document.querySelector('#lootly-widget');
    pointsForPurchase(
      widget.getAttribute( 'data-customer-id' ) || '',
      widget.getAttribute( 'data-customer-signature' ) || '',
      widget.getAttribute( 'data-shop-domain' ) || '',
      widget.getAttribute( 'data-shop-id' ) || '',
      );
    let sendMessage = function(msg) {
      iframe.contentWindow.postMessage(msg, '*')
    }

    let hasRef = false
    let ref = getQueryVariable('lootlyref')
    if (ref) {
      hasRef = true
      sendMessage(JSON.stringify({ action: 'receive-reward', data: { referral_slug: ref } }))
    } else {
      ref = getQueryVariable('loref')
      if (ref) {
        hasRef = true
        sendMessage(JSON.stringify({ action: 'receive-reward', data: { referral_slug: ref } }))
      }
    }

    if (!hasRef) {
      let lootlyWidgetPage = getQueryVariable('lourl')
      if (lootlyWidgetPage) {
        switch (lootlyWidgetPage) {
          case 'earn-points':
            sendMessage(JSON.stringify({ action: 'earn-points' }))
            break;
          case 'get-coupon':
            sendMessage(JSON.stringify({ action: 'get-coupon' }))
            break;
        }
      }
    }
    window.openLootlyWidget = function (e) {
      e.preventDefault()
      sendMessage(JSON.stringify({ action: 'toggle-widget' }))
    }
  }
}

function pointsForPurchase( customer_id, customer_signature, shop_domain, shop_id ) {

  let inline_points_element = document.getElementById('lootly-inline-points' );

  //If product page and lootly points div is inserted into code
  if( inline_points_element ) {

    //Customer is not logged in
    if( customer_id === '' ) {

      makeCallAndGetPoints( customer_id, customer_signature, shop_domain, shop_id, inline_points_element, "/api/widget/actions" );
    }

    //Customer is logged in
    else {

      makeCallAndGetPoints( customer_id, customer_signature, shop_domain, shop_id, inline_points_element, "/api/widget/customer/actions" );
    }
  }
}

function makeCallAndGetPoints( customer_id, customer_signature, shop_domain, shop_id, div, path ){

  let widget = document.querySelector('#lootly-widget');
  let iframe_url = (widget.getAttribute('data-provider') || 'https://lootly.io').replace(new RegExp('\/$'), '');

  var xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function() {
    if ( this.readyState == 4 && this.status == 200 ) {

      getPointsPerDollar( this.responseText, div );

    }
  };
  xhttp.open("POST",iframe_url + path, true);
  xhttp.setRequestHeader("Content-type", "application/json;charset=UTF-8");
  xhttp.send( JSON.stringify({"shop": { "domain" : shop_domain, "signature" : shop_id }, "customer" : { "id" : customer_id, "signature" : customer_signature } } ) );
}

function getPointsPerDollar( response, div ){
  let result = JSON.parse( response );
  if ( result.data ) {
    let actions = result.data;
    if (actions.length) {

      let makePurchaseAction = actions.filter( function( action ) {
        return action.action.url === 'make-a-purchase';
      })[0];

      let pointsPerDollar = makePurchaseAction.point_value;

      let price = div.getAttribute( 'data-product-price' ) / 100;

      let pointsToEarn = Math.floor( pointsPerDollar * price );

      let text = 'Members will earn ' + pointsToEarn + ' points with this purchase';
      div.innerHTML = text;
    }
  }
}

function sendWindowWidth(){
  document.querySelector('#lootly-widget iframe').contentWindow.postMessage(JSON.stringify({
    action: 'viewport-width',
    data: {
      width: window.innerWidth
    }
  }), '*')
}

function bindEvent(element, eventName, eventHandler) {
  if (element.addEventListener) {
    element.addEventListener(eventName, eventHandler, false)
  } else if (element.attachEvent) {
    element.attachEvent('on' + eventName, eventHandler)
  }
}

function openLootlyWidget(e) {
  e.preventDefault()
}

function getQueryVariable(variable) {
  let query = window.location.search.substring(1)
  let vars = query.split('&')
  for (let i = 0; i < vars.length; i++) {
    let pair = vars[i].split('=')
    if (pair[0] == variable) { return pair[1] }
  }
  return (false)
}

export {init}
