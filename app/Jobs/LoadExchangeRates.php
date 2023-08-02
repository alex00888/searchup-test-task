<?php

namespace App\Jobs;

use App\Models\ExchangeRate;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class LoadExchangeRates implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public string $date)
    {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $date = Carbon::createFromFormat('Y-m-d', $this->date)->format('d/m/Y');
        $url = "https://www.cbr.ru/scripts/XML_daily.asp?date_req={$date}";
        $xml = simplexml_load_file($url);
        if ($xml === false) {
            throw new Exception("Failed to parse XML from URL '{$url}'");
        }
        $rows = [];
        foreach ($xml->Valute as $valute) {
            $value = str_replace(',', '.', $valute->Value);
            $rows[] = [
                'date' => $this->date,
                'currency' => $valute->CharCode,
                'rate' => $value / $valute->Nominal,
                'created_at' => DB::raw('now()'),
                'updated_at' => DB::raw('now()'),
            ];
        }
        ExchangeRate::insertOrIgnore($rows);
    }
}
