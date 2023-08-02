<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\LoadExchangeRates;
use App\Models\ExchangeRate;
use Illuminate\Support\Carbon;
use function abort;

class ExchangeRateController extends Controller
{
    public function get(string $date, string $currency, string $baseCurrency = 'RUR'): array
    {
        $carbonDate = Carbon::createFromFormat('Y-m-d', $date);
        $rate = $this->getRelativeRate($date, $currency, $baseCurrency);
        $previousDate = $carbonDate->subDay()->toDateString();
        $previousRate = $this->getRelativeRate($previousDate, $currency, $baseCurrency);
        return [
            'data' => [
                'rate' => $rate,
                'previous' => $previousRate,
                'delta' => $rate - $previousRate,
            ]
        ];
    }

    private function getRelativeRate(string $date, string $currency, string $baseCurrency): float
    {
        return $this->getRate($date, $currency) / $this->getRate($date, $baseCurrency);
    }

    private function getRate(string $date, string $currency): float
    {
        if (strtoupper($currency) === 'RUR') {
            return 1.0;
        }
        if (!ExchangeRate::where('date', $date)->exists()) {
            LoadExchangeRates::dispatchSync($date);
        }
        return (float) ExchangeRate::where([
            'date' => $date,
            'currency' => $currency
        ])->firstOrFail()->rate;
    }
}
