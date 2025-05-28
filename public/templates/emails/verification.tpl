<!DOCTYPE html>
<html>
<head>
    <title>Подтверждение email</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .button {
            display: inline-block; padding: 10px 20px;
            background-color: #4CAF50; color: white;
            text-decoration: none; border-radius: 5px;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>Подтвердите ваш email</h1>
    <p>Для завершения регистрации на Bookstore, пожалуйста, подтвердите ваш email:</p>
    <p><a href="{{verification_link}}" class="button">Подтвердить email</a></p>
    <p>Если вы не регистрировались на нашем сайте, просто проигнорируйте это письмо.</p>
</div>
</body>
</html>