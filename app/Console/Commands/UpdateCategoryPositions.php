<?php

namespace App\Console\Commands;

use Illuminate\Support\Facades\Http;
use Illuminate\Console\Command;
use App\Models\CategoryPositions;
use Illuminate\Support\Facades\Validator;

class UpdateCategoryPositions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update_categories {date_from} {date_to}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Updates categories top positions';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    protected function print_errors($message, $errors)
    {
        $this->info($message);

        foreach ($errors as $error) {
            $this->error($error);
        }
    }

    protected function args_valid($args)
    {
        $validator = Validator::make($args, [
            'date_from' => ['required', 'regex:/^\d{4}-\d{2}-\d{2}$/'],
            'date_to' => ['required', 'regex:/^\d{4}-\d{2}-\d{2}$/']
        ]);

        if ($validator->fails())
        {
            $this->print_errors("Couldn't execute the command:", $validator->errors()->all());
            return false;
        }

        return true;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $args = $this->arguments();

        if (!$this->args_valid($args))
            return false;

        $response = Http::get("https://api.apptica.com/package/top_history/1421444/1?date_from={$args['date_from']}&date_to={$args['date_to']}&B4NKGg=fVN5Q9KVOlOHDx9mOsKPAQsFBlEhBOwguLkNEDTZvKzJzT3l");
        $categories = $response->json();

        if ($categories['status_code'] != 200)
        {
            $this->print_errors($categories['message'], [$categories['data']]);
            return 0;
        }

        foreach ($categories['data'] as $category_id => $parent_category)
        {
            foreach ($parent_category as $sub_category_id => $sub_category)
            {
                foreach ($sub_category as $date => $position)
                {
                    if ($position)
                    {
                        $data = CategoryPositions::where([
                            'category_id' => $category_id,
                            'sub_category_id' => $sub_category_id,
                            'date' => $date
                        ])->first();

                        if (!$data)
                        {
                            CategoryPositions::create([
                                'category_id' => $category_id,
                                'sub_category_id' => $sub_category_id,
                                'position' => $position,
                                'date' => $date
                            ]);
                        }
                    }
                }
            }
        }
    }
}
