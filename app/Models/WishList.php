<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WishList extends Model
{
    use HasFactory;
    protected $table="wishlists";
    protected $fillable = ['book_id'];

    public function wishlistBook($book_id, $userId) {
        return WishList::where([
            ['book_id', '=', $book_id],
            ['user_id', '=', $userId]
        ])->first();
    }

    public function getALLWishlistBooks($userId) {
        return Wishlist::leftJoin('books', 'wishlists.book_id', '=', 'books.id')
        ->select('books.id', 'books.name', 'books.author', 'books.description', 'books.Price')
        ->where('wishlists.user_id', '=', $userId)
        ->get();
    }

    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
