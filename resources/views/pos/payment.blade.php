@extends('layouts.master')

@section('title', __('messages.payment.page_title'))

@section('content')
@include('layouts.partials.sweet_alert')

<div class="container vh-100 d-flex align-items-center justify-content-center">
    <div class="col-md-6 col-lg-5">
        <div class="card shadow-lg rounded-4 p-4">
            <div class="card-body">
                <h2 class="card-title text-center mb-3">{{ __('messages.payment.page_title') }}</h2>
                <h4 class="text-center text-muted mb-4">
                    {{ __('messages.payment.invoice_number') }}: <span class="text-dark">#{{ $bill->id }}</span>
                </h4>

                <p class="text-center fs-4 mb-4">
                    {{ __('messages.payment.amount_due') }}:
                    <strong class="text-success">{{ number_format($bill->net_amount, 2) }} $</strong>
                </p>

                @if(session('error'))
                    <div class="alert alert-danger text-center">{{ session('error') }}</div>
                @endif

                <form action="{{ route('pos.paymentProcess', $bill->id) }}" method="POST" id="payment-form">
                    @csrf
                    <div id="card-element" class="mb-3 border rounded-3 p-3"></div>

                    <button class="btn btn-success w-100 d-flex align-items-center justify-content-center gap-2 fs-5">
                        <i class="bi bi-credit-card-fill fs-4"></i>
                        {{ __('messages.payment.pay_now') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Stripe --}}
<script src="https://js.stripe.com/v3/"></script>
<script>
    let stripe = Stripe("{{ env('STRIPE_KEY') }}");
    let elements = stripe.elements();
    let card = elements.create('card');
    card.mount('#card-element');

    document.getElementById('payment-form').addEventListener('submit', async function(e) {
        e.preventDefault();
        const {token, error} = await stripe.createToken(card);
        if (error) {
            alert('{{ __("messages.payment.card_error") }}' + error.message);
        } else {
            let form = document.getElementById('payment-form');
            let hidden = document.createElement('input');
            hidden.type = 'hidden';
            hidden.name = 'stripeToken';
            hidden.value = token.id;
            form.appendChild(hidden);
            form.submit();
        }
    });
</script>
@endsection
