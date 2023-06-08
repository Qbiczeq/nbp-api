<?php
require_once 'api.php';
require_once 'database.php';
require_once 'conversions.php';

$apiClient = new NBPCurrencyApiClient();
$database = new CurrencyDatabase('localhost', 'root', 'root', 'nbp');

$currencies = $apiClient->getCurrencies();
foreach ($currencies as $currency => $rate) {
    $database->saveCurrencyRate($currency, $rate);
}

$currencyRates = $database->getCurrencyRates();
$currencyConversions = $database->getCurrencyConversions();
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <title>Aplikacja przewalutowania</title>
    <link rel="stylesheet" href="styles.css"
</head>
<body>
<h1>Kantor walut</h1>

<h2>Kursy walut</h2>
<table>
    <tr>
        <th>Kod waluty</th>
        <th>Kurs</th>
    </tr>
    <?php
    foreach ($currencyRates as $currencyCode => $exchangeRate) {
        echo "<tr>";
        echo "<td>{$currencyCode}</td>";
        echo "<td>{$exchangeRate}</td>";
        echo "</tr>";
    }
    ?>
</table>

<h2>Kalkulator przewalutowań</h2>
<form method="post" action="index.php">
    <label for="amount">Kwota:</label>
    <input type="number" id="amount" name="amount" min="0.01" step="0.01" required>

    <label for="from_currency">Waluta źródłowa:</label>
    <select id="from_currency" name="from_currency" required>
        <?php
        foreach ($currencyRates as $currencyCode => $exchangeRate) {
            echo "<option value='{$currencyCode}'>{$currencyCode}</option>";
        }
        ?>
    </select>

    <label for="to_currency">Waluta docelowa:</label>
    <select id="to_currency" name="to_currency" required>
        <?php
        foreach ($currencyRates as $currencyCode => $exchangeRate) {
            echo "<option value='{$currencyCode}'>{$currencyCode}</option>";
        }
        ?>
    </select>

    <input type="submit" value="Przewalutuj">
</form>
<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $amount = $_POST["amount"];
    $fromCurrency = $_POST["from_currency"];
    $toCurrency = $_POST["to_currency"];

    $converter = new CurrencyConverter($database);
    $convertedAmount = $converter->convertCurrency($amount, $fromCurrency, $toCurrency);
    echo "Przewalutowano {$amount} {$fromCurrency} na {$convertedAmount} {$toCurrency}";
}
?>

<h2>Historia</h2>
<table>
    <tr>
        <th>Kwota</th>
        <th>Waluta źródłowa</th>
        <th>Waluta docelowa</th>
        <th>Przewalutowana kwota</th>
    </tr>
    <?php
    foreach ($currencyConversions as $conversion) {
        echo "<tr>";
        echo "<td>{$conversion['amount']}</td>";
        echo "<td>{$conversion['from_currency']}</td>";
        echo "<td>{$conversion['to_currency']}</td>";
        echo "<td>{$conversion['converted_amount']}</td>";
        echo "</tr>";
    }

    ?>
</table>

<?php
$database->closeConnection();
?>
</body>
</html>
