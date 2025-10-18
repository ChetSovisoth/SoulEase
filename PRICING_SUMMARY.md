# Standardized Pricing Summary

## Overview

All therapy sessions are now priced at a **standardized $100/hour rate**, regardless of individual therapist rates.

## Exact Prices to Create in Paddle

Create these 4 prices in your **Paddle Sandbox Dashboard**:

| Duration | Calculation | Discount | **Final Price** |
|----------|-------------|----------|-----------------|
| **30 minutes** | ($100 ÷ 60) × 30 | 0% | **$50.00 USD** |
| **60 minutes** | ($100 ÷ 60) × 60 | 0% | **$100.00 USD** |
| **90 minutes** | ($100 ÷ 60) × 90 = $150 | 5% off | **$142.50 USD** |
| **120 minutes** | ($100 ÷ 60) × 120 = $200 | 10% off | **$180.00 USD** |

## Discount Structure

- **30-60 minutes**: No discount (1.0x multiplier)
- **90 minutes**: 5% discount (0.95x multiplier)
- **120 minutes**: 10% discount (0.90x multiplier)
- **Over 120 minutes**: 15% discount (0.85x multiplier) - *Not commonly used*

## Setup Steps

### 1. Create Prices in Paddle Sandbox

1. Go to https://sandbox-vendors.paddle.com/
2. Navigate to **Catalog** → **Products** → Click on your "Therapy Session" product
3. Click **Add Price** for each duration:

   **For 30-minute session:**
   - Description: "30-minute therapy session"
   - Amount: **50.00**
   - Currency: USD
   - Billing type: One-time
   - Save and **copy the Price ID** (e.g., `pri_01abc123...`)

   **For 60-minute session:**
   - Description: "60-minute therapy session"
   - Amount: **100.00**
   - Currency: USD
   - Billing type: One-time
   - Save and **copy the Price ID**

   **For 90-minute session:**
   - Description: "90-minute therapy session"
   - Amount: **142.50**
   - Currency: USD
   - Billing type: One-time
   - Save and **copy the Price ID**

   **For 120-minute session:**
   - Description: "120-minute therapy session"
   - Amount: **180.00**
   - Currency: USD
   - Billing type: One-time
   - Save and **copy the Price ID**

### 2. Add Price IDs to `.env`

Add these lines to your `.env` file:

```env
PADDLE_PRICE_30MIN=pri_01xxxxxxxxxxxxxxxxxxxxx
PADDLE_PRICE_60MIN=pri_01xxxxxxxxxxxxxxxxxxxxx
PADDLE_PRICE_90MIN=pri_01xxxxxxxxxxxxxxxxxxxxx
PADDLE_PRICE_120MIN=pri_01xxxxxxxxxxxxxxxxxxxxx
```

### 3. Clear Laravel Cache

```bash
php artisan config:clear
```

### 4. Test!

Book a session and try the "Pay Now with Cards" button. It should:
- ✅ No errors in console
- ✅ Paddle checkout opens
- ✅ Shows correct price

## What Was Changed

### Files Modified:

1. **`app/Services/PricingService.php`**
   - Changed `$hourlyRate` from dynamic (therapist-specific) to fixed `$100`
   - All pricing calculations now use standardized rate

2. **`app/Http/Controllers/Api/PaddleController.php`**
   - Removed dependency on `therapistProfile->hourly_rate`
   - Returns standardized `hourly_rate: 100` in API responses

3. **`app/Services/PaddlePriceMapper.php`**
   - Maps duration → price ID
   - Tries pre-configured prices first (fast & reliable)
   - Falls back to API if needed (currently has auth issues)

## How It Works Now

### When a patient books a session:

1. **Patient selects duration** (30, 60, 90, or 120 minutes)
2. **System calculates price** using `PricingService` (always $100/hour base)
3. **Creates payment record** with calculated amount
4. **Displays session** with payment pending

### When patient clicks "Pay Now":

1. **Frontend calls** `/api/create-paddle-price` with duration
2. **Backend looks up** pre-configured price ID for that duration
3. **Returns price ID** to frontend
4. **Paddle checkout opens** with that price
5. **Patient completes payment**
6. **Webhook updates** payment status to "completed"

## Benefits of Standardization

✅ **Simpler pricing** - No confusion about different therapist rates
✅ **Easier to manage** - Only 4 prices to create in Paddle
✅ **Consistent experience** - All patients pay the same for same duration
✅ **No API issues** - Uses pre-configured prices (more reliable)

## Future Considerations

If you want to add therapist-specific pricing later:
- Remove the hardcoded `$hourlyRate = 100`
- Restore `$hourlyRate = $therapist->therapistProfile->hourly_rate ?? 100`
- Create multiple price sets in Paddle for different rate tiers
- Update `PaddlePriceMapper` to handle rate-specific mappings

For now, the standardized approach is simpler and works great!
