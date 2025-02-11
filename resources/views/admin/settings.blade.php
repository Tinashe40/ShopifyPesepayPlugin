@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h2>Pesepay Payment Gateway Settings</h2>

    @if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form method="POST" action="{{ url('/settings') }}">
        @csrf

        <div class="form-group">
            <label for="pesepay_integration_key">Pesepay Integration Key</label>
            <input type="text" class="form-control" id="pesepay_integration_key" name="pesepay_integration_key"
                   value="{{ old('pesepay_integration_key', $merchant->pesepay_integration_key ?? '') }}" required>
        </div>

        <div class="form-group">
            <label for="pesepay_encryption_key">Pesepay Encryption Key</label>
            <input type="text" class="form-control" id="pesepay_encryption_key" name="pesepay_encryption_key"
                   value="{{ old('pesepay_encryption_key', $merchant->pesepay_encryption_key ?? '') }}" required>
        </div>

        <button type="submit" class="btn btn-primary mt-3">Save Settings</button>
    </form>
</div>
@endsection
