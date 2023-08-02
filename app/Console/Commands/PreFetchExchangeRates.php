<?php

namespace App\Console\Commands;

use App\Jobs\LoadExchangeRates;
use App\Models\ExchangeRate;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Queue;

class PreFetchExchangeRates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:pre-fetch-rates';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Pre fetch exchange rates for previous 180 days';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $carbonDate = Carbon::now('UTC');
        for ($n = 0; $n < 180; $n++) {
            $date = $carbonDate->toDateString();
            if (!ExchangeRate::where('date', $date)->exists()) {
                Queue::push(new LoadExchangeRates($date));
            }
            $carbonDate->subDay();
        }
    }
}
