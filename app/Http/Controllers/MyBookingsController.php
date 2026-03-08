<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AstrologerBookingService;

class MyBookingsController extends Controller
{
    /**
     * Display the user's bookings page.
     */
    public function index(Request $request, AstrologerBookingService $bookingService)
    {
        $token = $request->cookie('auth_api_token');
        $response = $bookingService->getBookings($token);
        $bookings = $response['data'] ?? [];
        return view('my-bookings', compact('bookings'));
    }

    public function show($id, Request $request, AstrologerBookingService $bookingService)
    {
        $token = $request->cookie('auth_api_token');
        $response = $bookingService->getBookings($token);
        $booking = collect($response['data'] ?? [])->firstWhere('id', (int)$id);
        $suggestedProducts = [];

        // Compute astrologer initials if booking and astrologer name exist
        $astroInitials = '';
        if ($booking && isset($booking['astrologer']['name'])) {
            $names = preg_split('/\s+/', trim($booking['astrologer']['name']));
            foreach ($names as $n) {
                if ($n !== '') {
                    $astroInitials .= mb_strtoupper(mb_substr($n, 0, 1));
                }
            }
        }

        if ($booking) {
            $astrologerId = $this->firstNumericValue($booking, [
                'astrologer_id',
                'astrologer.id',
                'assigned_astrologer_id',
            ]) ?? session('api_user_id');

            $cartResponse = app(\App\Services\Api\Clients\AstrologerApiService::class)->getAstrologerCarts([
                'astrologer_id' => (int) $astrologerId,
                'booking_id' => (int) $booking['id'],
            ], $token);
            //dd($cartResponse);
            $suggestedProducts = $this->extractSuggestedProductsForBooking($cartResponse, (int) $booking['id']);
        }

        return view('booking-details', compact('booking', 'astroInitials', 'suggestedProducts'));
    }

    public function activeConsultationStatus(Request $request, AstrologerBookingService $bookingService)
    {
        $token = $request->cookie('auth_api_token');

        if (!$token) {
            return response()->json([
                'success' => true,
                'active' => false,
            ]);
        }

        $response = $bookingService->getBookings($token);
        $bookings = collect($response['data'] ?? []);

        $activeBooking = $bookings
            ->filter(function ($booking) {
                return is_array($booking) && (($booking['status'] ?? null) === 'in_progress');
            })
            ->sortByDesc(function ($booking) {
                return (int) ($booking['id'] ?? 0);
            })
            ->first();

        if (!$activeBooking) {
            return response()->json([
                'success' => true,
                'active' => false,
            ]);
        }

        $bookingId = (int) ($activeBooking['id'] ?? 0);

        return response()->json([
            'success' => true,
            'active' => true,
            'bookingId' => $bookingId,
            'status' => $activeBooking['status'] ?? 'in_progress',
            'joinUrl' => route('customer.consultation.video', ['meetingId' => 'astro-' . $bookingId]),
            'bookingDetailsUrl' => route('booking.details', ['id' => $bookingId]),
            'astrologerName' => $activeBooking['astrologer']['name'] ?? 'your astrologer',
        ]);
    }

    private function extractSuggestedProductsForBooking(array $response, int $bookingId): array
    {
        $payload = $response['data'] ?? $response;
        $items = [];

        if (isset($payload['data']) && is_array($payload['data'])) {
            $items = $payload['data'];
        } elseif (isset($payload['items']) && is_array($payload['items'])) {
            $items = $payload['items'];
        } elseif (isset($payload['carts']) && is_array($payload['carts'])) {
            $items = $payload['carts'];
        } elseif (array_is_list($payload)) {
            $items = $payload;
        }

        $normalized = array_values(array_filter(array_map(function ($item) use ($bookingId) {
            return is_array($item) ? $this->normalizeSuggestedProductItem($item, $bookingId) : null;
        }, $items)));

        usort($normalized, function ($left, $right) {
            return ($right['id'] ?? 0) <=> ($left['id'] ?? 0);
        });

        return $normalized;
    }

    private function normalizeSuggestedProductItem(array $item, int $bookingId): ?array
    {
        $itemBookingId = $this->firstNumericValue($item, [
            'booking_id',
            'booking.id',
            'cart.booking_id',
        ]);

        if ($itemBookingId === null || (int) $itemBookingId !== $bookingId) {
            return null;
        }

        $productId = $this->firstNumericValue($item, [
            'product_id',
            'product.id',
        ]);

        $variationId = $this->firstNumericValue($item, [
            'product_variation_options_id',
            'variation_id',
            'variation.id',
            'variant_id',
        ]);

        $quantity = $this->firstNumericValue($item, [
            'quantity',
            'qty',
        ]) ?? 1;

        $price = $this->firstNumericValue($item, [
            'product.price',
            'product.product_price',
            'amount',
            'price',
            'variation_price',
        ]);

        $productPrice = $this->firstNumericValue($item, [
            'product.product_price',
            'product.price',
            'price',
        ]);

        $discountRate = $this->firstNumericValue($item, [
            'product.discount_rate',
            'discount_rate',
        ]);

        $discountedPrice = $productPrice;

        if ($productPrice !== null && $discountRate !== null && $discountRate > 0) {
            $discountedPrice = round($productPrice - (($productPrice * $discountRate) / 100), 2);
        }

        if ($discountedPrice === null) {
            $discountedPrice = $price;
        }

        $image = $this->firstScalarValue($item, [
            'product.image',
            'product.image_url',
            'image',
            'image_url',
        ]);

        return [
            'id' => $item['id'] ?? null,
            'product_id' => $productId,
            'variation_id' => $variationId,
            'name' => $this->firstScalarValue($item, [
                'product.name',
                'product.title',
                'product_name',
                'name',
            ]) ?? 'Suggested Product',
            'price' => $discountedPrice,
            'original_price' => $productPrice ?? $price,
            'product_price' => $productPrice,
            'discount_rate' => $discountRate,
            'currency_symbol' => $this->firstScalarValue($item, [
                'currency_symbol',
                'product.currency_symbol',
            ]) ?? '₹',
            'quantity' => $quantity,
            'image' => $image,
            'slug' => $this->firstScalarValue($item, [
                'product.slug',
                'slug',
            ]),
            'url' => $this->firstScalarValue($item, [
                'product.url',
                'product.product_url',
                'url',
            ]),
            'grade' => $this->firstScalarValue($item, [
                'product.product_grade_name',
                'product.product_grade.name',
                'product.grade.name',
                'variation_name',
                'grade',
            ]),
            'ratti' => $this->firstScalarValue($item, [
                'product.ratti',
                'product.weight.ratti',
                'ratti',
            ]),
            'carat' => $this->firstScalarValue($item, [
                'product.carat',
                'product.weight.carat',
                'carat',
            ]),
        ];
    }

    private function firstScalarValue(array $source, array $paths): ?string
    {
        foreach ($paths as $path) {
            $value = data_get($source, $path);

            if (!is_scalar($value)) {
                continue;
            }

            $value = trim((string) $value);
            if ($value === '') {
                continue;
            }

            return $value;
        }

        return null;
    }

    private function firstNumericValue(array $source, array $paths): ?float
    {
        foreach ($paths as $path) {
            $value = data_get($source, $path);

            if (!is_numeric($value)) {
                continue;
            }

            return (float) $value;
        }

        return null;
    }
}
