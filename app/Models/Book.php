<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;

    protected $table="books";
    protected $fillable = [
        'name',
        'description',
        'author',
        'image',
        'Price',
        'quantity'
    ];

    public function adminOrUserVerification($currentUserId){
        $adminId = User::select('id')->where([['role', '=', 'admin'], ['id', '=', $currentUserId]])->get();
        return $adminId;
    }

    public function getBookDetails($bookName) {
        return Book::select('id','name','quantity','author','Price')
                    ->where('name', '=', $bookName)
                    ->first();
    }

    public function getBookDetailsById($id) {
        return Book::select('id', 'name', 'quantity', 'author', 'Price')
                    ->where('id', '=', $id)
                    ->first();
    }

    public function findBook($bookId) {
        $book = Book::where('id', $bookId)->first();
        return $book;
    }

    public function ascendingOrder(){
        return Book::orderBy('Price')->get();
    }

    public function descendingOrder(){
        return Book::orderBy('Price', 'desc')->get();
    }

    public function user() {
        return $this->belongsTo(User::class);
    }

}
