<!DOCTYPE html>
<html>
<head>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <title>Register</title>
</head>
<body>
<div class="auth-container">
    <div class="auth-header">
        <h1 class="auth-title">Register</h1>
    </div>

    {% if error %}
    <div class="error-message" style="text-align: center; margin-bottom: 1rem;">{{ error }}</div>
    {% endif %}

    <form class="auth-form" id="register-form" action="/bookstore/public/register/post" method="post">
        <div class="form-group">
            <label class="form-label" for="register-name">Username</label>
            <input class="form-input" type="text" id="register-name" name="name" required>
            <div class="error-message" id="register-name-error"></div>
        </div>

        <div class="form-group">
            <label class="form-label" for="register-email">Email</label>
            <input class="form-input" type="email" id="register-email" name="email" required>
            <div class="error-message" id="register-email-error"></div>
        </div>

        <div class="form-group">
            <label class="form-label" for="register-password">Password</label>
            <input class="form-input" type="password" id="register-password" name="password" required>
            <div class="error-message" id="register-password-error"></div>
        </div>

        <div class="form-group">
            <label class="form-label" for="register-confirm-password">Confirm Password</label>
            <input class="form-input" type="password" id="register-confirm-password" name="confirm_password" required>
            <div class="error-message" id="register-confirm-password-error"></div>
        </div>

        <div class="form-group">
            <div class="g-recaptcha" data-sitekey="6Ldm4UgrAAAAAGmMreNtBk6TK_-NbNDC7mEGwjLn"></div>
            <input type="hidden" name="g-recaptcha-response" id="register-g-recaptcha-response">
            <div class="error-message" id="register-captcha-error"></div>
        </div>

        <input type="hidden" name="hashed_password" id="hashed_password">
        <input type="hidden" name="salt" id="salt">

        <button type="submit" class="auth-btn">Register</button>

        <div class="auth-footer">
            Already have an account? <a class="auth-link" href="/bookstore/public/login">Login</a>
        </div>
    </form>
</div>
</body>
</html>
