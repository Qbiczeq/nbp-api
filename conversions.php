<?php

class CurrencyConverter
{
    private CurrencyDatabase $database;

    public function __construct(CurrencyDatabase $database)
    {
        $this->database = $database;
    }

    /**
     * Convert an amount from one currency to another and save the conversion in a
     * database.
     *
     * @param float $amount The amount of money to be converted, represented as a float.
     * @param string $fromCurrency The currency code of the currency you want to convert from. For
     * example, "USD" for US dollars.
     * @param string $toCurrency The currency code of the currency you want to convert to. For
     * example, "EUR" for Euro.
     *
     * @return float a float value which represents the converted amount from one currency to another.
     */
    public function convertCurrency(float $amount, string $fromCurrency, string $toCurrency): float
    {
        $fromRate = $this->database->getCurrencyRate($fromCurrency);
        $toRate = $this->database->getCurrencyRate($toCurrency);
        $convertedAmount = number_format($amount * ($fromRate / $toRate), 2, '.', '');

        $this->database->saveCurrencyConversion($amount, $fromCurrency, $toCurrency, $convertedAmount);

        return $convertedAmount;
    }
}