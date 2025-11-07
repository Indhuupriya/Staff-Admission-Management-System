1. Clone the repository
git clone "https://github.com/Indhuupriya/Staff-Admission-Management-System.git"
cd Staff-Admission-Management-System
Explanation:
This downloads the project from GitHub to your local machine.
cd moves you inside the project folder.

2. Install PHP dependencies
composer install
Explanation:
Installs all required PHP packages for CodeIgniter 4 (like JWT, database library, etc.) from composer.json.
vendor folder will be created.

3. Set up environment file
Copy .env.example to .env:
copy .env.example .env   # Windows
cp .env.example .env     # Linux/Mac
Edit .env:
Set your database credentials:
database.default.database = your_database_name
database.default.username = root
database.default.password =
Add a JWT secret token:
JWT_SECRET=your_generated_token

4. Generate JWT token
php -r "echo bin2hex(random_bytes(32));"
Explanation:
Generates a 32-byte random string, used as your JWT secret key.
Copy this value and set it as JWT_SECRET in .env.

5. Run migrations
php spark migrate
Explanation:
Creates all required tables in your database (staffs, locations, staff_locations, etc.).
Migrations are like “database blueprints” for your app.

6. Seed initial data
php spark db:seed StaffSeeder
Explanation:
Populates the database with default data, e.g., an admin user.
This is why you can login immediately with:
username: admin
password: Test@123

7. Start the local server
php spark serve
Explanation:
Starts a development server, usually at http://localhost:8080.
Now you can access the app in your browser.

8. Test login
Go to http://localhost:8080
Use the credentials seeded by StaffSeeder:
username: admin
password: Test@123

After login:

JWT token is created and stored in session.
You can access /dashboard and staff management pages.
If session is destroyed (logout), JWT token is invalid, and login page shows.
Process Flow Overview
User visits / → AuthController::showLogin
If session exists → redirect to /dashboard
Else → show login form.
Login form submitted → AuthController::login
Validate credentials.
If valid → create session + JWT token.
Redirect to /dashboard.
Dashboard → shows staff list, admissions, etc.
AJAX requests → hit /api/* routes
Filtered by jwtAuth middleware (JWT token required).
Staff CRUD
apiList → fetch staff + locations
apiCreate → add staff + save locations
apiUpdate → edit staff + update locations
apiDelete → delete staff + remove from pivot table

