<?php

namespace App\Http\Controllers;

use App\Exceptions\BookStoreException;
use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class AddressController extends Controller
{
    /**
     * @OA\Post(
     *   path="/api/addaddress",
     *   summary="Add Address",
     *   description="User Can Add Address ",
     *   @OA\RequestBody(
     *         @OA\JsonContent(),
     *         @OA\MediaType(
     *            mediaType="multipart/form-data",
     *            @OA\Schema(
     *               type="object",
     *               required={"address","city","state","landmark", "pincode", "address_type"},
     *               @OA\Property(property="address", type="string"),
     *               @OA\Property(property="city", type="string"),
     *               @OA\Property(property="state", type="string"),
     *               @OA\Property(property="landmark", type="string"),
     *               @OA\Property(property="pincode", type="integer"),
     *               @OA\Property(property="address_type", type="string"),
     *            ),
     *        ),
     *    ),
     *   @OA\Response(response=201, description="Address Added Successfully"),
     *   @OA\Response(response=401, description="Address alredy present for the user"),
     *   security = {
     * {
     * "Bearer" : {}}}
     * )
     * */
     /**
     * This method will take input address,city,state,landmark,pincode and addresstype from user
     * and will store in the database for the respective user
     */
    public function addAddress(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'address' => 'required|string|between:2,600',
            'city' => 'required|string|between:2,100',
            'state' => 'required|string|between:2,100',
            'landmark' => 'required|string|between:2,100',
            'pincode' => 'required|string|between:2,10',
            'address_type' => 'required|string|between:2,100',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }

        $addressArray = array(
            'address' => $request->address,
            'city' => $request->city,
            'state' => $request->state,
            'landmark' => $request->landmark,
            'pincode' => $request->pincode,
            'address_type' => $request->address_type,
        );

        try {
            $currentUser = JWTAuth::parseToken()->authenticate();
            if ($currentUser) {
                $address = new Address();
                $address_exist = $address->addressExist($currentUser->id);

                if ($address_exist) {
                    Log::error('Address alredy present');
                    throw new BookStoreException("Address alredy present for the user", 401);
                }

                $address->user_id = $currentUser->id;
                $address->address = $request->input('address');
                $address->city = $request->input('city');
                $address->state = $request->input('state');
                $address->landmark = $request->input('landmark');
                $address->pincode = $request->input('pincode');
                $address->address_type = $request->input('address_type');
                $address->save();
                Log::info('Address Added To Respective User', ['user_id', '=', $currentUser->id]);
                    return response()->json([
                        'message' => ' Address Added Successfully'
                    ], 201);
            }
        } catch (BookStoreException $exception) {
            return $exception->message();
        }
    }

     /**
     * @OA\Post(
     *   path="/api/updateaddress",
     *   summary="Update Address",
     *   description="User Can Update Address ",
     *   @OA\RequestBody(
     *         @OA\JsonContent(),
     *         @OA\MediaType(
     *            mediaType="multipart/form-data",
     *            @OA\Schema(
     *               type="object",
     *               required={"address","city","state","landmark", "pincode", "address_type"},
     *               @OA\Property(property="address", type="string"),
     *               @OA\Property(property="city", type="string"),
     *               @OA\Property(property="state", type="string"),
     *               @OA\Property(property="landmark", type="string"),
     *               @OA\Property(property="pincode", type="integer"),
     *               @OA\Property(property="address_type", type="string"),
     *            ),
     *        ),
     *    ),
     *   @OA\Response(response=201, description="Address Updated Successfully"),
     *   @OA\Response(response=401, description="Address not present add address first"),
     *   security = {
     * {
     * "Bearer" : {}}}
     * )
     * */
     /**
     * This method will take input address,city,state,landmark,pincode,addresstype and where user
     * want to change then can update and will save in database the updated data which updated by
     * respective user
     */
    public function updateAddress(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'address' => 'required|string|between:2,600',
            'city' => 'required|string|between:2,100',
            'state' => 'required|string|between:2,100',
            'landmark' => 'required|string|between:2,100',
            'pincode' => 'required|string|between:2,10',
            'address_type' => 'required|string|between:2,100',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }
        try {
            $currentUser = JWTAuth::parseToken()->authenticate();
            if ($currentUser) {
                $address = new Address();
                $address_exist = $address->addressExist($currentUser->id);
                // $address_exist = Address::select('address')->where([
                //     ['user_id', '=', $currentUser->id]
                // ])->get();

                if (!$address_exist) {
                    Log::error('Address is empty');
                    throw new BookStoreException("Address not present add address first", 401);
                }

                // $address = Address::where('user_id', $currentUser->id)->first();
                $address_exist->fill($request->all());
                // $value = Cache::remember('addresses', 3600, function () {
                //     return DB::table('addresses')->get();
                // });
                if ($address_exist->save()) {
                    Log::info('Address Updated For Respective User', ['user_id', '=', $currentUser->id]);
                    return response()->json([
                        'message' => ' Address Updated Successfully'
                    ], 201);
                }
            }
        } catch (BookStoreException $exception) {
            return $exception->message();
        }
    }

     /**
     * @OA\Post(
     *   path="/api/deleteaddress",
     *   summary="Delete Address",
     *   description=" Delete Address ",
     *   @OA\RequestBody(
     *         @OA\JsonContent(),
     *         @OA\MediaType(
     *            mediaType="multipart/form-data",
     *            @OA\Schema(
     *               type="object",
     *               required={"user_id"},
     *               @OA\Property(property="user_id", type="integer"),
     *            ),
     *        ),
     *    ),
     *   @OA\Response(response=201, description="Book deleted Sucessfully from cart"),
     *   @OA\Response(response=404, description="Invalid authorization token"),
     *   security = {
     * {
     * "Bearer" : {}}}
     * )
     */
    /**
     * This method will take input from user as userId and will delete the address present for
     * the respective user in database
     */
    public function deleteAddress(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }
        try {
            $user_id = $request->input('user_id');
            $currentUser = JWTAuth::parseToken()->authenticate();
            $address = new Address();
            $address_exist = $address->addressExist($user_id);
            // $user = $currentUser->addresses()->find($user_id);

            if (!$address_exist) {
                throw new BookStoreException('User not Found', 404);
            }

            if ($address_exist->delete()) {
                Log::info('Address Deleted For Respective User', ['user_id', '=', $currentUser->id]);
                return response()->json(['message' => 'Address deleted Sucessfully'], 201);
            }
        } catch (BookStoreException $exception) {
            return $exception->message();
        }
    }

     /**
     * @OA\Get(
     *   path="/api/getaddress",
     *   summary="Get address ",
     *   description=" Get Address ",
     *   @OA\RequestBody(
     *
     *    ),
     *   @OA\Response(response=404, description="Address not found"),
     *   security = {
     * {
     * "Bearer" : {}}}
     * )
     */
    /**
     * This method will authenticate the user and will return all the address of respective user
     */
    public function getAddress()
    {
        $currentUser = JWTAuth::parseToken()->authenticate();
        try {
            if ($currentUser) {
                $address = new Address();
                $user = $address->userAddress($currentUser->id);
                // $user = Address::select('addresses.id', 'addresses.user_id', 'addresses.address', 'addresses.city', 'addresses.state', 'addresses.landmark', 'addresses.pincode', 'addresses.address_type')
                //     ->where([['addresses.user_id', '=', $currentUser->id]])
                //     ->get();
                if ($user == []) {
                    throw new BookStoreException("Address not found", 404);
                }
                Log::info('Address fetched For Respective User', ['user_id', '=', $currentUser->id]);
                return response()->json([
                    'address' => $user,
                    'message' => 'Fetched Address Successfully'
                ], 201);
            }
        } catch (BookStoreException $exception) {
            return $exception->message();
        }
    }
}
