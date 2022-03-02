<?php

namespace App\Http\Controllers;

use App\Exceptions\BookStoreException;
use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class BookController extends Controller
{
    /**
     * @OA\Post(
     *   path="/api/addbook",
     *   summary="Add Book",
     *   description="Admin Can Add Book ",
     *   @OA\RequestBody(
     *         @OA\JsonContent(),
     *         @OA\MediaType(
     *            mediaType="multipart/form-data",
     *            @OA\Schema(
     *               type="object",
     *               required={"name","description","author","image", "Price", "quantity"},
     *               @OA\Property(property="name", type="string"),
     *               @OA\Property(property="description", type="string"),
     *               @OA\Property(property="author", type="string"),
     *               @OA\Property(property="image", type="file"),
     *               @OA\Property(property="Price", type="decimal"),
     *               @OA\Property(property="quantity", type="integer"),
     *            ),
     *        ),
     *    ),
     *   @OA\Response(response=201, description="Book created successfully"),
     *   @OA\Response(response=404, description="Invalid authorization token"),
     *   security = {
     * {
     * "Bearer" : {}}}
     * )
     * */
     /*
     * Function add a new book with proper name, description, author, image
     * image will be stored in aws S3 bucket and bucket will generate
     * an url and that urlwill be stored in mysql database and admin bearer token
     * must be passed because only admin can add or remove books .
    */
    public function addBook(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|between:2,100',
            'description' => 'required|string|between:5,1000',
            'author' => 'required|string|between:5,300',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'Price' => 'required',
            'quantity' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }
        try {
            $currentUser = JWTAuth::parseToken()->authenticate();
            if ($currentUser) {
                $book = new Book();
                $adminId = $book->adminOrUserVerification($currentUser->id);
                if (!$adminId) {
                    throw new BookStoreException("You are not an ADMIN", 404);
                }
                $book = Book::where('name', $request->name)->first();
                if ($book) {
                    throw new BookStoreException("Book is already exist in store", 401);
                }
                $imageName = time() . '.' . $request->image->extension();
                $path = Storage::disk('s3')->put('images', $request->image);
                $url = env('AWS_URL') . $path;
                $book = new Book;
                $book->name = $request->input('name');
                $book->description = $request->input('description');
                $book->author = $request->input('author');
                $book->image = $url;
                $book->Price = $request->input('Price');
                $book->quantity = $request->input('quantity');
                $book->user_id = $currentUser->id;
                $book->save();
            } else {
                Log::error('Invalid User');
                throw new BookStoreException("Invalid authorization token", 404);
            }
            Cache::forget('books');
            Cache::remember('books', 3600, function () {
                return DB::table('books')->get();
            });
            Log::info('book created', ['admin_id' => $book->user_id]);
            return response()->json(['message' => 'Book created successfully'], 201);
        } catch (BookStoreException $exception) {
            return $exception->message();
        }
    }

    /**
     * @OA\Post(
     *   path="/api/updatebook",
     *   summary="Update Book",
     *   description="Admin Can Update Book ",
     *   @OA\RequestBody(
     *         @OA\JsonContent(),
     *         @OA\MediaType(
     *            mediaType="multipart/form-data",
     *            @OA\Schema(
     *               type="object",
     *               required={"id","name","description","author","image", "Price"},
     *               @OA\Property(property="id", type="integer"),
     *               @OA\Property(property="name", type="string"),
     *               @OA\Property(property="description", type="string"),
     *               @OA\Property(property="author", type="string"),
     *               @OA\Property(property="image", type="file"),
     *               @OA\Property(property="Price", type="decimal"),
     *            ),
     *        ),
     *    ),
     *   @OA\Response(response=201, description="Book updated Sucessfully"),
     *   @OA\Response(response=404, description="Invalid authorization token"),
     *   security = {
     * {
     * "Bearer" : {}}}
     * )
     * */
     /*
     * Function Update the existing book with  proper name, description, author, image
     * image will be stored in aws S3 bucket and bucket will generate
     * a url and that urlwill be stored in mysql database and admin bearer token
     * must be passed because only admin can add or remove books .
    */
    public function updateBookByBookId(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'name' => 'required|string|between:2,100',
            'description' => 'required|string|between:5,2000',
            'author' => 'required|string|between:5,300',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'Price' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }
        try {
            $currentUser = JWTAuth::parseToken()->authenticate();
            if (!$currentUser) {
                Log::error('Invalid User');
                throw new BookStoreException("Invalid authorization token", 404);
            }
            $book = new Book();
            $adminId = $book->adminOrUserVerification($currentUser->id);
            if (!$adminId) {
                return response()->json(['message' => 'You are not an ADMIN'], 404);
            }
            $book = Book::find($request->id);
            if (!$book) {
                throw new BookStoreException("Book not Found", 404);
            }
            if ($request->image) {
                $path = str_replace(env('AWS_URL'), '', $book->image);

                if (Storage::disk('s3')->exists($path)) {
                    Storage::disk('s3')->delete($path);
                }
                $path = Storage::disk('s3')->put('images', $request->image);
                $pathurl = env('AWS_URL') . $path;
                $book->image = $pathurl;
            }
            $book->fill($request->except('image'));
            Cache::forget('books');

            if ($book->save()) {
                Log::info('book updated', ['admin_id' => $book->user_id]);
                return response()->json(['message' => 'Book updated Sucessfully'], 201);
            }
        } catch (BookStoreException $exception) {
            return $exception->message();
        }
    }

    /**
     * @OA\Post(
     *   path="/api/addquantity",
     *   summary="Add Quantity to Existing Book",
     *   description=" Add Book Quantity ",
     *   @OA\RequestBody(
     *         @OA\JsonContent(),
     *         @OA\MediaType(
     *            mediaType="multipart/form-data",
     *            @OA\Schema(
     *               type="object",
     *               required={"id", "quantity"},
     *               @OA\Property(property="id", type="integer"),
     *               @OA\Property(property="quantity", type="integer"),
     *            ),
     *        ),
     *    ),
     *   @OA\Response(response=201, description="Book Quantity updated Successfully"),
     *   @OA\Response(response=404, description="Invalid authorization token"),
     *   security = {
     * {
     * "Bearer" : {}}}
     * )
     */
    /*
     *Function takes perticular Bookid and a Quantity value and then take input
     *valid Authentication token as an input and fetch the book stock in the book store
     *and performs addquantity operation on that perticular Bookid.
    */
    public function addQuantityToExistingBook(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'quantity' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }
        try {
            $currentUser = JWTAuth::parseToken()->authenticate();
            if (!$currentUser) {
                Log::error('Invalid User');
                throw new BookStoreException("Invalid authorization token", 404);
            }
            $book = new Book();
            $adminId = $book->adminOrUserVerification($currentUser->id);
            if (count($adminId) == 0) {
                return response()->json(['message' => 'You are not an ADMIN'], 404);
            }
            $book = Book::find($request->id);
            if (!$book) {
                throw new BookStoreException("Couldnot found a book with that given id", 404);
            }
            $book->quantity += $request->quantity;
            $book->save();
            Cache::forget('books');
            return response()->json([
                'status' => 201,
                'message' => 'Book Quantity updated Successfully'
            ], 201);
        } catch (BookStoreException $exception) {
            return $exception->message();
        }
    }

    /**
     * @OA\Post(
     *   path="/api/deletebook",
     *   summary="Delete the book from BookStoreApp",
     *   description=" Delete Book ",
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
     *   @OA\Response(response=201, description="Book deleted Sucessfully"),
     *   @OA\Response(response=404, description="Invalid authorization token"),
     *   security = {
     * {
     * "Bearer" : {}}}
     * )
     */
    /*
     * Function takes perticular Bookid and a valid Authentication token as an input
     * and fetch the book in the bookstore database and performs delete operation on
     * on that perticular Bookid
    */
    public function deleteBookByBookId(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }
        try {
            $currentUser = JWTAuth::parseToken()->authenticate();
            if (!$currentUser) {
                Log::error('Invalid User');
                throw new BookStoreException("Invalid authorization token", 404);
            }
            $book = new Book();
            $adminId = $book->adminOrUserVerification($currentUser->id);
            if (count($adminId) == 0) {
                return response()->json(['message' => 'You are not an ADMIN'], 401);
            }
            $book = Book::find($request->id);
            if (!$book) {
                return response()->json(['message' => 'Book not Found'], 404);
            }

            $path = str_replace(env('AWS_URL'), '', $book->image);
            if (Storage::disk('s3')->exists($path)) {
                Storage::disk('s3')->delete($path);
                if ($book->delete()) {
                    Log::info('book deleted', ['user_id' => $currentUser, 'book_id' => $request->id]);
                    Cache::forget('books');
                    return response()->json(['message' => 'Book deleted Sucessfully'], 201);
                }
            }
            return response()->json(['message' => 'File image was not deleted'], 402);
        } catch (BookStoreException $exception) {
            return $exception->message();
        }
    }

    /**
     * @OA\Get(
     *   path="/api/displaybooks",
     *   summary="Display All Books",
     *   description=" Display All Books Present in the BookStore ",
     *   @OA\RequestBody(
     *
     *    ),
     *   @OA\Response(response=201, description="Books Available in the Bookstore are"),
     *   @OA\Response(response=404, description="Books are not there"),
     * )
     */
    /*
     *Function returns all the added books in the store .
    */
    public function getAllBooks()
    {
        try {
            $book = Cache::remember('books', 3600, function () {
                return DB::table('books')->get();
            });
            if ($book == []) {
                throw new BookStoreException("Books are not there", 404);
            }
            return response()->json([
                'message' => 'Books Available in the Bookstore are :',
                'books' => $book

            ], 201);
        } catch (BookStoreException $exception) {
            return $exception->message();
        }
    }

}
