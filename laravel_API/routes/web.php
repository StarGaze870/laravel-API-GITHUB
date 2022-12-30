<?php
ini_set('memory_limit', '10000M');
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/setup', function() {

    $credentials = [
        'email' => 'admin@admin.com',
        'password' => 'password'
    ];

    if (!Auth::attempt($credentials)) {

        $user = new \App\Models\User();

        $user->name= 'Admin';
        $user->email = $credentials['email'];
        $user->password = Hash::make($credentials['password']);        
        $user->save();

        if (Auth::attempt($credentials)) {

            $adminToken = $user->createToken('admin-token', ['create', 'update', 'delete']);
            $updateToken = $user->createToken('update-token', ['create', 'update']);
            $basicToken = $user->createToken('basic-token', ['none']);            
            
            $result = DB::table('users')
            ->where('id', $user->id)
            ->update([
                'remember_token' => $adminToken->plainTextToken                
            ]);

            return [
                'admin' => $adminToken->plainTextToken,
                'update' => $updateToken->plainTextToken,
                'basic' => $basicToken->plainTextToken,
            ];
        }             
    }
    if (Auth::attempt($credentials)) {

        $user = auth()->user();   
        $token = DB::table('users')->select('remember_token')->where('id','=', "$user->id")->get();

        return [
            'admin' => $token,           
        ];

        // $user->update(['remember_token' => $plan]);

        // $adminToken = $user->createToken('admin-token', ['create', 'update', 'delete']);
        // $updateToken = $user->createToken('update-token', ['create', 'update']);
        // $basicToken = $user->createToken('basic-token', ['none']);

        // return [
        //     'admin' => $adminToken->plainTextToken,
        //     'update' => $updateToken->plainTextToken,
        //     'basic' => $basicToken->plainTextToken,
        // ];

        // \Log::info($user);

        // $test = DB::table('personal_access_tokens')->where('tokenable_id','=', "$user->id")->get();

        // $data = [];

        // foreach ($test->toArray() as $obj) {

        //     $data[] = [$obj->name => $obj->token];
        // }
        
        // return [            
        //     $data        
        // ];
    }  
});
  
