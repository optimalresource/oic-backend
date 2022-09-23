<?php

namespace App\Http\Controllers;

use App\Models\Gig;
use App\Models\Tag;
use Illuminate\Http\Request;
use App\Http\Requests\AddGigRequest;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class GigController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['index', 'show']]);
    }


    /**
    * @OA\Get(
    * path="/api/v1/gigs",
    * operationId="fetchGigs",
    * tags={"Gig"},
    * summary="All Gigs",
    * description="Fetch all gigs",
    *      @OA\Response(
    *          response=201,
    *          description="Fetched Gigs Successfully",
    *          @OA\JsonContent()
    *       ),
    *      @OA\Response(
    *          response=200,
    *          description="Fetched Gigs Successfully",
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

    public function index()
    {
        try {
            return response()->json([
                'success' => true,
                'message' => 'All Gigs fetched successfully',
                'data' => Gig::with("creator_info")->with("tags")->get()
            ], 200);
        }catch(\Exception $e) {
            return response()->json([
                "success" => false,
                "message" => "Error occured while fetching all gigs",
           ], 500);
        }
    }


    /**
    * @OA\Post(
    * path="/api/v1/gig",
    * operationId="createGig",
    * tags={"Gig"},
    * summary="Create Gig",
    * description="Create a new gig",
    *     security={ {"bearer_token": {} }},
    *     @OA\RequestBody(
    *           @OA\JsonContent(),
    *           @OA\MediaType(
    *               mediaType="multipart/form-data",
    *               @OA\Schema(
    *                   type="object",
    *                   required={"min_salary", "max_salary", "role", "company", "country", "state", "address", "tags"},
    *                   @OA\Property(property="min_salary", type="integer"),
    *                   @OA\Property(property="max_salary", type="integer"),
    *                   @OA\Property(property="role", type="string"),
    *                   @OA\Property(property="company", type="string"),
    *                   @OA\Property(property="country", type="string"),
    *                   @OA\Property(property="state", type="string"),
    *                   @OA\Property(property="address", type="string"),
    *                   @OA\Property(property="tags", type="array", collectionFormat="multi",
    *                      @OA\Items(
    *                          type="string",
    *                          example={"The tag field is required."},
    *                       ),
    *                   ),
    *               ),
    *           ),
    *       ),
    *      @OA\Response(
    *          response=201,
    *          description="Created Gig Successfully",
    *          @OA\JsonContent()
    *       ),
    *      @OA\Response(
    *          response=200,
    *          description="Created Gig Successfully",
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

    
    public function store(AddGigRequest $request) {
        $input = $request->validated();

        try {
            DB::beginTransaction();
            $user = $request->user();
            $gig = Gig::create([
                'min_salary' => $input['min_salary'],
                'max_salary' => $input['max_salary'],
                'role' => $input['role'],
                'company' => $input['company'],
                'country' => $input['country'],
                'state' => $input['state'],
                'address' => $input['address'],
                'creator' => $user->id,
            ]);

            foreach($input['tags'] as $key => $tag){
                DB::table("tags")->updateOrInsert([
                    'name' => $tag,
                    'gig_id' => $gig->id,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ],[
                    'creator' => $user->id,
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Gig successfully created.'
            ], 200);

        }catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                "success" => false,
                "message" => "Error occured while creating a new gig",
           ], 500);
        }
    }

     /**
    * @OA\Get(
    * path="/api/v1/gig",
    * operationId="fetchGig",
    * tags={"Gig"},
    * summary="Show a Gig",
    * description="Fetch a gig record",
    *     @OA\Parameter(
    *        description="ID of Gig",
    *        in="path",
    *        name="gigID",
    *        required=true,
    *        example="1",
    *        @OA\Schema(
    *           type="integer",
    *           format="int64"
    *        )
    *     ),
    *      @OA\Response(
    *          response=201,
    *          description="Fetched Gig Successfully",
    *          @OA\JsonContent()
    *       ),
    *      @OA\Response(
    *          response=200,
    *          description="Fetched Gig Successfully",
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
   
    public function show(Gig $gig)
    {
        try {
            return response()->json([
                'success' => true,
                'message' => 'Gig record fetched successfully.',
                'data' => $gig->load('creator_info', 'tags')
            ], 200); 
        }catch(\Exception $e) {
            return response()->json([
                "success" => false,
                "message" => "Error occured while viewing the gig record",
           ], 500);
        }
    }

    /**
    * @OA\Put(
    * path="/api/v1/gig",
    * operationId="updateGig",
    * tags={"Gig"},
    * summary="Update a Gig",
    * description="Update a gig",
    *     security={ {"bearer_token": {} }},
    *     @OA\Parameter(
    *        description="ID of Gig",
    *        in="path",
    *        name="gigID",
    *        required=true,
    *        example="1",
    *        @OA\Schema(
    *           type="integer",
    *           format="int64"
    *        )
    *     ),
    *     @OA\RequestBody(
    *           @OA\JsonContent(),
    *           @OA\MediaType(
    *               mediaType="multipart/form-data",
    *               @OA\Schema(
    *                   type="object",
    *                   required={"min_salary", "max_salary", "role", "company", "country", "state", "address", "tags"},
    *                   @OA\Property(property="min_salary", type="integer"),
    *                   @OA\Property(property="max_salary", type="integer"),
    *                   @OA\Property(property="role", type="string"),
    *                   @OA\Property(property="company", type="string"),
    *                   @OA\Property(property="country", type="string"),
    *                   @OA\Property(property="state", type="string"),
    *                   @OA\Property(property="address", type="string"),
    *                   @OA\Property(property="tags", type="array", collectionFormat="multi",
    *                      @OA\Items(
    *                          type="string",
    *                          example={"The tag field is required."},
    *                       ),
    *                   ),
    *               ),
    *           ),
    *       ),
    *      @OA\Response(
    *          response=201,
    *          description="Created Gig Successfully",
    *          @OA\JsonContent()
    *       ),
    *      @OA\Response(
    *          response=200,
    *          description="Created Gig Successfully",
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

    public function update(AddGigRequest $request, Gig $gig)
    {
        $input = $request->validated();

        try {
            DB::beginTransaction();
            $user = $request->user();
            if($user->id === $gig->creator || strtolower($user->role->name) === "admin" || strtolower($user->role->name) === "super admin"){
                $gig->min_salary = $input['min_salary'];
                $gig->max_salary = $input['max_salary'];
                $gig->role = $input['role'];
                $gig->company = $input['company'];
                $gig->country = $input['country'];
                $gig->state = $input['state'];
                $gig->address = $input['address'];
                $gig->save();
            }else{
                return response()->json([
                    "success" => false,
                    "message" => "Unauthorized, you cannot update this gig",
                ], 401);
            }


            foreach($input['tags'] as $tag){
                DB::table('tags')->updateOrInsert([
                    'name' => $tag,
                    'gig_id' => $gig->id
                ],[
                    'creator' => $user->id,
                    'updated_at' => Carbon::now()
                ]);
            }

            DB::commit();

            return response()->json([
                "success" => true,
                "message" => "You updated the gig successfully",
            ], 200);
            
        }catch(\Exception $e) {
            DB::rollBack();
            return response()->json([
                "success" => false,
                "message" => "Error occured while updating a gig",
           ], 500);
        }
    }

    
    /**
    * @OA\Delete(
    * path="/api/v1/gig",
    * operationId="deleteGig",
	* @OA\Parameter(
    *    description="ID of Gig",
    *    in="path",
    *    name="gigID",
    *    required=true,
    *    example="1",
    *    @OA\Schema(
    *       type="integer",
    *       format="int64"
    *    )
    * ),
    * security={ {"bearer_token": {} }},
    * tags={"Gig"},
    * summary="Delete a Gig",
    * description="Delete a gig record",
    *      @OA\Response(
    *          response=201,
    *          description="Deleted Gig Successfully",
    *          @OA\JsonContent()
    *       ),
    *      @OA\Response(
    *          response=200,
    *          description="Deleted Gig Successfully",
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


    public function destroy(Gig $gig, Request $request)
    {
        try {
            DB::beginTransaction();
            $user = $request->user();
            if($user->id === $gig->creator || strtolower($user->role->name) === "admin" || strtolower($user->role->name) === "super admin"){
                if($gig->delete()){
                    DB::commit();
                    return response()->json([
                        "success" => true,
                        "message" => "You deleted the gig successfully",
                    ], 200);
                }
            }else {
                return response()->json([
                    "success" => false,
                    "message" => "Unauthorized, you cannot delete this gig",
                ], 401);
            }
        }catch(\Exception $e) {
            DB::rollBack();
            return response()->json([
                "success" => false,
                "message" => "Error occured while deleting a gig",
           ], 500);
        }
    }
}
