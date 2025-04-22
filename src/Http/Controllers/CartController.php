<?php

namespace MiladSarli\CartSystem\Http\Controllers;

use Illuminate\Routing\Controller;
use MiladSarli\CartSystem\Models\Cart;
use MiladSarli\CartSystem\Models\Transaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CartController extends Controller
{
    public function index(): JsonResponse
    {
        $cartItems = Cart::with(['product', 'productAttrValue.color', 'productAttrValue.attrValue.attr'])
            ->where('user_id', Auth::id())
            ->pending()
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'quantity' => $item->quantity,
                    'price' => $item->price,
                    'product' => [
                        'id' => $item->product->id,
                        'name' => $item->product->name,
                        'price' => $item->product->price,
                        'images' => $item->product->images,
                    ],
                    'variant' => $item->productAttrValue ? [
                        'id' => $item->productAttrValue->id,
                        'price' => $item->productAttrValue->price,
                        'color' => $item->productAttrValue->color ? [
                            'name' => $item->productAttrValue->color->color_name,
                            'code' => $item->productAttrValue->color->color
                        ] : null,
                        'attribute' => $item->productAttrValue->attrValue ? [
                            'name' => $item->productAttrValue->attrValue->attr->name,
                            'value' => $item->productAttrValue->attrValue->value
                        ] : null,
                    ] : null,
                    'total_price' => $item->getTotalPrice()
                ];
            });

        $totalAmount = $cartItems->sum('total_price');

        return response()->json([
            'status' => 'success',
            'data' => [
                'items' => $cartItems,
                'total_amount' => $totalAmount
            ]
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'variant_id' => 'nullable|exists:product_attr_values,id',
            'quantity' => 'required|integer|min:1',
            'tenant_id' => 'nullable|exists:tenants,id'
        ]);

        $productModel = config('cart.models.product');
        $productAttrValueModel = config('cart.models.product_attr_value');

        // Check if product exists and is available
        $product = $productModel::findOrFail($request->product_id);
        if (!$product->exist || !$product->status) {
            throw ValidationException::withMessages([
                'product' => 'محصول در دسترس نمی‌باشد'
            ]);
        }

        // Check variant if provided
        if ($request->variant_id) {
            $variant = $productAttrValueModel::findOrFail($request->variant_id);
            if (!$variant->exist || $variant->quantity < $request->quantity) {
                throw ValidationException::withMessages([
                    'quantity' => 'موجودی محصول کافی نیست'
                ]);
            }
        } elseif ($product->quantity < $request->quantity) {
            throw ValidationException::withMessages([
                'quantity' => 'موجودی محصول کافی نیست'
            ]);
        }

        // Check if item already exists in cart
        $cartQuery = Cart::where('user_id', Auth::id())
            ->where('product_id', $request->product_id)
            ->where('product_attr_value_id', $request->variant_id);

        // Add tenant_id to query if provided
        if ($request->tenant_id) {
            $cartQuery->where('tenant_id', $request->tenant_id);
        }

        $cartItem = $cartQuery->pending()->first();

        DB::beginTransaction();
        try {
            if ($cartItem) {
                $cartItem->update([
                    'quantity' => $cartItem->quantity + $request->quantity
                ]);
                $cartItem->updatePrice();
            } else {
                $cartData = [
                    'user_id' => Auth::id(),
                    'product_id' => $request->product_id,
                    'product_attr_value_id' => $request->variant_id,
                    'product_code' => $variant->product_code ?? $product->product_code,
                    'quantity' => $request->quantity,
                    'color_code' => $variant->color->color ?? null,
                    'ip_address' => $request->ip()
                ];

                // Add tenant_id if provided
                if ($request->tenant_id) {
                    $cartData['tenant_id'] = $request->tenant_id;
                } elseif ($product->tenant_id) {
                    $cartData['tenant_id'] = $product->tenant_id;
                }

                $cartItem = Cart::create($cartData);
                $cartItem->updatePrice();
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }

        return response()->json([
            'status' => 'success',
            'message' => 'محصول با موفقیت به سبد خرید اضافه شد',
            'data' => $cartItem
        ]);
    }

    public function update(Request $request, Cart $cart): JsonResponse
    {
        if ($cart->user_id !== Auth::id()) {
            return response()->json([
                'status' => 'error',
                'message' => 'شما دسترسی به این آیتم ندارید'
            ], 403);
        }

        if ($cart->status !== 'pending') {
            return response()->json([
                'status' => 'error',
                'message' => 'این سبد خرید قابل ویرایش نیست'
            ], 400);
        }

        $request->validate([
            'quantity' => 'required|integer|min:1'
        ]);

        // Check quantity availability
        if ($cart->product_attr_value_id) {
            if ($cart->productAttrValue->quantity < $request->quantity) {
                throw ValidationException::withMessages([
                    'quantity' => 'موجودی محصول کافی نیست'
                ]);
            }
        } elseif ($cart->product->quantity < $request->quantity) {
            throw ValidationException::withMessages([
                'quantity' => 'موجودی محصول کافی نیست'
            ]);
        }

        DB::beginTransaction();
        try {
            $cart->update([
                'quantity' => $request->quantity
            ]);
            $cart->updatePrice();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }

        return response()->json([
            'status' => 'success',
            'message' => 'تعداد محصول با موفقیت بروزرسانی شد',
            'data' => $cart
        ]);
    }

    public function destroy(Cart $cart): JsonResponse
    {
        if ($cart->user_id !== Auth::id()) {
            return response()->json([
                'status' => 'error',
                'message' => 'شما دسترسی به این آیتم ندارید'
            ], 403);
        }

        if ($cart->status !== 'pending') {
            return response()->json([
                'status' => 'error',
                'message' => 'این سبد خرید قابل حذف نیست'
            ], 400);
        }

        $cart->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'محصول با موفقیت از سبد خرید حذف شد'
        ]);
    }

    public function clear(): JsonResponse
    {
        Cart::where('user_id', Auth::id())->pending()->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'سبد خرید با موفقیت خالی شد'
        ]);
    }

    public function checkout(Request $request): JsonResponse
    {
        $request->validate([
            'address_id' => 'required|exists:addresses,id'
        ]);

        $cartItems = Cart::where('user_id', Auth::id())
            ->pending()
            ->get();

        if ($cartItems->isEmpty()) {
            return response()->json([
                'status' => 'error',
                'message' => 'سبد خرید شما خالی است'
            ], 400);
        }

        $totalAmount = $cartItems->sum(function ($item) {
            return $item->getTotalPrice();
        });

        DB::beginTransaction();
        try {
            // Update cart items with address and mark as processing
            foreach ($cartItems as $item) {
                $item->update([
                    'address_id' => $request->address_id,
                    'status' => 'processing'
                ]);
            }

            // Create transaction
            $transaction = Transaction::create([
                'user_id' => Auth::id(),
                'cart_id' => $cartItems->first()->id,
                'address_id' => $request->address_id,
                'amount' => $totalAmount,
                'status' => 'pending'
            ]);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'سفارش شما با موفقیت ثبت شد',
                'data' => [
                    'transaction_id' => $transaction->id,
                    'amount' => $totalAmount
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
