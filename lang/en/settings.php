<?php

return [
	
	'title' => 'Analytics',

	'siteId' => [
		'label' => 'Site ID',
		'info' => 'Should look like: ga:xxxxxxxx.'
	],

	'clientId' => [
		'label' => 'Client ID',
		'info' => 'Should look like xxxxxxxxxxxx-xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx.apps.googleusercontent.com',
	],

	'serviceEmail' => [
		'label' => 'Service E-mail',
		'info' => 'Should look like xxxxxxxxxxxx-xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx@developer.gserviceaccount.com',
	],

	'certificatePath' => [
		'label' => 'Certificate .p12',
		'info' => 'Should look like laravel-analytics/xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx-privatekey.p12',
	],

	'map_mode' => [
		'label' => 'Map mode',
		'info' => 'Choose map mode for primary widget',
		'values' => [
			'worldLow' => 'World',
			'czechRepublicLowGA' => 'Czech republic',
		],
	],

	'ga_ua' => [
		'label' => 'Universal Analytics ID',
		'info' => 'Should look like UA-XXXXXXXX-X',
	],

	'ga_ua_admin' => [
		'label' => 'Universal Analytics ID (admin)',
		'info' => 'Should look like UA-XXXXXXXX-X',
	],

	'ga_admin' => [
		'label' => 'Track admin area',
		'info' => 'Track users in admin area (setting on will help us improve)',
		'values' => [
			'true' => 'Enabled',
			'false' => 'Disabled'
		]
	],
];