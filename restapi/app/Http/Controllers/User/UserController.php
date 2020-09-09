<?php

namespace App\Http\Controllers\User;

use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\ApiController;
use App\Transformers\UserTransformer;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Mail;

class UserController extends ApiController
{
    public function __construct()
    {
        parent::__construct();

        $this->middleware("transform.input:" . UserTransformer::class)->only(["update", "store"]);

    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::all();
        return $this->successResponse(["data" => $users], 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rules = [
            "name" => "required|min:3",
            "email" => "required|email|unique:users",
            "password" => "min:8|confirmed|required",
            "gender" => "in:male,female"
        ];

        $this->validate($request, $rules);

        $data = $request->all();
        $data["verified"] = User::UNVERIFIED_USER;
        $data["verification_token"] = User::generateVerificationToken();
        $data["admin"] = User::REGULAR_USER;

        $user = User::create($data);
        
        return $this->successResponse(["data" =>  $user], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $user = User::findOrFail($id);
            return $this->successResponse(["data" => $user], 200);
        } catch(ModelNotFoundException $e) {
            $errMsg = "Oops.. Data user tidak ditemukan";
            return $this->errorResponse(["error" =>  $errMsg, "code" => 409], 409);
        }
          
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            $user = User::findOrFail($id);
            
        } catch(ModelNotFoundException $e) {
            $errMsg = "Oops.. Data user tidak ditemukan";
            return $this->errorResponse(["error" =>  $errMsg, "code" => 409], 409);
        }

        $rules = [
            "name" => "min:3",
            "email" => "email|unique:users,email," . $user->id,
            "password" => "min:8|confirmed",
            "gender" => "in:male,female",
            "admin" => "in:" . USER::REGULAR_USER . "," . USER::ADMIN_USER
        ];

        $this->validate($request, $rules);

        if($request->has("name") && $request->get("name") != $user->name) {
            $user->name = $request->get("name");
        }

        if($request->has("email") && $request->get("email") != $user->email) {
            $user->verified = User::UNVERIFIED_USER;
            $user->verification_token = User::generateVerificationToken();
            $user->email = $request->email;
        }

        if($request->has("password")) {
            $user->password = $request->get("password");
        }

        if($request->has("gender") && $request->get("gender") != $user->gender) {
            $user->gender = $request->get("gender");
        }

        if($request->has("admin") && $request->get("admin") != $user->admin) {
            if($request->get("admin") == "true") {
                if(!$user->isVerified()) {
                    $errMsg = "User harus verified sebelum jadi admin";
                    return $this->errorResponse(["error" =>  $errMsg, "code" => 409], 409);
                }
            } else {
                $user->admin = $request->get("admin");
            }
            
        }

        if(!$user->isDirty()) {
            $errMsg = "Tidak ada nilai yang diupdate sama sekali";
            return $this->errorResponse(["error" =>  $errMsg, "code" => 422], 422);
        }

        $user->save();
        
        return $this->successResponse(["data" => $user], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        
        try {
            $user = User::findOrFail($id);
            
        } catch(ModelNotFoundException $e) {
            $errMsg = "Oops.. Data user tidak ditemukan";
            return $this->errorResponse(["error" =>  $errMsg, "code" => 409], 409);
        }

        $user->delete();

        return $this->successResponse(["message" => "data berhasil dihapus!"], 200);
    }

    public function indexAdmin(Request $request) {
        $admins = User::admin()->get();
        
        $admins = $this->transformData($admins, new UserTransformer);
       
        return $this->successResponse(["data" => $admins], 200);

    }

    public function verifyUser($token) {
        $user = User::where("verification_token", $token)->firstOrFail();

        $user->verification_token = null;
        $user->verified = User::VERIFIED_USER;
        $user->save(); 

        return $this->successResponse(["msg" => "verification is succesful", "data" => $user], 200);
    }

    public function resendEmail($id) {
        $user = User::findOrFail($id);

        if($user->isVerified()) {
            return $this->errorResponse(["msg" => "already verified"], 422);
        }

        Mail::to($user->email)->send(new \App\Mail\UserCreated($user));

        return $this->successResponse(["msg" => "new verification email has been sent", "data" => $user], 200);

    }
   
}
