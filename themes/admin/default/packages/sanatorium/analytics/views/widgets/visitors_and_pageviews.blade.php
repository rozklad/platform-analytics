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

	$(function(){

		//NVD3 Charts
		d3.json('{{ route('sanatorium.analytics.data.visitors.and.pageviews', $days) }}', function(data) {

	        // line chart
	        (function() {
	        	nv.addGraph(function() {
	        		var chart = nv.models.lineChart()
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
	        		.showLegend(false)
	        		.margin({
	        			left: 30,
	        			bottom: 35
	        		})
	        		.useInteractiveGuideline(true);

	        		chart.xAxis
	        		.tickFormat(function(d) {
	        			return d3.time.format('%e %B')(new Date(d))
	        		});

	        		chart.yAxis.tickFormat(d3.format('d'));

	        		// update max and min on Y axis
	        		chart.forceY([data.min,data.max]);

	        		d3.select('.nvd3-line svg')
	        		.datum(data.lines)
	        		.transition().duration(500)
	        		.call(chart);

	        		nv.utils.windowResize(chart.update);

	        		$('#visitors-and-pageviews').data('chart', chart);

	        		return chart;
	        	});
	        })();
	    });

	});
</script>
@stop

<!-- START WIDGET -->
<div class="row">
<div class="col-md-12">

<div class="widget-12 panel no-border widget-loader-circle no-margin">
	<div class="row">
		<div class="col-xlg-8 ">
			<div class="panel-heading pull-up top-right ">
				<div class="panel-controls">
					<ul>
						<li class="hidden-xlg">
							<div class="dropdown">
								<a data-target="#" href="#" data-toggle="dropdown" aria-haspopup="true" role="button" aria-expanded="false">
									<i class="portlet-icon portlet-icon-settings"></i>
								</a>
							</div>
						</li>
						<li>
							<a data-toggle="refresh" class="portlet-refresh text-black" href="#"><i class="portlet-icon portlet-icon-refresh"></i></a>
						</li>
					</ul>
				</div>
			</div>
		</div>
	</div>
	<div class="panel-body">
		<div class="row">
			<div class="col-xlg-8 ">
				<div class="p-l-10">
					<h2 class="pull-left">{{ trans('sanatorium/analytics::widgets.visitors_and_pageviews.title') }}</h2>
					<h2 class="pull-left m-l-50 {{ $extras['visitors_difference'] > -1 ? 'text-success' : 'text-danger' }}">
						<span class="bold" title="{{ trans('sanatorium/analytics::widgets.visitors_and_pageviews.visitors_this_period') }}">{{ $extras['visitors_this_period'] }}</span>
						<span class="{{ $extras['visitors_difference'] > -1 ? 'text-success' : 'text-danger' }} fs-12" title="{{ trans('sanatorium/analytics::widgets.visitors_and_pageviews.visitors_difference') }}">{{ $extras['visitors_difference'] > -1 ? '+' : '-' }}{{ $extras['visitors_difference'] }}</span>
					</h2>
					<div class="clearfix"></div>
					<div class="full-width">
						{{--
						<ul class="list-inline">
							<li><a href="#" class="font-montserrat text-master">1D</a>
							</li>
							<li class="active"><a href="#" class="font-montserrat  bg-master-light text-master">5D</a>
							</li>
							<li><a href="#" class="font-montserrat text-master">1M</a>
							</li>
							<li><a href="#" class="font-montserrat text-master">1Y</a>
							</li>
						</ul>
						--}}
					</div>
					<div class="nvd3-line line-chart text-center" id="visitors-and-pageviews" data-x-grid="false">
						<svg></svg>
					</div>
				</div>
			</div>
			<div class="col-xlg-4 visible-xlg">
				<div class="widget-12-search">
					<p class="pull-left">{{ trans('sanatorium/analytics::widgets.visitors_and_pageviews.subtitle') }}
						<span class="bold">{{ trans('sanatorium/analytics::widgets.visitors_and_pageviews.subtitle_bold') }}</span>
					</p>
					<div class="clearfix"></div>
					{{--
					<input type="text" placeholder="Search list" class="form-control m-t-5">
					--}}
				</div>
				<div class="company-stat-boxes">
					<div data-index="0" class="company-stat-box m-t-15 active padding-20 bg-master-lightest">
						<div>
							<button type="button" class="close" data-dismiss="modal">
								<i class="pg-close fs-12"></i>
							</button>
							<p class="company-name pull-left text-uppercase bold no-margin">
								<span class="fa fa-circle text-success fs-11"></span> {{ trans('sanatorium/analytics::widgets.visitors') }}
							</p>
							<small class="hint-text m-l-10">{{ trans('sanatorium/analytics::widgets.visitors_short_desc') }}</small>
							<div class="clearfix"></div>
						</div>
						<div class="m-t-10">
							<p class="pull-left small hint-text no-margin p-t-5">{{ trans('sanatorium/analytics::widgets.visitors_desc') }}</p>
							<div class="clearfix"></div>
						</div>
					</div>
					<div data-index="1" class="company-stat-box m-t-15  padding-20 bg-master-lightest">
						<div>
							<button type="button" class="close" data-dismiss="modal">
								<i class="pg-close fs-12"></i>
							</button>
							<p class="company-name pull-left text-uppercase bold no-margin">
								<span class="fa fa-circle text-danger fs-11"></span> {{ trans('sanatorium/analytics::widgets.pageviews') }}
							</p>
							<small class="hint-text m-l-10">{{ trans('sanatorium/analytics::widgets.pageviews_short_desc') }}</small>
							<div class="clearfix"></div>
						</div>
						<div class="m-t-10">
							<p class="pull-left small hint-text no-margin p-t-5">{{ trans('sanatorium/analytics::widgets.pageviews_desc') }}</p>
							<div class="clearfix"></div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

</div>
</div>
<!-- END WIDGET -->