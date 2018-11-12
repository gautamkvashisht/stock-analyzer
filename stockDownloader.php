<?php

include_once 'app/config.php';
    function createURL($ticker)
    {
        return "https://www.alphavantage.co/query?function=TIME_SERIES_DAILY&symbol=$ticker&outputsize=compact&apikey=Y0GX9HUCJFQRPRAD&datatype=csv";
    }


    function getCSVFile($url, $outputFile)
    {
        $content = file_get_contents($url);
        $content = str_replace("timestamp,open,high,low,close,volume", "", $content);
        $content = trim($content);
        file_put_contents($outputFile, $content);
    }

    function fileToDatabase($txtFile, $tableName)
    {
        $db = new MyDB();
        if (!$db) {
            echo $db->lastErrorMsg();
        }
        $file = fopen($txtFile, "r");
        while (!feof($file)) {
            $line = fgets($file);
            $pieces = explode(",", $line);
            $date = $pieces[0];
            $open = $pieces[1];
            $high = $pieces[2];
            $low = $pieces[3];
            $close = $pieces[4];
            $volume = $pieces[5];
            $amount_change = $close-$open;
            $percent_change = ($amount_change/$open)*100;
            $sql = "CREATE TABLE IF NOT EXISTS " . $tableName . " (
                date TEXT PRIMARY KEY,
                open FLOAT,
                high FLOAT,
                low FLOAT,
                close FLOAT,
                volume INT,
                amount_change FLOAT,
                percent_change FLOAT
            )";
            $db->query($sql);
            $insertQuery = $db->prepare("INSERT OR REPLACE INTO " . $tableName . " (date, open, high, low, close, volume, amount_change, percent_change) VALUES (
                :date, :open, :high, :low, :close, :volume, :amount_change, :percent_change
            )");
            $insertQuery->bindValue(':date', $date, SQLITE3_TEXT);
            $insertQuery->bindValue(':open', $open, SQLITE3_FLOAT);
            $insertQuery->bindValue(':high', $high, SQLITE3_FLOAT);
            $insertQuery->bindValue(':low', $low, SQLITE3_FLOAT);
            $insertQuery->bindValue(':close', $close, SQLITE3_FLOAT);
            $insertQuery->bindValue(':volume', $volume, SQLITE3_INTEGER);
            $insertQuery->bindValue(':amount_change', $amount_change, SQLITE3_FLOAT);
            $insertQuery->bindValue(':percent_change', $percent_change, SQLITE3_FLOAT);
            $insertQuery->execute();
        }
        fclose($file);
    }

function main()
{
    $pathToTickerFile = __DIR__ . "/tickerMaster.txt";
    $mainTickerFile = fopen($pathToTickerFile, "r");
    while (!feof($mainTickerFile)) {
        $ticker = fgets($mainTickerFile);
        $ticker = trim($ticker);
        if ($ticker) {
            $fileURL = createURL($ticker);
            $companyTxtFile = __DIR__ . "/txtFiles/" . $ticker . ".txt";
            getCSVFile($fileURL, $companyTxtFile);
            fileToDatabase($companyTxtFile, $ticker);
        }
    }
    fclose($mainTickerFile);
    echo "The stocks have been downloaded!";
}

main();

?>

<?php include_once APP_ROOT . '/templates/header.php'; ?>
        <link rel="shortcut icon" href="app/images/favicon.ico" type="image/x-icon">
        <title>Stock Downloader</title>
    </head>
    <body>
<?php include_once APP_ROOT . '/templates/footer.php'; ?>
