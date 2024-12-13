# Invoicing API

A Laravel-based REST API for managing customer invoices based on user events. The system calculates charges based on the most expensive event per user within a billing period.

## Features

- Create invoices for customers based on user events
- Calculate invoice amounts based on different event types:
  - Registration: 100 SAR
  - Activation: 50 SAR
  - Appointment: 200 SAR
- Intelligent billing logic:
  - Charges only the most expensive event per user
  - Only charges the difference if user was previously charged for a less expensive event
  - Considers events within the specified date range
- Detailed session tracking:
  - Records all user sessions within the billing period
  - Tracks both activation and appointment dates
  - Provides comprehensive user activity history
- Event-based pricing for user activities
- Session tracking and user metrics
- Comprehensive invoice reporting
- Token-based authentication

## Event Pricing

- Registration: 100 SAR
- Activation: 50 SAR
- Appointment: 200 SAR

*Note: Only the most expensive event per user is charged within a billing period.*

## API Endpoints

### Create Invoice
```http
POST /api/v1/invoices
```

**Request Body:**
```json
{
    "customer_id": 1,
    "start_date": "2024-01-01",
    "end_date": "2024-01-31"
}
```

**Response:**
```json
{
    "id": 1,
    "customer_id": 1,
    "start_date": "2024-01-01T00:00:00.000000Z",
    "end_date": "2024-01-31T23:59:59.999999Z",
    "total_amount": 550,
    "total_users": 5,
    "active_users": 3,
    "registered_users": 4,
    "appointment_users": 2,
    "created_at": "2024-12-13T10:50:16.000000Z",
    "updated_at": "2024-12-13T10:50:16.000000Z"
}
```

### Get Invoice
```http
GET /api/v1/invoices/{invoice_id}
```

**Response:**
```json
{
    "invoice_id": 1,
    "customer_id": 1,
    "period": {
        "start": "2021-01-01T00:00:00.000000Z",
        "end": "2021-02-01T23:59:59.000000Z"
    },
    "events": [
        {
            "type": "activation",
            "date": "2021-01-15T10:00:00.000000Z",
            "price": "50.00",
            "user_id": 1
        },
        {
            "type": "appointment",
            "date": "2021-01-15T10:00:00.000000Z",
            "price": "200.00",
            "user_id": 2
        }
    ],
    "event_frequency": {
        "activation": 1,
        "appointment": 1
    },
    "price_per_event": {
        "registration": 100,
        "activation": 50,
        "appointment": 200
    },
    "total_amount": "250.00",
    "user_metrics": {
        "total_users": 2,
        "active_users": 1,
        "registered_users": 0,
        "appointment_users": 1
    }
}
```

## Authentication

The API uses token-based authentication. Include your API token in the request headers:

```http
Authorization: Bearer your-api-token
```

## Error Responses

```json
{
    "error": "Error message",
    "code": "ERROR_CODE"
}
```

Common error codes:
- `INVALID_DATES`: Invalid date range provided
- `CUSTOMER_NOT_FOUND`: Customer ID does not exist
- `UNAUTHORIZED`: Invalid or missing API token

## Installation

1. Clone the repository:
```bash
git clone [repository-url]
cd invoicing-api
```

2. Install dependencies:
```bash
composer install
```

3. Set up environment:
```bash
cp .env.example .env
php artisan key:generate
```

4. Configure database in `.env`:
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=invoicing
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

5. Run migrations and seed test data:
```bash
php artisan migrate:fresh --seed --seeder=TestDataSeeder
```

This command will:
- Set up the database tables
- Create a test customer
- Create test users with various scenarios
- Generate and display your API token
- Show test scenarios and expected charges

The seeder output will show:
```
Test data seeded successfully!
Customer ID: 1
API Token: your-api-token

Test scenarios for period 2021-01-01 to 2021-02-01:
User A (ID: 1): Should be charged 50 SAR
  - Registered before period (2020-12-01)
  - Activated in period (2021-01-15, 2021-01-18)

User B (ID: 2): Should be charged 200 SAR
  - Registered before period (2020-12-15)
  - Made appointment in period (2021-01-15)

User C (ID: 3): Should be charged 100 SAR
  - Registered in period (2021-01-01)
  - Activated in period (2021-01-10)

User D (ID: 4): Should NOT be charged
  - All events before period
  - Registration (2020-09-01)
  - Activations (2020-10-11, 2020-12-12)
  - Appointment (2020-12-27)
```

Copy the displayed API token to use in your requests.

## Testing

The API includes a comprehensive test suite. To run the tests:

```bash
php artisan test
```

## Postman Collection

A Postman collection is included in the repository for easy API testing. To use it:

1. Import `invoicing-api.postman_collection.json` into Postman
2. Create an environment and set the `base_url` variable (default: `http://localhost:8000`)
3. Set the `api_token` variable to the token displayed during seeding
4. Use the collection to test the API endpoints with example data

## Example Scenarios

The test data seeder (`TestDataSeeder`) includes several example scenarios:

1. User A: Registered before period, activated during period
   - Should be charged 50 SAR for activation
   - Has multiple activation sessions in period

2. User B: Registered before period, made appointment during period
   - Should be charged 200 SAR for appointment
   - Has one appointment session in period

3. User C: Registered and activated during period
   - Should be charged 100 SAR (registration is more expensive than activation)
   - Has both activation and appointment sessions in period

4. User D: All events before period
   - Should not be charged (0 SAR)
   - Has multiple sessions before the period

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
