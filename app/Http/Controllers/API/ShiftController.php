<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Shift;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Helpers\CustomResponse;

class ShiftController extends Controller {

    public function index(){
        $user = Auth::guard('api')->user();

        $Shifts = Shift::with('users')->where('current_user',$user->id)->get();

        return CustomResponse::success('List of Shifts', $Shifts);

    }

    public function store(Request $request){

        $validator = Validator::make($request->all(), [
            'start_shift' => 'required|date_format:Y-m-d H:i:s',
            'end_shift' => 'required|date_format:Y-m-d H:i:s',
            'state' => 'required|in:available,reserved',
            'current_user' => 'required|exists:users,id'
        ],
        [   
            'start_shift.required'    => 'The name is required to create.',
            'end_shift.required'   => 'The email is required, Thank You.',
            'state.required'=> 'The password is required to be created, Thank You.',
            'current_user.required'     => 'The rol user is required to be created, Thank You.',
        ]);
 
        if ($validator->fails()) {
            return  CustomResponse::error($validator->errors(), $request->all() );
        }

        try{
        
            $shift = DB::transaction(function() use($request){

                $user = Auth::guard('api')->user();
                
                $shift = Shift::create([
                    'start_shift' => $request->start_shift,
                    'end_shift' => $request->end_shift,
                    'state' => $request->state,
                    'current_user' => $request->current_user,
                ]);
                
        		return compact('shift');
            });

            return CustomResponse::success('Shift created successfuly', $shift);
           
        }catch(\Exception $e){

            return  CustomResponse::error("Error to create", $e->getMessage() );

        }
       
    }

    public function me($id){
        try{

            $user = Auth::guard('api')->user();

            $Shifts = Shift::with('users')->where('current_user',$user->id)->first();

            return CustomResponse::success('Single of Shifts', $Shifts);

        }catch(\Exception $e){
            return  CustomResponse::error("Error to find", $e->getMessage() );
        }
    }

    public function update(Request $request, $id){

        $validator = Validator::make($request->all(), [
            'start_shift' => 'required|date_format:Y-m-d H:i:s',
            'end_shift' => 'required|date_format:Y-m-d H:i:s',
            'state' => 'required|in:available,reserved',
            'current_user' => 'required|exists:users,id'
        ],
        [   
            'start_shift.required'    => 'The name is required to create.',
            'end_shift.required'   => 'The email is required, Thank You.',
            'state.required'=> 'The password is required to be created, Thank You.',
            'current_user.required'     => 'The rol user is required to be created, Thank You.',
        ]);
 
        if ($validator->fails()) {
            return  CustomResponse::error($validator->errors(), $request->all() );
        }

        try{

            $shift = DB::transaction(function() use($request, $id){

                $shift = Shift::where('id',$id)->update([
                    'start_shift' => $request->start_shift,
                    'end_shift' => $request->end_shift,
                    'state' => $request->state,
                    'current_user' => $request->current_user,
                ]);

        		return compact('shift');
            });

            return CustomResponse::success('Shift update successfuly', $shift);

        }catch(\Exception $e){
            return  response()->json($e->getMessage(), 404);
        }
    }

    public function delete($id)
    {
        try{

            $shift = DB::transaction(function() use($id){

                $shift = Shift::find($id);

                $shift->delete();

        		return compact('shift');
            });

            return CustomResponse::success('Shift deleted successfuly', $shift);

        }catch(\Exception $e){
            return  response()->json($e->getMessage(), 404);
        }
    }

}