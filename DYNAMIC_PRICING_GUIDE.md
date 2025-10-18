# Dynamic Pricing with Paddle - Implementation Guide

This guide explains how the dynamic pricing system works with Paddle integration in your online therapy application.

## Overview

The dynamic pricing system calculates session costs based on:
- Session duration (30min, 60min, 90min, 120min)
- Therapist's hourly rate
- Duration-based discounts for longer sessions

## Architecture

### Backend Components

#### 1. **PricingService** (`app/Services/PricingService.php`)
Handles all pricing calculations.

**Key Methods:**
- `calculateSessionPrice(User $therapist, int $durationMinutes): float`
  - Calculates the final price for a session
  - Example: 60-minute session with $100/hour therapist = $100

- `getDurationMultiplier(int $durationMinutes): float`
  - Returns discount multipliers:
    - 30-60 min: 1.0 (no discount)
    - 90 min: 0.95 (5% discount)
    - 120 min: 0.90 (10% discount)
    - 120+ min: 0.85 (15% discount)

- `getPriceBreakdown(User $therapist, int $durationMinutes): array`
  - Returns detailed pricing information including base price, discounts, and final price

#### 2. **PaddleService** (`app/Services/PaddleService.php`)
Integrates with Paddle's API.

**Key Methods:**
- `createCustomPrice(string $productName, float $amount, string $currency = 'USD'): ?string`
  - Creates a custom price in Paddle for one-time payments
  - Returns a `price_id` for use in checkout

- `createTransaction(array $items, array $customer, array $customData = []): ?array`
  - Creates a Paddle transaction (optional, for server-side checkout)

#### 3. **PaddleController** (`app/Http/Controllers/Api/PaddleController.php`)
API endpoints for pricing and Paddle integration.

**Endpoints:**

| Method | Route | Description |
|--------|-------|-------------|
| POST | `/api/create-paddle-price` | Creates a custom Paddle price |
| POST | `/api/calculate-session-price` | Calculates price for given duration |
| GET | `/api/session-duration-options` | Returns available durations with prices |
| POST | `/api/paddle/webhook` | Handles Paddle webhooks |

### Frontend Components

#### 1. **Sessions/Show.vue** (Updated)
- Initializes Paddle on component mount
- `openCheckout()` function creates custom price and opens Paddle checkout
- Includes session_id in customData for webhook tracking

#### 2. **DynamicPricingSelector.vue** (New Component)
Reusable component for selecting session duration with live pricing.

**Props:**
- `therapistId`: The therapist's user ID
- `modelValue`: Two-way binding for selected duration

**Events:**
- `update:modelValue`: Emits selected duration
- `priceCalculated`: Emits price breakdown data

**Usage Example:**
```vue
<DynamicPricingSelector
    :therapist-id="therapist.id"
    v-model="selectedDuration"
    @priceCalculated="handlePriceCalculated"
/>
```

## Configuration

### Environment Variables

Add these to your `.env` file:

```env
PADDLE_API_KEY=your_paddle_api_key
PADDLE_VENDOR_ID=your_vendor_id
PADDLE_CLIENT_SIDE_TOKEN=your_client_side_token
```

### Paddle Environment

Update in `Show.vue` line 263:
```javascript
window.Paddle.Environment.set('sandbox'); // For testing
// or
window.Paddle.Environment.set('production'); // For production
```

## Usage Guide

### 1. Booking a Session with Dynamic Pricing

When creating a booking form, include the duration selector:

```vue
<template>
    <form @submit.prevent="bookSession">
        <!-- Therapist selection -->
        <select v-model="selectedTherapist">
            <option v-for="therapist in therapists" :value="therapist.id">
                {{ therapist.name }}
            </option>
        </select>

        <!-- Dynamic Pricing Component -->
        <DynamicPricingSelector
            v-if="selectedTherapist"
            :therapist-id="selectedTherapist"
            v-model="selectedDuration"
            @priceCalculated="priceBreakdown = $event"
        />

        <!-- Time slot selection -->
        <!-- ... -->

        <button type="submit">
            Book Session - ${{ priceBreakdown?.final_price }}
        </button>
    </form>
</template>

<script setup>
import DynamicPricingSelector from '@/Components/DynamicPricingSelector.vue';
import { ref } from 'vue';

const selectedTherapist = ref(null);
const selectedDuration = ref(60);
const priceBreakdown = ref(null);

const bookSession = () => {
    // Submit with selectedDuration
    router.post('/sessions', {
        therapist_id: selectedTherapist.value,
        availability_slot_id: selectedSlot.value,
        duration_minutes: selectedDuration.value,
    });
};
</script>
```

### 2. Processing Payments

The payment flow in `Show.vue`:

1. User clicks "Pay Now with Cards"
2. `openCheckout()` is called
3. Backend creates a custom Paddle price via `/api/create-paddle-price`
4. Paddle checkout overlay opens with the custom price
5. User completes payment
6. Paddle webhook notifies your server at `/api/paddle/webhook`
7. Payment status is updated in the database

### 3. Webhook Setup

Configure Paddle webhook in your Paddle dashboard:
- **URL**: `https://yourdomain.com/api/paddle/webhook`
- **Events to subscribe**:
  - `transaction.completed`
  - `transaction.payment_failed`

## Customization

### Modify Duration Options

Edit `PricingService.php`:

```php
public function getAvailableDurations(): array
{
    return [
        ['value' => 30, 'label' => '30 minutes'],
        ['value' => 45, 'label' => '45 minutes'], // Add new duration
        ['value' => 60, 'label' => '1 hour'],
        ['value' => 90, 'label' => '1.5 hours'],
        ['value' => 120, 'label' => '2 hours'],
    ];
}
```

### Modify Discount Rates

Edit the `getDurationMultiplier()` method in `PricingService.php`:

```php
private function getDurationMultiplier(int $durationMinutes): float
{
    return match (true) {
        $durationMinutes <= 30 => 1.1,      // 10% premium for short sessions
        $durationMinutes <= 60 => 1.0,      // Standard rate
        $durationMinutes <= 90 => 0.93,     // 7% discount
        $durationMinutes <= 120 => 0.88,    // 12% discount
        default => 0.80,                     // 20% discount for very long
    };
}
```

## API Reference

### Calculate Session Price

```http
POST /api/calculate-session-price
Content-Type: application/json

{
    "therapist_id": 1,
    "duration_minutes": 90
}
```

**Response:**
```json
{
    "hourly_rate": 100,
    "duration_minutes": 90,
    "duration_hours": 1.5,
    "base_price": 150,
    "multiplier": 0.95,
    "discount": 7.5,
    "final_price": 142.5,
    "currency": "USD"
}
```

### Get Duration Options

```http
GET /api/session-duration-options?therapist_id=1
```

**Response:**
```json
{
    "durations": [
        {
            "value": 30,
            "label": "30 minutes",
            "price": 50
        },
        {
            "value": 60,
            "label": "1 hour",
            "price": 100
        }
    ],
    "therapist": {
        "id": 1,
        "name": "Dr. Smith",
        "hourly_rate": 100
    }
}
```

### Create Paddle Price

```http
POST /api/create-paddle-price
Content-Type: application/json

{
    "productName": "Therapy session with Dr. Smith",
    "price": 142.5,
    "quantity": 1,
    "userId": 5
}
```

**Response:**
```json
{
    "price_id": "pri_01ht2v...",
    "amount": 142.5
}
```

## Testing

### Test the Pricing Calculation

```php
use App\Services\PricingService;
use App\Models\User;

$pricingService = new PricingService();
$therapist = User::find(1);

// Test 90-minute session
$price = $pricingService->calculateSessionPrice($therapist, 90);
// Expected: (hourly_rate / 60 * 90) * 0.95

// Test breakdown
$breakdown = $pricingService->getPriceBreakdown($therapist, 90);
// Returns detailed pricing info
```

### Test Paddle Integration (Sandbox)

1. Set Paddle to sandbox mode in `.env`
2. Use Paddle test cards: https://developer.paddle.com/concepts/payment-methods/credit-debit-card
3. Monitor webhook calls in Paddle dashboard

## Troubleshooting

### Issue: "Paddle is not loaded"

**Solution:** Ensure Paddle.js script is loaded in `resources/views/app.blade.php`:
```html
<script src="https://cdn.paddle.com/paddle/v2/paddle.js"></script>
```

### Issue: API returns 401 Unauthorized

**Solution:** Ensure Sanctum authentication is working. Check that:
1. User is logged in
2. CSRF token is included in requests
3. Sanctum middleware is applied to API routes

### Issue: Webhook not receiving events

**Solution:**
1. Verify webhook URL in Paddle dashboard
2. Check that route is not protected by auth middleware
3. Review webhook logs in Paddle dashboard
4. Check your application logs: `storage/logs/laravel.log`

## Security Considerations

1. **Price Validation**: Always recalculate prices server-side, never trust client-submitted prices
2. **Webhook Verification**: Paddle webhooks should be verified (implement signature verification for production)
3. **CSRF Protection**: API routes use Sanctum authentication
4. **Environment Variables**: Never commit `.env` file with real credentials

## Future Enhancements

Potential additions:
- [ ] Package deals (multiple sessions at discounted rate)
- [ ] Promotional codes/coupons
- [ ] Dynamic pricing based on therapist availability
- [ ] Subscription-based pricing for regular clients
- [ ] Currency conversion for international clients
- [ ] Tax calculation integration
- [ ] Refund handling through Paddle API

## Support

For issues related to:
- **Pricing Logic**: Check `PricingService.php`
- **Paddle Integration**: Check `PaddleService.php` and Paddle documentation
- **API Errors**: Check `PaddleController.php` and application logs
- **Frontend Issues**: Check browser console and Vue DevTools
