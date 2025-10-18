# RTH Canonical Event Schema

All events processed by the Real-Time Hub (RTH) must conform to the following JSON structure.

## Top-Level Structure

| Field      | Type   | Description                               |
|------------|--------|-------------------------------------------|
| `rth_meta` | object | Metadata for tracing and idempotency.     |
| `type`     | string | The type of event (e.g., `pos.sale.completed`). |
| `payload`  | object | The event-specific data.                  |

## `rth_meta` Object

| Field             | Type   | Description                                                      |
|-------------------|--------|------------------------------------------------------------------|
| `event_id`        | string | A unique identifier (UUID v4) for the event.                     |
| `source`          | string | The source of the event (e.g., `POS`, `SD`, `MM`, `CRM`).          |
| `source_id`       | string | The unique identifier of the event from the source system.       |
| `created_at`      | string | The timestamp (ISO 8601) when the event was created.             |
| `trace_id`        | string | A unique identifier for tracing the event across systems.        |
| `idempotency_key` | string | A unique key to prevent duplicate processing of the same event.  |

## Example Event (`pos.sale.completed`)

```json
{
  "rth_meta": {
    "event_id": "uuid-v4",
    "source": "POS",
    "source_id": "terminal-42-invoice-1001",
    "created_at": "2025-10-18T10:15:00Z",
    "trace_id": "trace-abc-123",
    "idempotency_key": "pos-42-invoice-1001"
  },
  "type": "pos.sale.completed",
  "payload": {
    "sale_id": 1001,
    "invoice_no": "POS/2025/1001",
    "terminal_id": 42,
    "branch_id": "BR-01",
    "items": [
      {
        "material_id": 555,
        "sku": "SKU123",
        "qty": 2,
        "unit_price": 150.00,
        "cost_price": 90.00
      }
    ],
    "total_amount": 300.00,
    "tax_amount": 18.00,
    "discount_amount": 0.00,
    "net_amount": 318.00,
    "payments": [
      {
        "mode": "card",
        "amount": 318.00,
        "reference": "txn-xyz"
      }
    ],
    "customer_id": 345
  }
}
```

## Core Event Types

- `pos.sale.completed`
- `pos.sale.returned`
- `pos.shift.closed`
- `sd.order.created`
- `sd.delivery.completed`
- `mm.stock.adjusted`
- `mm.goods.receipt`
- `crm.loyalty.earned`
- `crm.loyalty.redeemed`
- `fina.journal.posted`
- `rth.health.check`
- `rth.replay.requested`