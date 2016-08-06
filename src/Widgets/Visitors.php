<?php namespace Sanatorium\Analytics\Widgets;

use LaravelAnalytics;
use Platform\Foundation\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Config\Repository as Config;

class Visitors extends Controller
{

    protected $configured = false;

    public function __construct(Config $config)
    {
        $this->config = $config;

        if ( !$this->checkConfiguration() )
        {
            $this->configured = false;

            return $this->error('configuration');
        } else
        {
            $this->configured = true;
        }
    }

    public function getVisitorsAndPageViews($days = 7, $previous = true)
    {
        if ( !$this->checkConfiguration() )
        {
            return $this->error('configuration');
        }

        // Total visitors, pageviews and their differences
        list($messages, $extras) = $this->getExtrasData($days);

        if ( !empty($messages) )
        {
            return implode(', ', $messages);
        }

        return view('sanatorium/analytics::widgets/visitors_and_pageviews', compact('days', 'extras', 'previous'));
    }

    public function getVisitorsAndPageViewsData($days = 7, $previous = true)
    {
        // Check configuration before runing queries
        if ( !$this->configured )
            return $this->error('configuration');

        try
        {

            // If days are not given, try to get them from parameters
            if ( !is_numeric($days) )
            {
                if ( request()->has('days') )
                {
                    $days = request()->get('days');
                }
            }

            // If days still not available, give default value
            if ( !is_numeric($days) )
                $days = 7;

            /*
            * $data now contains a Collection with 3 columns: "date", "visitors" and "pageViews"
            */
            $data = LaravelAnalytics::getVisitorsAndPageViews($days * ($previous ? 2 : 1));

            $max = 0;

            $d3data = [
                'min'   => 0,
                'max'   => $max,
                'lines' => [
                    [
                        'key'    => trans('sanatorium/analytics::widgets.visitors'),
                        'values' => [],
                        'area'   => true,
                    ],
                    [
                        'key'    => trans('sanatorium/analytics::widgets.pageviews'),
                        'values' => [],
                        'area'   => true,
                    ],
                ],
            ];

            if ( $previous )
            {

                $d3data['lines'][] = [
                    'key'    => trans('sanatorium/analytics::widgets.visitors_previous'),
                    'values' => [],
                    'area'   => false,
                ];

                $d3data['lines'][] = [
                    'key'    => trans('sanatorium/analytics::widgets.pageviews_previous'),
                    'values' => [],
                    'area'   => false,
                ];

            }

            foreach ( $data as $key => $item )
            {
                if ( $previous && $key < $days )
                {
                    $d3data['lines'][2]['values'][] = [
                        ($item['date']->timestamp + ($days * 24 * 60 * 60)) * 1000,
                        $item['visitors'],
                    ];
                    $d3data['lines'][3]['values'][] = [
                        ($item['date']->timestamp + ($days * 24 * 60 * 60)) * 1000,
                        $item['pageViews'],
                    ];
                } elseif ( $previous && $key == $days )
                {
                    // Current
                    $d3data['lines'][0]['values'][] = [
                        $item['date']->timestamp * 1000,
                        $item['visitors'],
                    ];
                    $d3data['lines'][1]['values'][] = [
                        $item['date']->timestamp * 1000,
                        $item['pageViews'],
                    ];

                    // Previous
                    $d3data['lines'][2]['values'][] = [
                        ($item['date']->timestamp + ($days * 24 * 60 * 60)) * 1000,
                        $item['visitors'],
                    ];
                    $d3data['lines'][3]['values'][] = [
                        ($item['date']->timestamp + ($days * 24 * 60 * 60)) * 1000,
                        $item['pageViews'],
                    ];
                } else
                {
                    $d3data['lines'][0]['values'][] = [
                        $item['date']->timestamp * 1000,
                        $item['visitors'],
                    ];
                    $d3data['lines'][1]['values'][] = [
                        $item['date']->timestamp * 1000,
                        $item['pageViews'],
                    ];
                }

                if ( $item['pageViews'] > $max )
                {
                    $max = $item['pageViews'];
                }

                if ( $item['visitors'] > $max )
                {
                    $max = $item['visitors'];
                }
            }

            // Update max
            // set max as highest value + 20% of the height
            $d3data['max'] = (int) ($max + $max * 0.2);

        } catch (\Google_Service_Exception $e)
        {

            return 'Exception (sanatorium/analytics): ' . $e->getMessage();

        }

        return $d3data;
    }

    /**
     * @param  integer $days [description]
     * @return [type]        [description]
     */
    public function getExtrasData($days = 7)
    {
        // Check configuration before runing queries
        if ( !$this->configured )
            return $this->error('configuration');

        $messages = [];
        $extras = [];

        try
        {

            // Get current period totals
            $totals = LaravelAnalytics::performQuery(Carbon::now()->subDays($days), Carbon::now(), 'ga:sessions,ga:pageviews');

            $visitors_this_period = $totals->rows[0][0];
            $pageviews_this_period = $totals->rows[0][1];

            // Get last equal period totals
            $totals_before = LaravelAnalytics::performQuery(Carbon::now()->subDays($days * 2), Carbon::now()->subDays($days), 'ga:sessions,ga:pageviews');

            $visitors_before_period = $totals_before->rows[0][0];
            $pageviews_before_period = $totals_before->rows[0][1];

            // Get period totals difference
            $visitors_difference = $visitors_this_period - $visitors_before_period;
            $pageviews_difference = $pageviews_this_period - $pageviews_before_period;

        } catch (\Google_Service_Exception $e)
        {

            $messages[] = 'Exception (sanatorium/analytics): ' . $e->getMessage();

            return [
                $messages,
                $extras,
            ];
        }

        $extras = [
            'visitors_this_period'    => $visitors_this_period,
            'pageviews_this_period'   => $pageviews_this_period,
            'visitors_before_period'  => $visitors_before_period,
            'pageviews_before_period' => $pageviews_before_period,
            'visitors_difference'     => $visitors_difference,
            'pageviews_difference'    => $pageviews_difference,
        ];

        return [
            $messages,
            $extras,
        ];
    }

    /**
     * Get regional visitors data
     *
     * @param  integer $days [description]
     * @return [type]        [description]
     */
    public function getRegional($days = 365)
    {
        // Check configuration before runing queries
        if ( !$this->configured )
            return $this->error('configuration');

        try
        {

            // Get current period data
            $data = LaravelAnalytics::performQuery(
                Carbon::now()->subDays($days),
                Carbon::now(),
                'ga:sessions,ga:pageviews',
                ['dimensions' => 'ga:region,ga:country']
            );

            $column_headers = $data->getColumnHeaders();

            $results = [];

            // Get map mode
            $map_mode = config('sanatorium-analytics.map_mode');

            // Based on map mode
            // key is either region or country
            switch ( $map_mode )
            {
                case 'czechRepublicLowGA':
                    $ammap_key = 'ga:region';
                    break;

                default:
                    $ammap_key = 'ga:country';
                    break;
            }

            // Store ammap ids
            $this->ammapIds = self::getAmmapIds($map_mode);

            if ( $data->rows )
            {
                foreach ( $data->rows as $key => $row )
                {

                    $result = [];
                    $key = null;

                    foreach ( $data->columnHeaders as $headerkey => $header )
                    {

                        $result[ $header->name ] = $row[ $headerkey ];

                        if ( $header->name == $ammap_key )
                        {
                            $key = $this->getAmmapId($row[ $headerkey ]);
                        }
                    }

                    $results[ $key ] = $result;
                }
            }

            // ColorSteps identical to number of regions displayed
            $colorSteps = 14;

        } catch (\Google_Service_Exception $e)
        {

            return 'Exception (sanatorium/analytics): ' . $e->getMessage();
        }

        return view('sanatorium/analytics::widgets/regional', compact('results', 'colorSteps', 'map_mode'));
    }

    public function getAmmapId($title = null)
    {
        $key = array_search($title, array_column($this->ammapIds, 'title'));

        return $this->ammapIds[ $key ]['id'];
    }

    public static function getAmmapIds($map_mode = null)
    {
        // Get the .js file contents
        $map_contents = file_get_contents(__DIR__ . '/../../themes/admin/default/packages/sanatorium/analytics/assets/ammap/maps/js/' . $map_mode . '.js');

        // Remove non JSON data
        $map_contents = str_replace(
            [
                'AmCharts.maps.' . $map_mode . '=',
                '};',
            ],
            [
                '',
                '}',
            ],
            $map_contents
        );

        // Remove js comments
        $pattern = '/(?:(?:\/\*(?:[^*]|(?:\*+[^*\/]))*\*+\/)|(?:(?<!\:|\\\|\')\/\/.*))/';
        $map_contents = preg_replace($pattern, '', $map_contents);

        $map_data = json_decode($map_contents, true);

        if ( isset($map_data['svg']) )
        {
            if ( isset($map_data['svg']['g']) )
            {
                if ( isset($map_data['svg']['g']['path']) )
                {
                    return $map_data['svg']['g']['path'];
                }
            }
        }

        return null;
    }

    /**
     * @todo finish - the key in cache::forget has to be dynamically resolved
     */
    public function clear()
    {
        $success = \Cache::forget('spatie.laravel-analytics.c8bc2370c4e1a1a399d0bee8404b7990');

        return [
            'success' => $success,
        ];
    }

    /**
     * Checks the google analytics account configuration
     *
     * @param  boolean $return [<description>]
     * @return [type] [description]
     */
    public function checkConfiguration($return = false)
    {
        $tests = [
            'certificate',
            'siteId',
            'clientId',
            'serviceEmail',
        ];

        $results = [];

        foreach ( $tests as $test )
        {

            $result = $this->checkConfigurationItem($test);

            if ( $return )
            {
                $results[ $test ] = $result;
            } else if ( !$result )
            {
                return $result;
            }
        }

        if ( $return )
        {
            return $results;
        }

        return true;
    }

    public function checkConfigurationItem($type = null)
    {
        switch ( $type )
        {

            // Check config values
            case 'siteId':
            case 'clientId':
            case 'serviceEmail':
                return $this->checkConfigurationConfig($type);
                break;

            // Check existence of google .p12 certificate
            case 'certificate':
                return $this->checkConfigurationCertificate();
                break;
        }

        return true;
    }

    public function checkConfigurationConfig($type = null)
    {
        if ( !config('laravel-analytics.' . $type) )
            return false;

        return true;
    }

    public function checkConfigurationCertificate()
    {
        if ( !\File::exists(config('laravel-analytics.certificatePath')) )
            return false;

        return true;
    }

    public function error($type = \ null)
    {
        $tests = $this->checkConfiguration(true);

        // Pass configuration data to display in error message
        $data = [
            'certificate_path' => config('laravel-analytics.certificatePath'),
            'siteId'           => config('laravel-analytics.siteId'),
            'clientId'         => config('laravel-analytics.clientId'),
            'serviceEmail'     => config('laravel-analytics.serviceEmail'),
        ];

        // Available automatic repairs
        $repairs = [
            'certificate' => route('sanatorium.analytics.repair.certificate'),
        ];

        return view("sanatorium/analytics::widgets/error.{$type}", compact('type', 'tests', 'data', 'repairs'));
    }

    /**
     * Repairs
     */
    public function repairCertificate()
    {
        $this->config->persist('laravel-analytics.certificatePath', storage_path('laravel-analytics/xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx-privatekey.p12'));

        return redirect()->back();
    }
}
