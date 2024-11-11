<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Helpers\CustomResponse;
use Illuminate\Support\Facades\Hash;
use App\Rules\RfcValidator;

class UsersController extends Controller
{
    //
    public function index(){

        $users = User::all();

        return CustomResponse::success('List of Users', $users);
    }

    public function me($id){

        $user = User::where('id',$id)->first();

        return CustomResponse::success('Get User', $user);
    }

    public function update(Request $request, $id){
        
        
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255',
            'phone' => 'required',
            'email' => 'nullable|max:255|email',
            'password' => 'required',
            'password_confirm' => 'nullable',
            'rfc' => ['required', new RfcValidator],
            'notes' => 'nullable',
        ],
        [   
            'name.required'    => 'The name is required to create.',
            'email.required'   => 'The email is required, Thank You.',
            'password.required'     => 'The created_homework_at is required to be created, Thank You.',
        ]);
 
        if ($validator->fails()) {
            return  CustomResponse::error($validator->errors(), $request->all() );
        }

        try{

            $user = DB::transaction(function() use($request, $id){

                $user = User::where('id',$id)->update([
                    'name' => $request->name,
                    'phone' => $request->phone,
                    'password' => Hash::make($request->password),
                    'rfc' => $request->rfc,
                ]);

        		return compact('user');
            });

            return CustomResponse::success('User update successfuly', $user);

        }catch(\Exception $e){
            return  response()->json($e->getMessage(), 404);
        }
        
    }

    public function delete($id)
    {
        try{

            $product = DB::transaction(function() use($id){

                $product = Homeworks::find($id);

                $product->delete();

        		return compact('product');
            });

            return CustomResponse::success('Homework deleted successfuly', $product);

        }catch(\Exception $e){
            return  response()->json($e->getMessage(), 404);
        }
    }
}
