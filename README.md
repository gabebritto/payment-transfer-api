# Backend Wallet Transfer

Implemented with **Laravel**, **Docker**, **MySQL**, and **Redis**. It handles user registration, wallet management, and secure money transfers between users and retailers.

## Technologies

- **PHP 8.2**
- **Laravel 11**
- **MySQL 8.0**
- **Redis**
- **Nginx**
- **Docker & Docker Compose**

## Features

### User Types
- **Common User**: Can send and receive money.
- **Retailer**: Can only receive money.

### Wallet & Transfers
- **Wallet**: Each user has a wallet with a balance.
- **Transfer**: Secure money transfer between wallets.
    - **Validation**: Checks for sufficient balance and valid payer/payee.
    - **Authorization**: Consults an external mock service to authorize the transfer.
    - **Transaction**: Uses database transactions (ACID) to ensure data integrity.
    - **Notification**: Asynchronously notifies the payee via an external mock service (Email/SMS).

### Observability
- **Structured Logging**: JSON logs output to `stderr` for easy ingestion.
- **Request Tracing**: Every request is assigned a unique `X-Request-ID`, which is included in all logs and the response header.
- **Exception Logging**: Global exception handler logs errors with full context (User ID, Request ID, Stack Trace).

## Installation & Setup

### Prerequisites
- Docker and Docker Compose installed.

### Steps

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd payment-transfer-api
   ```

2. **Configure Environment**
   ```bash
   cp .env.example .env
   ```
   *Note: The `.env.example` is already configured for the Docker environment.*

3. **Start Containers**
   ```bash
   docker-compose up -d --build
   ```

4. **Install Dependencies**
   ```bash
   docker-compose exec app composer install
   ```

5. **Run Migrations**
   ```bash
   docker-compose exec app php artisan migrate
   ```

## API Documentation

The API enforces `application/json` responses.

Auto-generated API documentation is available at `/docs/api`.

## Testing

To run the automated test suite:

```bash
docker-compose exec app php artisan test
```
