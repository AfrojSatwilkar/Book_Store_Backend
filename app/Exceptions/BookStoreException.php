<?php

namespace App\Exceptions;

use Exception;

class BookStoreException extends Exception
{
    public function message()
    {
        return response()->json([
            'status' => $this->getCode(),
            'message' => $this->getMessage()
        ]);
    }
}
