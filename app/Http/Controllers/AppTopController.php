<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CategoryPositions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;


class AppTopController extends Controller
{

    /**
     * Get category top position for a specific date
     *
     * @param Request $request
     * @return void
     */
    public function show(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'date' => ['required', 'regex:/^\d{4}-\d{2}-\d{2}$/' ]
        ]);

        if ($validator->fails()) {

            Log::channel("api_logs")->info("API /appTopCategory call failure", $validator->messages()->toArray());

            return [
                'status_code' => 400,
                'message' => $validator->messages()->toArray()
            ];
        }

        $categories = CategoryPositions::select(["category_id", DB::raw("MIN(position) as 'position'")])
            ->where("date", $request->date)
            ->groupBy('category_id')->get()->toArray();

        $formatted = [];

        foreach ($categories as $category)
        {
            $formatted[$category['category_id']] = $category['position'];
        }

        Log::channel("api_logs")->info("API /appTopCategory GET request success", $request->all());

        return [
            'status_code' => 200,
            'message' => 'ok',
            'data' => $formatted
        ];
    }

}
