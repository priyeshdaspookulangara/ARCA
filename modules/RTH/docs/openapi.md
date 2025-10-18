# RTH API OpenAPI Specification

## Version: 1.0.0

### Introduction

This document provides the OpenAPI specification for the ARCA.RTH (Real-Time Hub) REST API.

### Servers

- `/`

### Paths

#### /rth/v1/events

##### POST

**Summary:** Ingest a new event into the Real-Time Hub.

**Request Body:**

- **Content Type:** `application/json`
- **Schema:** `RTHEvent`

**Responses:**

- **202 Accepted:** The event has been accepted for processing. The response body will contain the `rth_event_id`.
- **400 Bad Request:** The request is malformed or fails validation.
- **401 Unauthorized:** The client is not authorized to publish events.

#### /rth/v1/events/{rth_event_id}

##### GET

**Summary:** Fetch the status and processing log of a specific event.

**Parameters:**

- `rth_event_id` (path, string, required): The unique identifier of the RTH event.

**Responses:**

- **200 OK:** Returns the event details.
- **404 Not Found:** The event with the specified ID was not found.

#### /rth/v1/events/{rth_event_id}/replay

##### POST

**Summary:** Replay a specific event.

**Parameters:**

- `rth_event_id` (path, string, required): The unique identifier of the RTH event.

**Responses:**

- **202 Accepted:** The event has been queued for replay.
- **404 Not Found:** The event with the specified ID was not found.

#### /rth/v1/dlq

##### GET

**Summary:** List messages in the Dead-Letter Queue (DLQ).

**Responses:**

- **200 OK:** Returns a list of DLQ messages.

#### /rth/v1/mappings

##### POST

**Summary:** Update a routing or account mapping.

**Request Body:**

- **Content Type:** `application/json`
- **Schema:** `Mapping`

**Responses:**

- **200 OK:** The mapping was successfully updated.
- **400 Bad Request:** The request is malformed.

#### /rth/v1/health

##### GET

**Summary:** Get the health status of the RTH components.

**Responses:**

- **200 OK:** Returns the health status.

### Components

#### Schemas

##### RTHEvent

(See `canonical-event-schema.md` for details)

##### Mapping

- `id` (integer)
- `mapping_key` (string)
- `source` (string)
- `target` (string)
- `config_json` (object)