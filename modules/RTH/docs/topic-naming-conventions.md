# RTH Topic & Queue Naming Conventions

This document outlines the naming conventions for topics and queues used by the ARCA.RTH (Real-Time Hub).

## Topic Naming

Topics are named using the following pattern:

`rth.<source>.<entity>.<action>`

- `rth`: The top-level namespace for all RTH topics.
- `<source>`: The source of the event (e.g., `pos`, `sd`, `mm`, `crm`).
- `<entity>`: The entity the event relates to (e.g., `events`, `sale`).
- `<action>`: The action that occurred (e.g., `completed`, `created`).

### Example Topics

- `rth.pos.events`: Raw events from POS terminals.
- `rth.sd.events`: Raw events from the SD module.
- `rth.mm.events`: Raw events from the MM module.
- `rth.loyalty.events`: Raw events related to loyalty from the CRM module.
- `rth.audit.events`: Events for auditing purposes.

## Downstream Event Topics

Events that have been normalized and are ready for consumption by other modules are published to topics with the following pattern:

`<target>.<entity>.<action>`

- `<target>`: The target module (e.g., `inventory`, `journal`, `loyalty`).
- `<entity>`: The entity the event relates to (e.g., `adjust`, `post`, `update`).
- `<action>`: The action to be performed (e.g., `create`).

### Example Downstream Topics

- `inventory.adjust`: For adjusting inventory in the MM module.
- `journal.post`: For posting a journal entry in the FINA module.
- `loyalty.update`: For updating loyalty points in the CRM module.
- `order.update`: For updating an order in the SD module.
- `analytics.ingest`: For ingesting events into the analytics pipeline.

## Queue Naming

Queues are named using the following pattern:

`rth.<consumer>.<source>.<entity>`

- `rth`: The top-level namespace for all RTH queues.
- `<consumer>`: The consumer of the queue (e.g., `normalization`, `routing`).
- `<source>`: The source of the event (e.g., `pos`, `sd`).
- `<entity>`: The entity the event relates to (e.g., `events`).

### Example Queues

- `rth.normalization.pos.events`: The queue for normalizing POS events.
- `rth.routing.fina.events`: The queue for routing events to the FINA module.
- `rth.dlq`: The dead-letter queue for all failed events.