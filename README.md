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
  - Only charges events within the specified date range
  - Handles multiple events per user correctly
- User event tracking:
  - Registration dates
  - Activation dates
  - Appointment dates
- Comprehensive metrics:
  - Total users
  - Active users
  - Registered users
  - Appointment users
- Token-based authentication
- Detailed invoice reporting

## Event Pricing

- Registration: 100 SAR
- Activation: 50 SAR
- Appointment: 200 SAR

*Note: Only the most expensive event per user is charged within a billing period.*

## API Endpoints

### Create Invoice
```http
POST /api/v1/invoices
Authorization: Bearer your-api-token
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
    "message": "Invoice created successfully",
    "invoice_id": 1
}
```

### Get Invoice Details
```http
GET /api/v1/invoices/{invoice_id}
Authorization: Bearer your-api-token
```

**Response:**
```json
{
    "invoice_id": 1,
    "customer_id": 1,
    "period": {
        "start": "2024-01-01T00:00:00.000000Z",
        "end": "2024-01-31T23:59:59.999999Z"
    },
    "events": [
        {
            "type": "registration",
            "date": "2024-01-15T10:00:00.000000Z",
            "price": "100.00",
            "user_id": 1
        }
    ],
    "event_frequency": {
        "registration": 1,
        "activation": 0,
        "appointment": 0
    },
    "price_per_event": {
        "registration": 100,
        "activation": 50,
        "appointment": 200
    },
    "total_amount": "100.00",
    "user_metrics": {
        "total_users": 1,
        "active_users": 0,
        "registered_users": 1,
        "appointment_users": 0
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
git clone git@github.com:ahmeddhamdallah/invoicing-api.git
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

The seeder will:
- Create a test customer
- Create test users with various scenarios
- Generate and display your API token
- Show test scenarios and expected charges

## Testing

The API includes comprehensive test coverage. To run the tests:

```bash
php artisan test
```

This will run:
- Unit tests for the invoice service
- Unit tests for the token guard
- Tests for all billing scenarios:
  - Registration only
  - Activation only
  - Appointment only
  - Multiple events (highest price charged)
  - Events outside billing period

## Example Scenarios

The test data includes several scenarios:

1. User A: Registered before period, activated during period
   - Should be charged 50 SAR for activation
   - Previous registration not charged (outside period)

2. User B: Registered before period, made appointment during period
   - Should be charged 200 SAR for appointment
   - Previous registration not charged (outside period)

3. User C: Multiple events during period
   - Registration (100 SAR)
   - Activation (50 SAR)
   - Appointment (200 SAR)
   - Should be charged 200 SAR (highest price only)

4. User D: All events before period
   - Should not be charged (0 SAR)
   - All events outside billing period

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
