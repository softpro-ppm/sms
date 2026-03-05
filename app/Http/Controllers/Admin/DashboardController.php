<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Course;
use App\Models\Batch;
use App\Models\Enrollment;
use App\Models\Payment;
use App\Models\AssessmentResult;
use App\Models\Certificate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Live Statistics Cards
        $stats = [
            'total_students' => Student::where('status', 'approved')->count(),
            'pending_students' => Student::where('status', 'pending')->count(),
            'total_courses' => Course::where('is_active', true)->count(),
            'active_batches' => Batch::where('is_active', true)
                ->where('start_date', '<=', now())
                ->where('end_date', '>=', now())
                ->count(),
            'total_enrollments' => Enrollment::where('status', 'active')->count(),
            'pending_payments' => Payment::where('status', 'pending')->count(),
            'total_payments' => Payment::where('status', 'approved')->sum('amount'),
            'total_revenue' => Payment::where('status', 'approved')->sum('amount'),
            'certificates_issued' => Certificate::where('is_issued', true)->count(),
        ];

        // Recent Activities - Optimized queries with specific fields
        $recentActivities = [
            'recent_students' => Student::select('id', 'full_name', 'email', 'status', 'created_at')
                ->where('status', 'approved')
                ->latest('created_at')
                ->limit(5)
                ->get(),
            'recent_payments' => Payment::select('id', 'student_id', 'amount', 'status', 'created_at')
                ->with(['student:id,full_name'])
                ->where('status', 'approved')
                ->latest('created_at')
                ->limit(5)
                ->get(),
            'recent_assessments' => AssessmentResult::select('id', 'student_id', 'assessment_id', 'total_marks', 'percentage', 'is_passed', 'created_at')
                ->with(['student:id,full_name', 'assessment:id,course_id'])
                ->latest('created_at')
                ->limit(5)
                ->get(),
        ];

        // Chart Data for Dashboard (excluding payment trends - loaded via AJAX)
        $chartData = [
            'monthly_enrollments' => $this->getMonthlyEnrollments(),
            'monthly_revenue' => $this->getMonthlyRevenue(),
            'course_popularity' => $this->getCoursePopularity(),
            'batch_performance' => $this->getBatchPerformance(),
        ];

        // Extract recent activities for easier access in view
        $recentStudents = $recentActivities['recent_students'];
        $recentPayments = $recentActivities['recent_payments'];
        $recentAssessments = $recentActivities['recent_assessments'];

        return view('admin.dashboard', compact('stats', 'recentActivities', 'chartData', 'recentStudents', 'recentPayments', 'recentAssessments'));
    }

    private function getMonthlyEnrollments()
    {
        // Detect database driver and use appropriate date functions
        $driver = DB::connection()->getDriverName();
        
        if ($driver === 'sqlite') {
            // SQLite uses strftime
            return Enrollment::select(
                    DB::raw('CAST(strftime("%m", enrollment_date) AS INTEGER) as month'),
                    DB::raw('CAST(strftime("%Y", enrollment_date) AS INTEGER) as year'),
                    DB::raw('COUNT(*) as count')
                )
                ->where('enrollment_date', '>=', now()->subMonths(12))
                ->groupBy('year', 'month')
                ->orderBy('year', 'asc')
                ->orderBy('month', 'asc')
                ->get();
        } else {
            // MySQL uses MONTH and YEAR
            return Enrollment::select(
                    DB::raw('MONTH(enrollment_date) as month'),
                    DB::raw('YEAR(enrollment_date) as year'),
                    DB::raw('COUNT(*) as count')
                )
                ->where('enrollment_date', '>=', now()->subMonths(12))
                ->groupBy('year', 'month')
                ->orderBy('year', 'asc')
                ->orderBy('month', 'asc')
                ->get();
        }
    }

    private function getMonthlyRevenue()
    {
        // Detect database driver and use appropriate date functions
        $driver = DB::connection()->getDriverName();
        
        if ($driver === 'sqlite') {
            // SQLite uses strftime
            return Payment::select(
                    DB::raw('CAST(strftime("%m", created_at) AS INTEGER) as month'),
                    DB::raw('CAST(strftime("%Y", created_at) AS INTEGER) as year'),
                    DB::raw('SUM(amount) as total')
                )
                ->where('status', 'approved')
                ->where('created_at', '>=', now()->subMonths(12))
                ->groupBy('year', 'month')
                ->orderBy('year', 'asc')
                ->orderBy('month', 'asc')
                ->get();
        } else {
            // MySQL uses MONTH and YEAR
            return Payment::select(
                    DB::raw('MONTH(created_at) as month'),
                    DB::raw('YEAR(created_at) as year'),
                    DB::raw('SUM(amount) as total')
                )
                ->where('status', 'approved')
                ->where('created_at', '>=', now()->subMonths(12))
                ->groupBy('year', 'month')
                ->orderBy('year', 'asc')
                ->orderBy('month', 'asc')
                ->get();
        }
    }

    private function getCoursePopularity()
    {
        return Course::withCount(['enrollments' => function($query) {
                $query->where('status', 'active');
            }])
            ->orderBy('enrollments_count', 'desc')
            ->limit(10)
            ->get();
    }

    private function getBatchPerformance()
    {
        return Batch::with(['course', 'enrollments'])
            ->withCount(['enrollments' => function($query) {
                $query->where('status', 'active');
            }])
            ->where('end_date', '<=', now())
            ->orderBy('enrollments_count', 'desc')
            ->limit(10)
            ->get();
    }

}
