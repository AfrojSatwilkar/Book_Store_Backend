<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    /**
     * @test for
     * user registration successfull
     *
     */

    public function test_SuccessfulRegistration()
    {
        $response = $this->withHeaders([
            'Content-Type' => 'Application/json',
        ])->json('POST', '/api/register', [
            "role" => "user",
            "firstname" => "Afroj",
            "lastname" => "satwilkar",
            "email" => "afrozsatvilka2014@gmail.com",
            "phone_no"=> "8766846428",
            "password" => "Afroj@786",
            "confirm_password" => "Afroj@786"
        ]);
        $response->assertStatus(201)->assertJson(['message' => 'User successfully registered']);
    }
    /**
     * @test for
     * Unsuccessfull User Registration
     */
    public function test_UnSuccessfulRegistration()
    {
        $response = $this->withHeaders([
            'Content-Type' => 'Application/json',
        ])->json('POST', '/api/register', [
            "role" => "user",
            "firstname" => "zaheer",
            "lastname" => "gadkari",
            "email" => "afrozsatvilkar2014@gmail.com",
            "phone_no"=> "9685741236",
            "password" => "zaheer@123",
            "confirm_password" => "zaheer@123"
        ]);
        $response->assertStatus(401)->assertJson(['message' => 'The email has already been taken']);
    }
    /**
     * @test for
     * Successfull Login
     */
    public function test_SuccessfulLogin()
    {
        $response = $this->withHeaders([
            'Content-Type' => 'Application/json',
        ])->json('POST', '/api/login',
        [
            "email" => "zgadkari1@gmail.com",
            "password" => "zaheer123"
        ]);
        $response->assertStatus(200)->assertJson(['message' => 'Login successfull']);
    }
    /**
     * @test for
     * Unsuccessfull Login
     */
    public function test_UnSuccessfulLogin()
    {
        $response = $this->withHeaders([
            'Content-Type' => 'Application/json',
        ])->json('POST', '/api/login',
        [
            "email" => "zaheergadkari@gmail.com",
            "password" => "zaheer123"
        ]);
        $response->assertStatus(401)->assertJson(['message' => 'we can not find the user with that e-mail address You need to register first']);
    }
    /**
     * @test for
     * Successfull Logout
     */
    public function test_SuccessfulLogout()
    {
        $response = $this->withHeaders([
            'Content-Type' => 'Application/json',
            'Authorization' => 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC8xMjcuMC4wLjE6ODAwMFwvYXBpXC9hdXRoXC9sb2dpbiIsImlhdCI6MTY0MTg3NDkxNSwiZXhwIjoxNjQxODc4NTE1LCJuYmYiOjE2NDE4NzQ5MTUsImp0aSI6Im9iQ3FQVUJNRDJqWjU3RlgiLCJzdWIiOjEzLCJwcnYiOiIyM2JkNWM4OTQ5ZjYwMGFkYjM5ZTcwMWM0MDA4NzJkYjdhNTk3NmY3In0.prg4TCsRpkLMXTCI1yEqFy9GTvp99lrBy0AgRKQiKVY'
        ])->json('POST', '/api/logout');
        $response->assertStatus(201)->assertJson(['message'=> 'User successfully signed out']);
    }
    /**
     * @test for
     * Successfull forgotpassword
     */
    public function test_SuccessfulForgotPassword()
    {
        {
            $response = $this->withHeaders([
                'Content-Type' => 'Application/json',
            ])->json('POST', '/api/forgotpassword', [
                "email" => "zgadkari1@gmail.com.com"
            ]);

            $response->assertStatus(200)->assertJson(['message'=> 'we have mailed your password reset link to respective E-mail']);
        }

    }
    /**
     * @test for
     * UnSuccessfull forgotpassword
     */
    public function test_UnSuccessfulForgotPassword()
    {
      {
          $response = $this->withHeaders([
              'Content-Type' => 'Application/json',
          ])->json('POST', '/api/forgotpassword', [
              "email" => "zaheer@gmail.com"
          ]);

          $response->assertStatus(404)->assertJson(['message'=> 'we can not find a user with that email address']);
      }
    }
    /**
     * @test for
     * Successfull resetpassword
     */
    public function test_SuccessfulResetPassword()
    {
        {
          $response = $this->withHeaders([
              'Content-Type' => 'Application/json',
              'Authorization' => 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC8xMjcuMC4wLjE6ODAwMFwvYXBpXC9hdXRoXC9sb2dpbiIsImlhdCI6MTY0MTg3NDkxNSwiZXhwIjoxNjQxODc4NTE1LCJuYmYiOjE2NDE4NzQ5MTUsImp0aSI6Im9iQ3FQVUJNRDJqWjU3RlgiLCJzdWIiOjEzLCJwcnYiOiIyM2JkNWM4OTQ5ZjYwMGFkYjM5ZTcwMWM0MDA4NzJkYjdhNTk3NmY3In0.prg4TCsRpkLMXTCI1yEqFy9GTvp99lrBy0AgRKQiKVY'
          ])->json('POST', '/api/resetpassword', [
              "new_password" => "afroj786",
              "confirm_password" => "afroj786"
          ]);

          $response->assertStatus(201)->assertJson(['message'=> 'Password reset successfull!']);
        }
    }
    /**
     * @test for
     * UnSuccessfull resetpassword
     */
    public function test_UnSuccessfulResetPassword()
    {
        {
          $response = $this->withHeaders([
              'Content-Type' => 'Application/json',
              'Authorization' => 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC8xMjcuMC4wLjE6ODAwMFwvYXBpXC9hdXRoXC9sb2dpbiIsImlhdCI6MTY0MTg3NDkxNSwiZXhwIjoxNjQxODc4NTE1LCJuYmYiOjE2NDE4NzQ5MTUsImp0aSI6Im9iQ3FQVUJNRDJqWjU3RlgiLCJzdWIiOjEzLCJwcnYiOiIyM2JkNWM4OTQ5ZjYwMGFkYjM5ZTcwMWM0MDA4NzJkYjdhNTk3NmY3In0.prg4TCsRpkLMXTCI1yEqFy9GTvp99lrBy0AgRKQiKVY'
          ])->json('POST', '/api/resetpassword', [
              "new_password" => "zaheer123",
              "confirm_password" => "zaheer123"
          ]);

          $response->assertStatus(400)->assertJson(['message'=> 'we cannot find the user with that e-mail address']);
        }
    }
}
