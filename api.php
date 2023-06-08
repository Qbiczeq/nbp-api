<?php

class NBPCurrencyApiClient
{
    /**
     * Retrieve currency exchange rates from the NBP API and return an array of
     * currency codes and their corresponding mid rates.
     *
     * @return array Currency codes and their mid exchange rates, obtained from the NBP API.
     */
    public function getCurrencies(): array
    {
        $response = file_get_contents('http://api.nbp.pl/api/exchangerates/tables/A/?format=json');
        $data = json_decode($response, true);
        $currencyCodes = [];
        foreach ($data[0]['rates'] as $currency) {
            $currencyCodes[$currency['code']] = $currency['mid'];
        }
        return $currencyCodes;
    }
}