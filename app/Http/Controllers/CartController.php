<?php

namespace App\Http\Controllers;

use App\Exceptions\BookStoreException;
use App\Models\Book;
use App\Models\Cart;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class CartController extends Controller
{
    /**
     * @OA\Post(
     *   path="/api/addtocart",
     *   summary="Add the book to Cart",
     *   description=" Add to cart ",
     *   @OA\RequestBody(
     *         @OA\JsonContent(),
     *         @OA\MediaType(
     *            mediaType="multipart/form-data",
     *            @OA\Schema(
     *               type="object",
     *               required={"book_id"},
     *               @OA\Property(property="book_id", type="integer"),
     *            ),
     *        ),
     *    ),
     *   @OA\Response(response=201, description="Book added to Cart Sucessfully"),
     *   @OA\Response(response=404, description="Invalid authorization token"),
     *   security = {
     * {
     * "Bearer" : {}}}
     * )
     */
    /**
     * This Function will take book id as input and it will ad that book to cart
     * as per user's requirement
     */
    public function addBookToCartByBookId(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'book_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }

        try {
            $currentUser = JWTAuth::parseToken()->authenticate();
            $cart = new Cart();
            $book = new Book();
            $user = new User();
            $userId = $user->userVerification($currentUser->id);
            if (count($userId) == 0) {
                return response()->json(['message' => 'You are not an User'], 404);
            }
            if ($currentUser) {
                $book_id = $request->input('book_id');
                $book_existance = $book->findBook($book_id);

                if (!$book_existance) {
                    return response()->json([
                        'status' => 404,
                        'message' => 'Book not Found'
                    ], 404);
                }
                $books = Book::find($book_id);
                if ($books->quantity == 0) {
                    return response()->json([
                        'status' => 404,
                        'message' => 'OUT OF STOCK'
                    ], 404);
                }
                $book_cart = $cart->bookCart($book_id, $currentUser->id);
                // $book_cart = Cart::select('id')->where([
                //     ['book_id', '=', $book_id],
                //     ['user_id', '=', $currentUser->id]
                // ])->get();

                if (!$book_cart) {
                    $book_cart->book_quantity += 1;
                    $book_cart->save();
                    return response()->json([
                        'status' => 404,
                        'message' => 'Book quantity increament'
                    ], 404);
                }

                $cart->book_id = $request->get('book_id');

                if ($currentUser->carts()->save($cart)) {
                    $value = Cache::remember('carts', 3600, function () {
                        return DB::table('carts')->get();
                    });
                    return response()->json([
                        'cart' => $book_cart,
                        'message' => 'Book added to Cart Sucessfully'], 201);
                }

                return response()->json(['message' => 'Book cannot be added to Cart'], 405);
            } else {
                Log::error('Invalid User');
                throw new BookStoreException("Invalid authorization token", 404);
            }
        } catch (BookStoreException $exception) {
            return $exception->message();
        }
    }

    /**
     * @OA\Post(
     *   path="/api/deletecart",
     *   summary="Delete the book from cart",
     *   description=" Delete cart ",
     *   @OA\RequestBody(
     *         @OA\JsonContent(),
     *         @OA\MediaType(
     *            mediaType="multipart/form-data",
     *            @OA\Schema(
     *               type="object",
     *               required={"id"},
     *               @OA\Property(property="id", type="integer"),
     *            ),
     *        ),
     *    ),
     *   @OA\Response(response=201, description="Book deleted Sucessfully from cart"),
     *   @OA\Response(response=404, description="Invalid authorization token"),
     *   security = {
     * {
     * "Bearer" : {}}}
     * )
     */
    /**
     * This Function will take cart Id as input and will perform the delete operation
     * for the perticular cart which the user want to delete from cart
     */
    public function deleteBookByCartId(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }

        try {
            $id = $request->input('id');
            $currentUser = JWTAuth::parseToken()->authenticate();
            // $cart = new Cart();
            $user = new User();
            $userId = $user->userVerification($currentUser->id);
            if (!$userId) {
                return response()->json([
                    'status' => 404,
                    'message' => 'You are not an User'
                ], 404);
            }
            if (!$currentUser) {
                Log::error('Invalid User');
                throw new BookStoreException("Invalid authorization token", 404);
            }
            $book = $currentUser->carts()->find($id);
            if (!$book) {
                Log::error('Book Not Found', ['id' => $request->id]);
                return response()->json(['message' => 'Book not Found in cart'], 404);
            }

            if ($book->delete()) {
                Log::info('book deleted', ['user_id' => $currentUser, 'book_id' => $request->id]);
                Cache::forget('carts');
                return response()->json(['message' => 'Book deleted Sucessfully from cart'], 201);
            }
        } catch (BookStoreException $exception) {
            return $exception->message();
        }
    }

    /**
     * @OA\Get(
     *   path="/api/getcart",
     *   summary="Get All Books Present in Cart",
     *   description=" Get All Books Present in Cart ",
     *   @OA\RequestBody(
     *
     *    ),
     *   @OA\Response(response=404, description="Invalid authorization token"),
     *   security = {
     * {
     * "Bearer" : {}}}
     * )
     */
     /**
     * This method will execute and return for the current user which books are added
     * in the cart and return all data
     */
    public function getAllBooksByUserId()
    {
        try {
            $currentUser = JWTAuth::parseToken()->authenticate();
            $user = new User();
            // $cart = new Cart();
            $userId = $user->userVerification($currentUser->id);
            if (!$userId) {
                return response()->json(['message' => 'You are not an User'], 404);
            }
            if ($currentUser) {
                $books = Cart::leftJoin('books', 'carts.book_id', '=', 'books.id')
                    ->select('books.id', 'books.name', 'books.author', 'books.description', 'books.Price', 'carts.book_quantity')
                    ->where('carts.user_id', '=', $currentUser->id)
                    ->get();

                if ($books == '[]') {
                    Log::error('Book Not Found');
                    return response()->json(['message' => 'Books not found'], 404);
                }
                Log::info('All book Presnet in cart are fetched');
                return response()->json([
                    'message' => 'Books Present in Cart :',
                    'Cart' => $books,

                ], 201);
            } else {
                Log::error('Invalid User');
                throw new BookStoreException("Invalid authorization token", 404);
            }
        } catch (BookStoreException $exception) {
            return $exception->message();
        }
    }
}