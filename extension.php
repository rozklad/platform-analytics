<?php

use Illuminate\Foundation\Application;
use Cartalyst\Extensions\ExtensionInterface;
use Cartalyst\Settings\Repository as Settings;
use Cartalyst\Permissions\Container as Permissions;

return [

	/*
	|--------------------------------------------------------------------------
	| Name
	|--------------------------------------------------------------------------
	|
	| This is your extension name and it is only required for
	| presentational purposes.
	|
	*/

	'name' => 'Analytics',

	/*
	|--------------------------------------------------------------------------
	| Slug
	|--------------------------------------------------------------------------
	|
	| This is your extension unique identifier and should not be changed as
	| it will be recognized as a new extension.
	|
	| Ideally, this should match the folder structure within the extensions
	| folder, but this is completely optional.
	|
	*/

	'slug' => 'sanatorium/analytics',

	/*
	|--------------------------------------------------------------------------
	| Author
	|--------------------------------------------------------------------------
	|
	| Because everybody deserves credit for their work, right?
	|
	*/

	'author' => 'Sanatorium',

	/*
	|--------------------------------------------------------------------------
	| Description
	|--------------------------------------------------------------------------
	|
	| One or two sentences describing the extension for users to view when
	| they are installing the extension.
	|
	*/

	'description' => 'Package to retrieve Google Analytics data',

	/*
	|--------------------------------------------------------------------------
	| Version
	|--------------------------------------------------------------------------
	|
	| Version should be a string that can be used with version_compare().
	| This is how the extensions versions are compared.
	|
	*/

	'version' => '1.2.4',

	/*
	|--------------------------------------------------------------------------
	| Requirements
	|--------------------------------------------------------------------------
	|
	| List here all the extensions that this extension requires to work.
	| This is used in conjunction with composer, so you should put the
	| same extension dependencies on your main composer.json require
	| key, so that they get resolved using composer, however you
	| can use without composer, at which point you'll have to
	| ensure that the required extensions are available.
	|
	*/

	'require' => [
        'sanatorium/dashboards'
    ],

	/*
	|--------------------------------------------------------------------------
	| Autoload Logic
	|--------------------------------------------------------------------------
	|
	| You can define here your extension autoloading logic, it may either
	| be 'composer', 'platform' or a 'Closure'.
	|
	| If composer is defined, your composer.json file specifies the autoloading
	| logic.
	|
	| If platform is defined, your extension receives convetion autoloading
	| based on the Platform standards.
	|
	| If a Closure is defined, it should take two parameters as defined
	| bellow:
	|
	|	object \Composer\Autoload\ClassLoader      $loader
	|	object \Illuminate\Foundation\Application  $app
	|
	| Supported: "composer", "platform", "Closure"
	|
	*/

	'autoload' => 'composer',

	/*
	|--------------------------------------------------------------------------
	| Service Providers
	|--------------------------------------------------------------------------
	|
	| Define your extension service providers here. They will be dynamically
	| registered without having to include them in app/config/app.php.
	|
	*/

	'providers' => [
		'Sanatorium\Analytics\Providers\AnalyticsServiceProvider'
	],

	/*
	|--------------------------------------------------------------------------
	| Routes
	|--------------------------------------------------------------------------
	|
	| Closure that is called when the extension is started. You can register
	| any custom routing logic here.
	|
	| The closure parameters are:
	|
	|	object \Cartalyst\Extensions\ExtensionInterface  $extension
	|	object \Illuminate\Foundation\Application        $app
	|
	*/

	'routes' => function(ExtensionInterface $extension, Application $app)
	{
		Route::group([
			'prefix' => admin_uri().'/analytics/',
			'namespace' => 'Sanatorium\Analytics'
			], function(){

				Route::get('vistorsandpageviews/{days}', ['as' => 'sanatorium.analytics.data.visitors.and.pageviews', 'uses' => 'Widgets\Visitors@getVisitorsAndPageViewsData'] );
				Route::post('registrations/settings', ['as' => 'sanatorium.analytics.registrations.settings', 'uses' => 'Widgets\Visitors@settings'] );
				Route::get('registrations/data/{days}', ['as' => 'sanatorium.analytics.data.registrations', 'uses' => 'Widgets\Visitors@getRegistrationsData'] );

				Route::get('repair/certificate', ['as' => 'sanatorium.analytics.repair.certificate', 'uses' => 'Widgets\Visitors@repairCertificate'] );

				Route::get('cache/clear', ['as' => 'sanatorium.analytics.cache.clear', 'uses' => 'Widgets\Visitors@clear'] );

			});
	},

	/*
	|--------------------------------------------------------------------------
	| Database Seeds
	|--------------------------------------------------------------------------
	|
	| Platform provides a very simple way to seed your database with test
	| data using seed classes. All seed classes should be stored on the
	| `database/seeds` directory within your extension folder.
	|
	| The order you register your seed classes on the array below
	| matters, as they will be ran in the exact same order.
	|
	| The seeds array should follow the following structure:
	|
	|	Vendor\Namespace\Database\Seeds\FooSeeder
	|	Vendor\Namespace\Database\Seeds\BarSeeder
	|
	*/

	'seeds' => [

	],

	/*
	|--------------------------------------------------------------------------
	| Permissions
	|--------------------------------------------------------------------------
	|
	| Register here all the permissions that this extension has. These will
	| be shown in the user management area to build a graphical interface
	| where permissions can be selected to allow or deny user access.
	|
	| For detailed instructions on how to register the permissions, please
	| refer to the following url https://cartalyst.com/manual/permissions
	|
	*/

	'permissions' => function(Permissions $permissions)
	{

	},

	/*
	|--------------------------------------------------------------------------
	| Widgets
	|--------------------------------------------------------------------------
	|
	| Closure that is called when the extension is started. You can register
	| all your custom widgets here. Of course, Platform will guess the
	| widget class for you, this is just for custom widgets or if you
	| do not wish to make a new class for a very small widget.
	|
	*/

	'widgets' => function()
	{

	},

	/*
	|--------------------------------------------------------------------------
	| Settings
	|--------------------------------------------------------------------------
	|
	| Register any settings for your extension. You can also configure
	| the namespace and group that a setting belongs to.
	|
	*/

	'settings' => function(Settings $settings, Application $app)
	{
		$settings->find('platform')->section('analytics', function ($s) {
			$s->name = trans('sanatorium/analytics::settings.title');

            $s->fieldset('analytics', function ($f) {
                $f->name = trans('sanatorium/analytics::common.title');

                


                $f->field('ga_ua', function ($f) {
                    $f->name   = trans('sanatorium/analytics::settings.ga_ua.label');
                    $f->info   = trans('sanatorium/analytics::settings.ga_ua.info');
                    $f->type   = 'input';
                    $f->config = 'sanatorium-analytics.ga_ua';
                });

                $f->field('siteId', function ($f) {
                    $f->name   = trans('sanatorium/analytics::settings.siteId.label');
                    $f->info   = trans('sanatorium/analytics::settings.siteId.info');
                    $f->type   = 'input';
                    $f->config = 'laravel-analytics.siteId';
                });

                $f->field('clientId', function ($f) {
                    $f->name   = trans('sanatorium/analytics::settings.clientId.label');
                    $f->info   = trans('sanatorium/analytics::settings.clientId.info');
                    $f->type   = 'input';
                    $f->config = 'laravel-analytics.clientId';
                });

                $f->field('serviceEmail', function ($f) {
                    $f->name   = trans('sanatorium/analytics::settings.serviceEmail.label');
                    $f->info   = trans('sanatorium/analytics::settings.serviceEmail.info');
                    $f->type   = 'input';
                    $f->config = 'laravel-analytics.serviceEmail';
                });

                $f->field('certificatePath', function ($f) {
                    $f->name   = trans('sanatorium/analytics::settings.certificatePath.label');
                    $f->info   = trans('sanatorium/analytics::settings.certificatePath.info');
                    $f->type   = 'input';
                    $f->config = 'laravel-analytics.certificatePath';
                });

                $f->field('map_mode', function ($f) {
                    $f->name   = trans('sanatorium/analytics::settings.map_mode.label');
                    $f->info   = trans('sanatorium/analytics::settings.map_mode.info');
                    $f->type   = 'select';
                    $f->config = 'sanatorium-analytics.map_mode';

                    $f->option('czechRepublicLowGA', function ($o) {
                        $o->value = 'czechRepublicLowGA';
                        $o->label = trans('sanatorium/analytics::settings.map_mode.values.czechRepublicLowGA');
                    });

                    $f->option('worldLow', function ($o) {
                        $o->value = 'worldLow';
                        $o->label = trans('sanatorium/analytics::settings.map_mode.values.worldLow');
                    });
                });


                $f->field('ga_admin', function ($f) {
                    $f->name   = trans('sanatorium/analytics::settings.ga_admin.label');
                    $f->info   = trans('sanatorium/analytics::settings.ga_admin.info');
                    $f->type   = 'radio';
                    $f->config = 'sanatorium-analytics.ga_admin';

                    $f->option('true', function ($o) {
                        $o->value = true;
                        $o->label = trans('sanatorium/analytics::settings.ga_admin.values.true');
                    });

                    $f->option('false', function ($o) {
                        $o->value = false;
                        $o->label = trans('sanatorium/analytics::settings.ga_admin.values.false');
                    });
                });

                if ( config('sanatorium-analytics.ga_admin') ) {

                	$f->field('ga_ua_admin', function ($f) {
	                    $f->name   = trans('sanatorium/analytics::settings.ga_ua_admin.label');
	                    $f->info   = trans('sanatorium/analytics::settings.ga_ua_admin.info');
	                    $f->type   = 'input';
	                    $f->config = 'sanatorium-analytics.ga_ua_admin';
	                });

                }

            });
        });
	},

	/*
	|--------------------------------------------------------------------------
	| Menus
	|--------------------------------------------------------------------------
	|
	| You may specify the default various menu hierarchy for your extension.
	| You can provide a recursive array of menu children and their children.
	| These will be created upon installation, synchronized upon upgrading
	| and removed upon uninstallation.
	|
	| Menu children are automatically put at the end of the menu for extensions
	| installed through the Operations extension.
	|
	| The default order (for extensions installed initially) can be
	| found by editing app/config/platform.php.
	|
	*/

	'menus' => [

		'admin' => [

			/*[
				'slug'  => 'admin-sanatorium-analytics',
				'name'  => 'Analytics',
				'class' => 'fa fa-circle-o',
				'uri'   => 'analytics',
				'regex' => '/:admin\/analytics/i',
			],*/

		],

		'main' => [

		],

	],

];
