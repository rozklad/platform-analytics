<?php namespace Sanatorium\Analytics\Widgets;

use LaravelAnalytics;
use Platform\Foundation\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Config\Repository as Config;

class Registrations extends Controller
{

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    public function registrations($days = 7, $options = false)
    {
        $users_count_total = \Platform\Users\Models\User::all()->count();
        $users_count_admin = \Platform\Roles\Models\Role::where('slug', 'admin')->first()->users()->count();
        $users_count_registered = $users_count_total - $users_count_admin;

        return view('sanatorium/analytics::widgets/registrations', compact('days', 'users_count_total', 'users_count_registered', 'users_count_admin', 'options'));
    }

    public function getRegistrationsData($days = 7)
    {
        $date = new \DateTime('today -' . $days . ' days');

        // lists() does not accept raw queries,
        // so you have to specify the SELECT clause
        $stats = \Platform\Users\Models\User::select([
            \DB::raw('DATE(`created_at`) as `date`'),
            \DB::raw('COUNT(*) as `count`'),
        ])
            ->where('created_at', '>', $date)
            ->groupBy('date')
            ->orderBy('date', 'DESC')
            ->lists('count', 'date');

        $results = [];
        $ranges = [0, 100, 150, 200, 250, 300, 350, 400];
        $tickvalues = [];

        // Notice lists returns an associative array with its second and
        // optional param as the key, and the first param as the value
        for ( $i = $days; $i > 0; $i -- )
        {
            $dateobj = new \DateTime('today -' . $i . ' days');
            $date = $dateobj->format('Y-m-d');

            if ( isset($stats[ $date ]) )
            {
                $value = $stats[ $date ];
            } else
            {
                $value = 0;
            }

            $results[] = [
                strtotime($date) * 1000,
                $value,
            ];

            $tickvalues[] = $dateobj->format('j.n.');
        }

        return [
            'registrations' => [
                [
                    'key'    => 'Profiles',
                    'values' => $results,
                ],
            ],
            'tickvalues'    => $tickvalues,
        ];
    }

    public function settings()
    {
        $setting = request()->get('setting');
        $value = request()->get('value');

        $result = $this->config->persist($setting, $value);

        return ['success' => $result, 'setting' => $setting, 'value' => $value];
    }

}
