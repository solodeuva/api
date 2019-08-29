<?php

namespace App\Http\Controllers;

use Auth;
use JWTAuth;
use JWTFactory;
use Validator;
use App\Models\JWT\User;
use Illuminate\Http\Request;

class TokenController extends Controller{
    
    public function login(Request $request){

    	$validator = Validator::make($request->all(),[
    		'nit' => 'required',
    		'password' => 'required'
    	]);

    	if($validator->fails())
    		return response()->json(['errors'=>'Las credenciales son obligatorias.','status'=>401],401); //401 UNAUTHORIZED

    	$u = User::where('nit',$request->nit)->where('password',$request->password)->first();

    	if(!$u)
    		return response()->json(['errors'=>'Credenciales incorrectas!','status'=>401],401); //401 UNAUTHORIZED

        try{ 
	        // Authenticando a partir del usuario encontrado
	        if(! $token = JWTAuth::fromUser($u))
	            return response()->json(['errors' => 'Credenciales incorrectas!','status'=>401], 401);
	    } catch (JWTException $e) { 
	        return response()->json(['errors' => 'No se pudo crear el token','status'=>500], 500); 
	    }

    	return response()->json(['message'=>'Very Gon','token'=>$token,'status'=>200],200);
    }

    public function saludo(Request $request){
    	return response()->json(['message'=>'Saluditos '.Auth::user()->name,'status'=>200],200);
    }

    public function logout(){
    	$token = JWTAuth::getToken();

    	try{
    		JWTAuth::invalidate($token);
    		return response()->json(['message'=>'Se cerró la sesión correctamente!','status'=>200],200);
    	}catch(JWTException $e){
    		return response()->json(['errors' => 'Hubo un error al cerrar sesión!','status'=>422], 422); //422 UNPROCESSABLE ENTITY 
    	}
    }
}
