# Quick Fix: Use Pre-configured Paddle Prices

Since the Paddle API authentication is having issues, I've implemented a **simpler approach** that works immediately.

## How It Works Now

Instead of creating prices dynamically via API, you'll create prices once in your Paddle Dashboard and reference them directly.

## Setup Steps (5 minutes)

### 1. Create Prices in Paddle Sandbox

1. Go to https://sandbox-vendors.paddle.com/
2. Navigate to **Catalog** → **Products**
3. Find or create your "Therapy Session" product
4. Click on the product and go to **Prices**
5. Create 4 prices:

   **30-Minute Session:**
   - Amount: $50.00 USD
   - Billing type: One-time
   - Click Save
   - **Copy the Price ID** (looks like `pri_01xxxx...`)

   **60-Minute Session:**
   - Amount: $100.00 USD
   - Billing type: One-time
   - Click Save
   - **Copy the Price ID**

   **90-Minute Session:**
   - Amount: $142.50 USD
   - Billing type: One-time
   - Click Save
   - **Copy the Price ID**

   **120-Minute Session:**
   - Amount: $180.00 USD
   - Billing type: One-time
   - Click Save
   - **Copy the Price ID**

### 2. Add Price IDs to `.env`

Add these lines to your `.env` file with the actual price IDs you copied:

```env
PADDLE_PRICE_30MIN=pri_01xxxxxxxxxxxxxxxxxxxxxxxxxx
PADDLE_PRICE_60MIN=pri_01xxxxxxxxxxxxxxxxxxxxxxxxxx
PADDLE_PRICE_90MIN=pri_01xxxxxxxxxxxxxxxxxxxxxxxxxx
PADDLE_PRICE_120MIN=pri_01xxxxxxxxxxxxxxxxxxxxxxxxxx
```

### 3. Clear Cache

```bash
php artisan config:clear
```

### 4. Test!

Now when you click "Pay Now with Cards", it will use the pre-configured price IDs instead of trying to create them dynamically via API.

## Benefits of This Approach

✅ **No API authentication issues**
✅ **Faster checkout** (no API call needed)
✅ **More reliable** (no network dependencies)
✅ **Better for production** (recommended by Paddle)

## How the Code Works

The system now:
1. Checks if you have a pre-configured price ID for the session duration
2. If yes → Uses that price ID ✅
3. If no → Tries dynamic API creation (fallback)
4. If both fail → Shows helpful error message

## Adjusting Prices

You can adjust prices easily:
- **Option A**: Update prices in Paddle Dashboard (recommended - affects all sessions)
- **Option B**: Modify the discount multipliers in `app/Services/PricingService.php`

## Testing

Try booking a 60-minute session and paying. You should see:
1. No errors in the browser console
2. Paddle checkout opens successfully
3. The price shows correctly ($100 for 60 min)

## If You Still See Errors

Check the Laravel logs:
```bash
tail -f storage/logs/laravel.log
```

Look for messages like:
- ✅ "Using pre-configured Paddle price" → Working!
- ❌ "Paddle price not configured" → Add the price ID to `.env`

## Future: Fix API Authentication

If you want to use dynamic pricing later, you need to:
1. Verify you're logged into **Paddle Sandbox**: https://sandbox-vendors.paddle.com/
2. Generate a **NEW API key** from Developer Tools → Authentication
3. Copy the **FULL key** (don't truncate it)
4. Update `PADDLE_API_KEY` in `.env`
5. Make sure there are no quotes or spaces around the key

But for now, the pre-configured approach will work perfectly!
