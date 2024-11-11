<?php



namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Helpers\CustomResponse;
use App\Rules\RfcValidator;


class AuthController extends Controller
{
    //

    public function login(Request $request)
    {
        
        $validator =  Validator::make( $request->all(), [
            'email' => 'required|string|email|exists:users,email',
            'password' => 'required|string',
        ]);
        
        if ($validator->fails()) {
            return  CustomResponse::error($validator->errors(), $request->all() );
        }

        try{

            $credentials = $request->only('email', 'password');

            $token = Auth::guard('api')->attempt($credentials);
            
            if (!$token) {
                return CustomResponse::error('Unauthorized');
            }

            $user = Auth::guard('api')->user();
            
            
            return CustomResponse::success([
                'user' => $user,
                'token' => $token,
                'type' => 'bearer',
            
            ]);
            
        }catch(\Exception $e){
            dd($e->getMessage());
        }
        
    }

    public function me()
    {
        $user = Auth::guard('api')->user();

        return response()->json([
            "Usuario obtenido correctamente",
            ['user' => $user]
        ]);
    }

    public function register(Request $request)
    {
        
        
        
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255',
            'email' => 'required|max:255|email',
            'password' => 'required',
            'rol' => 'required'
        ],
        [   
            'name.required'    => 'The name is required to create.',
            'email.required'   => 'The email is required, Thank You.',
            'password.required'=> 'The password is required to be created, Thank You.',
            'rol.required'     => 'The rol user is required to be created, Thank You.',
        ]);
 
        if ($validator->fails()) {
            return  CustomResponse::error($validator->errors(), $request->all() );
        }
       
        try{
        
            $user = DB::transaction(function() use($request){

                $user = User::create([
                    'name' => $request->name,
                    'phone' => $request->phone,
                    'email' => $request->email,
                    'password' => Hash::make($request->password),
                    'rol' => $request->rol
                ]);

        		return compact('user');
            });

            return CustomResponse::success('User created successfuly', $user);
           
        }catch(\Exception $e){

            return  CustomResponse::error("Error to create", $e->getMessage() );

        }
           
        
    }

    public function logout()
    {
        Auth::logout();
        return response()->json('Successfully logged out');
    }

    public function refresh()
    {
        return response()->json([
            'user' => Auth::user(),
            'authorisation' => [
                'token' => Auth::refresh(),
                'type' => 'bearer',
            ]
        ]);
    }

}
