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

    public function findBook($bookId) {
        $book = Book::where('id', $bookId)->get();
        return $book;
    }

    public function user() {
        return $this->belongsTo(User::class);
    }

}
