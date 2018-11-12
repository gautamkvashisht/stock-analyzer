<?php
include_once 'app/config.php';
?>
<?php include_once APP_ROOT . '/templates/header.php'; ?>
        <link rel="stylesheet" href="app/css/main.css">
        <link rel="shortcut icon" href="app/images/favicon.ico" type="image/x-icon">
        <title>Stock Market Analyzer</title>
    </head>
    <body>
        <header>
            <h2>Welcome to the Stock Analyzer!</h2>
        </header>
        <img src="app/images/stocks-64.png" alt="Stocks">
        <div id="downloadStocks">
            <button class="inverse"><a href="stockDownloader.php">Download the stocks</a></button>
            <button class="inverse"><a href="analysis/analysis_a.php">Analyse the stocks and fetch the results</a></button>
        </div>
<?php include_once APP_ROOT . '/templates/footer.php'; ?>
