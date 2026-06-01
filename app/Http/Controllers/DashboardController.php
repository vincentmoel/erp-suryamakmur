<?php

namespace App\Http\Controllers;

use App\Helpers\Date;
use App\Helpers\MoneyFormatter;
use App\Models\Invoice;
use App\Models\InvoiceDurationDetail;
use App\Models\InvoiceItemDetail;
use App\Models\ItemCategory;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $transactionToday = self::transactionToday();
        $incomeToday = self::incomeToday();
        $incomeThisWeek = self::incomeThisWeek();
        $incomeThisMonth = self::incomeThisMonth();

        $chartIncome = self::chartIncome();
        $chartIncomeMonthlyByYear = self::chartIncomeMonthlyByYear();
        $chartIncomeYearly = self::chartIncomeYearly();
        $chartIncomeByCategory = self::chartIncomeByCategory();

        $itemCategories = ItemCategory::pluck('name')->toArray();

        return view('dashboard.index', [
            'transactionToday'          => count($transactionToday),
            'incomeToday'               => MoneyFormatter::rupiah($incomeToday),
            'incomeThisWeek'            => MoneyFormatter::rupiah($incomeThisWeek),
            'incomeThisMonth'           => MoneyFormatter::rupiah($incomeThisMonth),
            'chartIncome'               => $chartIncome,
            'chartIncomeMonthlyByYear'  => $chartIncomeMonthlyByYear,
            'chartIncomeYearly'         => $chartIncomeYearly,
            'chartIncomeByCategory'     => $chartIncomeByCategory,
            'itemCategories'            => $itemCategories,
            'years'                     => Date::year(),
        ]);
    }

    public static function transactionToday()
    {
        $invoices = Invoice::whereDate('created_at', Carbon::today())->get();

        return $invoices;
    }

    public static function incomeToday()
    {
        $invoices = Invoice::whereDate('created_at', Carbon::today())->sum('total');

        return $invoices;
    }

    public static function incomeThisWeek()
    {
        $invoices = Invoice::whereBetween('created_at', [
            Carbon::now()->startOfWeek(),
            Carbon::now()->endOfWeek()
        ])->sum('total');

        return $invoices;
    }

    public static function incomeThisMonth()
    {
        $invoices = Invoice::whereBetween('created_at', [
            Carbon::now()->startOfMonth(),
            Carbon::now()->endOfMonth()
        ])->sum('total');

        return $invoices;
    }

    public static function chartIncome()
    {
        // Get current date
        $endDate = Carbon::today();
        $startDate = Carbon::today()->subDays(13); // 14 days including today

        $labels = [];
        $income = [];

        for ($date = clone $startDate; $date->lte($endDate); $date->addDay()) {
            $day = $date->day;
            $month = $date->translatedFormat('F'); // This will use the current locale for month name
            $year = $date->year;

            $labels[] = [$day, $month, $year];

            // Get total income for this day
            $dailyIncome = Invoice::whereDate('created_at', $date->toDateString())
                ->sum('total'); // Assuming 'total_amount' is the column for payment amount

            $income[] = $dailyIncome ?: 0; // Use 0 if no data exists
        }

        return [
            'label' => $labels,
            'income' => $income
        ];
    }

    public static function chartIncomeMonthlyByYear()
    {
        $year = request()->has('chartIncomeMonthly') ? request()->get('chartIncomeMonthly') : Carbon::now()->year;

        $labels = [];
        $income = [];

        // Generate data for each month in the selected year
        for ($month = 1; $month <= 12; $month++) {
            $date = Carbon::createFromDate($year, $month, 1);

            // Get month name based on locale
            $monthName = $date->translatedFormat('F');

            $labels[] = [$monthName];

            // Calculate total income for this month
            $monthlyIncome = Invoice::whereYear('created_at', $year)
                ->whereMonth('created_at', $month)
                ->sum('total');

            $income[] = $monthlyIncome ?: 0; // Use 0 if no data exists
        }

        return [
            'label' => $labels,
            'income' => $income
        ];
    }

    public static function chartIncomeYearly()
    {
        // Get current year
        $rangeYear = Date::year();
        $startYear = $rangeYear[0];
        $currentYear = Carbon::now()->year;

        $labels = [];
        $income = [];

        // Generate data for each year
        for ($year = $startYear; $year <= $currentYear; $year++) {
            $labels[] = [$year];

            // Calculate total income for this year
            $yearlyIncome = Invoice::whereYear('created_at', $year)
                ->sum('total');

            $income[] = $yearlyIncome ?: 0; // Use 0 if no data exists
        }

        return [
            'label' => $labels,
            'income' => $income
        ];
    }

    public static function chartIncomeByCategory()
    {
        $endDate = Carbon::today();
        $startDate = Carbon::today()->subDays(13);

        $itemCategories = ItemCategory::all();
        $income = [];
        $labels = [];

        // Initialize arrays for each category
        foreach ($itemCategories as $itemCategory) {
            $income[$itemCategory->id] = [];
        }

        // Get date range
        for ($date = clone $startDate; $date->lte($endDate); $date->addDay()) {
            // $labels[] = $date->format('d F Y');

            $day = $date->day;
            $month = $date->translatedFormat('F'); // This will use the current locale for month name
            $year = $date->year;

            $labels[] = [$day, $month, $year];
        }

        // Query all invoice item details for the date range at once
        $invoiceItemDetails = InvoiceItemDetail::whereHas('invoice', function ($query) use ($startDate, $endDate) {
            $query->whereDate('created_at', '>=', $startDate->toDateString())
                ->whereDate('created_at', '<=', $endDate->toDateString());
        })
            ->with(['invoice', 'item' => function ($query) {
                $query->withTrashed(); // hanya untuk relasi 'item'
            }, 'item.itemCategory'])
            ->get();

        // Process the data in memory
        for ($date = clone $startDate; $date->lte($endDate); $date->addDay()) {
            $currentDate = $date->toDateString();

            // Filter for current date
            $dailyDetails = $invoiceItemDetails->filter(function ($detail) use ($currentDate) {
                return $detail->invoice->created_at->toDateString() === $currentDate;
            });

            foreach ($itemCategories as $itemCategory) {
                // Filter by category and sum
                $categoryTotal = $dailyDetails
                    ->filter(function ($detail) use ($itemCategory) {
                        return $detail->item->itemCategory->id === $itemCategory->id;
                    })
                    ->sum('total');

                $income[$itemCategory->id][] = $categoryTotal ?: 0;
            }
        }

        $invoiceDurationDetails = InvoiceDurationDetail::whereHas('invoice', function ($query) use ($startDate, $endDate) {
            $query->whereDate('created_at', '>=', $startDate->toDateString())
                ->whereDate('created_at', '<=', $endDate->toDateString());
        })
            ->with(['invoice', 'duration'])
            ->get();

        // Process the data in memory
        for ($date = clone $startDate; $date->lte($endDate); $date->addDay()) {
            $currentDate = $date->toDateString();

            // Filter for current date
            $sumDurationDetail = $invoiceDurationDetails->filter(function ($detail) use ($currentDate) {
                return $detail->invoice->created_at->toDateString() === $currentDate;
            })->sum('total');

            $income['durations'][] = $sumDurationDetail ?: 0;
        }


        $resultIncome = [];

        foreach ($income as $key => $incomeByCategory) {
            if ($key == strtolower('durations')) {
                $resultIncome[] = [
                    "name"  => "Duration",
                    "data"  => $incomeByCategory
                ];

                continue;
            }

            $resultIncome[] = [
                "name"  => $itemCategories->where('id', $key)->first()->name,
                "data"  => $incomeByCategory
            ];
        }

        return [
            'label' => $labels,
            'income' => $resultIncome
        ];
    }
}
