# ARCA GRC Module Integration Guide

This document provides instructions for integrating other ARCA modules with the Governance, Risk & Compliance (GRC) module.

## Emitting Auditable Events

To ensure that user actions are properly audited, your module should dispatch a `UserActionEvent` whenever a critical action is performed. This event should be dispatched after the action has been successfully completed.

### Example

```php
use Modules\GRC\SharedKernel\Events\UserActionEvent;

// ...

// After creating a new customer in the CRM module
event(new UserActionEvent(
    auth()->id(),
    'CRM',
    'create',
    $customer,
    $customer->toArray()
));
```

## Checking Policies

Before performing a potentially sensitive transaction, your module should check with the GRC module to ensure that the transaction does not violate any Segregation of Duties (SoD) policies.

### Example

```php
use Illuminate\Support\Facades\Http;

// ...

$response = Http::post('api/grc/check-policy', [
    'user_id' => auth()->id(),
    'action' => 'approve_invoice',
    'entity' => $invoice->toArray(),
]);

if ($response->json('result') === 'block') {
    // Block the transaction
}
```

## Managing Consent

To check if a customer has provided consent for a specific purpose, you can query the `/api/grc/consents` endpoint.

### Example

```php
use Illuminate\Support\Facades\Http;

// ...

$response = Http::get('api/grc/consents', [
    'customer_id' => $customer->id,
    'purpose' => 'marketing_emails',
]);

if ($response->json('granted')) {
    // Send marketing email
}
```