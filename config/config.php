<?php

return [

    'map_mode'    => 'czechRepublicLowGA',
    'ga_ua'       => 'UA-XXXXXXXX-X',
    'ga_admin'    => true,
    'ga_ua_admin' => 'UA-71753817-1',

    /*
    |--------------------------------------------------------------------------
    | Widget: Visitors & Pageviews
    |--------------------------------------------------------------------------
    |
    | curved_line => (false|'basis'|'basis-open'|'basis-closed'|'step-before'|'step-after'|'bundle'|'cardinal'|'cardinal-open'|'cardinal-closed'|'monotone') Interpolation of lines
    | show_y_axis => (true|false) Show Y Axis legend
    | show_x_avis => (true|false) Show X Avis legend
    | show_y_grid => (true|false) Show Y Grid (thin lines behind the graph)
    | show_x_grid => (true|false) Show X Grid (thin lines behind the graph)
    |
    */
    'visitors_and_pageviews' => [

        'curved_line' => 'cardinal',
        'show_y_axis' => false,
        'show_x_axis' => true,
        'show_y_grid' => false,
        'show_x_grid' => true,

    ],

];
