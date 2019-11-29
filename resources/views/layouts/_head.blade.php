<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="theme-color" content="#37528c" />
<link rel="shortcut icon" type="image/png" href="{{ url('favicon.png') }}">

<title>Lootly - @yield('title')</title>

<!-- CSRF Token -->
<meta name="csrf-token" content="{{ csrf_token() }}">

<!-- CSS -->
<link href="{{ url('css/app.css') }}" rel="stylesheet">

@yield('header')

<!-- Global Spark Object -->
<script>
	<?php $sparkObject = Spark::scriptVariables(); ?>
    window.Spark = <?php echo json_encode(array_merge(
         $sparkObject, []
    )); ?>;
</script>

<!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-TQLL3SK');</script>
<!-- End Google Tag Manager -->

<!-- Hotjar Tracking Code for https://lootly.io -->
<script>
    (function(h,o,t,j,a,r){
        h.hj=h.hj||function(){(h.hj.q=h.hj.q||[]).push(arguments)};
        h._hjSettings={hjid:1144129,hjsv:6};
        a=o.getElementsByTagName('head')[0];
        r=o.createElement('script');r.async=1;
        r.src=t+h._hjSettings.hjid+j+h._hjSettings.hjsv;
        a.appendChild(r);
    })(window,document,'https://static.hotjar.com/c/hotjar-','.js?sv=');
</script>

<script>
  window.intercomSettings = {
    app_id: "k3oh0xnb",
    name: "{{ $sparkObject['state']['user']['name']}}",
    email: "{{ $sparkObject['state']['user']['email'] }}",
    plan: "{{ count($sparkObject['state']['user']['subscriptions']) > 0 ? $sparkObject['state']['user']['subscriptions'][count($sparkObject['state']['user']['subscriptions'])-1]['plan_id'] : '1' }}",
    shopify_installed: "{{ $sparkObject['state']['currentTeam']['shopify_installed'] == '0' ? 'false' : 'true' }}",
    created_at: "{{ $sparkObject['state']['user']['created_at'] }}"
  };
  </script>
<script>(function(){var w=window;var ic=w.Intercom;if(typeof ic==="function"){ic('reattach_activator');ic('update',intercomSettings);}else{var d=document;var i=function(){i.c(arguments)};i.q=[];i.c=function(args){i.q.push(args)};w.Intercom=i;function l(){var s=d.createElement('script');s.type='text/javascript';s.async=true;s.src='https://widget.intercom.io/widget/k3oh0xnb';var x=d.getElementsByTagName('script')[0];x.parentNode.insertBefore(s,x);}if(w.attachEvent){w.attachEvent('onload',l);}else{w.addEventListener('load',l,false);}}})()</script>

<!-- Global site tag (gtag.js) - Google Analytics 
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-120019995-1"></script>
<script>
 window.dataLayer = window.dataLayer || [];
 function gtag(){dataLayer.push(arguments);}
 gtag('js', new Date());

 gtag('config', 'UA-120019995-1');
</script>
-->

<script id="profitwell-js" data-pw-auth="037b1356225540355454f74cff20dcfa">
(function(i,s,o,g,r,a,m){i[o]=i[o]||function(){(i[o].q=i[o].q||[]).push(arguments)}; a=s.createElement(g);m=s.getElementsByTagName(g)[0];a.async=1;a.src=r+'?auth='+s.getElementById(o+'-js').getAttribute('data-pw-auth');m.parentNode.insertBefore(a,m);})(window,document,'profitwell','script','https://public.profitwell.com/js/profitwell.js');
profitwell('start', { 'user_email': "{{$sparkObject['state']['user']['email']}}" });
</script>
