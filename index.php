<?php
include_once 'app/config.php';
?>
<?php include_once APP_ROOT . '/templates/header.php'; ?>
        <link rel="stylesheet" href="app/css/main.css">
        <title>Stock Market Analyzer</title>
    </head>
    <body>
        <header>
            <h2>Welcome to the Stock Analyzer!</h2>
        </header>
        <img src="app/images/stocks-64.png" alt="Stocks">
        <div id="downloadStocks">
            <h4><a href="stockDownloader.php">Download the stocks</a></h4>
        </div>
<?php include_once APP_ROOT . '/templates/footer.php'; ?>
