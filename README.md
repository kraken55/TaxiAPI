# Taxi API

A simple REST API for querying passenger transport vehicles based on suitability for a given trip.

## Overview

This REST API allows users to find suitable taxi vehicles based on the number of passengers and the distance of the trip. It calculates the assumed profit for each suitable vehicle by subtracting refueling costs from the estimated travel fare.


## Tech Stack

* PHP 8.2
* [Laravel](https://laravel.com/) 12
* SQLite
* [Laravel Sail](https://laravel.com/docs/12.x/sail) for streamlined containerization

## Setup and Installation

1.  **Clone the repository:**
    ```bash
    git clone <repository-url>
    cd taxi-api
    ```

2.  **Install Dependencies:**
    ```bash
    composer install && npm install
    ```

3.  **.env Configuration:**
    * Create your local `.env` file and generate an application key and empty database
        ```bash
        cp .env.example .env
        php artisan key:generate
        touch database/database.sqlite
        ```


4.  **Run Database Migrations and Seeders:**
    ```bash
    php artisan migrate:fresh --seed
    ```

5.  **Run the Development Server:**
    ```bash
    php artisan serve
    ```

6. **Optionally: use Laravel Sail for containerization:**
    ```bash
    ./vendor/bin/sail up -d  
    ```

## API Endpoints

All endpoints are prefixed with `/api`.

### Vehicles

* **`GET /vehicles`**
    * Functionality: Retrieves a list of all vehicles.
    * Response: `200 OK` with a JSON array of vehicles.

* **`POST /vehicles`**
    * Functionality: Creates a new vehicle.
    * Request Body (JSON):
        ```json
        {
            "passenger_capacity": 4,
            "range": 500,
            "fuel_type_id": 1
        }
        ```
    * Response: `201 Created` with the created vehicle data.

* **`GET /vehicles/{id}`**
    * Functionality: Retrieves a specific vehicle by its ID.
    * Response: `200 OK` with the vehicle data, or `404 Not Found`.

* **`PATCH /vehicles/{id}`** (or `PUT`)
    * Functionality: Updates an existing vehicle.
    * Request Body (JSON): Fields to update.
        ```json
        {
            "passenger_capacity": 5,
            "range": 550
        }
        ```
    * Response: `200 OK` with the updated vehicle data, or `404 Not Found`.

* **`DELETE /vehicles/{id}`**
    * Functionality: Deletes a vehicle.
    * Response: `204 No Content`, or `404 Not Found`.

* **`GET /vehicles/suitable`**
    * Functionality: Finds suitable vehicles based on passenger count and travel distance. Returns vehicles sorted by profitability (descending).
    * Request Body (JSON):
        ```json
        {
            "passengers": 5,
            "distance": 550
        }
        ```
    * Example Request: `GET /api/vehicles/suitable?passengers=2&distance=100`
    * Response: `200 OK` with a JSON array of suitable vehicles, including calculated `refueling_cost`, `profit`, and `effective_range`.

## Running Tests

To run the automated tests:
```bash
php artisan test