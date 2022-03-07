<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class OrderControllerTest extends TestCase
{
    /**
     * @test
     * for Successfull Placing an Order
     */
    public function test_SuccessfullPlaceOrder()
    {
        $response = $this->withHeaders([
            'Content-Type' => 'Application/json',
            'Authorization' => 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC8xMjcuMC4wLjE6ODAwMFwvYXBpXC9hdXRoXC9sb2dpbiIsImlhdCI6MTY0MjU2NzMwMSwiZXhwIjoxNjQyNTcwOTAxLCJuYmYiOjE2NDI1NjczMDEsImp0aSI6IjZFZTFpS1FqZHd1NjIzR08iLCJzdWIiOjksInBydiI6IjIzYmQ1Yzg5NDlmNjAwYWRiMzllNzAxYzQwMDg3MmRiN2E1OTc2ZjcifQ.3tXavu4g9QVlS9byH215sMC3VjQZIbvpnjc2EgJvw9o'
        ])->json(
            'POST',
            '/api/placeorder',
            [
                "address_id" => 6,
                "name" => "Python",
                "quantity" => "2",
            ]
        );
        $response->assertStatus(201)->assertJson(['message' => 'Order Successfully Placed...']);
    }

    /**
     * @test
     * for UnSuccessfull Placing an Order
     */
    public function test_UnSuccessfullPlaceOrder()
    {
        $response = $this->withHeaders([
            'Content-Type' => 'Application/json',
            'Authorization' => 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC8xMjcuMC4wLjE6ODAwMFwvYXBpXC9hdXRoXC9sb2dpbiIsImlhdCI6MTY0MjU2NzMwMSwiZXhwIjoxNjQyNTcwOTAxLCJuYmYiOjE2NDI1NjczMDEsImp0aSI6IjZFZTFpS1FqZHd1NjIzR08iLCJzdWIiOjksInBydiI6IjIzYmQ1Yzg5NDlmNjAwYWRiMzllNzAxYzQwMDg3MmRiN2E1OTc2ZjcifQ.3tXavu4g9QVlS9byH215sMC3VjQZIbvpnjc2EgJvw9o'
        ])->json(
            'POST',
            '/api/placeorder',
            [
                "address_id" => 6,
                "name" => ".Net",
                "quantity" => "4",
            ]
        );
        $response->assertStatus(401)->assertJson(['message' => 'We Do not have this book in the store...']);
    }
}
