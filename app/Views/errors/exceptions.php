<?php

/** @var string $title */
/** @var Throwable $exception */ ?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?= $title ?></title>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
        <!-- System CSS-->
        <link rel="stylesheet" href="/public/css/style.css">

        <style>
            .container {
                position: absolute;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
            }
        </style>
    </head>

    <body class="h-100">
        <div class="container d-flex justify-content-center">
            <div class="card" style="max-width: 400px;">
                <div class="card-body text-center">
                    <h5 class="card-title"><?= $title ?? 'Unknown Error' ?></h5>
                    <p class="card-text"><?= $exception->getMessage() ?></p>
                    <h5 class="card-title">Development Mode</h5>
                    <p>Backtrace:</p>
                    <pre style="font-size: small"><?= $exception->getTraceAsString() ?></pre>
                </div>
            </div>
        </div>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.js"></script>
    </body>
</html>