# Project Title

Currency conversion app that utilizes the NBP (Narodowy Bank Polski) API to fetch currency exchange rates. It allows to save the retrieved currency rates to a database and display them in a table format. Additionally, the application enables users to convert a given amount from one currency to another and store the conversion results in the database.

## Demo

https://nbp-api.herokuapp.com/index.php


## Installation

SQL to create required tables

```sql
CREATE TABLE `currency_rates` (
`currency_code` varchar(32) NOT NULL,
`exchange_rate` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `currency_rates`
ADD PRIMARY KEY (`currency_code`);
COMMIT;

CREATE TABLE `currency_conversions` (
`conversion_id` int(32) NOT NULL,
`amount` double NOT NULL,
`from_currency` varchar(32) NOT NULL,
`to_currency` varchar(32) NOT NULL,
`converted_amount` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `currency_conversions`
ADD PRIMARY KEY (`conversion_id`);

ALTER TABLE `currency_conversions`
MODIFY `conversion_id` int(32) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=66;
COMMIT;
```
    
