#!/bin/bash

echo "🔧 Setting up local PostgreSQL database for Incident Management System"
echo "========================================================================"

# Colors
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m' # No Color

# Database configuration from .env
DB_NAME="localims"
DB_USER="munaad"
DB_PASSWORD="Strong#Local_2025"

echo ""
echo "📋 Configuration:"
echo "   Database: $DB_NAME"
echo "   User: $DB_USER"
echo ""

# Check if PostgreSQL is running
if ! pg_isready -h localhost -p 5432 > /dev/null 2>&1; then
    echo -e "${RED}❌ PostgreSQL is not running!${NC}"
    echo "   Please start PostgreSQL first:"
    echo "   brew services start postgresql@14"
    exit 1
fi

echo -e "${GREEN}✅ PostgreSQL is running${NC}"

# Try to connect as current user first (peer authentication)
echo ""
echo "🔐 Attempting to create database and user..."

# Create SQL script
SQL_FILE=$(mktemp)
cat > "$SQL_FILE" << EOF
-- Create user if not exists
DO \$\$
BEGIN
    IF NOT EXISTS (SELECT FROM pg_user WHERE usename = '$DB_USER') THEN
        CREATE USER $DB_USER WITH PASSWORD '$DB_PASSWORD';
        RAISE NOTICE 'User $DB_USER created';
    ELSE
        RAISE NOTICE 'User $DB_USER already exists';
    END IF;
END
\$\$;

-- Create database if not exists
SELECT 'CREATE DATABASE $DB_NAME OWNER $DB_USER'
WHERE NOT EXISTS (SELECT FROM pg_database WHERE datname = '$DB_NAME')\gexec

-- Grant privileges
GRANT ALL PRIVILEGES ON DATABASE $DB_NAME TO $DB_USER;
ALTER DATABASE $DB_NAME OWNER TO $DB_USER;
EOF

# Try different connection methods
echo "   Trying to connect to PostgreSQL..."

# Method 1: Try as current user (peer auth)
if psql -U $(whoami) -d postgres -f "$SQL_FILE" 2>/dev/null; then
    echo -e "${GREEN}✅ Database and user created successfully!${NC}"
    rm "$SQL_FILE"
    exit 0
fi

# Method 2: Try with createdb command
if createdb -U $(whoami) "$DB_NAME" 2>/dev/null; then
    echo -e "${YELLOW}⚠️  Database created, but user creation may have failed${NC}"
    echo "   You may need to run the user creation manually"
fi

# Method 3: Manual instructions
echo ""
echo -e "${YELLOW}⚠️  Automatic setup failed. Please run these commands manually:${NC}"
echo ""
echo "1. Connect to PostgreSQL:"
echo "   psql -U $(whoami) -d postgres"
echo ""
echo "2. Run these SQL commands:"
echo "   CREATE USER $DB_USER WITH PASSWORD '$DB_PASSWORD';"
echo "   CREATE DATABASE $DB_NAME OWNER $DB_USER;"
echo "   GRANT ALL PRIVILEGES ON DATABASE $DB_NAME TO $DB_USER;"
echo ""
echo "3. Or use this SQL file:"
echo "   psql -U $(whoami) -d postgres -f $SQL_FILE"
echo ""
echo "SQL file location: $SQL_FILE"

