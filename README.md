# Manajemen Alumni API

A robust backend API for managing alumni data, built using Laravel. This project is designed for organizations or educational institutions to efficiently track, manage, and interact with their alumni network.

## Features

- Alumni registration and authentication
- Profile management for alumni
- Data migration and seeding for easy setup
- RESTful API endpoints for integration with frontend or mobile apps
- Secure, scalable, and easy to deploy

## Getting Started

Follow these steps to set up the project locally.

### Prerequisites

- PHP 8.0 or later
- Composer
- MySQL or any supported database
- [Laravel](https://laravel.com/) (Framework)

### Installation

1. **Clone the repository**
    ```bash
    git clone https://github.com/Fyrmansyah/manajemen-alumni-api.git
    cd manajemen-alumni-api
    ```

2. **Install dependencies**
    ```bash
    composer install
    ```

3. **Set up environment file**
    - Copy `.env.example` to `.env` and adjust any environment variables as needed (such as database credentials):
      ```bash
      cp .env.example .env
      ```

4. **Generate application key**
    ```bash
    php artisan key:generate
    ```

5. **Run migrations and seed the database**
    ```bash
    php artisan migrate --seed
    ```

6. **Serve the application locally**
    ```bash
    php artisan serve
    ```

## API Documentation

You can interact with the API using tools like Postman or Insomnia. For detailed API routes and usage, please refer to the [routes/api.php](routes/api.php) file or generate documentation using Laravel API tools.

## Contributing

Contributions are welcome! Please open an issue or submit a pull request for any enhancements or bug fixes.

## License

This project is open-sourced under the MIT License.

---

**Maintainer:** [Fyrmansyah](https://github.com/Fyrmansyah)
