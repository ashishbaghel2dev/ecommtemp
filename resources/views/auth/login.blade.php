<div class="login-container">
    <div class="login-box glass-panel">
        <div class="login-header">
            <h2>Welcome Back</h2>
            <p>Login to manage your dashboard</p>
        </div>

        <!-- Session Errors -->
        @if ($errors->any())
            <div class="alert alert-danger">
                {{ $errors->first() }}
            </div>
        @endif


        <form action="{{ route('login') }}" method="POST" class="login-form">
            @csrf
            @method('post')
            <div class="input-group">
                <label>Email Address</label>
                <input type="email" name="email" placeholder="ashish@example.com" required value="{{ old('email') }}">
            </div>

            <div class="input-group">
                <label>Password</label>
                <input type="password" name="password" placeholder="••••••••" required>
            </div>

            <button type="submit" class="btn-login btn-primary">Login In</button>
        </form>

        <div class="separator">
            <span>OR</span>
        </div>

        <!-- Google Social Login Button -->
        <a href="{{ route('auth.google') }}" class="btn-social btn-google">
            <img src="https://upload.wikimedia.org/wikipedia/commons/c/c1/Google_%22G%22_logo.svg" alt="Google Logo">
            Continue with Google
        </a>

        <div class="login-footer">
            <p>Don't have an account? <a href="#">Create one</a></p>
        </div>
    </div>
</div>
<style>
    /* Container styling */
.login-container {
    height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
}

/* Glassmorphism Box */
.glass-panel {
    background: rgba(255, 255, 255, 0.8);
    backdrop-filter: blur(12px);
    border: 1px solid rgba(255, 255, 255, 0.3);
    padding: 2.5rem;
    border-radius: 20px;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
    width: 100%;
    max-width: 400px;
}

.login-header h2 {
    font-size: 1.8rem;
    color: #1e272e;
    margin-bottom: 0.5rem;
}

.input-group {
    margin-bottom: 1.2rem;
}

.input-group label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 500;
    color: #485460;
}

.input-group input {
    width: 100%;
    padding: 12px;
    border: 1px solid #d2dae2;
    border-radius: 10px;
    outline: none;
    transition: 0.3s;
}

.input-group input:focus {
    border-color: var(--primary-color, #3490dc);
    box-shadow: 0 0 0 3px rgba(52, 144, 220, 0.1);
}

/* Primary Button */
.btn-login {
    width: 100%;
    padding: 12px;
    border: none;
    border-radius: 10px;
    font-weight: 600;
    cursor: pointer;
    background: #3490dc;
    color: white;
    transition: 0.3s;
}

.btn-login:hover {
    background: #2779bd;
    transform: translateY(-2px);
}

/* Google Button Styling */
.btn-social {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    width: 100%;
    padding: 10px;
    border: 1px solid #d2dae2;
    border-radius: 10px;
    text-decoration: none;
    color: #1e272e;
    font-weight: 500;
    transition: 0.3s;
    background: white;
}

.btn-social:hover {
    background: #f1f2f6;
}

.btn-social img {
    width: 20px;
}

.separator {
    text-align: center;
    margin: 1.5rem 0;
    position: relative;
}

.separator::before {
    content: "";
    position: absolute;
    left: 0;
    top: 50%;
    width: 45%;
    height: 1px;
    background: #d2dae2;
}

.separator::after {
    content: "";
    position: absolute;
    right: 0;
    top: 50%;
    width: 45%;
    height: 1px;
    background: #d2dae2;
}
</style>