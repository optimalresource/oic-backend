<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterUserRequest;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     *  Registration for users
     *
     * @return \Illuminate\Http\Response
     */
    /**
    * @OA\Post(
    * path="/api/v1/auth/register",
    * operationId="Register",
    * tags={"Register"},
    * summary="User Register",
    * description="User Register here",
    *     @OA\RequestBody(
    *         @OA\JsonContent(),
    *         @OA\MediaType(
    *            mediaType="multipart/form-data",
    *            @OA\Schema(
    *               type="object",
    *               required={"name","email", "password", "password_confirmation"},
    *               @OA\Property(property="name", type="text"),
    *               @OA\Property(property="email", type="text"),
    *               @OA\Property(property="password", type="password"),
    *               @OA\Property(property="password_confirmation", type="password")
    *            ),
    *        ),
    *    ),
    *      @OA\Response(
    *          response=201,
    *          description="Register Successfully",
    *          @OA\JsonContent()
    *       ),
    *      @OA\Response(
    *          response=200,
    *          description="Register Successfully",
    *          @OA\JsonContent()
    *       ),
    *      @OA\Response(
    *          response=422,
    *          description="Unprocessable Entity",
    *          @OA\JsonContent()
    *       ),
    *      @OA\Response(response=400, description="Bad request"),
    *      @OA\Response(response=404, description="Resource Not Found"),
    *      @OA\Response(response=500, description="Internal Server Error"),
    * )
    */
    public function register(RegisterUserRequest $request)
    {
        $input = $request->validated();

        try {
            $role = Role::where('name', 'user')->first();
            $input['password'] = Hash::make($input['password']);
            
            $user = User::create(array_merge(['role_id' => $role->id], $input));

            $token = $user->createToken('authToken')->accessToken;
            return response()->json([
                'success' => true,
                'message' => 'Account successfully created.',
                'data' => ['token' => $token, 'user' => $user]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                "success" => false,
                "message" => "Error occured while trying to create user account.",
           ], 500);
        }
    }

    /**
    * @OA\Post(
    * path="/api/v1/auth/login",
    * operationId="authLogin",
    * tags={"Login"},
    * summary="User Login",
    * description="Login User Here",
    *     @OA\RequestBody(
    *         @OA\JsonContent(),
    *         @OA\MediaType(
    *            mediaType="multipart/form-data",
    *            @OA\Schema(
    *               type="object",
    *               required={"email", "password"},
    *               @OA\Property(property="email", type="email"),
    *               @OA\Property(property="password", type="password")
    *            ),
    *        ),
    *    ),
    *      @OA\Response(
    *          response=201,
    *          description="Login Successfully",
    *          @OA\JsonContent()
    *       ),
    *      @OA\Response(
    *          response=200,
    *          description="Login Successfully",
    *          @OA\JsonContent()
    *       ),
    *      @OA\Response(
    *          response=422,
    *          description="Unprocessable Entity",
    *          @OA\JsonContent()
    *       ),
    *      @OA\Response(response=400, description="Bad request"),
    *      @OA\Response(response=404, description="Resource Not Found"),
    *      @OA\Response(response=500, description="Internal Server Error"),
    * )
    */

    public function login(LoginRequest $request)
    {
        $req = $request->validated();

        try {
            $user = User::with('role')->where('email', $req['email'])->first();

            if ($user) {
                if (Hash::check($req['password'], $user->password)) {
                    $token = $user->createToken('authToken')->accessToken;
                    return response()->json([
                        'success' => true,
                        'data' => ['token' => $token, 'user' => $user]
                    ], 200);
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => 'The given data was invalid.',
                        'errors' => ['email' => ['The email or password is invalid']],
                    ], 422);
                }
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'The given data was invalid.',
                    'errors' => ['email' => ['The email or password is invalid']],
                ], 422);
            }
        }catch (\Exception $e) {
            return response()->json([
                "success" => false,
                "message" => "Error occured while trying to login",
           ], 500);
        }
    }
}

?>