# 🗄️ Local PostgreSQL Setup Guide

## Current Status

✅ **Production Config Fixed:** `APP_DEBUG=false` in `.do/app.yaml`  
⚠️ **Local PostgreSQL:** Needs manual setup

## Your Current .env Configuration

```
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=localims
DB_USERNAME=munaad
DB_PASSWORD="Strong#Local_2025"
```

## Manual Setup Steps

Since PostgreSQL requires password authentication, you'll need to set up the database manually:

### Option 1: Using psql (Recommended)

1. **Connect to PostgreSQL:**
   ```bash
   psql -U $(whoami) -d postgres
   # Or if that doesn't work:
   psql -U postgres -d postgres
   # You'll be prompted for password
   ```

2. **Once connected, run these SQL commands:**
   ```sql
   -- Create the user
   CREATE USER munaad WITH PASSWORD 'Strong#Local_2025';
   
   -- Create the database
   CREATE DATABASE localims OWNER munaad;
   
   -- Grant privileges
   GRANT ALL PRIVILEGES ON DATABASE localims TO munaad;
   
   -- Connect to the new database
   \c localims
   
   -- Grant schema privileges
   GRANT ALL ON SCHEMA public TO munaad;
   ```

3. **Exit psql:**
   ```sql
   \q
   ```

### Option 2: Using createdb command

```bash
# Create database (if you have permissions)
createdb -U $(whoami) localims

# Then connect and create user
psql -U $(whoami) -d localims
```

Then run:
```sql
CREATE USER munaad WITH PASSWORD 'Strong#Local_2025';
GRANT ALL PRIVILEGES ON DATABASE localims TO munaad;
ALTER DATABASE localims OWNER TO munaad;
```

### Option 3: Using a SQL file

1. **Create a setup file:**
   ```bash
   cat > setup_db.sql << 'EOF'
   CREATE USER munaad WITH PASSWORD 'Strong#Local_2025';
   CREATE DATABASE localims OWNER munaad;
   GRANT ALL PRIVILEGES ON DATABASE localims TO munaad;
   EOF
   ```

2. **Run it:**
   ```bash
   psql -U $(whoami) -d postgres -f setup_db.sql
   ```

## After Database Setup

Once the database and user are created, run Laravel migrations:

```bash
cd "/Users/munad/Documents/Websites/Incident Management System"

# Test connection
php artisan db:show

# Run migrations
php artisan migrate

# Seed initial data
php artisan db:seed --class=ResolutionTeamSeeder
php artisan db:seed --class=UserSeeder
```

## Troubleshooting

### If you get "password authentication failed"

1. **Check if user exists:**
   ```sql
   SELECT usename FROM pg_user WHERE usename = 'munaad';
   ```

2. **Reset password if needed:**
   ```sql
   ALTER USER munaad WITH PASSWORD 'Strong#Local_2025';
   ```

### If you get "database does not exist"

```sql
CREATE DATABASE localims OWNER munaad;
```

### If you get "permission denied"

You may need to connect as a superuser (postgres user):
```bash
psql -U postgres -d postgres
```

### Check PostgreSQL is running

```bash
brew services list | grep postgresql
# Or
pg_isready
```

### Start PostgreSQL if not running

```bash
brew services start postgresql@14
```

## Verify Setup

Test the connection:

```bash
php artisan db:show
```

You should see:
- Database: localims
- Driver: pgsql
- Connection: OK

## Next Steps

1. ✅ Fix production `APP_DEBUG=false` - **DONE**
2. ⏳ Set up local PostgreSQL database - **IN PROGRESS**
3. ⏳ Run migrations
4. ⏳ Test application locally

