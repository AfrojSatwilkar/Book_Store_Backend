<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AddressControllerTest extends TestCase
{
    /**
     * @test for
     * Address add to respective user successfull
     *
     */

    public function test_SuccessfulAddAddress()
    {
        $response = $this->withHeaders([
            'Content-Type' => 'Application/json',
            'Authorization' => 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC8xMjcuMC4wLjE6ODAwMFwvYXBpXC9hdXRoXC9sb2dpbiIsImlhdCI6MTY0NDYzODcyMSwiZXhwIjoxNjQ0NjQyMzIxLCJuYmYiOjE2NDQ2Mzg3MjEsImp0aSI6InJ2ZGdEd3E2bkRoMTBhWmwiLCJzdWIiOjEsInBydiI6IjIzYmQ1Yzg5NDlmNjAwYWRiMzllNzAxYzQwMDg3MmRiN2E1OTc2ZjcifQ.0YawnQa2rL6YyPu0Fg3tFcCgA1NFpSiDdfdfkEp5Hlc'
        ])->json('POST', '/api/addaddress', [
            "address" => "malad",
            "city" => "mumbei",
            "state" => "maharashtra",
            "landmark" => "near big hanuman mandir",
            "pincode"=> "416824",
            "address_type" => "home",
        ]);
        $response->assertStatus(201)->assertJson(['message' => 'Address Added Successfully']);
    }

    /**
     * @test for
     * Address add to respective user Unsuccessfull
     *
     */
    public function test_UnSuccessfulAddAddress()
    {
        $response = $this->withHeaders([
            'Content-Type' => 'Application/json',
            'Authorization' => 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC8xMjcuMC4wLjE6ODAwMFwvYXBpXC9hdXRoXC9sb2dpbiIsImlhdCI6MTY0NDYzODcyMSwiZXhwIjoxNjQ0NjQyMzIxLCJuYmYiOjE2NDQ2Mzg3MjEsImp0aSI6InJ2ZGdEd3E2bkRoMTBhWmwiLCJzdWIiOjEsInBydiI6IjIzYmQ1Yzg5NDlmNjAwYWRiMzllNzAxYzQwMDg3MmRiN2E1OTc2ZjcifQ.0YawnQa2rL6YyPu0Fg3tFcCgA1NFpSiDdfdfkEp5Hlc'
        ])->json('POST', '/api/addaddress', [
            "address" => "malad",
            "city" => "mumbai",
            "state" => "maharashtra",
            "landmark" => "near hanuman mandir",
            "pincode"=> "436825",
            "addresstype" => "home",
        ]);
        $response->assertStatus(401)->assertJson(['message' => 'Address alredy present for the user']);
    }

    /**
     * @test for
     * Address Update to respective user successfull
     *
     */
    public function test_SuccessfulUpdateAddress()
    {
        $response = $this->withHeaders([
            'Content-Type' => 'Application/json',
            'Authorization' => 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC8xMjcuMC4wLjE6ODAwMFwvYXBpXC9hdXRoXC9sb2dpbiIsImlhdCI6MTY0NDYzODcyMSwiZXhwIjoxNjQ0NjQyMzIxLCJuYmYiOjE2NDQ2Mzg3MjEsImp0aSI6InJ2ZGdEd3E2bkRoMTBhWmwiLCJzdWIiOjEsInBydiI6IjIzYmQ1Yzg5NDlmNjAwYWRiMzllNzAxYzQwMDg3MmRiN2E1OTc2ZjcifQ.0YawnQa2rL6YyPu0Fg3tFcCgA1NFpSiDdfdfkEp5Hlc'
        ])->json('POST', '/api/updateaddress', [
            "address" => "sakharpa",
            "city" => "ratnagiri",
            "state" => "maharashtra",
            "landmark" => "muslim wadi",
            "pincode"=> "832872",
            "addresstype" => "work",
        ]);
        $response->assertStatus(201)->assertJson(['message' => 'Address Updated Successfully']);
    }

    /**
     * @test for
     * Address Update to respective user Unsuccessfull
     *
     */
    public function test_UnSuccessfulUpdateAddress()
    {
        $response = $this->withHeaders([
            'Content-Type' => 'Application/json',
            'Authorization' => 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC8xMjcuMC4wLjE6ODAwMFwvYXBpXC9hdXRoXC9sb2dpbiIsImlhdCI6MTY0NDYzODcyMSwiZXhwIjoxNjQ0NjQyMzIxLCJuYmYiOjE2NDQ2Mzg3MjEsImp0aSI6InJ2ZGdEd3E2bkRoMTBhWmwiLCJzdWIiOjEsInBydiI6IjIzYmQ1Yzg5NDlmNjAwYWRiMzllNzAxYzQwMDg3MmRiN2E1OTc2ZjcifQ.0YawnQa2rL6YyPu0Fg3tFcCgA1NFpSiDdfdfkEp5Hlc'
        ])->json('POST', '/api/updateaddress', [
            "address" => "sakharpa",
            "city" => "ratnagiri",
            "state" => "maharashtra",
            "landmark" => "muslim wadi",
            "pincode"=> "728732",
            "addresstype" => "work",
        ]);
        $response->assertStatus(401)->assertJson(['message' => 'Address not present add address first']);
    }

    /**
     * @test
     * for delete address successfull
     */
    public function test_SuccessfullDeleteAddress()
    {
        $response = $this->withHeaders([
            'Content-Type' => 'Application/json',
            'Authorization' => 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC8xMjcuMC4wLjE6ODAwMFwvYXBpXC9hdXRoXC9sb2dpbiIsImlhdCI6MTY0NDYzODcyMSwiZXhwIjoxNjQ0NjQyMzIxLCJuYmYiOjE2NDQ2Mzg3MjEsImp0aSI6InJ2ZGdEd3E2bkRoMTBhWmwiLCJzdWIiOjEsInBydiI6IjIzYmQ1Yzg5NDlmNjAwYWRiMzllNzAxYzQwMDg3MmRiN2E1OTc2ZjcifQ.0YawnQa2rL6YyPu0Fg3tFcCgA1NFpSiDdfdfkEp5Hlc'
        ])->json(
            'POST',
            '/api/deleteadress',
            [
                "user_id" => "3",
            ]
        );
        $response->assertStatus(201)->assertJson(['message' => 'Address deleted Sucessfully']);
    }

    /**
     * @test
     * for delete address Unsuccessfull
     */
    public function test_UnSuccessfullDeleteAddress()
    {
        $response = $this->withHeaders([
            'Content-Type' => 'Application/json',
            'Authorization' => 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC8xMjcuMC4wLjE6ODAwMFwvYXBpXC9hdXRoXC9sb2dpbiIsImlhdCI6MTY0NDYzODcyMSwiZXhwIjoxNjQ0NjQyMzIxLCJuYmYiOjE2NDQ2Mzg3MjEsImp0aSI6InJ2ZGdEd3E2bkRoMTBhWmwiLCJzdWIiOjEsInBydiI6IjIzYmQ1Yzg5NDlmNjAwYWRiMzllNzAxYzQwMDg3MmRiN2E1OTc2ZjcifQ.0YawnQa2rL6YyPu0Fg3tFcCgA1NFpSiDdfdfkEp5Hlc'
        ])->json(
            'POST',
            '/api/deleteadress',
            [
                "user_id" => "16",
            ]
        );
        $response->assertStatus(404)->assertJson(['message' => 'User not Found']);
    }

    /**
     * @test for successfull display all Address
     * for respective user
     */
    public function test_SuccessfullDisplayAddress()
    {
        $response = $this->withHeaders([
            'Content-Type' => 'Application/json',
            'Authorization' => 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC8xMjcuMC4wLjE6ODAwMFwvYXBpXC9hdXRoXC9sb2dpbiIsImlhdCI6MTY0NTc2NzE4MiwiZXhwIjoxNjQ1NzcwNzgyLCJuYmYiOjE2NDU3NjcxODIsImp0aSI6IlVLU2VMcmJ2N2JjWEFzTjciLCJzdWIiOjEsInBydiI6IjIzYmQ1Yzg5NDlmNjAwYWRiMzllNzAxYzQwMDg3MmRiN2E1OTc2ZjcifQ.jkObC5B5kQCv87JTfkFO4a0HuO33JWuWBlGzcP-sXmI'
        ])->json(
            'GET',
            '/api/getaddress',
            [

            ]
        );
        $response->assertStatus(201)->assertJson(['message' => 'Fetched Address Successfully']);
    }

    /**
     * @test for Unsuccessfull display all Address
     * for respective user
     */
    public function test_UnSuccessfullDisplayAddress()
    {
        $response = $this->withHeaders([
            'Content-Type' => 'Application/json',
            'Authorization' => 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC8xMjcuMC4wLjE6ODAwMFwvYXBpXC9hdXRoXC9sb2dpbiIsImlhdCI6MTY0NTc2NzE4MiwiZXhwIjoxNjQ1NzcwNzgyLCJuYmYiOjE2NDU3NjcxODIsImp0aSI6IlVLU2VMcmJ2N2JjWEFzTjciLCJzdWIiOjEsInBydiI6IjIzYmQ1Yzg5NDlmNjAwYWRiMzllNzAxYzQwMDg3MmRiN2E1OTc2ZjcifQ.jkObC5B5kQCv87JTfkFO4a0HuO33JWuWBlGzcP-sXmI'
        ])->json(
            'GET',
            '/api/getaddress',
            [

            ]
        );
        $response->assertStatus(404)->assertJson(['message' => 'Address not found']);
    }
}
