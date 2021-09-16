<?php

namespace App\Http\Controllers\Executive;

use App\Builds;
use App\Categories;
use App\Challenge;
use App\Employee;
use App\Http\Controllers\API\ApiController;
use App\Industry;
use App\Purchase;
use App\Reward;
use App\Subcategory;
use App\Upload;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __invoke(Request $request)
    {

        // Pick logged in user id as users are represented as company.
        $employee =  optional(session('employee'));
        $companyId = $employee->company_id;

        // Current model/table names are designed in a poor way and we might need to refactor DB.
        // Use table names pragmatically to prevent breaking due to refactoring.
        $challengeTable = (new Challenge())->getTable();
        $industryTable = (new Industry())->getTable();
        $categoryTable = (new Categories())->getTable();
        $employeeTable = (new Employee())->getTable();
        $uploadTable = (new Upload())->getTable();
        $purchaseTable = (new Purchase())->getTable();
        $rewardTable = (new Reward())->getTable();
        $buildTable = (new Builds())->getTable();
        $subCategoryTable = (new Subcategory())->getTable();

        // Get user input for date range
//        $hasDateFilter = $request->input('from') && $request->input('to');
        $dateFrom = $request->input('from', now()->startOfMonth()->toDateString());
        $dateTo = $request->input('to', now()->toDateString());
        $hasDateFilter = $dateFrom && $dateFrom;

        if ($dateFrom) {
            $dateFrom = Carbon::createFromFormat('Y-m-d', $dateFrom)->startOfDay()->toDateTimeString();
        }

        if ($dateTo) {
            $dateTo = Carbon::createFromFormat('Y-m-d', $dateTo)->endOfDay()->toDateTimeString();
        }

//        dd($hasDateFilter, $dateFrom, $dateTo);

        // Reusable query scope to filter by date
        $selectedDateRangeQuery = function ($table) use ($dateFrom, $dateTo) {
            return function ($query) use ($table, $dateFrom, $dateTo) {
                return $query->whereBetween("{$table}.created_at", [$dateFrom, $dateTo]);
            };
        };

        // Get user input for store
        $hasStoreFilter = !empty($request->input('store'));
        $selectedStore = $request->input('store');

        // Reusable query scope to filter by store
        $selectedStoreQuery = function ($table, $column = 'sendto_region') use ($industryTable, $selectedStore) {
            return function ($query) use ($industryTable, $table, $column, $selectedStore) {
                $query->join($industryTable, DB::raw("FIND_IN_SET({$industryTable}.id, {$table}.{$column})"), '>', DB::raw("'0'"))
                    ->whereIn("{$industryTable}.id", is_array($selectedStore) ? $selectedStore : [$selectedStore]);
            };
        };

        // Get user input for store
        $hasAccessLevelFilter = (string) $request->input('access_level') != '';
        $selectedAccessLevel = $request->input('access_level');

        // Reusable query scope to filter by store
        $selectedAccessLevelQuery = function ($query) use ($employeeTable, $selectedAccessLevel) {
            $query->where("{$employeeTable}.access_level", (int) $selectedAccessLevel);
        };

        // All stores of the current company
        $stores = Industry::query()
            ->where('company_id', $companyId)
            ->orderBy('industry_name', 'ASC')
            ->get();

        // All employees of the current company
        $employees = Employee::query()
            ->select("{$employeeTable}.*")
            ->with(['lostBuilds', 'wonBuilds', 'allBuilds.challenge'])
            ->when($hasStoreFilter, $selectedStoreQuery($employeeTable, 'industry'))
            ->when($hasAccessLevelFilter, $selectedAccessLevelQuery)
            ->where("{$employeeTable}.company_id", $companyId)
            ->where("{$employeeTable}.is_deleted", '0')
            ->where("{$employeeTable}.id",'!=', $employee->id)
            ->where("{$employeeTable}.is_request",'0')
            ->where("{$employeeTable}.access_level",'<=', $employee->id)
            ->get();

        $categories = Categories::query()
            ->where('company_id', $companyId)
            ->get();

        $selectableColumns = [
            "{$buildTable}.id",
            "{$challengeTable}.id",
            "{$challengeTable}.company_id",
            "{$challengeTable}.category_id",
            "{$challengeTable}.subcategory_id",
            "{$challengeTable}.sendto_region",
            "{$categoryTable}.category_name",
            "{$subCategoryTable}.subcategory_name",
            "{$employeeTable}.id",
            "{$employeeTable}.full_name",
        ];

        $challenges = Builds::query()
            ->select(array_merge($selectableColumns, [
                "{$employeeTable}.full_name as employee_name",
                DB::raw("GROUP_CONCAT({$industryTable}.industry_name) as store_name"),
            ]))
            ->join($challengeTable, "{$challengeTable}.id", '=', "{$buildTable}.challenge_id")
            ->join($employeeTable, "{$employeeTable}.id", '=', "{$buildTable}.employee_id")
            ->leftJoin($subCategoryTable, DB::raw("FIND_IN_SET({$subCategoryTable}.id, {$buildTable}.subcategory)"), '>', DB::raw("'0'"))
            ->leftJoin($categoryTable, "{$categoryTable}.id", '=', "{$subCategoryTable}.category_id")
            ->leftJoin($industryTable, DB::raw("FIND_IN_SET({$industryTable}.id, {$employeeTable}.industry)"), '>', DB::raw("'0'"))
            ->when($hasAccessLevelFilter, $selectedAccessLevelQuery)
            ->when($hasDateFilter, $selectedDateRangeQuery($buildTable))
            ->when($hasStoreFilter, function ($query) use ($industryTable, $selectedStore) {
                return $query->where("{$industryTable}.id", $selectedStore);
            })
            ->where("{$buildTable}.company_id", $companyId)
            ->whereIn("{$buildTable}.status", ['1', '2'])
            ->where("{$employeeTable}.is_deleted", '0')
            ->groupBy("{$buildTable}.id")
            ->get();


        // Format challenges into simple data format
        $challenges = $challenges->map(function ($challenge, $key) {
            $item = new \stdClass();
            $item->id = $challenge->id;
            $item->stores = explode(',', $challenge->store_name);
            $item->category = $challenge->category_name;
            $item->subcategory = $challenge->subcategory_name;
            $item->employee = $challenge->employee_name;

            return $item;
        });

        // Group challenges by store names
        $challengesByStore = $challenges
            ->groupBy('stores')
            ->map(function ($challenges, $store) {
                return $challenges->groupBy('subcategory');
            })
            ->toArray();

        // Set stores that dont have any challenges, with empty array for charts
        foreach ($stores as $store) {
            if (count($challengesByStore) &&  !isset($challengesByStore[$store->industry_name])) {
                $challengesByStore[$store->industry_name] = [];
            }
        }

        // Group challenges by category names
        $challengesByCategory = $challenges
            ->groupBy('category')
            ->map(function ($challenges, $category) {
                return $challenges->groupBy('subcategory');
            })
            ->sortBy(function ($challenges, $category) {
                return strtolower(explode(" ", $category)[0]);
            })
            ->toArray();

        // Set employees that dont have any challenges, with empty array for charts
        foreach ($categories as $category) {
            if (count($challengesByCategory) &&  !isset($challengesByCategory[$category->category_name])) {
                $challengesByCategory[$category->category_name] = [];
            }
        }

        $challengesByCategory = collect($challengesByCategory)
            ->sortBy(function ($challenges, $category) {
                return strtolower(explode(" ", $category)[0]);
            })->toArray();

        // Group challenges by employee names
        $challengesByEmployee = $challenges
            ->groupBy('employee')
            ->map(function ($challenges, $employee) use ($employees) {
                return $challenges->groupBy('subcategory');
            })
            ->toArray();

        // Set employees that dont have any challenges, with empty array for charts
        foreach ($employees as $employee) {
            if (count($challengesByEmployee) &&  !isset($challengesByEmployee[$employee->full_name])) {
                $challengesByEmployee[$employee->full_name] = [];
            }
        }

        $challengesByEmployee = collect($challengesByEmployee)
            ->sortBy(function ($challenges, $employee) {
                return strtolower(explode(" ", $employee)[0]);
            })->toArray();

        // Group challenges by category => employee names
        $challengesByEmployeePerCategory = $challenges
            ->groupBy([
                'category',
                function ($challenge) {
                    return $challenge->employee;
                }
            ])
            ->map(function ($challengesPerEmployee, $category) use ($employees) {
                // Group challenges by sub-category names
                foreach ($challengesPerEmployee as $employee => $groupedChallenges) {
                    $challengesPerEmployee[$employee] = $groupedChallenges->groupBy('subcategory')->toArray();
                }

                // Set employees that dont have any challenges, with empty array for charts
                foreach ($employees as $employee) {
                    if (!$challengesPerEmployee->get($employee->full_name)) {
                        $challengesPerEmployee->put($employee->full_name, []);
                    }
                }

                return $challengesPerEmployee->sortBy(function ($challenges, $employee) {
                    return strtolower(explode(" ", $employee)[0]);
                });
            })
            ->toArray();


        $builds = Builds::query()
            ->select([
                "{$buildTable}.id",
                "{$categoryTable}.category_name",
                "{$subCategoryTable}.subcategory_name",
                "{$employeeTable}.id",
                "{$employeeTable}.full_name as employee_name",
                DB::raw("GROUP_CONCAT({$industryTable}.industry_name) as store_name"),
            ])
            ->join($employeeTable, "{$employeeTable}.id", '=', "{$buildTable}.employee_id")
            ->leftJoin($subCategoryTable, DB::raw("FIND_IN_SET({$subCategoryTable}.id, {$buildTable}.subcategory)"), '>', DB::raw("'0'"))
            ->leftJoin($categoryTable, "{$categoryTable}.id", '=', "{$subCategoryTable}.category_id")
            ->leftJoin($industryTable, DB::raw("FIND_IN_SET({$industryTable}.id, {$employeeTable}.industry)"), '>', DB::raw("'0'"))
            ->when($hasAccessLevelFilter, $selectedAccessLevelQuery)
            ->when($hasDateFilter, $selectedDateRangeQuery($buildTable))
            ->when($hasStoreFilter, function ($query) use ($industryTable, $selectedStore) {
                return $query->where("{$industryTable}.id", $selectedStore);
            })
            ->where("{$buildTable}.company_id", $companyId)
            ->whereIn("{$buildTable}.status", ['1', '2'])
            ->groupBy("{$buildTable}.id")
            ->where("{$employeeTable}.is_deleted", '0')
            ->get();

        // Format challenges into simple data format
        $builds = $builds->map(function ($challenge, $key) {
            $item = new \stdClass();
            $item->id = $challenge->id;
            $item->stores = explode(',', $challenge->store_name);
            $item->category = $challenge->category_name;
            $item->subcategory = $challenge->subcategory_name;
            $item->employee = $challenge->employee_name;

            return $item;
        });

        // Group builds by employee names
        $categoriesByEmployee = $builds
            ->groupBy('category')
            ->map(function ($builds, $category) {
                return $builds->groupBy('subcategory');
            })
            ->toArray();

        // Group challenges by category => employee names
        $categoriesByEmployeePerEmployee = $builds
            ->groupBy([
                'employee',
                function ($build) {
                    return $build->category;
                }
            ])
            ->map(function ($categoriesPerEmployee, $employee) use ($employees) {
                // Group challenges by sub-category names
                foreach ($categoriesPerEmployee as $category => $groupedBuilds) {
                    $categoriesPerEmployee[$category] = $groupedBuilds->groupBy('subcategory')->toArray();
                }

                return $categoriesPerEmployee->sortBy(function ($builds, $employee) {
                    return strtolower(explode(" ", $employee)[0]);
                });
            })
            ->toArray();

        // Set employees that dont have any challenges, with empty array for charts
        foreach ($employees as $employee) {
            if (count($categoriesByEmployeePerEmployee) && !isset($categoriesByEmployeePerEmployee[$employee->full_name])) {
                $categoriesByEmployeePerEmployee[$employee->full_name] = [];
            }
        }

        $categoriesByEmployeePerEmployee = collect($categoriesByEmployeePerEmployee)
            ->sortBy(function ($categories, $employee) {
                return strtolower(explode(" ", $employee)[0]);
            })->toArray();

        // Get metrics
        $metrics = [];

        $metrics['challengesCompleted'] = Builds::query()
            ->select("{$buildTable}.id")
            ->join($challengeTable, "{$challengeTable}.id", '=', "{$buildTable}.challenge_id")
            ->leftJoin($employeeTable, "{$employeeTable}.id", '=', "{$buildTable}.employee_id")
            ->when($hasAccessLevelFilter, $selectedAccessLevelQuery)
            ->when($hasDateFilter, $selectedDateRangeQuery($challengeTable))
            ->when($hasStoreFilter, $selectedStoreQuery($challengeTable, 'sendto_region'))
            ->where("{$challengeTable}.company_id", $companyId)
            ->where("{$buildTable}.status", '1')
            ->groupBy("{$buildTable}.id")
            ->get()
            ->count();

        $metrics['categoriesApproved'] = Builds::query()
            ->leftJoin($employeeTable, "{$employeeTable}.id", '=', "{$buildTable}.employee_id")
            ->when($hasAccessLevelFilter, $selectedAccessLevelQuery)
            ->when($hasDateFilter, $selectedDateRangeQuery($buildTable))
            ->when($hasStoreFilter, $selectedStoreQuery($employeeTable, 'industry'))
            ->where(function ($query) use ($buildTable, $companyId, $employee) {
                return $query->where("{$buildTable}.employee_id", $employee->id)
                    ->orWhere("{$buildTable}.company_id", $companyId);
            })
            ->whereIn("{$buildTable}.status", ['1', '2'])
            ->get()
            ->count();

        $api = new ApiController;

        $metrics['topEmployees'] = $employees->map(function ($employee) use ($api) {
            $employee->point = $api->countPoint($employee);
            return $employee;
        })->sortByDesc('point');

        $metrics['totalEmployees'] = $employees->count();

        $metrics['totalAnnouncements'] = Upload::query()
            ->when($hasStoreFilter, $selectedStoreQuery($uploadTable, 'sendto_region'))
            ->where("{$uploadTable}.company_id", $companyId)
            ->count();

        $metrics['rewardsPurchased'] = Reward::query()
            ->join($purchaseTable, "{$purchaseTable}.rewarditem_id", '=', "{$rewardTable}.id")
            ->join($employeeTable, "{$employeeTable}.id", '=', "{$purchaseTable}.employee_id")
            ->when($hasAccessLevelFilter, $selectedAccessLevelQuery)
            ->when($hasDateFilter, $selectedDateRangeQuery($purchaseTable))
            ->when($hasStoreFilter, $selectedStoreQuery($employeeTable, 'industry'))
            ->where("{$rewardTable}.company_id", $companyId)
            ->count();

        return view('executive.dashboard', compact(
            'stores',
            'challengesByStore',
            'challengesByCategory',
            'challengesByEmployee',
            'challengesByEmployeePerCategory',
            'categoriesByEmployee',
            'categoriesByEmployeePerEmployee',
            'metrics'
        ));
    }
}
