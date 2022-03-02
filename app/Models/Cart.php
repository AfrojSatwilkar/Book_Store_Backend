<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;
    protected $table="carts";
    protected $fillable = ['book_id'];

    public function userVerification($currentUserId){
        $userId = User::select('id')->where([['role', '=', 'user'], ['id', '=', $currentUserId]])->get();
        return $userId;
    }

    public function bookCart($book_id, $userId) {
        return Cart::where([
            ['book_id', '=', $book_id],
            ['user_id', '=', $userId]
        ])->get();
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
