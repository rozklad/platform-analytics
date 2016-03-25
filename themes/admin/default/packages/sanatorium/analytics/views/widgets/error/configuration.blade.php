<div class="panel">
	<div class="panel-body">
		<p class="lead">
			{{ trans('sanatorium/analytics::message.errors.configuration') }}
		</p>

			
			@if ( isset($tests) )
				<table class="table">
				@foreach( $tests as $name => $test )
					<tr class="{{ $test ? '' : 'warning' }}">
						<td>
							{!! trans('sanatorium/analytics::message.tests.'.$name, $data) !!}
						</td>
						<td>
							@if ( isset($repairs[$name]) )
								<a href="{{ $repairs[$name] }}">
									<i class="fa fa-wrench"></i>
								</a>
							@endif
						</td>
						<td>
							@if ( $test )
								<i class="fa fa-check"></i>
							@else
								<i class="fa fa-times"></i>
							@endif
						</td>
					</tr>
				@endforeach
				</table>
			@endif

		<p>
			<a href="{{ route('admin.setting', 'analytics') }}" class="btn btn-primary">{{ trans('sanatorium/analytics::message.errors.configuration_btn') }}</a>
		</p>

	</div>
</div>