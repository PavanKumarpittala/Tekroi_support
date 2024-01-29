<?php

namespace App\Http\Controllers;

use DB;
use Response;
use Hash;
use Auth;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $user_id = Auth::id();
        $role = Auth::user()->role;
        $open_issues = [];
        $open_issues_count = 0;
        if ($role == 1 ) {

            $open_issues['beyondmonth'] = DB::table('support_tickets')
                ->where('status', 0)
                ->whereDate('created_at', '<=', now()->subMonth(1))
                ->count();

            $open_issues['today'] = DB::table('support_tickets')
                ->where('status', 0)
                ->whereDate('created_at', now()->today())
                ->count();

            $open_issues['yesterday'] = DB::table('support_tickets')
                ->where('status', 0)
                ->whereDate('created_at', today()->subDays(1))
                ->count();

                $open_issues['week'] = DB::table('support_tickets')
                ->where('status', 0)
                ->whereDate('created_at', '>=', now()->subDays(7))
                ->count();
            
            $open_issues['month'] = DB::table('support_tickets')
                ->where('status', 0)
                ->whereDate('created_at', '>=', now()->subDays(30))
                ->count();
            
        } else if ($role == 2 || $role == 3) {
            $open_issues['beyondmonth'] = DB::table('support_tickets')
                ->where('status', 0)
                ->where(function($query) use($user_id) {
                    $query->where('assigned',$user_id)
                        ->oRwhere('re_assigned', $user_id);
                })
                ->whereDate('created_at', '<', now()->subMonth(1))
                ->count();

            $open_issues['today'] = DB::table('support_tickets')
                ->where('status', 0)
                ->where(function($query) use($user_id) {
                    $query->where('assigned',$user_id)
                        ->oRwhere('re_assigned', $user_id);
                })
                ->whereDate('created_at', now()->today())
                ->count();

            $open_issues['yesterday'] = DB::table('support_tickets')
                ->where('status', 0)
                ->where(function($query) use($user_id) {
                    $query->where('assigned',$user_id)
                        ->oRwhere('re_assigned', $user_id);
                })
                ->whereDate('created_at', today()->subDays(1))
                ->count();

            $open_issues['week'] = DB::table('support_tickets')
                ->where('status', 0)
                ->where(function($query) use($user_id) {
                    $query->where('assigned',$user_id)
                        ->oRwhere('re_assigned', $user_id);
                })
                ->whereDate('created_at', '>=', now()->subDays(7))
                ->count();

            $open_issues['month'] = DB::table('support_tickets')
                ->where('status', 0)
                ->where(function($query) use($user_id) {
                    $query->where('assigned',$user_id)
                        ->oRwhere('re_assigned', $user_id);
                })
                ->whereDate('created_at', '>=', now()->subMonth(1))
                ->count();

        } else {
            // Client Count
            $open_issues_count = 10;

            $role_email = Auth::user()->email;
            list($username, $domain) = explode('@', $role_email);

            $open_issues['beyondmonth'] = DB::table('support_tickets')
                ->where('status', 0)
                ->where('domain', $domain)
                ->whereDate('created_at', '<=', now()->subMonth(1))
                ->count();

            $open_issues['today'] = DB::table('support_tickets')
                ->where('status', 0)
                ->where('domain', $domain)
                ->whereDate('created_at', now()->today())
                ->count();

            $open_issues['yesterday'] = DB::table('support_tickets')
                ->where('status', 0)
                ->where('domain', $domain)
                ->whereDate('created_at', today()->subDays(1))
                ->count();

                $open_issues['week'] = DB::table('support_tickets')
                ->where('status', 0)
                ->where('domain', $domain)
                ->whereDate('created_at', '>=', now()->subDays(7))
                ->count();
            
            $open_issues['month'] = DB::table('support_tickets')
                ->where('status', 0)
                ->where('domain', $domain)
                ->whereDate('created_at', '>=', now()->subDays(30))
                ->count();
        }

        $last_timesheet = DB::table('timesheets')->select('created_at')->orderBy('created_at', 'desc')->limit(1)->first();


        return view(
            'home',
            [
                'open_issues' => $open_issues,
                'last_timesheet'    => $last_timesheet
            ]
        );
    }
}
