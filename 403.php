<?php
// Set HTTP 403 status code
http_response_code(403);

// Start session if not already started
if (!isset($_SESSION)) {
    session_start();
}

// Include required files for siteUrl function
require_once('lib/globals.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 - Access Forbidden</title>
    <link href="<?php echo siteUrl('assets/css/globalstyle.css')?>" type="text/css" rel="stylesheet">
    <style type="text/css">
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
        }
        .error-container {
            text-align: center;
            padding: 100px 20px;
            min-height: 500px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }
        .error-code {
            font-size: 150px;
            font-weight: bold;
            color: #f44336;
            margin: 0;
            line-height: 1;
        }
        .error-title {
            font-size: 36px;
            color: #333;
            margin: 20px 0;
        }
        .error-message {
            font-size: 18px;
            color: #666;
            margin: 20px 0;
            max-width: 600px;
            line-height: 1.6;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-code">403</div>
        <h1 class="error-title">Access Forbidden</h1>
        <p class="error-message">
            You don't have permission to access this resource.<br>
            Please contact your administrator if you believe this is an error.
        </p>
    </div>
</body>
</html>
