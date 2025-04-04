<?php 
namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ExchangeRateService
{
    //Fetch the exchange rate from USD to EUR
    public function getExchangeRate(): float
    {
        try {
            $response = Http::timeout(5)->get("https://open.er-api.com/v6/latest/USD");

            if ($response->successful() && isset($response['rates']['EUR'])) {
                return $response['rates']['EUR'];
            }
        } catch (\Exception $e) {
            Log::error("Exchange rate API error: " . $e->getMessage());
        }

        return env('EXCHANGE_RATE', 0.85);
    }
}
