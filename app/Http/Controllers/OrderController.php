<?php

namespace App\Http\Controllers;

use App\Exceptions\BookStoreException;
use App\Models\Address;
use App\Models\Book;
use App\Models\Cart;
use App\Models\Order;
use App\Models\User;
use App\Notifications\SendOrderDetails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class OrderController extends Controller
{
    /**
     * @OA\Post(
     *   path="/api/placeorder",
     *   summary="Place  Order",
     *   description=" Place a order ",
     *   @OA\RequestBody(
     *         @OA\JsonContent(),
     *         @OA\MediaType(
     *            mediaType="multipart/form-data",
     *            @OA\Schema(
     *               type="object",
     *               required={"address_id","name", "quantity"},
     *               @OA\Property(property="address_id", type="integer"),
     *               @OA\Property(property="name", type="string"),
     *               @OA\Property(property="quantity", type="integer"),
     *            ),
     *        ),
     *    ),
     *   @OA\Response(response=201, description="Order Successfully Placed..."),
     *   @OA\Response(response=401, description="We Do not have this book in the store..."),
     *   security = {
     * {
     * "Bearer" : {}}}
     * )
     */
    public function placeOrder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'address_id' => 'required',
            'name' => 'required',
            'quantity' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }
        try {
            $currentUser = JWTAuth::parseToken()->authenticate();
            if ($currentUser) {
                $book = new Book();
                $address = new Address();
                $bookDetails = $book->getBookDetails($request->input('name'));
                if ($bookDetails == '') {
                    Log::error('Book is not available');
                    throw new BookStoreException("We Do not have this book in the store...", 401);
                }

                if ($bookDetails['quantity'] < $request->input('quantity')) {
                    Log::error('Book stock is not available');
                    throw new BookStoreException("This much stock is unavailable for the book", 401);
                }

                //getting addressID
                $getAddress = $address->addressExist($request->input('address_id'));
                if (!$getAddress) {
                    throw new BookStoreException("This address id not available", 401);
                }

                //calculate total price
                $total_price = $request->input('quantity') * $bookDetails['Price'];

                $order = Order::create([
                    'user_id' => $currentUser->id,
                    'book_id' => $bookDetails['id'],
                    'address_id' => $getAddress['id'],
                ]);

                $userId = User::where('id', $currentUser->id)->first();

                $delay = now()->addSeconds(5);
                $userId->notify((new SendOrderDetails($order->order_id, $bookDetails['name'], $bookDetails['author'], $request->input('quantity'), $total_price))->delay($delay));

                $bookDetails['quantity'] -= $request->quantity;
                $bookDetails->save();
                return response()->json([
                    'message' => 'Order Successfully Placed...',
                    'OrderId' => $order->order_id,
                    'Quantity' => $request->input('quantity'),
                    'Total_Price' => $total_price,
                    'message1' => 'Mail also sent to the user with all details',
                ], 201);
                Cache::remember('orders', 3600, function () {
                    return DB::table('orders')->get();
                });
            }
        } catch (BookStoreException $exception) {
            return $exception->message();
        }
    }

    /**
     * @OA\Post(
     *   path="/api/placeorderbycartid",
     *   summary="Place  Order",
     *   description=" Place a order ",
     *   @OA\RequestBody(
     *         @OA\JsonContent(),
     *         @OA\MediaType(
     *            mediaType="multipart/form-data",
     *            @OA\Schema(
     *               type="object",
     *               required={"address_id","user_id"},
     *               @OA\Property(property="address_id", type="integer"),
     *               @OA\Property(property="user_id", type="integer"),
     *            ),
     *        ),
     *    ),
     *   @OA\Response(response=201, description="Order Successfully Placed..."),
     *   @OA\Response(response=401, description="We Do not have this book in the store..."),
     *   security = {
     * {
     * "Bearer" : {}}}
     * )
     */
    public function placeOrderByCartId(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'address_id' => 'required',
            'user_id' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }
        try {
            $currentUser = JWTAuth::parseToken()->authenticate();
            if ($currentUser) {
                $total_order_price = 0;
                $cart = new Cart();
                $book = new Book();
                $address = new Address();

                $userId = $request->input('user_id');
                $cartDetails = $cart->getCartDetails($userId);

                if (!$cartDetails) {
                    throw new BookStoreException("We do not have this cart id", 401);
                }

                foreach ($cartDetails as $bookInfo) {
                    $bookDetails = $book->getBookDetailsById($bookInfo['book_id']);
                    if ($bookDetails == []) {
                        Log::error('Book is not available');
                        throw new BookStoreException($bookDetails . "We Do not have this book in the store...", 401);
                    }

                    if ($bookDetails['quantity'] < $bookInfo['book_quantity']) {
                        Log::error('Book stock is not available');
                        throw new BookStoreException("This much stock is unavailable for the book", 401);
                    }

                    $total_price = $bookInfo['book_quantity'] * $bookDetails['Price'];
                    $total_order_price += $total_price;
                    $getAddress = $address->addressExist($request->input('address_id'));
                    if (!$getAddress) {
                        throw new BookStoreException("This address id not available", 401);
                    }
                    $order = Order::create([
                        'user_id' => $currentUser->id,
                        'book_id' => $bookDetails['id'],
                        'address_id' => $getAddress['id'],
                    ]);

                    $bookDetails['quantity'] -= $bookInfo['book_quantity'];
                    $bookDetails->save();

                    $userId = User::where('id', $currentUser->id)->first();

                    $delay = now()->addSeconds(5);
                    $userId->notify((new SendOrderDetails($order->order_id, $bookDetails['name'], $bookDetails['author'], $bookInfo['book_quantity'], $total_price))->delay($delay));
                    $bookInfo->delete();
                }

                return response()->json([
                    'message' => 'Order Successfully Placed...',
                    'Total_Price' => $total_order_price,
                    'message1' => 'Mail also sent to the user with all details',
                ], 201);
                Cache::remember('orders', 3600, function () {
                    return DB::table('orders')->get();
                });
            }
        } catch (BookStoreException $exception) {
            return $exception->message();
        }
    }
}
