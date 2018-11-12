<?php

include_once '../app/config.php';

$db = new MyDB();
if (!$db) {
    echo $db->lastErrorMsg();
}

function masterLoop($db)
{
    $mainTickerFile = fopen("../tickerMaster.txt", "r");
    while (!feof($mainTickerFile)) {
        $companyTicker = fgets($mainTickerFile);
        $companyTicker = trim($companyTicker);

        if (!empty($companyTicker)) {
            $nextDayIncrease = 0;
            $nextDayDecrease = 0;
            $nextDayNoChange = 0;
            $total = 0;

            $sumOfIncreases = 0;
            $sumOfDecreases = 0;

            $sql = "SELECT date, amount_change, percent_change FROM " . $companyTicker . " WHERE percent_change > 0 ORDER BY date ASC";
            $ret = $db->query($sql);
            $multiArray = array();
            $count = 0;
            while ($row = $ret->fetchArray(SQLITE3_ASSOC)) {
                foreach ($row as $i=>$value) {
                    $multiArray[$count][$i] = $value;
                }
                $count++;
            }
            foreach ($multiArray as $data) {
                $date = $data['date'];
                $amount_change = $data['amount_change'];
                $percent_change = $data['percent_change'];
                $sql2 = "SELECT date, amount_change, percent_change FROM " . $companyTicker . " WHERE date > '" . $date . "' ORDER BY date ASC LIMIT 1";
                $ret2 = $db->query($sql2);
                $row2 = $ret2->fetchArray(SQLITE3_ASSOC);
                $tom_date = $row2['date'];
                $tom_amount_change = $row2['amount_change'];
                $tom_percent_change = $row2['percent_change'];
                if ($tom_percent_change > 0) {
                    $nextDayIncrease++;
                    $sumOfIncreases += $amount_change;
                } elseif ($tom_percent_change < 0) {
                    $nextDayDecrease++;
                    $sumOfDecreases += $amount_change;
                } else {
                    $nextDayNoChange++;
                }
                $total++;
            }

            $nextDayIncreasePercent = ($nextDayIncrease/$total)*100;
            $nextDayDecreasePercent = ($nextDayDecrease/$total)*100;
            $averageIncrease = $sumOfIncreases/$nextDayIncrease;
            $averageDecrease = $sumOfDecreases/$nextDayDecrease;

            insertIntoResultTable($db, $companyTicker, $nextDayIncrease, $nextDayIncreasePercent, $averageIncrease, $nextDayDecrease, $nextDayDecreasePercent, $averageDecrease);
        }
    }
}

function insertIntoResultTable($db, $companyTicker, $nextDayIncrease, $nextDayIncreasePercent, $averageIncrease, $nextDayDecrease, $nextDayDecreasePercent, $averageDecrease)
{
    $buyValue = $nextDayIncreasePercent * $averageIncrease;
    $sellValue = $nextDayDecreasePercent * $averageDecrease;
    $createTableQuery = "CREATE TABLE IF NOT EXISTS analysisA (
        companyTicker TEXT PRIMARY KEY NOT NULL,
        nextDayIncrease INT,
        nextDayIncreasePercent FLOAT,
        averageIncrease FLOAT,
        nextDayDecrease INT,
        nextDayDecreasePercent FLOAT,
        averageDecrease FLOAT,
        buyValue FLOAT,
        sellValue FLOAT
    )";
    $db->query($createTableQuery);
    $insertQuery = $db->prepare("INSERT OR REPLACE INTO analysisA (companyTicker, nextDayIncrease, nextDayIncreasePercent, averageIncrease, nextDayDecrease, nextDayDecreasePercent, averageDecrease, buyValue, sellValue) VALUES (
        :companyTicker, :nextDayIncrease, :nextDayIncreasePercent, :averageIncrease, :nextDayDecrease, :nextDayDecreasePercent, :averageDecrease, :buyValue, :sellValue
    )");
    $insertQuery->bindValue(':companyTicker', $companyTicker, SQLITE3_TEXT);
    $insertQuery->bindValue(':nextDayIncrease', $nextDayIncrease, SQLITE3_INTEGER);
    $insertQuery->bindValue(':nextDayIncreasePercent', $nextDayIncreasePercent, SQLITE3_FLOAT);
    $insertQuery->bindValue(':averageIncrease', $averageIncrease, SQLITE3_FLOAT);
    $insertQuery->bindValue(':nextDayDecrease', $nextDayDecrease, SQLITE3_INTEGER);
    $insertQuery->bindValue(':nextDayDecreasePercent', $nextDayDecreasePercent, SQLITE3_FLOAT);
    $insertQuery->bindValue(':averageDecrease', $averageDecrease, SQLITE3_FLOAT);
    $insertQuery->bindValue(':buyValue', $buyValue, SQLITE3_FLOAT);
    $insertQuery->bindValue(':sellValue', $sellValue, SQLITE3_FLOAT);
    $insertQuery->execute();
}

masterLoop($db);
?>

<?php include_once APP_ROOT . '/templates/header.php'; ?>
        <link rel="shortcut icon" href="../app/images/favicon.ico" type="image/x-icon">
        <title>Analysis A</title>
    </head>
    <body>
        <div>
            <h4>Analysis A has been performed on the available stocks!</h4>
        </div>
<?php include_once APP_ROOT . '/templates/footer.php'; ?>
