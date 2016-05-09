{{-- Chart lib --}}
{{ Asset::queue('nvd3', 'nvd3/nv.d3.min.css', 'style') }}
{{ Asset::queue('d3', 'nvd3/lib/d3.v3.js', 'jquery') }}
{{ Asset::queue('nvd3', 'nvd3/nv.d3.min.js', 'jquery') }}
{{ Asset::queue('utils', 'nvd3/src/utils.js', 'jquery') }}
{{ Asset::queue('tooltip', 'nvd3/src/tooltip.js', 'jquery') }}
{{ Asset::queue('interactiveLayer', 'nvd3/src/interactiveLayer.js', 'jquery') }}
{{ Asset::queue('axis', 'nvd3/src/models/axis.js', 'jquery') }}
{{ Asset::queue('line', 'nvd3/src/models/line.js', 'jquery') }}
{{ Asset::queue('lineWithFocusChart', 'nvd3/src/models/lineWithFocusChart.js', 'jquery') }}

@section('scripts')
@parent
<script type="text/javascript">

	// Cache retrieved data
	window.visitors_graph_data = {};
	window.visitors_graph_loaded = false;
	window.visitors_graph_current = null;

	var data = getVisitorsAndPageviewsData({{ $days }});

	// Create chart
	var chart = nv.models.lineChart()
		@if ( config('sanatorium-analytics.visitors_and_pageviews.curved_line') )
		.interpolate('{{ config('sanatorium-analytics.visitors_and_pageviews.curved_line') }}')
		@endif
		.x(function(d) {
			return d[0]
		})
		.y(function(d) {
			return d[1]
		})
		.color([
			$.Pages.getColor('success'),
			$.Pages.getColor('danger'),
			$.Pages.getColor('primary'),
			$.Pages.getColor('complete'),
		])
		.transitionDuration(350)
		.showLegend(false)
		.showYAxis({{ config('sanatorium-analytics.visitors_and_pageviews.show_y_axis') ? 'true' : 'false' }})
		.showXAxis({{ config('sanatorium-analytics.visitors_and_pageviews.show_x_axis') ? 'true' : 'false' }})
		.margin({
			left: {{ config('sanatorium-analytics.visitors_and_pageviews.show_y_axis') ? 35 : 0 }},
			right: {{ config('sanatorium-analytics.visitors_and_pageviews.show_y_axis') ? 35 : 0 }},
			bottom: {{ config('sanatorium-analytics.visitors_and_pageviews.show_x_axis') ? 35 : 10 }},
			top: 10,
		})
		.useInteractiveGuideline(true);

	// Format of values on X axis
	chart.xAxis
			.tickPadding(20)
			.tickFormat(function(d) {
				return d3.time.format('%e %B')(new Date(d))
			});

	// Format of values on Y axis
	chart.yAxis
			.tickFormat(d3.format('d'));

	function loadGraph() {
		var data = window.visitors_graph_current;

		console.log(data);

		// update max and min on Y axis
		chart.forceY([data.min,data.max]);

		d3.select('.nvd3-line svg')
				.datum(data.lines)
				.transition()
				.duration(1500)
				.call(chart);

		nv.utils.windowResize(chart.update);

		$('#visitors-and-pageviews').data('chart', chart);

		return chart;

	}

	function getVisitorsAndPageviewsData(days) {

		$.ajax({
			type: 'GET',
			url: '{{ route('sanatorium.analytics.data.visitors.and.pageviews') }}',
			data: {days: days}
		}).success(function(response){

			window.visitors_graph_data[days] = response;
			window.visitors_graph_current = response;

			if ( window.visitors_graph_loaded === false ) {
				nv.addGraph(loadGraph);
			} else {
				loadGraph();
			}
		});

	}


	$(function(){

		$('[data-visitors]').click(function(){

			var days = $(this).data('visitors');

			if ( typeof window.visitors_graph_data[days] === 'undefined' ) {
				window.visitors_graph_data[days] = getVisitorsAndPageviewsData(days);
			} else {
				window.visitors_graph_current = window.visitors_graph_data[days];
				loadGraph();
			}
		});

	});


</script>
@stop

@section('styles')
@parent
<style type="text/css">
	.graph-overlay,
	.graph-info-box {
		position: absolute;
		top: 0;
		left: 0;
		width: 100%;
		z-index: 1;
	}
	.graph-info-box {
		width: 20%;
	}
	.graph-info-box:nth-child(1) {
		left: 20%;
	}
</style>
@stop

<div class="graph-overlay">
	<div class="graph-info-box">
		<span class="difference">{{ ($extras['visitors_this_period'] > $extras['visitors_before_period']) ? '+' : '-' }} {{ $extras['visitors_difference'] }}</span>
		<span class="this-period">{{ $extras['visitors_this_period'] }}</span>
		<span class="before-period">{{ $extras['visitors_before_period'] }}</span>
	</div>
	<div class="graph-info-box">
		<span class="difference">{{ ($extras['pageviews_this_period'] > $extras['pageviews_before_period']) ? '+' : '-' }}  {{ $extras['pageviews_difference'] }}</span>
		<span class="this-period">{{ $extras['pageviews_this_period'] }}</span>
		<span class="before-period">{{ $extras['pageviews_before_period'] }}</span>
	</div>
</div>

<a href="#" data-visitors="7">
	Last week
</a>

<a href="#" data-visitors="30" class="active">
	Last month
</a>

<a href="#" data-visitors="90">
	Last quarter
</a>

<a href="#" data-visitors="365">
	Last year
</a>

<!-- Widget: Visitors and pageviews -->
<div class="row">
	<div class="col-md-12">
		<div class="nvd3-line line-chart text-center"
			 id="visitors-and-pageviews"
			 data-y-grid="{{ config('sanatorium-analytics.visitors_and_pageviews.show_y_grid') ? 'true' : 'false' }}"
			 data-x-grid="{{ config('sanatorium-analytics.visitors_and_pageviews.show_x_grid') ? 'true' : 'false' }}"
			 style="height:50vh">
			<svg></svg>
		</div>
	</div>
</div>

{{-- trans('sanatorium/analytics::widgets.visitors_and_pageviews.subtitle') --}}
{{-- trans('sanatorium/analytics::widgets.visitors_and_pageviews.subtitle_bold') --}}

{{-- trans('sanatorium/analytics::widgets.visitors') --}}
{{-- trans('sanatorium/analytics::widgets.visitors_short_desc') --}}
{{-- trans('sanatorium/analytics::widgets.visitors_desc') --}}

{{-- trans('sanatorium/analytics::widgets.pageviews') --}}
{{-- trans('sanatorium/analytics::widgets.pageviews_short_desc') --}}
{{-- trans('sanatorium/analytics::widgets.pageviews_desc') --}}