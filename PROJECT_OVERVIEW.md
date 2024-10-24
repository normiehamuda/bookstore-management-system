# Project Overview: Bookstore Management System

## Introduction

This project implements a Bookstore Management System using the Laravel framework. It provides a web API for managing a collection of books, allowing users to perform CRUD (Create, Read, Update, Delete) operations on book records. The system also incorporates user authentication and role-based access control, ensuring that only authorized users can modify book data.

## Key Features:

* **Book Management:** Users can add new books, view book details, update existing book information, and delete books from the system.
* **User Authentication:** Users can register and log in to the system using secure credentials.
* **Role-Based Access Control:** The system defines different user roles (Admin, User). Admin users have full access to all features, while regular users can only view the book list.
* **Search Functionality:** Users can search for books by title, author, or ISBN using the integrated OpenSearch engine, enabling faster and more flexible search capabilities.
* **API-Driven:** All functionalities are exposed through a RESTful API, making it easy to integrate with other applications or front-end interfaces.

## Technology Stack



* **Laravel (PHP Framework):** Laravel provides the foundation for the backend application, offering features like routing, controllers, models, and database interaction, simplifying development and ensuring code organization.
* **MySQL (Database):** MySQL serves as the relational database for storing book information, user accounts, and other application data, ensuring data persistence and integrity.
* **OpenSearch (Search Engine):** OpenSearch enables fast and efficient search capabilities, allowing users to search for books by various criteria like title, author, or ISBN, improving user experience.
* **Docker (Containerization):** Docker facilitates containerization of the application, ensuring consistency across different environments and simplifying deployment.
* **GitHub Actions (CI/CD):** GitHub Actions automates the build, testing, and deployment processes, ensuring code quality and streamlining the development workflow.


## System Architecture

The Bookstore Management System follows a modular architecture, comprising the following key components:

* **API (Application Programming Interface):**
   - Built using Laravel, the API exposes RESTful endpoints for interacting with the system.
   - It handles user authentication, authorization, and validation of requests.
   - It interacts with the database to manage book data and with OpenSearch for search functionality.

* **Database (MySQL):**
   - Stores all persistent data, including book information, user accounts, and roles.
   - The API interacts with the database using Eloquent ORM (Object-Relational Mapper) provided by Laravel.

* **Search Engine (OpenSearch):**
   - Provides efficient search capabilities for books.
   - The API interacts with OpenSearch using the OpenSearch PHP client to index book data and perform search queries.

### Interaction Flow:

1. **User Interaction:** A user interacts with the system through a client application (e.g., web browser, mobile app).
2. **API Request:** The client application sends API requests to the Laravel API.
3. **Authentication & Authorization:** The API authenticates the user (if required) and verifies if they have permission to perform the requested action.
4. **Data Processing:** The API processes the request and interacts with the database or OpenSearch as needed. 
   - For CRUD operations on books, the API interacts with the database.
   - For search operations, the API interacts with OpenSearch.
5. **Response:** The API returns a response to the client application, usually in JSON format.

### Simplified Diagram:

[![Diagram](http://minassah.ly/diagram.svg "Diagram")](http://minassah.ly/diagram.svg "Diagram")
