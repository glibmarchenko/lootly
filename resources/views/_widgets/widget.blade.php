<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Lootly - widget</title>

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
	<link rel="stylesheet" type="text/css" href="{{ mix('css/widget.css') }}">

</head>
<body>
	<div id="app"
		 :class="{'mobile-view': viewportWidth < 600, 'hide-lootly-footer': globalWidgetSettings.hideLootlyLogo}"
		 :style="{ fontFamily: globalWidgetSettings.fontFamily }"
		 v-cloak>

		<div class="lootly-widget" :style="{ color: globalWidgetSettings.primaryColor }" v-if="isAuth">

			<div class="widget-tab-contents" :class="{ 'widget-closed': !widgetOpened }" :display-on="widgetSettings.tab.display_on">
				<div class="loading-block" v-if="$root.loading">
					<div class="loading" :style="{ borderColor: globalWidgetSettings.primaryColor }"></div>
				</div>

				<router-view v-else></router-view>
			</div>

			<span v-if="widgetSettings.tab.enable_reminders">
				<tab-reminder v-if="isLogin && widgetSettings.tab.display_on != 'none'" v-show="!widgetOpened"></tab-reminder>
			</span>

			<div class="widget-tab-button"
				 :class="{'btn-left': widgetSettings.tab.position == 'left'}"
				 v-show="widgetSettings.tab.display_on != 'none'">

				<button id="lootly-widget-button"
						:class="[{'widget-opened': widgetOpened}, widgetSettings.tab.desktop_layout]"
						:style="{ background: widgetSettings.tab.bg_color, color: widgetSettings.tab.font_color }"
						@click="toggleWidget">
					<span v-if="!widgetOpened">
						<img v-if="widgetSettings.tab.custom_icon == 1" :src="widgetSettings.tab.icon">
                        <i v-else :class="widgetSettings.tab.icon"></i>
						<span class="widget-btn-text" v-text="widgetSettings.tab.text || 'Rewards'"></span>
					</span>
					<span class="widget-close-btn" v-else><svg version="1.1" xmlns="http://www.w3.org/2000/svg" width="20" viewBox="0 0 640 640"><path :fill="widgetSettings.tab.font_color" d="M320 274.752l-226.272-226.272-45.248 45.248 226.272 226.272-226.272 226.272 45.248 45.248 226.272-226.272 226.272 226.272 45.248-45.248-226.272-226.272 226.272-226.272-45.248-45.248-226.272 226.272z"></path></svg></span>
				</button>

			</div>
		</div>

		<div v-else>
			{{--<div class="alert alert-danger" style="margin: 10px; max-width: 400px">
				Error: Invalid Info!
			</div>--}}
		</div>
	</div>


	<script type="text/javascript" src="{{ mix('js/widget.js') }}"></script>
</body>
</html>
