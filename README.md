# BlogArticleController Summary

## Overview

The `BlogArticleController` manages blog articles, providing functionality for creating, retrieving, updating, and deleting articles. The controller utilizes Symfony's routing and serialization features.

## Routes

### 1. Retrieve All Articles
- **Method**: GET
- **Path**: `/blog_articles`
- **Description**: Fetches all blog articles and returns them as a JSON response.

### 2. Create a New Article
- **Method**: POST
- **Path**: `/blog_articles`
- **Description**: Creates a new blog article.
- **Parameters**:
  - `title` (required)
  - `content` (required)
  - `author_id` (required)
  - `cover_picture_ref` (required file)
- **Validation**: Checks for required fields and uses the `BlogArticleUtils` service to filter banned words.

### 3. Update an Article by ID
- **Method**: PATCH
- **Path**: `/blog_articles/{id}`
- **Description**: Updates an existing blog article.
- **Parameters**:
  - `title` (required)
  - `content` (required)
  - `author_id` (required)
  - `keywords` (optional)
  - `cover_picture_ref` (optional file)
- **Validation**: Checks for required fields.

### 4. Retrieve a Specific Article by ID
- **Method**: GET
- **Path**: `/blog_articles/{id}`
- **Description**: Fetches a specific blog article by its ID.
- **Response**: Returns a 404 error if the article is not found.

### 5. Delete an Article by ID
- **Method**: DELETE
- **Path**: `/blog_articles/{id}`
- **Description**: Deletes a specific blog article by its ID and removes its associated cover picture file if it exists.

## Error Handling

- Returns appropriate JSON error messages for missing or invalid inputs.
- Handles file system errors when dealing with uploaded files.

This controller provides a comprehensive API for managing blog articles in a Symfony application.

# UserController Summary with JWT Implementation

## Overview

The `UserController` manages user registration and authentication using JWT (JSON Web Tokens) for secure API access. It includes methods for registering new users and logging in existing users to retrieve a JWT.

## Routes

### 1. User Registration
- **Method**: POST
- **Path**: `/register`
- **Description**: Registers a new user.
- **Request Parameters**:
  - `username` (required)
  - `email` (required)
  - `password` (required)
- **Response**: 
  - Success: `{"message": "User registered successfully!"}` (HTTP 201)

### 2. User Login
- **Method**: POST
- **Path**: `/login`
- **Description**: Authenticates a user and returns a JWT.
- **Request Parameters**:
  - `username` (required)
  - `password` (required)
- **Response**:
  - Success: `{"token": "JWT_TOKEN"}` (HTTP 200)
  - Error: `{"error": "Invalid credentials"}` (HTTP 401)

## Key Features

- **User Registration**: 
  - Hashes passwords using `UserPasswordHasherInterface`.
  - Stores user data in the database using Doctrine's EntityManager.

- **JWT Authentication**:
  - Utilizes `lexik/jwt-authentication-bundle` for generating and managing JWTs.
  - Securely authenticates users and issues tokens for access to protected routes.

## Security Configuration

- JWT is configured in `config/packages/security.yaml`.
- Protects routes under the `/api` path, allowing anonymous access to registration and login.

## Error Handling

- Returns appropriate JSON error messages for invalid credentials during login.
