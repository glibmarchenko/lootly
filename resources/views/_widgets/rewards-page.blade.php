<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Lootly - Rewards Page</title>

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
	<link rel="stylesheet" type="text/css" href="{{ mix('css/rewards-page.css') }}">

	<!-- Fonts -->
	@if($data['branding_font'] == 'roboto')
		<link href="https://fonts.googleapis.com/css?family=Roboto:400,500,700" rel="stylesheet">
	@else
		<link href="https://fonts.googleapis.com/css?family=Lato:300,400,700" rel="stylesheet">
	@endif

</head>
<body>
	@if($data)
	<div id="rewards-page" class="rewards-container" style="font-family: {{ $data['branding_font'] }}" v-cloak>
		<div v-if="html_mode == 0">
			<rewards-page-header :data="form.header"></rewards-page-header>

			<rewards-page-how-it-works :data="form.howItWorks"></rewards-page-how-it-works>

			<rewards-page-earning :data="form.earning" :title="replacePointsTag(form.earning.title)"></rewards-page-earning>

			<rewards-page-vip :data="form.vip" :title="replacePointsTag(form.vip.title)"></rewards-page-vip>

			<rewards-page-spending :data="form.spending" :title="replacePointsTag(form.spending.title)"></rewards-page-spending>

			<rewards-page-referral :data="form.referral"></rewards-page-referral>

			<rewards-page-faq :data="form.faq"></rewards-page-faq>
		</div>
		<div v-else>
			{!! stripslashes($data['reward_settings']['html']) !!}
		</div>

		<footer v-if="form.branding">
			<div class="lootly-copyright">
				<span>Powered By</span>
				<a href="{{ config('app.website-url') }}">
			   	<img src="{{ config('app.logo-inner') }}" style="width: 100px;">
				</a>
			</div>
		</footer>
	</div>
	@else 
		<div class="alert alert-danger" style="margin: 10px; max-width: 400px">
			Error: Invalid Info!
		</div>
	@endif
	<script type="text/javascript">
		var data = {!! json_encode($data) !!};
	</script>
	<script type="text/javascript" src="{{ mix('js/rewards-page.js') }}"></script>
</body>
</html>