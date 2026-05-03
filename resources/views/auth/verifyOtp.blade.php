<!DOCTYPE html>
<html>
<head>
    <title>Verify OTP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5">
    <div class="col-md-5 mx-auto">

        <div class="card">
            <div class="card-header text-center">
                <h4>Verify OTP</h4>
            </div>

            <div class="card-body">

                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form method="POST" action="{{ route('otp.verify') }}">
                    @csrf

                    <input type="hidden" name="user_id" value="{{ $user->id }}">

                    <div class="mb-3">
                        <label>Enter OTP</label>
                        <input type="text" name="otp" class="form-control" required>
                    </div>

                    <button class="btn btn-primary w-100">Verify OTP</button>
                </form>

            </div>
        </div>

    </div>
</div>

</body>
</html>
