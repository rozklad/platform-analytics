<?php namespace Sanatorium\Analytics\Widgets;

class DashboardVisitors {

    /**
     * Name that will appear in dashboard manager
     */
    const NAME = 'Analytics: Visitors & pageviews';

    /**
     * Widget configuration on dashboard
     * @var array
     */
    public $configuration = [
        'previous' => [
            'label' => 'Show previous',
            'type' => 'boolean',
        ]
    ];
    
    public function run()
    {
        // @todo: load configuration values
        $days = 7;
        $previous = true;
        
        return widget('sanatorium/analytics::visitors.getVisitorsAndPageViews', compact('days', 'previous'));
    }
    
}
