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

		$('[data-settings]').change(function(){

			var value = $(this).val(),
				checked = $(this).is(':checked'),
				setting = $(this).data('settings'),
				message = $(this).data('msg');

			if ( value == 'email' && !checked ) {
				value = 'automatic';
			}

			if ( value == '1' && !checked ) {
				value = 0;
			}

			$.ajax({
				url: '{{ route('sanatorium.analytics.registrations.settings') }}',
				data: {setting: setting, value: value},
				method: 'post',
			}).success(function(msg){
				$('body').pgNotification({
					'style' : 'bar',
					'position' : 'top',
					'message' : message,
					'type' : 'success',
					'showClose' : false
				}).show();
			});
		});

		d3.json('{{ route('sanatorium.analytics.data.registrations', 7) }}', function(data) {

            // Widget-15
            nv.addGraph(function() {
                var chart = nv.models.lineChart()
                    .x(function(d) {
                        return d[0]
                    })
                    .y(function(d) {
                        return d[1]
                    })
                    .color(['#ed1c24'])
                    .useInteractiveGuideline(true)
                    .margin({
                        top: 10,
                        right: -10,
                        bottom: 10,
                        left: -10
                    })
                    .showXAxis(false)
                    .showYAxis(false)
                    .showLegend(false);

                chart.xAxis
	        		.tickFormat(function(d) {
	        			return d3.time.format('%e %B')(new Date(d))
	        		});

                d3.select('.registrations-chart svg')
                    .datum(data.registrations)
                    .call(chart);

                nv.utils.windowResize(chart.update);

                nv.utils.windowResize(function() {
                    setTimeout(function() {
                        $('.registrations-chart .nvd3 circle.nv-point').attr("r", "4");
                    }, 500);
                });

                return chart;
            }, function() {
                setTimeout(function() {
                    $('.registrations-chart .nvd3 circle.nv-point').attr("r", "4");
                }, 500);
            });

		});

	});
</script>
@stop

<!-- START WIDGET -->
<div class="relative no-overflow">
	<div class="registrations-chart line-chart" data-line-color="success" data-points="true" data-point-color="white" data-stroke-width="2">
		<svg></svg>
	</div>
</div>
@if ( $options )
<div class="b-b b-t b-grey p-l-20 p-r-20 p-b-10 p-t-10">
	<p class="pull-left">{{ trans('sanatorium/analytics::widgets.registrations.enabled') }}</p>
	<div class="pull-right">
		<input type="checkbox" data-init-plugin="switchery" data-settings="platform-users.registration" data-msg="{{ trans('sanatorium/analytics::widgets.registrations.enabled_changed') }}" value="1" {{ config('platform-users.registration') ? 'checked="checked"' : '' }} />
	</div>
	<div class="clearfix"></div>
</div>
<div class="b-b b-grey p-l-20 p-r-20 p-b-10 p-t-10">
	<p class="pull-left">{{ trans('sanatorium/analytics::widgets.registrations.mail_activation') }}</p>
	<div class="pull-right">
		<input type="checkbox" data-init-plugin="switchery" data-settings="platform-users.activation" data-msg="{{ trans('sanatorium/analytics::widgets.registrations.mail_activation_changed') }}" value="email" {{ config('platform-users.activation') == 'email' ? 'checked="checked"' : '' }} />
	</div>
	<div class="clearfix"></div>
</div>
@endif
<!-- END WIDGET -->