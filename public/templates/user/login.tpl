<!DOCTYPE html>
<html>
<head>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <title>Login</title>
</head>
<body>
<div class="auth-container">
    <div class="auth-header">
        <h1 class="auth-title">Вход</h1>
    </div>

    {% if error %}
    <div class="error-message" style="text-align: center; margin-bottom: 1rem;">{{ error }}</div>
    {% endif %}

    <form class="auth-form" id="login-form" action="/bookstore/public/login/post" method="post">
        <div class="form-group">
            <label class="form-label" for="login-username">Имя</label>
            <input class="form-input" type="text" id="login-username" name="username" required value="{{ username ?? '' }}">
            <div class="error-message" id="login-username-error"></div>
        </div>

        <div class="form-group">
            <label class="form-label" for="login-password">Пароль</label>
            <input class="form-input" type="password" id="login-password" name="password" required>
            <div class="error-message" id="login-password-error"></div>
        </div>

        <div class="form-group">
            <div class="g-recaptcha" data-sitekey="6Ldm4UgrAAAAAGmMreNtBk6TK_-NbNDC7mEGwjLn"></div>
            <input type="hidden" name="g-recaptcha-response" id="login-g-recaptcha-response">
            <div class="error-message" id="login-captcha-error"></div>
        </div>

        <div class="form-group">
            <label>
                <input type="checkbox" name="remember_me"> Запомнить меня
            </label>
        </div>

        <button type="submit" class="auth-btn">Login</button>

        <div class="auth-footer">
            Ещё нет аккаунта?<a class="auth-link" href="/bookstore/public/register">Регистрация</a>
        </div>
    </form>
</div>
</body>
</html>
