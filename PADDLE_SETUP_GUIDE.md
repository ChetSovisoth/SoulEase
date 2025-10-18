# Paddle Setup Guide for Dynamic Pricing

## Current Error

You're seeing "Failed to create price in Paddle" because the Paddle integration needs to be configured properly.

## Setup Steps

### Step 1: Get Your Paddle Credentials

1. Log in to your **Paddle Dashboard**: https://vendors.paddle.com/
2. Go to **Developer Tools** → **Authentication**
3. Get/Create your credentials:
   - **API Key** (for server-side)
   - **Client Side Token** (for checkout)
   - **Vendor ID**

### Step 2: Configure Environment Variables

Add these to your `.env` file:

```env
PADDLE_API_KEY=your_api_key_here
PADDLE_VENDOR_ID=your_vendor_id_here
PADDLE_CLIENT_SIDE_TOKEN=your_client_token_here
```

### Step 3: Choose Your Integration Approach

You have **2 options**:

---

## **Option A: Dynamic Pricing (Flexible)**

This creates custom prices on-the-fly for each session duration.

### Setup:

1. **Create a Product in Paddle Dashboard**:
   - Go to **Catalog** → **Products**
   - Click **New Product**
   - Name: "Therapy Session"
   - Description: "Online therapy session"
   - Tax Category: Standard
   - Click **Create**

2. **Copy the Product ID**:
   - After creating, you'll see a Product ID like `pro_01h...`
   - Copy this ID

3. **Add to `.env`**:
   ```env
   PADDLE_PRODUCT_ID=pro_01hxxxxxxxxxxxxxxx
   ```

4. **Done!** The system will now create custom prices for each amount.

### How it works:
- Patient books a 90-minute session
- Backend calculates: $150 (with discount)
- API creates a custom price: `pri_01hxxx` for $150
- Checkout opens with that exact price

---

## **Option B: Pre-configured Prices (Simple)**

This uses fixed price tiers you create in Paddle dashboard.

### Setup:

1. **Create a Product** (same as Option A, step 1)

2. **Create Prices for Common Durations**:

   In Paddle Dashboard → Your Product → Add Prices:

   - **30-minute session**: $50 (one-time)
     - Price ID: `pri_30min`

   - **60-minute session**: $100 (one-time)
     - Price ID: `pri_60min`

   - **90-minute session**: $142.50 (one-time)
     - Price ID: `pri_90min`

   - **120-minute session**: $180 (one-time)
     - Price ID: `pri_120min`

3. **Update Frontend** to use fixed price IDs:

   Instead of calling `/api/create-paddle-price`, directly use price IDs:

   ```javascript
   const openCheckout = (priceId) => {
       if (window.Paddle) {
           window.Paddle.Checkout.open({
               items: [{ priceId: priceId, quantity: 1 }],
               customer: { email: props.session.patient.email },
               customData: {
                   session_id: props.session.id,
                   user_id: props.session.patient.id,
               }
           });
       }
   };
   ```

4. **Map durations to price IDs in your controller**:

   ```php
   private function getPriceIdForDuration($durationMinutes) {
       return match($durationMinutes) {
           30 => 'pri_30min',
           60 => 'pri_60min',
           90 => 'pri_90min',
           120 => 'pri_120min',
           default => 'pri_60min',
       };
   }
   ```

---

## Recommended Approach

**For Development/Testing**: Use **Option A** (Dynamic Pricing)
- More flexible
- Automatic price calculation
- No need to pre-create many prices

**For Production**: Consider **Option B** (Pre-configured Prices)
- Faster checkout (no API call needed)
- More reliable (no API failures)
- Simpler architecture

---

## Testing with Paddle Sandbox

1. **Switch to Sandbox Mode**:
   - Update `.env`:
     ```env
     PADDLE_ENVIRONMENT=sandbox
     ```

   - Update `Show.vue` line 263:
     ```javascript
     window.Paddle.Environment.set('sandbox');
     ```

2. **Use Sandbox Credentials**:
   - Get sandbox credentials from Paddle Dashboard → Sandbox
   - Use different API keys/tokens for sandbox

3. **Test Cards**:
   - Use Paddle test cards: https://developer.paddle.com/concepts/payment-methods/credit-debit-card
   - Example: `4242 4242 4242 4242`

---

## Troubleshooting

### Error: "Paddle API key not configured"
**Solution**: Add `PADDLE_API_KEY` to your `.env` file

### Error: "Paddle integration is not fully configured"
**Solution**: Add `PADDLE_PRODUCT_ID` to your `.env` file

### Error: "Paddle API error: ..."
**Solution**: Check Laravel logs at `storage/logs/laravel.log` for detailed error

### Error: "Paddle is not loaded"
**Solution**: Ensure Paddle.js script is in `app.blade.php`:
```html
<script src="https://cdn.paddle.com/paddle/v2/paddle.js"></script>
```

### Checkout opens but shows error
**Solution**:
- Verify your Client Side Token is correct
- Check you're using the right environment (sandbox vs production)
- Verify price IDs exist in your Paddle account

---

## Next Steps

1. Choose your approach (Option A or B)
2. Configure Paddle credentials in `.env`
3. Test the payment flow
4. Configure Paddle webhooks (see main guide)

For webhook setup, see `DYNAMIC_PRICING_GUIDE.md`
