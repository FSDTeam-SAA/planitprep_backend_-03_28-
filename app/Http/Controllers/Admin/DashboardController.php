<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Package;
use App\Models\Payment;
use App\Models\User;
use App\Models\UserMembership;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    // public function __construct()
    // {
    //     $this->middleware('auth:admin');
    // }

    public function index(Request $request)
    {
        $today_dt = date('Y-m-d');

        $totalUsers = User::where('active', 1)->count();
        $totalPackages = Package::where('active', 1)->count();
        $activeMemberships = UserMembership::where('active', 1)->where('end_date', '>=', $today_dt)->count();
        $totalPayments = Payment::count();

        $latestUsers = User::latest()->limit(5)->get();
        $latestPackages = Package::latest()->limit(5)->get();
        $latestPayments = Payment::latest()->limit(5)->get();
        $latestOrders = [];

        // Payments chart
        $payments = DB::table('payments')
            ->select(
                DB::raw('MONTH(created_at) as payment_month'),
                DB::raw('YEAR(created_at) as payment_year'),
                DB::raw('COUNT(*) as payment_count')
            )
            ->groupBy('payment_year', 'payment_month')
            ->orderBy('payment_year')
            ->orderBy('payment_month')
            ->get();

        // Define an array for month names
        $monthNames = [
            1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr',
            5 => 'May', 6 => 'Jun', 7 => 'Jul', 8 => 'Aug',
            9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dec',
        ];

        $paymentMonths = [];
        $paymentCounts = $payments->pluck('payment_count')->toArray();

        foreach ($payments as $payment) {
            // Get the abbreviated month name
            $monthName = $monthNames[$payment->payment_month];
            $paymentMonths[] = "{$monthName}";
        }

        // Encode for JSON
        $paymentMonths = json_encode($paymentMonths);
        $paymentCounts = json_encode($paymentCounts);

        // Membership chart
        $memberships = DB::table('user_memberships')
            ->select(
                DB::raw('MONTH(created_at) as membership_month'),
                DB::raw('YEAR(created_at) as membership_year'),
                DB::raw('COUNT(*) as membership_count')
            )
            ->groupBy('membership_year', 'membership_month')
            ->orderBy('membership_year')
            ->orderBy('membership_month')
            ->get();

        $membershipMonths = [];
        $membershipCounts = $memberships->pluck('membership_count')->toArray();

        foreach ($memberships as $membership) {
            // Get the abbreviated month name
            $monthName = $monthNames[$membership->membership_month];
            $membershipMonths[] = "{$monthName}";
        }

        // Encode for JSON
        $membershipMonths = json_encode($membershipMonths);
        $membershipCounts = json_encode($membershipCounts);

        return view('admin.dashboard', compact(
            'totalUsers',
            'totalPackages',
            'activeMemberships',
            'totalPayments',
            'latestUsers',
            'latestPackages',
            'latestPayments',
            'latestOrders',
            'latestPayments',
            'paymentMonths',
            'paymentCounts',
            'membershipMonths',
            'membershipCounts',
        ));
    }
}
