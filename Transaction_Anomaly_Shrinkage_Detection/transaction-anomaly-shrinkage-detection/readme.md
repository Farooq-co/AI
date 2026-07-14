# Transaction Anomaly & Shrinkage Detection

## Project Overview

The **Transaction Anomaly & Shrinkage Detection** project is a FastAPI-based analytics application designed to identify suspicious transactions and operational anomalies that may indicate fraud, shrinkage, or cashier discrepancies within a retail or fuel station environment.

The application follows a modular architecture that separates routing, business logic, data models, and database management into dedicated components. This structure improves maintainability, testing, and future scalability.

The project consists of:

* **FastAPI** for the web framework and API routing
* **Jinja2** templates for server-side rendered dashboards
* **Bootstrap 5** and **Chart.js** for responsive data visualization
* **SQLAlchemy** as the ORM layer
* **SQLite** as the project database
* **Scikit-learn** for anomaly detection
* **Poetry** for dependency management

Project modules include:

* **Routes** – Handle HTTPS requests and responses.
* **Services** – Contain business logic, analytics, dashboard metrics, anomaly detection, and alert processing.
* **Models** – Define database entities.
* **Database** – Manages database connectivity and sessions.
* **Templates & Static Assets** – Provide the dashboard interface and styling.

---

# AI Tool Integration

The development workflow combines multiple AI assistants, each used for a different stage of the software development lifecycle.

## Debugging with GitHub Copilot

GitHub Copilot is primarily used during debugging and code refinement.

Its responsibilities include:

* Identifying syntax errors
* Suggesting fixes for runtime exceptions
* Improving existing functions
* Recommending code refactoring
* Completing repetitive implementation patterns
* Assisting with SQLAlchemy queries
* Suggesting corrections for FastAPI routing issues
* Helping resolve template rendering and JavaScript integration problems

Copilot serves as an in-editor assistant that accelerates debugging while allowing the developer to review and validate every suggested change.

---

## Logic Development with Claude

Claude is used for higher-level reasoning and application logic.

Typical responsibilities include:

* Designing business workflows
* Developing anomaly detection strategies
* Planning service-layer architecture
* Solving complex programming problems
* Breaking large development tasks into manageable components
* Reviewing application structure for maintainability
* Assisting with algorithm design
* Providing implementation strategies before coding begins

Claude is primarily used when architectural or logical reasoning is required rather than simple code completion.

---

## Front-end & Back-end Development with ChatGPT

ChatGPT is used throughout both front-end and back-end development.

### Front-end Development

ChatGPT assists with:

* Dashboard layout design
* Bootstrap UI development
* Responsive interface improvements
* Jinja2 template generation
* Chart.js integration
* HTML structure optimization
* CSS improvements
* User interface refinement

### Back-end Development

ChatGPT assists with:

* FastAPI endpoint development
* SQLAlchemy model design
* Service layer implementation
* Database interaction
* Dashboard metric generation
* API organization
* Route structuring
* Error handling improvements
* General code review and optimization

ChatGPT is also used for project documentation, workflow planning, feature implementation guidance, and explaining framework behavior during development.

---

# Database Management

## SQLite vs. PostgreSQL Rationale

The project currently uses **SQLite** instead of PostgreSQL for several practical reasons.

### Simplicity

SQLite requires no separate database server or installation, making project setup straightforward for development and demonstration purposes.

### Lightweight Development

Because the application is intended for rapid prototyping and analytics experimentation, SQLite provides an efficient embedded database without additional infrastructure.

### Easy Distribution

The database is stored as a single file (`transaction_anomaly.db`), allowing the complete project—including data—to be shared without external database configuration.

### Reduced Configuration

Unlike PostgreSQL, SQLite does not require:

* database server installation
* user management
* network configuration
* connection pooling
* server administration

This minimizes setup complexity for contributors and evaluators.

### Suitable for Current Scale

The current application primarily performs analytics, dashboard reporting, anomaly detection, and seeded transaction processing. SQLite provides sufficient performance for these workloads during development.

PostgreSQL may be considered in future production deployments requiring higher concurrency, larger datasets, or distributed access.

---

## SQLite Integration Details

SQLite is integrated through SQLAlchemy.

Database management includes:

* SQLAlchemy ORM models
* Centralized database configuration
* Session management
* Persistent local database file
* ORM-based CRUD operations
* Model-based schema management

The project database currently stores entities including:

* Transactions
* Refunds
* Alerts
* Shift information

Business services interact with the database through SQLAlchemy rather than executing raw SQL queries, improving maintainability and portability.

---

# Project Dependencies

## Dependency List

### Core Dependencies

| Dependency       | Purpose                                            |
| ---------------- | -------------------------------------------------- |
| FastAPI          | Web framework for APIs and server-side application |
| Uvicorn          | ASGI server used to run the FastAPI application    |
| SQLAlchemy       | ORM for database access                            |
| Alembic          | Database migration management                      |
| Pandas           | Data analysis and aggregation                      |
| NumPy            | Numerical computations                             |
| Scikit-learn     | Machine learning and anomaly detection             |
| Faker            | Seed data generation for testing                   |
| Python-dotenv    | Environment variable management                    |
| Python-Multipart | Form and file upload handling                      |
| Jinja2           | HTML template rendering                            |
| Aiofiles         | Asynchronous file handling                         |

### Development Dependencies

| Dependency | Purpose                          |
| ---------- | -------------------------------- |
| Black      | Automatic Python code formatting |
| isort      | Import organization              |
| pytest     | Unit and integration testing     |

---

## Key Dependency Explanations

### FastAPI

Provides the application's routing, request handling, dependency injection, and API framework.

---

### SQLAlchemy

Acts as the Object Relational Mapper (ORM), enabling Python models to interact with the SQLite database while reducing direct SQL usage.

---

### Alembic

Supports version-controlled database schema migrations, making future database changes easier to manage.

---

### Pandas

Processes transaction datasets, performs aggregations, and prepares metrics for dashboard visualization.

---

### NumPy

Supports efficient numerical operations and data transformations used during analytics processing.

---

### Scikit-learn

Provides machine learning algorithms for anomaly detection, including the project's transaction analysis and fraud detection capabilities.

---

### Faker

Generates realistic sample transactions, customers, and related records to populate the database during development and testing.

---

### Jinja2

Renders dynamic HTML templates for the analytics dashboard, enabling server-side generation of KPI cards, tables, and charts.

---

### Python-dotenv

Loads configuration values from environment files, simplifying local development and deployment configuration.

---

### Python-Multipart

Enables FastAPI to process multipart form submissions and uploaded files when required.

---

### Aiofiles

Provides asynchronous file operations for improved performance during file handling tasks.

---

### Uvicorn

Runs the FastAPI application as an ASGI server, supporting asynchronous request processing during development and deployment.

---

## Development Workflow Summary

The project follows a layered architecture:

1. Incoming requests are handled by FastAPI routes.
2. Routes delegate business operations to dedicated service modules.
3. Services retrieve and process data using SQLAlchemy models.
4. Machine learning components analyze transactions for anomalies.
5. Dashboard metrics are generated and passed to Jinja2 templates.
6. Bootstrap and Chart.js render interactive visualizations in the browser.
7. SQLite stores all project data in a lightweight embedded database.

This separation of concerns improves readability, maintainability, and future extensibility while supporting rapid development and experimentation with anomaly detection techniques.
