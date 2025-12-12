Manual Setup

If you cloned the repository directly and need to set up manually, or want to customize the installation process:

1. Install Dependencies

composer install

npm install

2. Environment Configuration

cp .env.example .env

php artisan key:generate

3. Database Setup

By default, this project uses SQLite. The database file will be created automatically during migration.

If you prefer MySQL or PostgreSQL, update your .env file:

DB_CONNECTION=mysql

DB_HOST=127.0.0.1

DB_PORT=3306

DB_DATABASE=your_database_name

DB_USERNAME=your_username

DB_PASSWORD=your_password

Run migrations:

php artisan migrate --seed

4. Now you can run

npm run build 

or

php artisan serve -- backend

npm run dev -- fronend

5. User Credential:

   email: admin@example.com

   password: password123 

