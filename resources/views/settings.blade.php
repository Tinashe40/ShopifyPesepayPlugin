<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pesepay Shopify Settings</title>

    <!-- Bootstrap for styling -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body { background-color: #f8f9fa; }
        .container { max-width: 600px; margin-top: 50px; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); }
        .form-label { font-weight: bold; }
        .btn-primary { width: 100%; background-color: #007bff; border: none; padding: 12px; font-size: 16px; border-radius: 5px; }
        .btn-primary:hover { background-color: #0056b3; }
        .alert { margin-bottom: 20px; }
    </style>
</head>
<body>

    <div class="container">
        <h2 class="text-center mb-4">Pesepay Shopify Settings</h2>

        <!-- Success Message -->
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <!-- Error Message -->
        @if($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('settings.save') }}" method="POST">
            @csrf
            <input type="hidden" name="shopify_store" value="{{ $shopifyStore }}">

            <div class="mb-3">
                <label class="form-label">PesePay Integration Key</label>
                <input type="text" name="pesepay_integration_key" class="form-control"
                       value="{{ $merchant->pesepay_integration_key ?? '' }}" required>
            </div>

            <div class="mb-3">
                <label class="form-label">PesePay Encryption Key</label>
                <input type="text" name="pesepay_encryption_key" class="form-control"
                       value="{{ $merchant->pesepay_encryption_key ?? '' }}" required>
            </div>

            <button type="submit" class="btn btn-primary">Save Settings</button>
        </form>
    </div>

</body>
</html>
