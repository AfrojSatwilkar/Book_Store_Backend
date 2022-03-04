<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CartControllerTest extends TestCase
{
   /**
     * @test
     * for add book to cart successfull
     */
    public function test_SuccessfullAddToCart()
    {
        $response = $this->withHeaders([
            'Content-Type' => 'Application/json',
            'Authorization' => 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC8xMjcuMC4wLjE6ODAwMFwvYXBpXC9hdXRoXC9sb2dpbiIsImlhdCI6MTY0NDYzODcyMSwiZXhwIjoxNjQ0NjQyMzIxLCJuYmYiOjE2NDQ2Mzg3MjEsImp0aSI6InJ2ZGdEd3E2bkRoMTBhWmwiLCJzdWIiOjEsInBydiI6IjIzYmQ1Yzg5NDlmNjAwYWRiMzllNzAxYzQwMDg3MmRiN2E1OTc2ZjcifQ.0YawnQa2rL6YyPu0Fg3tFcCgA1NFpSiDdfdfkEp5Hlc'
        ])->json(
            'POST',
            '/api/addtocart',
            [
                "book_id" => "5",
            ]
        );
        $response->assertStatus(201)->assertJson(['message' => 'Book added to Cart Sucessfully']);
    }

    /**
     * @test
     * for Unsuccessfull add book to cart
     */
    public function test_UnSuccessfullAddToCart()
    {
        $response = $this->withHeaders([
            'Content-Type' => 'Application/json',
            'Authorization' => 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC8xMjcuMC4wLjE6ODAwMFwvYXBpXC9hdXRoXC9sb2dpbiIsImlhdCI6MTY0NDYzODcyMSwiZXhwIjoxNjQ0NjQyMzIxLCJuYmYiOjE2NDQ2Mzg3MjEsImp0aSI6InJ2ZGdEd3E2bkRoMTBhWmwiLCJzdWIiOjEsInBydiI6IjIzYmQ1Yzg5NDlmNjAwYWRiMzllNzAxYzQwMDg3MmRiN2E1OTc2ZjcifQ.0YawnQa2rL6YyPu0Fg3tFcCgA1NFpSiDdfdfkEp5Hlc'
        ])->json(
            'POST',
            '/api/addtocart',
            [
                "book_id" => "17",
            ]
        );
        $response->assertStatus(404)->assertJson(['message' => 'Book not Found']);
    }

    /**
     * @test
     * for delet book from cart successfull
     */
    public function test_SuccessfullAddDeleteFromCart()
    {
        $response = $this->withHeaders([
            'Content-Type' => 'Application/json',
            'Authorization' => 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC8xMjcuMC4wLjE6ODAwMFwvYXBpXC9hdXRoXC9sb2dpbiIsImlhdCI6MTY0NDYzODcyMSwiZXhwIjoxNjQ0NjQyMzIxLCJuYmYiOjE2NDQ2Mzg3MjEsImp0aSI6InJ2ZGdEd3E2bkRoMTBhWmwiLCJzdWIiOjEsInBydiI6IjIzYmQ1Yzg5NDlmNjAwYWRiMzllNzAxYzQwMDg3MmRiN2E1OTc2ZjcifQ.0YawnQa2rL6YyPu0Fg3tFcCgA1NFpSiDdfdfkEp5Hlc'
        ])->json(
            'POST',
            '/api/deletecart',
            [
                "id" => "3",
            ]
        );
        $response->assertStatus(201)->assertJson(['message' => 'Book deleted Sucessfully from cart']);
    }

    /**
     * @test
     * for delet book from cart Unsuccessfull
     */
    public function test_UnSuccessfullAddDeleteFromCart()
    {
        $response = $this->withHeaders([
            'Content-Type' => 'Application/json',
            'Authorization' => 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC8xMjcuMC4wLjE6ODAwMFwvYXBpXC9hdXRoXC9sb2dpbiIsImlhdCI6MTY0NDYzODcyMSwiZXhwIjoxNjQ0NjQyMzIxLCJuYmYiOjE2NDQ2Mzg3MjEsImp0aSI6InJ2ZGdEd3E2bkRoMTBhWmwiLCJzdWIiOjEsInBydiI6IjIzYmQ1Yzg5NDlmNjAwYWRiMzllNzAxYzQwMDg3MmRiN2E1OTc2ZjcifQ.0YawnQa2rL6YyPu0Fg3tFcCgA1NFpSiDdfdfkEp5Hlc'
        ])->json(
            'POST',
            '/api/deletecart',
            [
                "id" => "13",
            ]
        );
        $response->assertStatus(404)->assertJson(['message' => 'Book not Found in cart']);
    }

    /**
     * @test
     * for Successfull Cart update by adding quantity
     */
    public function test_SuccessfullUpdateCart()
    {
        $response = $this->withHeaders([
            'Content-Type' => 'Application/json',
            'Authorization' => 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC8xMjcuMC4wLjE6ODAwMFwvYXBpXC9hdXRoXC9sb2dpbiIsImlhdCI6MTY0MjU2NzMwMSwiZXhwIjoxNjQyNTcwOTAxLCJuYmYiOjE2NDI1NjczMDEsImp0aSI6IjZFZTFpS1FqZHd1NjIzR08iLCJzdWIiOjksInBydiI6IjIzYmQ1Yzg5NDlmNjAwYWRiMzllNzAxYzQwMDg3MmRiN2E1OTc2ZjcifQ.3tXavu4g9QVlS9byH215sMC3VjQZIbvpnjc2EgJvw9o'
        ])->json(
            'POST',
            '/api/updatequantity',
            [
                "id" => "4",
                "book_quantity" => "4",
            ]
        );
        $response->assertStatus(201)->assertJson(['message' => 'Book Quantity updated Successfully']);
    }

    /**
     * @test
     * for Successfull Cart update by adding quantity
     */
    public function test_UnSuccessfullUpdateCart()
    {
        $response = $this->withHeaders([
            'Content-Type' => 'Application/json',
            'Authorization' => 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC8xMjcuMC4wLjE6ODAwMFwvYXBpXC9hdXRoXC9sb2dpbiIsImlhdCI6MTY0MjU2NzMwMSwiZXhwIjoxNjQyNTcwOTAxLCJuYmYiOjE2NDI1NjczMDEsImp0aSI6IjZFZTFpS1FqZHd1NjIzR08iLCJzdWIiOjksInBydiI6IjIzYmQ1Yzg5NDlmNjAwYWRiMzllNzAxYzQwMDg3MmRiN2E1OTc2ZjcifQ.3tXavu4g9QVlS9byH215sMC3VjQZIbvpnjc2EgJvw9o'
        ])->json(
            'POST',
            '/api/updatequantity',
            [
                "id" => "18",
                "book_quantity" => "4",
            ]
        );
        $response->assertStatus(404)->assertJson(['message' => 'Item Not found with this id']);
    }
    /**
     * @test for successfull display all bokks
     * present in the cart
     */
    public function test_SuccessfullDisplayBooksFromCart()
    {
        $response = $this->withHeaders([
            'Content-Type' => 'Application/json',
            'Authorization' => 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC8xMjcuMC4wLjE6ODAwMFwvYXBpXC9hdXRoXC9sb2dpbiIsImlhdCI6MTY0NTc2NzE4MiwiZXhwIjoxNjQ1NzcwNzgyLCJuYmYiOjE2NDU3NjcxODIsImp0aSI6IlVLU2VMcmJ2N2JjWEFzTjciLCJzdWIiOjEsInBydiI6IjIzYmQ1Yzg5NDlmNjAwYWRiMzllNzAxYzQwMDg3MmRiN2E1OTc2ZjcifQ.jkObC5B5kQCv87JTfkFO4a0HuO33JWuWBlGzcP-sXmI'
        ])->json(
            'GET',
            '/api/getcart',
            [

            ]
        );
        $response->assertStatus(201)->assertJson(['message' => 'Books Present in Cart :']);
    }

    /**
     * @test for Unsuccessfull display all bokks
     * present in the cart
     */
    public function test_UnSuccessfullDisplayBooksFromCart()
    {
        $response = $this->withHeaders([
            'Content-Type' => 'Application/json',
            'Authorization' => 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC8xMjcuMC4wLjE6ODAwMFwvYXBpXC9hdXRoXC9sb2dpbiIsImlhdCI6MTY0NDYzODcyMSwiZXhwIjoxNjQ0NjQyMzIxLCJuYmYiOjE2NDQ2Mzg3MjEsImp0aSI6InJ2ZGdEd3E2bkRoMTBhWmwiLCJzdWIiOjEsInBydiI6IjIzYmQ1Yzg5NDlmNjAwYWRiMzllNzAxYzQwMDg3MmRiN2E1OTc2ZjcifQ.0YawnQa2rL6YyPu0Fg3tFcCgA1NFpSiDdfdfkEp5Hlc'
        ])->json(
            'GET',
            '/api/getcart',
            [

            ]
        );
        $response->assertStatus(404)->assertJson(['message' => 'Invalid authorization token']);
    }
}
