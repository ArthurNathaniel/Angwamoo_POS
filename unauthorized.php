<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Unauthorized Access</title>
    <?php include 'cdn.php' ?>
    <link rel="stylesheet" href="./css/base.css">
    <style>
    .unauthorized_page{
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        text-align: center;
    } 
    .unauthorized p{
        margin-block: 15px;
    }
    .unathorized_btn button{
        padding: 15px 45px;
        background-color: #E4002B; 
        border: none;
        border-radius: 10px;
        color: #fff;
        text-transform: uppercase;
        
    }
    </style>
</head>
<body>
    <div class="all">
        <div class="unauthorized_page">
            <div class="logo"></div>
            <div class="unauthorized">
            <h1>Unauthorized Access</h1>
            <p>You do not have permission to access this page.</p>
           <div class="unathorized_btn">
           <a href="login.php">
            <button>Back to Login</button>
            </div>
           </a>
           </div>
        </div>
    </div>
</body>
</html>
