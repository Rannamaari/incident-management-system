<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Incident;
use Carbon\Carbon;

class ReportsController extends Controller
{
    /**
     * Display the reports dashboard
     */
    public function index(Request $request)
    {
        // Handle date filtering
        $dateFilter = $this->getDateFilter($request);
        
        // Get chart data for the reports with date filtering
        $chartData = $this->getChartData($dateFilter);
        
        return view('reports.index', compact('chartData'));
    }

    /**
     * Get date filter from request
     */
    private function getDateFilter(Request $request)
    {
        $startDate = null;
        $endDate = null;
        
        if ($request->has('preset') && $request->preset) {
            // Handle preset date ranges
            $now = Carbon::now();
            switch ($request->preset) {
                case 'last_month':
                    $startDate = $now->copy()->subMonth()->startOfMonth();
                    $endDate = $now->copy()->subMonth()->endOfMonth();
                    break;
                case 'last_quarter':
                    $startDate = $now->copy()->subQuarter()->startOfQuarter();
                    $endDate = $now->copy()->subQuarter()->endOfQuarter();
                    break;
                case 'last_6_months':
                    $startDate = $now->copy()->subMonths(6)->startOfMonth();
                    $endDate = $now->copy()->endOfDay();
                    break;
                case 'last_year':
                    $startDate = $now->copy()->subYear()->startOfYear();
                    $endDate = $now->copy()->subYear()->endOfYear();
                    break;
                case 'ytd':
                    $startDate = $now->copy()->startOfYear();
                    $endDate = $now->copy()->endOfDay();
                    break;
            }
        } elseif ($request->has('start_date') || $request->has('end_date')) {
            // Handle custom date range
            if ($request->start_date) {
                $startDate = Carbon::parse($request->start_date)->startOfDay();
            }
            if ($request->end_date) {
                $endDate = Carbon::parse($request->end_date)->endOfDay();
            }
        }
        
        return compact('startDate', 'endDate');
    }

    /**
     * Get data for charts
     */
    private function getChartData($dateFilter = [])
    {
        return [
            'severityData' => $this->getSeverityData($dateFilter),
            'statusData' => $this->getStatusData($dateFilter),
            'monthlyTrends' => $this->getMonthlyTrends($dateFilter),
            'categoryData' => $this->getCategoryData($dateFilter),
            'slaPerformance' => $this->getSlaPerformance($dateFilter),
            'totalIncidents' => $this->getIncidentQuery($dateFilter)->count(),
            'openIncidents' => $this->getIncidentQuery($dateFilter)->where('status', 'Open')->count(),
            'criticalIncidents' => $this->getIncidentQuery($dateFilter)->where('severity', 'Critical')->count(),
            'slaBreached' => $this->getIncidentQuery($dateFilter)->where('exceeded_sla', true)->count(),
        ];
    }

    /**
     * Get base incident query with date filtering
     */
    private function getIncidentQuery($dateFilter = [])
    {
        $query = Incident::query();
        
        if (!empty($dateFilter['startDate'])) {
            $query->where('created_at', '>=', $dateFilter['startDate']);
        }
        
        if (!empty($dateFilter['endDate'])) {
            $query->where('created_at', '<=', $dateFilter['endDate']);
        }
        
        return $query;
    }

    /**
     * Get incident data by severity
     */
    private function getSeverityData($dateFilter = [])
    {
        return [
            'Critical' => $this->getIncidentQuery($dateFilter)->where('severity', 'Critical')->count(),
            'High' => $this->getIncidentQuery($dateFilter)->where('severity', 'High')->count(),
            'Medium' => $this->getIncidentQuery($dateFilter)->where('severity', 'Medium')->count(),
            'Low' => $this->getIncidentQuery($dateFilter)->where('severity', 'Low')->count(),
        ];
    }

    /**
     * Get incident data by status
     */
    private function getStatusData($dateFilter = [])
    {
        return [
            'Open' => $this->getIncidentQuery($dateFilter)->where('status', 'Open')->count(),
            'In Progress' => $this->getIncidentQuery($dateFilter)->where('status', 'In Progress')->count(),
            'Monitoring' => $this->getIncidentQuery($dateFilter)->where('status', 'Monitoring')->count(),
            'Closed' => $this->getIncidentQuery($dateFilter)->where('status', 'Closed')->count(),
        ];
    }

    /**
     * Get monthly incident trends
     */
    private function getMonthlyTrends($dateFilter = [])
    {
        $months = [];
        $data = [];
        
        // Determine the range based on date filter or default to last 6 months
        $startDate = !empty($dateFilter['startDate']) ? $dateFilter['startDate']->copy() : Carbon::now()->subMonths(5);
        $endDate = !empty($dateFilter['endDate']) ? $dateFilter['endDate']->copy() : Carbon::now();
        
        // Generate months between start and end date
        $current = $startDate->copy()->startOfMonth();
        
        while ($current->lte($endDate)) {
            $months[] = $current->format('M Y');
            
            $count = $this->getIncidentQuery($dateFilter)
                          ->whereYear('created_at', $current->year)
                          ->whereMonth('created_at', $current->month)
                          ->count();
            $data[] = $count;
            
            $current->addMonth();
        }
        
        // If no data or less than 2 months, show last 6 months as fallback
        if (count($months) < 2) {
            $months = [];
            $data = [];
            for ($i = 5; $i >= 0; $i--) {
                $month = Carbon::now()->subMonths($i);
                $months[] = $month->format('M Y');
                
                $count = $this->getIncidentQuery($dateFilter)
                              ->whereYear('created_at', $month->year)
                              ->whereMonth('created_at', $month->month)
                              ->count();
                $data[] = $count;
            }
        }
        
        return [
            'labels' => $months,
            'data' => $data
        ];
    }

    /**
     * Get incident data by category
     */
    private function getCategoryData($dateFilter = [])
    {
        $categories = $this->getIncidentQuery($dateFilter)
                           ->select('category')
                           ->selectRaw('count(*) as count')
                           ->groupBy('category')
                           ->orderBy('count', 'desc')
                           ->get();

        return [
            'labels' => $categories->pluck('category')->toArray(),
            'data' => $categories->pluck('count')->toArray()
        ];
    }

    /**
     * Get SLA performance data
     */
    private function getSlaPerformance($dateFilter = [])
    {
        $total = $this->getIncidentQuery($dateFilter)->count();
        $breached = $this->getIncidentQuery($dateFilter)->where('exceeded_sla', true)->count();
        $achieved = $total - $breached;

        return [
            'labels' => ['SLA Achieved', 'SLA Breached'],
            'data' => [$achieved, $breached]
        ];
    }
}
