<?php

namespace App\Http\Controllers;

use App\User;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Rules\MatchOldPassword;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
   public function __construct() {
      $this->middleware('roleUser:Seller,Buyer')->only(['changePassword']);
   }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|unique:users|max:100',
            'password' => 'required|string|min:6',
            'role' => 'required|string',
        ]);
        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }

        $user = User::create([
            'email' => $request->email,
            'role' => $request->role,
            'password' => bcrypt($request->password)
        ]);

        return response()->json([
            'success' => true,
            'message' => 'new User has successfully created',
            'user' => $user
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        return response()->json($user, 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|unique:users,email,' .$user->id. '|max:100',
            'password' => 'required|string|min:6',
            'role' => 'required|string',
        ]);
        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }

        $updated = User::where('id', $user->id)
            ->update([
                'email' => $request->email,
                'role' => $request->role,
                'password' => bcrypt($request->password)
            ]);

        if ($updated)
            return response()->json([
                'success' => true,
                'message' => 'User data updated successfully!'
            ], 200);
        else
            return response()->json([
                'success' => false,
                'message' => 'User data can not be updated'
            ], 500);
    }

    public function changePassword(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'current' => ['required', new MatchOldPassword()],
            'new' => ['required', 'string', 'max:255'],
            'confirm' => ['same:new'],
        ]);
        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }

        $updated = User::where('id', $user->id)
            ->update([
                'password' => bcrypt($request->new)
            ]);

        if ($updated)
            return response()->json([
                'success' => true,
                'message' => 'Password data updated successfully!'
            ], 200);
        else
            return response()->json([
                'success' => false,
                'message' => 'Password data can not be updated'
            ], 500);
    }

    public function changeImage(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'image' => 'image|mimes:jpeg,png,jpg|max:2000',
        ]);
        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }

        $uploadFile = $request->file('image');
        if($uploadFile!=null){
            \File::delete(storage_path('app/').$user->profile_img);
            $path = $uploadFile->store('public/img/users');
        } else {
            $path = $user->image;
        }
        $updated = User::where('id', $user->id)
            ->update([
                'profile_img' => $path
            ]);

        if ($updated)
            return response()->json([
                'success' => true,
                'message' => 'Profile image has updated successfully!'
            ], 200);
        else
            return response()->json([
                'success' => false,
                'message' => 'Profile image can not be updated'
            ], 500);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(User $user)
    {
        if ($user->delete()) {
            return response()->json([
                'success' => true,
                'message' => 'User has successfully deleted'
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'User can not be deleted'
            ], 500);
        }
    }

    public function getResidenceNumber() {
        $user = Auth::user()->residence_number;

        if($user != null) {
            return response()->json([
                'success' => true,
                'message' => 'Success get the residence number',
                'residence_number' => $user,
            ], 200);
        } else {
            return response()->json([
                'success' => true,
                'message' => 'User doesn\'t have residence number',
                'residence_number' => 0,
            ], 200);
        }
    }
}
