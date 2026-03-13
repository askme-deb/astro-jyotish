<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\Api\BaseApiClient;

class AstrologerAppointmentsController extends Controller
{
    public function index(Request $request)
    {
        // $user = Auth::user();
        // $userId = $user ? $user->id : null;
            // Get user ID from session (set by LoginController)
            $userId = session('api_user_id');
            if (!$userId) {
                return redirect()->route('login');
            }

        // Use existing AstrologerApiService from Services/Api/Clients
        $apiService = app(\App\Services\Api\Clients\AstrologerApiService::class);
        $token = session('auth.api_token');
        $response = $apiService->getAstrologerBookings($userId, $token);
        dd($response);
        $appointments = $response['data'] ?? [];

        return view('astrologer.appointments', compact('appointments'));
    }


    public function show($id)
    {
        $userId = session('api_user_id');
        $token = session('auth.api_token');
        $apiService = app(\App\Services\Api\Clients\AstrologerApiService::class);
        $response = $apiService->getAstrologerBookings($userId, $token);
        $appointments = $response['data'] ?? [];
        $appointment = collect($appointments)->firstWhere('id', $id);
        $suggestedProducts = [];

        if (is_array($appointment) && !empty($appointment)) {
            $astrologerId = $this->firstNumericValue($appointment, [
                'astrologer_id',
                'astrologer.id',
                'assigned_astrologer_id',
            ]) ?? $userId;

            $cartResponse = $apiService->getAstrologerSuggestedProducts([
                'astrologer_id' => (int) $astrologerId,
                'booking_id' => (int) $appointment['id'],
            ], $token);

            $suggestedProducts = $this->extractSuggestedProductsForBooking($cartResponse, (int) $appointment['id']);
        }

        $productCategories = $this->getProductCategories();
        $productGrades = $this->getProductGrades();

        return view('astrologer.appointment-details', compact('appointment', 'suggestedProducts', 'productCategories', 'productGrades'));
    }

    private function getProductCategories(): array
    {
        $response = app(\App\Services\Api\Clients\AstrologerApiService::class)->getCategories(session('auth.api_token'));
        $payload = $response['data'] ?? $response;

        if (isset($payload['data']) && is_array($payload['data'])) {
            $payload = $payload['data'];
        } elseif (isset($payload['categories']) && is_array($payload['categories'])) {
            $payload = $payload['categories'];
        }

        if (!is_array($payload)) {
            return [];
        }

        $flatCategories = [];
        $groupedCategories = [];

        foreach ($payload as $item) {
            if (!is_array($item) || empty($item['id']) || empty($item['name'])) {
                continue;
            }

            $normalized = [
                'id' => (int) $item['id'],
                'name' => (string) $item['name'],
                'parent_id' => isset($item['parent_id']) && is_numeric($item['parent_id']) ? (int) $item['parent_id'] : null,
            ];

            $flatCategories[$normalized['id']] = $normalized;

            if (!empty($item['children']) && is_array($item['children'])) {
                $options = [];

                foreach ($item['children'] as $child) {
                    if (!is_array($child) || empty($child['id']) || empty($child['name'])) {
                        continue;
                    }

                    $options[] = [
                        'id' => (int) $child['id'],
                        'name' => (string) $child['name'],
                    ];

                    $flatCategories[(int) $child['id']] = [
                        'id' => (int) $child['id'],
                        'name' => (string) $child['name'],
                        'parent_id' => $normalized['id'],
                    ];
                }

                if ($options) {
                    $groupedCategories[] = [
                        'label' => $normalized['name'],
                        'options' => $options,
                    ];
                }
            }
        }

        if ($groupedCategories) {
            $usedOptionIds = collect($groupedCategories)
                ->flatMap(function ($group) {
                    return collect($group['options'] ?? [])->pluck('id');
                })
                ->all();

            $standaloneCategories = array_values(array_filter($flatCategories, function ($category) use ($usedOptionIds) {
                return !in_array($category['id'], $usedOptionIds, true) && $category['parent_id'] === null;
            }));

            if ($standaloneCategories) {
                $groupedCategories[] = [
                    'label' => 'Other Categories',
                    'options' => array_map(function ($category) {
                        return [
                            'id' => $category['id'],
                            'name' => $category['name'],
                        ];
                    }, $standaloneCategories),
                ];
            }

            return $groupedCategories;
        }

        $rootCategories = array_values(array_filter($flatCategories, function ($category) {
            return $category['parent_id'] === null;
        }));

        $childGroups = [];
        foreach ($flatCategories as $category) {
            if ($category['parent_id'] === null) {
                continue;
            }

            $parentName = $flatCategories[$category['parent_id']]['name'] ?? 'Other Categories';
            $childGroups[$parentName][] = [
                'id' => $category['id'],
                'name' => $category['name'],
            ];
        }

        $groups = [];
        foreach ($childGroups as $label => $options) {
            $groups[] = [
                'label' => $label,
                'options' => $options,
            ];
        }

        if ($rootCategories) {
            $groups[] = [
                'label' => 'Other Categories',
                'options' => array_map(function ($category) {
                    return [
                        'id' => $category['id'],
                        'name' => $category['name'],
                    ];
                }, $rootCategories),
            ];
        }

        return $groups;
    }

    private function getProductGrades(): array
    {
        $response = app(\App\Services\Api\Clients\AstrologerApiService::class)->getProductGrades(session('auth.api_token'));
        $payload = $response['data'] ?? $response;

        if (isset($payload['data']) && is_array($payload['data'])) {
            $payload = $payload['data'];
        } elseif (isset($payload['product_grades']) && is_array($payload['product_grades'])) {
            $payload = $payload['product_grades'];
        } elseif (isset($payload['items']) && is_array($payload['items'])) {
            $payload = $payload['items'];
        }

        if (!is_array($payload)) {
            return [];
        }

        $grades = [];

        foreach ($payload as $item) {
            if (!is_array($item) || empty($item['id']) || empty($item['name'])) {
                continue;
            }

            $grades[] = [
                'id' => (int) $item['id'],
                'name' => (string) $item['name'],
            ];
        }

        usort($grades, function ($left, $right) {
            return strcasecmp($left['name'], $right['name']);
        });

        return $grades;
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
