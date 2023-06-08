<?php

class CurrencyDatabase
{
    private mysqli $conn;

    public function __construct($servername, $username, $password, $dbname)
    {
        $this->conn = new mysqli($servername, $username, $password, $dbname);

        if ($this->conn->connect_error) {
            die("Nie udało się połączyć z bazą danych: " . $this->conn->connect_error);
        }
    }

    /**
     * Save a currency exchange rate to a database table, updating it if it already exists.
     *
     * @param string $currencyCode String variable that represents the code of the currency
     * for which we want to retrieve the exchange rate. For example, "USD" for US Dollars.
     * @param float $exchangeRate float type parameter that represents the exchange
     * rate of a currency.
     */
    public function saveCurrencyRate(string $currencyCode, float $exchangeRate): void
    {
        $sql = "INSERT INTO currency_rates (currency_code, exchange_rate) VALUES ('$currencyCode ', $exchangeRate)
                ON DUPLICATE KEY UPDATE exchange_rate = $exchangeRate";

        if ($this->conn->query($sql) !== true) {
            echo "Błąd przy zapisywaniu kursu waluty {$currencyCode}: " . $this->conn->error;
        }
    }

    /**
     * Retrieve the exchange rate for a given currency code from a database.
     *
     * @param string $currencyCode String variable that represents the code of the currency
     * for which we want to retrieve the exchange rate. For example, "USD" for US Dollars.
     *
     * @return float a float value representing the exchange rate of a given currency code.
     */
    public function getCurrencyRate(string $currencyCode): float
    {
        $sql = "SELECT exchange_rate FROM currency_rates WHERE currency_code LIKE '$currencyCode'";

        if ($result = $this->conn->query($sql)) {
            return $result->fetch_row()[0];
        } else {
            echo "Błąd przy odczytywaniu kursu waluty {$currencyCode}: " . $this->conn->error;
        }
    }

    /**
     * Retrieve currency exchange rates from a database and return them as an
     * associative array.
     *
     * @return array An array of currency rates, where the keys are the currency codes and the values
     * are the exchange rates.
     */
    public function getCurrencyRates(): array
    {
        $result = $this->conn->query("SELECT * FROM currency_rates");
        $currencyRates = array();
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $currencyRates[$row["currency_code"]] = $row["exchange_rate"];
            }
        }

        return $currencyRates;
    }

    /**
     * Save currency conversion data to a database.
     *
     * @param float $amount The amount of money being converted.
     * @param string $fromCurrency The currency code of the currency being converted from. For example,
     * "USD" for US dollars.
     * @param string $toCurrency The currency code of the currency being converted to.
     * @param float $convertedAmount The amount of money after conversion from the original currency to
     * the target currency.
     */
    public function saveCurrencyConversion(float $amount, string $fromCurrency, string $toCurrency, float $convertedAmount): void
    {
        $sql = "INSERT INTO currency_conversions (amount, from_currency, to_currency, converted_amount) VALUES ($amount, '$fromCurrency', '$toCurrency', $convertedAmount)";

        if ($this->conn->query($sql) !== true) {
            echo "Błąd przy zapisywaniu przewalutowania: " . $this->conn->error;
        }
    }

    /**
     * Retrieve the latest currency conversion records from a database and returns them
     * as an array.
     *
     * @param int $limit The  parameter is an optional integer parameter that specifies the
     * maximum number of currency conversions to retrieve from the database. If not specified, it
     * defaults to 10.
     *
     * @return array An array of currency conversion data, with each conversion represented as an
     * associative array containing the `amount`, `from_currency`, `to_currency`, and `converted_amount`. The
     * number of conversions returned is limited by the  parameter, which defaults to 10 if not
     * specified.
     */
    public function getCurrencyConversions(int $limit = 10): array
    {
        $sql = "SELECT * FROM currency_conversions ORDER BY conversion_id DESC LIMIT $limit";
        $result = $this->conn->query($sql);

        $currencyConversions = array();
        if (isset($result->num_rows) && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $conversion = array(
                    'amount' => $row['amount'],
                    'from_currency' => $row['from_currency'],
                    'to_currency' => $row['to_currency'],
                    'converted_amount' => $row['converted_amount']
                );
                $currencyConversions[] = $conversion;
            }
        }
        return $currencyConversions;
    }

    /**
     * Close the connection to a database.
     */
    public function closeConnection(): void
    {
        $this->conn->close();
    }
}