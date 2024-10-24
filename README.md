#  Bookstore Management System

This is a simple REST API for managing a bookstore built with Laravel. It includes features for:

* **Authentication:** User registration and login with Sanctum.
* **Book Management:** CRUD operations (Create, Read, Update, Delete) for books.
* **Search:** Search for books by title, author, or ISBN using both database queries and OpenSearch(ElasticSearch).

## Prerequisites

* **PHP 8.1 or higher** 
* **MySQL** 
* **Composer**
* **Node.js and npm** 
* **OpenSearch** (for Elasticsearch-like search functionality)

## Getting Started

1. **Clone the Repository:**
   ```bash
   git clone https://github.com/normiehamuda/bookstore-management-system.git
   cd bookstore-management-system
   ```

2. **Install Dependencies:**
   ```bash
   composer install
   ```

3. **Configure Environment Variables:**
   * Copy the `.env.example` file to `.env`:
     ```bash
     cp .env.example .env
     ```
   * Edit the `.env` file and update the following:
      * **Database Credentials:** 
         * `DB_CONNECTION=mysql`
         * `DB_HOST=127.0.0.1` (or your database host)
         * `DB_PORT=3306` (or your database port)
         * `DB_DATABASE=[your_database_name]` 
         * `DB_USERNAME=[your_database_user]`
         * `DB_PASSWORD=[your_database_password]`
      * **OpenSearch Credentials:** 
         * `OPENSEARCH_HOST=localhost` (or your OpenSearch host)
         * `OPENSEARCH_PORT=9200` (or your OpenSearch port)
         * `OPENSEARCH_SCHEME=http` (or `https` if using SSL)
         * `OPENSEARCH_USERNAME=[your_opensearch_username]` (if required)
         * `OPENSEARCH_PASSWORD=[your_opensearch_password]` (if required)


4. **Generate App Key:**
   ```bash
   php artisan key:generate
   ```
5. **Create the Database:**
   * **MySQL Command Line:**
      ```sql
      CREATE DATABASE [your_database_name];
      ```
   * **Or use a GUI tool like phpMyAdmin.**

6. **Run Migrations and Seed the Database:**
   ```bash
   php artisan migrate
   php artisan db:seed
   ```

7. **Start the Development Server:**
   ```bash
   php artisan serve
   ```

8. **Access the API:**

   The API will be available at `http://localhost:8000/api`.

## API Documentation

* **Swagger UI:** Access the interactive API documentation at `http://localhost:8000/api/documentation`.

   **Using the API Documentation:**

   1. **Registration:** 
      * Navigate to the `POST /api/register` endpoint.
      * Fill in the required fields (name, email, password, role ID).
      * Click "Try it out!" to send the registration request.
      * If successful, you will receive a response containing your Sanctum API token.

   2. **Login:**
      * Navigate to the `POST /api/login` endpoint.
      * Provide your registered email and password.
      * Click "Try it out!" to log in.
      * A successful login response will also include your Sanctum API token.

   3. **Authorization:**
      * At the top of the Swagger UI page, click the "Authorize" button.
      * In the "Value" field of the "Authorization" input, paste the Sanctum API token you received after registration or login.
      * Click "Authorize". 

   Now, when you test protected endpoints (like creating, updating, or deleting books), Swagger UI will automatically include your Sanctum token in the `Authorization` header of the requests.

## Test Results
You can run tests using command php artisan test

For details of the most recent test run, please refer to the [Test Results](TEST_RESULTS.md) document. 

## Additional Notes:

* **OpenSearch Setup:** You need to have an OpenSearch instance running. Refer to the OpenSearch documentation for installation and setup instructions ([https://docs.aws.amazon.com/opensearch-service/](https://docs.aws.amazon.com/opensearch-service/)).

