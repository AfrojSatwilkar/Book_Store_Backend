<?php

namespace App\Http\Controllers;

use App\Exceptions\BookStoreException;
use App\Models\Address;
use App\Models\Book;
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
     *               required={"name", "quantity"},
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
            'name' => 'required',
            'quantity' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }
        try {
            $currentUser = JWTAuth::parseToken()->authenticate();
            if ($currentUser) {
                $get_book = Book::where('name', '=', $request->input('name'))->first();
                if ($get_book == '') {
                    Log::error('Book is not available');
                    throw new BookStoreException("We Do not have this book in the store...", 401);
                }
                $get_quantity = Book::select('quantity')
                    ->where([['books.user_id', '=', $currentUser->id], ['books.name', '=', $request->input('name')]])
                    ->get();

                if ($get_quantity < $request->input('quantity')) {
                    Log::error('Book stock is not available');
                    throw new BookStoreException("This much stock is unavailable for the book", 401);
                }
                //getting bookID
                $get_bookid = Book::select('id')
                    ->where([['books.name', '=', $request->input('name')]])
                    ->value('id');

                //getting addressID
                $get_addressid = Address::select('id')
                    ->where([['user_id', '=', $currentUser->id]])
                    ->value('id');

                //get book name..
                $get_BookName = Book::select('name')
                    ->where('name', '=', $request->input('name'))
                    ->value('name');

                //get book author ...
                $get_BookAuthor = Book::select('author')
                    ->where('name', '=', $request->input('name'))
                    ->value('author');

                //get book price
                $get_price = Book::select('Price')
                    ->where([['books.name', '=', $request->input('name')]])
                    ->value('Price');

                //calculate total price
                $total_price = $request->input('quantity') * $get_price;

                $order = Order::create([
                    'user_id' => $currentUser->id,
                    'book_id' => $get_bookid,
                    'address_id' => $get_addressid
                ]);

                $userId = User::where('id', $currentUser->id)->first();

                $delay = now()->addSeconds(10);
                $userId->notify((new SendOrderDetails($order->order_id, $get_BookName, $get_BookAuthor, $request->input('quantity'), $total_price))->delay($delay));

                $book = Book::find($get_bookid);
                $book->quantity -= $request->quantity;
                $book->save();
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
}
