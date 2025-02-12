<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PesePay Shopify Settings</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="card">
            <div class="card-header">
                <h3>PesePay Shopify Plugin Settings</h3>
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                <form action="{{ url('/shopify/settings/save') }}" method="POST">
                    @csrf
                    <input type="hidden" name="shopify_store" value="{{ $shopifyStore }}">

                    <div class="mb-3">
                        <label class="form-label">PesePay Integration Key</label>
                        <input type="text" name="pesepay_integration_key" class="form-control" value="{{ $merchant->pesepay_integration_key ?? '' }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">PesePay Encryption Key</label>
                        <input type="text" name="pesepay_encryption_key" class="form-control" value="{{ $merchant->pesepay_encryption_key ?? '' }}" required>
                    </div>

                    <button type="submit" class="btn btn-primary">Save Settings</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
