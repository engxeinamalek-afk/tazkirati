# Tathkirati
Laravel API for event ticketing system.

## Event Handling & Queues Workflow

### BookingConfirmed Event
When the **`BookingConfirmed`** event is dispatched, the following listeners are triggered:

* **Queued Listeners (Medium Priority - `default` Queue):**
  * **`MakeQRCode`**, **`SendConfirmationSMS`**, and **`SendConfirmationEmail`** run asynchronously in the background because they are time-consuming operations.
  * Each of these listeners is configured with a **maximum of 3 retry attempts** in case of failure.

* **Immediate Listener (Instant Execution):**
  * **`SendConfirmationNotification`** executes instantly to trigger the notification system.
  * The resulting notifications are handled via the **`default` queue** with **3 retry attempts**, as the underlying notification class itself is queued.


### EventCancelled Event

When the **`EventCancelled`** event is dispatched, all its listeners (**`CacheBack`**, **`SendCancelledEmail`**, **`SendCancelledNotification`**, and **`SendCancelledSMS`**) execute `instantly`. They immediately dispatch background jobs into specific queues to prevent processing bottlenecks (e.g., avoiding sending multiple emails sequentially within the same listener).

The workflow details are as follows:

* **`CacheBack` (High Priority):**
  * Dispatches **`CacheBackJob`** into the **`high` queue** because it is a critical operation.
  * Configured with **3 retry attempts**.
  * On final failure, it automatically sends an alert notification to the system admins.

* **Queued Jobs (Medium Priority - `default` Queue):**
  * **`SendCancelledEmail`** dispatches **`SendCancelledEmailJob`** to handle email delivery in the background.
  * **`SendCancelledSMS`** dispatches **`SendCancellationSmsJob`** to handle SMS sending asynchronously.
  * **`SendCancelledNotification`** dispatches **`EventCancelledNotification`** to queue user notifications.
  * All these jobs run in the background because they are time-consuming.


### Delayed Background Job

* **`ExpirePendingBooking` (High Priority):**
  * Dispatched with a **10-minute delay** immediately after a booking is initiated.
  * Checks if the payment is completed; if not, it automatically releases and cancels the pending reservation.
  * Runs on the **`high` queue** due to its critical role in managing seat inventory.
  * Configured with **3 retry attempts**.
  * On final failure, it logs the error and sends an alert notification to system admins.

### Batch Email Processing (Promotional Campaigns)

* **`SendPromotionalEmail` (Targeted Marketing Job):**
  * **Memory-Efficient Data Chunking:** Iterates over all users who have existing bookings by loading records in chunks using Laravel's `chunkById()`. This prevents memory exhaustion and optimizes database query performance.
  * **Dedicated Queue Routing:** Dispatched exclusively onto the **`emails` queue** to isolate heavy email delivery traffic and prevent it from bottlenecking other system-critical workflows.
  * **Fault Tolerance:** Configured with a **maximum of 2 retry attempts** to handle temporary mail server failures gracefully.

## Queue Behavior on Code Updates

Queue workers load code into memory. When adding a new listener or updating a job, they will **not** recognize the changes automatically.

To apply updates, you must restart the queue: `php artisan queue:restart`


## Queue Management (Redis, Horizon & Commands)

This project uses **Redis** as the queue driver and **Laravel Horizon** for monitoring.

### 1. Setup
Set the queue connection in your `.env` file:   `QUEUE_CONNECTION=redis`
Run Horizon to start processing jobs:   `php artisan horizon`

### 2. Essential Commands
* **List failed jobs:** `php artisan queue:failed`
* **Retry all failed jobs:** `php artisan queue:retry all`
* **Retry specific job:** `php artisan queue:retry [job_id]`
* **Clear all failed jobs:** `php artisan queue:flush`
* **Clear a specific queue (Horizon):** `php artisan horizon:clear`

> **Tip for Testing Failures:** 
> To test how the system handles failed jobs and retries for emails or SMS, switch these variables to `true` in your `.env` file:
> ```env
> MAIL_SIMULATE_FAILURE=true
> SMS_SIMULATE_FAILURE=true
> ```








