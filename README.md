# Laravel Task Management API

A production-oriented RESTful Task Management API built with **Laravel 12**, **PHP**, **MySQL**, and **Laravel Sanctum**.

The API provides secure user authentication, task management, authorization, validation, pagination, searching, filtering, sorting, rate limiting, API versioning, automated testing, Swagger/OpenAPI documentation, GitHub Actions CI, and Docker-based development.

---

## 🚀 Features

### 🔐 Authentication

* User registration
* User login
* User logout
* Token-based authentication using Laravel Sanctum
* Protected API endpoints
* Token invalidation after logout
* Authentication error handling with JSON responses

### 📋 Task Management

Authenticated users can:

* Create tasks
* View their tasks
* View individual tasks
* Update tasks
* Delete tasks

Each task belongs to a specific user.

### 🔒 Authorization

Users can only access their own tasks.

The API prevents users from:

* Viewing another user's task
* Updating another user's task
* Deleting another user's task

Authorization is handled using Laravel Policies.

### ✅ Validation

The API validates incoming requests using Laravel Form Requests.

Validation includes:

* Required fields
* Email validation
* Unique email addresses
* Task title validation
* Task status validation
* Request validation error responses

### 🔎 Search, Filtering & Sorting

Task listing supports:

* Pagination
* Search
* Filtering
* Sorting

This makes the API suitable for larger task collections and frontend applications.

### 🛡️ Rate Limiting

API endpoints are protected against excessive requests using Laravel rate limiting.

The project includes automated tests verifying that excessive requests receive:

```text
429 Too Many Requests
```

### 📦 API Resources

Laravel API Resources are used to control and standardize API response structures.

### 📚 API Documentation

The API is documented using **Swagger/OpenAPI** annotations.

Documentation includes:

* Authentication endpoints
* Task endpoints
* Request parameters
* Request bodies
* Response information
* Bearer authentication

### 🧪 Automated Testing

The project includes both unit and feature tests.

Current test suite:

```text
28 tests
65 assertions
```

All tests pass successfully.

Tests cover:

* Authentication
* Registration
* Login
* Logout
* Token invalidation
* Task creation
* Task retrieval
* Task updates
* Task deletion
* Authorization
* Validation
* Rate limiting
* API error responses
* API route handling

Run the test suite with:

```bash
php artisan test
```

When using Docker:

```bash
docker compose exec app php artisan test
```

### ⚙️ Continuous Integration

GitHub Actions automatically runs the project's automated tests.

This helps ensure that new changes do not break existing functionality.

### 🐳 Docker

The project includes a Docker development environment.

Docker Compose runs:

* Laravel application
* PHP
* MySQL 8

The Laravel application is available at:

```text
http://localhost:8000
```

MySQL is exposed locally on:

```text
localhost:3307
```

The application communicates with MySQL through the Docker service network.

---

## 🏗️ Project Architecture

```text
Laravel Task API
│
├── Laravel 12
├── PHP
├── MySQL 8
├── Laravel Sanctum
├── Form Requests
├── API Resources
├── Policies
├── Rate Limiting
├── Swagger / OpenAPI
├── PHPUnit / Laravel Testing
├── GitHub Actions
└── Docker / Docker Compose
```

---

## 🛠️ Technologies Used

| Technology            | Purpose                     |
| --------------------- | --------------------------- |
| Laravel 12            | Backend framework           |
| PHP 8.2+              | Programming language        |
| MySQL 8               | Database                    |
| Laravel Sanctum       | API authentication          |
| Laravel Policies      | Authorization               |
| Laravel Form Requests | Request validation          |
| Laravel API Resources | API responses               |
| Swagger / OpenAPI     | API documentation           |
| PHPUnit               | Automated testing           |
| GitHub Actions        | CI                          |
| Docker                | Containerization            |
| Docker Compose        | Multi-container development |
| Git & GitHub          | Version control             |

---

# 📌 API Endpoints

## Authentication

| Method | Endpoint        | Authentication | Description            |
| ------ | --------------- | -------------- | ---------------------- |
| POST   | `/api/register` | No             | Register a new user    |
| POST   | `/api/login`    | No             | Login                  |
| POST   | `/api/logout`   | Yes            | Logout                 |
| GET    | `/api/user`     | Yes            | Get authenticated user |

## Tasks

| Method | Endpoint            | Authentication | Description   |
| ------ | ------------------- | -------------- | ------------- |
| GET    | `/api/tasks`        | Yes            | List tasks    |
| POST   | `/api/tasks`        | Yes            | Create a task |
| GET    | `/api/tasks/{task}` | Yes            | View a task   |
| PUT    | `/api/tasks/{task}` | Yes            | Update a task |
| DELETE | `/api/tasks/{task}` | Yes            | Delete a task |

---

# 🔑 Authentication

The API uses **Laravel Sanctum** bearer tokens.

After logging in successfully, the API returns a token.

Send the token in subsequent requests using:

```http
Authorization: Bearer YOUR_TOKEN
```

Example:

```http
Authorization: Bearer 1|xxxxxxxxxxxxxxxx
```

Protected endpoints require a valid token.

---

# 📥 Example API Requests

## Register

```http
POST /api/register
Content-Type: application/json
```

```json
{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password123"
}
```

## Login

```http
POST /api/login
Content-Type: application/json
```

```json
{
    "email": "john@example.com",
    "password": "password123"
}
```

## Create Task

```http
POST /api/tasks
Authorization: Bearer YOUR_TOKEN
Content-Type: application/json
```

```json
{
    "title": "Complete Laravel API",
    "description": "Finish the API project",
    "status": "Pending"
}
```

---

# 🐳 Docker Setup

## Requirements

Make sure you have:

* Docker Desktop
* Docker Compose

Check Docker:

```bash
docker --version
```

Check Docker Compose:

```bash
docker compose version
```

## Start the application

Clone the repository:

```bash
git clone https://github.com/Sandamal0333/task-api.git
```

Navigate to the project:

```bash
cd task-api
```

Start Docker:

```bash
docker compose up -d
```

Check running containers:

```bash
docker compose ps
```

The application should be available at:

```text
http://localhost:8000
```

## Configure the environment

The project uses a Docker-specific environment configuration.

Create your local Docker environment file based on the project's environment example/configuration.

Make sure the database configuration points to the Docker MySQL service:

```env
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=task_api
DB_USERNAME=task_user
DB_PASSWORD=task_password
```

> Do not commit environment files containing passwords or other secrets to GitHub.

## Generate application key

Inside the Laravel container:

```bash
docker compose exec app php artisan key:generate
```

## Run migrations

```bash
docker compose exec app php artisan migrate
```

## Run tests

```bash
docker compose exec app php artisan test
```

## Stop the application

```bash
docker compose down
```

To stop the containers **without deleting the database volume**, use:

```bash
docker compose down
```

Do not use `-v` unless you intentionally want to remove the Docker database volume.

---

# 🧪 Testing

Run all tests:

```bash
php artisan test
```

Or inside Docker:

```bash
docker compose exec app php artisan test
```

Current result:

```text
28 tests passed
65 assertions
```

The test suite includes unit and feature tests.

---

# 🔄 Continuous Integration

GitHub Actions is configured to automatically run the test suite when changes are pushed to GitHub.

This provides automated verification of the application during development.

---

# 📖 API Documentation

Swagger/OpenAPI documentation is included in the project.

The documentation provides an interactive overview of the available API endpoints, request parameters, authentication requirements, and responses.

---

# 📁 Project Structure

```text
task-api/
│
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   ├── Requests/
│   │   └── Resources/
│   │
│   ├── Models/
│   ├── Policies/
│   └── Services/
│
├── bootstrap/
├── config/
├── database/
│   ├── factories/
│   ├── migrations/
│   └── seeders/
│
├── routes/
│   ├── api.php
│   └── web.php
│
├── tests/
│   ├── Feature/
│   └── Unit/
│
├── Dockerfile
├── docker-compose.yml
├── .dockerignore
├── .gitignore
└── README.md
```

---

# 🔐 Security Considerations

The project includes several security-related features:

* Sanctum token authentication
* Authorization policies
* Request validation
* Password hashing through Laravel's authentication system
* API rate limiting
* Protected task ownership
* JSON responses for API authentication errors
* Environment variables for sensitive configuration

Sensitive environment files should never be committed to Git.

---

# 🚧 Future Improvements

Potential future improvements include:

* Production deployment
* Production Docker configuration
* Redis caching
* Background jobs and queues
* Email notifications
* Advanced task management
* Automated API documentation deployment
* Monitoring and logging
* Production database configuration
* Cloud deployment

---

# 👨‍💻 Author

**Dimuthu Sandamal**

GitHub:

https://github.com/Sandamal0333

Repository:

https://github.com/Sandamal0333/task-api

---

# 📄 License

This project is intended for educational and portfolio purposes.
