# Railway Deployment Guide for SQL Playground API

## Prerequisites
1. Create account at [Railway.app](https://railway.app)
2. Install Railway CLI: `npm install -g @railway/cli`
3. Login: `railway login`

## Deployment Steps

### 1. Create Railway Project
```bash
# In your backend directory
railway init

# Connect to your GitHub repository
railway connect
```

### 2. Add MySQL Database
```bash
# Add MySQL service
railway add mysql

# Railway will automatically provide these environment variables:
# MYSQL_URL, MYSQL_HOST, MYSQL_PORT, MYSQL_DATABASE, MYSQL_USER, MYSQL_PASSWORD
```

### 3. Set Environment Variables
In Railway dashboard, add these variables:

```env
# App Configuration
APP_NAME="SQL Playground API"
APP_ENV=production
APP_DEBUG=false
APP_KEY=base64:YOUR_APP_KEY_HERE

# Database (Railway auto-provides MYSQL_* variables)
DB_CONNECTION=mysql
DB_HOST=${{MYSQL_HOST}}
DB_PORT=${{MYSQL_PORT}}
DB_DATABASE=${{MYSQL_DATABASE}}
DB_USERNAME=${{MYSQL_USER}}
DB_PASSWORD=${{MYSQL_PASSWORD}}

# CORS - Your Netlify frontend URL
FRONTEND_URL=https://your-app.netlify.app

# Security
SESSION_SECURE_COOKIE=true
SANCTUM_STATEFUL_DOMAINS=your-app.netlify.app
```

### 4. Generate App Key
```bash
# Generate Laravel app key
php artisan key:generate --show

# Copy the output and set as APP_KEY in Railway dashboard
```

### 5. Deploy
```bash
# Deploy to Railway
railway up

# Or connect GitHub for automatic deployments
```

### 6. Database Setup
Railway will automatically run:
- `php artisan migrate:fresh --seed --force` (via Procfile)
- This creates all tables and populates sample data

## Post-Deployment

### 1. Get Your API URL
```bash
# Get your Railway app URL
railway status

# Your API will be available at:
# https://your-app.railway.app
```

### 2. Update Frontend
Update your Netlify environment variables:
```env
REACT_APP_API_URL=https://your-app.railway.app/api
```

### 3. Test API
```bash
# Test health endpoint
curl https://your-app.railway.app/api/health

# Test questions endpoint
curl https://your-app.railway.app/api/questions
```

## Files Created for Railway
- `railway.json` - Railway configuration
- `Procfile` - Process definitions
- `nixpacks.toml` - Build configuration
- `.env.production` - Production environment template
- Database migrations and seeders

## Troubleshooting

### Database Connection Issues
```bash
# Check Railway logs
railway logs

# Verify database variables
railway variables
```

### CORS Issues
- Ensure `FRONTEND_URL` matches your Netlify URL exactly
- Check `config/cors.php` includes your domain

### Migration Issues
```bash
# Manually run migrations if needed
railway run php artisan migrate:fresh --seed --force
```

## Commands for Development

```bash
# Local development with Railway database
railway run php artisan serve

# Run migrations
railway run php artisan migrate

# Check logs
railway logs --tail

# Shell access
railway shell
```

## Alternative: One-Click Deploy
Railway also supports one-click deploys from GitHub:
1. Go to Railway dashboard
2. Click "New Project"
3. Select "Deploy from GitHub repo"
4. Choose your repository
5. Railway auto-detects Laravel and configures build