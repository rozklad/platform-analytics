{{ Asset::queue('ammap', 'sanatorium/analytics::ammap/ammap.css') }}
{{ Asset::queue('ammap', 'sanatorium/analytics::ammap/ammap.js') }}
{{ Asset::queue('ammap-'.$map_mode, 'sanatorium/analytics::ammap/maps/js/'.$map_mode.'.js') }}

@section('styles')
@parent
<style type="text/css">
.regional-analytics {
	position: relative;
}
.regional-analytics h2 {
	position: absolute;
	color: #fff;
	right: 20px;
	top: 20px;
}
.amcharts-chart-div > a {
	color: #ffdd50!important;
}
.bg-yellow {
	background-color: #ffdd50;
}
</style>
@stop

@section('scripts')
@parent
<script>

	var map;

	AmCharts.ready(function() {
		map = new AmCharts.AmMap();

		map.balloon.color = "#000000";

		var dataProvider = {
			mapVar: AmCharts.maps.{{ $map_mode }},
			getAreasFromMap: true,
			areas: [
				@foreach($results as $id => $item)
			    {
			        id: "{{ $id }}",
			        value: {{ $item['ga:sessions'] }},
			        title: '{{ trans('sanatorium/analytics::widgets.visitors') }}: {{ $item['ga:sessions'] }} <br>{{ trans('sanatorium/analytics::widgets.pageviews') }}: {{ $item['ga:pageviews'] }}'
			    },
		       	@endforeach
			]
		};

		map.dataProvider = dataProvider;

		map.areasSettings = {
			autoZoom: false,
			selectedColor: "#CC0000",
			selectedOutlineColor: '',
			color: "#ffdd50",
			colorSolid: '#ffffff',
			rollOverColor: '#ffffff',
			rollOverOutlineColor: "#ffd300",
		};

		map.colorSteps = {{ $colorSteps }};
		map.zoomControl = {
			zoomControlEnabled: false,
			homeIconColor: '#ffffff',
			buttonSize: 0,
			buttonBorderThickness: 0
		};
		map.dragMap = false;
		map.creditsPosition = 'top-right';
		map.fitMapToContainer = false;

		map.write("regional-analytics");

	});

</script>
@stop

<div class="regional-analytics">
	<h2>{{ trans('sanatorium/analytics::widgets.regional.title') }}</h2>
	<div id="regional-analytics" class="bg-yellow" style="width: 100%; min-height: 450px;"></div>
</div>