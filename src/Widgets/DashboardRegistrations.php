<?php namespace Sanatorium\Analytics\Widgets;

use Widget;

class DashboardRegistrations {

    /**
     * Name that will appear in dashboard manager
     */
    const NAME = 'Analytics: Registrations';

    /**
     * Show wrapper around this widget
     */
    const HAS_WRAPPER = true;

    /**
     * Widget configuration on dashboard
     * @var array
     */
    public $configuration = [
    ];

    public function run()
    {
        // @todo: load configuration values
        $days = 7;
        $options = false;

        return Widget::make('sanatorium/analytics::registrations.registrations', compact('days', 'options'));
    }

}
