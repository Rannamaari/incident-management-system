<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In - Incident Management System</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f5f5f5; padding: 50px; margin: 0; }
        .login-form { max-width: 400px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input[type="email"], input[type="password"] { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; }
        button { background: #dc2626; color: white; padding: 12px 20px; border: none; border-radius: 4px; cursor: pointer; width: 100%; font-size: 16px; }
        button:hover { background: #b91c1c; }
        .error { color: red; margin-top: 5px; font-size: 14px; }
        h2 { text-align: center; margin-bottom: 30px; color: #dc2626; }
    </style>
</head>
<body>
    <div class="login-form">
        <h2>Incident Management System</h2>
        
        <form method="POST" action="/login">
            @csrf
            
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus>
                @error('email')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
                @error('password')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="form-group">
                <button type="submit">Sign In</button>
            </div>
        </form>
        
        <div style="text-align: center; margin-top: 20px; padding-top: 20px; border-top: 1px solid #eee; font-size: 12px; color: #999;">
            System developed and designed by Ibrahim Husham Abdullah
        </div>
    </div>
</body>
</html>