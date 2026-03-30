<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Menu;
use App\Models\user_menu;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use DB;


class UserController extends Controller
{

    public function makeHash(){
        $password = Hash::make('Ghauri#738');
        //Nida#022, Rauf#063
        return $password;
    }
	
	 public function testQuery(){
        DB::select(" ");
        return "done";
        
    }

    public function login(Request $request){
        
        $passvalue = "login";
        if($request->isMethod("post")){
        $userData = User::where(["name"=>$request->username])->first();
        if(!empty($userData)){

            if (Hash::check($request->password, $userData->password)) {
                $request->session()->put('user', $userData);
                return redirect('/');
            }else{
                $passvalue = "error";
                return view("login", compact('passvalue')); 
            }

            
        }else{
            $passvalue = "error";
            return view("login", compact('passvalue')); 
        }
    }
    return view("login", compact('passvalue')); 
}


public function allUsers(){

    $users = User::all();
    return view('Utilities.userinformation', compact('users'));
}

public function mwnuall($id=null){
    $menu = DB::select("select * from menupermission where uid = ? ", [$id]);
    return $menu;
}


public function editUsers(Request $request, $id=null){

    // $menu = DB::table('user_menus')->join('menus', 'user_menus.menu_subid', 'menus.sub_id')
    // ->select('menus.*', 'user_menus.status as um_status', 'user_menus.id as um_id')
    // ->where([['user_menus.user_id', $id]])->get();

    $menu = DB::select("select * from menupermission where uid = ? ", [$id]);

    if($request->isMethod('post')){
        $data = $request->all();
        
        User::where(['id'=>$id])->update(['name'=>$data['name'],
            'code'=>$data['code'],
            'email'=>$data['email'],
            'password'=>$data['pass'],
            'status'=>$data['status'],
            'role'=>$data['role'],
            'expiry_pass'=>$data['edays'],
            'time_from'=>$data['time_from'],
            'time_to'=>$data['time_to'],
            'cash_code'=>$data['cash_code'],
            ]);

        foreach($menu as $row){
            user_menu::where(['id'=>$row->umid])->update(['status'=>$data[$row->umid] ]);
        }    

        return redirect('/UserInformation');
        
    }
    
    $user = User::where(['id'=>$id])->first();
    return view('Utilities.userupdate', compact('user', 'menu'));
}

public function userView($id=null){

    $user = User::where(['id'=>$id])->first();
    $record = User::all();
    $path = "v";
    $log = new LogController();
    $log->logCreate("User", "View", $id);
    return view('Utilities.createuser', compact('record', 'user', 'path'));
    
}

public function createUser(Request $request){

    if($request->isMethod('post')){
        $data = $request->all();
        $menu = Menu::all();
        $user = new User;


        $user->name = $data['name'];
        $user->code = $data['code'];
        $user->email = $data['email'];
        $user->password = $data['pass'];
        $user->expiry_pass = $data['edays'];
        $user->role = $data['role'];
        $user->status = $data['status'];
        $user->time_from = $data['time_from'];
        $user->time_to = $data['time_to'];
        $user->cash_code = $data['cash_code'];

        if($user->save()){
            $current_user = $user->id;
            foreach($menu as $row){
                $submenutable = new user_menu;
                $submenutable->user_id = $current_user;
                $submenutable->menu_subid = $row->sub_id;
                $submenutable->status = 'Y';
                $submenutable->save();
            }
            return redirect('/UserInformation');
        }else{
            return "User not created";
        }
        

    }
    $record = User::all();
    $path = "c";
    $log->logCreate("User", "View", $id);
    return view('Utilities.createuser', compact('record', 'user', 'path'));

}

public function addPermission(String $uid, String $umid){
                $submenutable = new user_menu;
                $submenutable->user_id = $uid;
                $submenutable->menu_subid = $umid;
                $submenutable->status = 'Y';
                if($submenutable->save()){
                    return redirect('/UserUpdate/'.$uid);
                }
}

public function userPass(Request $request){

    if($request->isMethod('post')){
        $data = $request->all();
        $id = Session::get('user')['id'];
        $user = User::where(['id'=>$id])->first();

        $current_pass = $data['current_pass'];
        $new_pass = $data['new_pass'];
        $confirm_new_pass = $data['confirm_new_pass'];

        if($user->password == $current_pass){
            if($new_pass == $confirm_new_pass){
                User::where(['id'=>$id])->update(['password'=>$new_pass,]);
                return redirect('/');
            }else{
                return "Password Fields does not match.";
            }
        }else{
            return "Current Password is Wrong.";
        }
       
        }
        
    return view('Utilities.userpasswordupdate');

}
public function changePassword(Request $request){

    $userpass = Session::get('user')['password'];  
    $userid = Session::get('user')['id']; 

    // else if(Hash::check($request->fcur, $userpass)){}

    $prev_pass = DB::select('select prev1, prev2, prev3, prev4, prev5 from users where id = ?', [$userid]);
    $prev1 = $prev_pass[0]->prev1;
    $prev2 = $prev_pass[0]->prev2;
    $prev3 = $prev_pass[0]->prev3;
    $prev4 = $prev_pass[0]->prev4;
    $prev5 = $prev_pass[0]->prev5;


    if($request->isMethod("post")){

        if (Hash::check($request->fcur, $userpass)) {
            $passwordInHash = Hash::make($request->fnew);


        if(Hash::check($request->fnew, $userpass) || Hash::check($request->fnew, $prev1) || Hash::check($request->fnew, $prev2) 
            || Hash::check($request->fnew, $prev3) || Hash::check($request->fnew, $prev4) || Hash::check($request->fnew, $prev5)){

            echo "<script>";
            echo "alert('Password Exist in History. Try New Combinations.');";
            echo "</script>";

        }else{
            User::where(['id'=>$userid])->update(['password'=>$passwordInHash,
            'prev1'=>$userpass,
            'prev2'=>$prev1,
            'prev3'=>$prev2,
            'prev4'=>$prev3,
            'prev5'=>$prev4]);
            $message = 'Password Change!';

            return redirect('/logout')->with('status', 'Password Change. Login with new credentials');
        }

            
        }else{
            echo "<script>";
            echo "alert('Current password is incorrect');";
            echo "</script>";
        }
    }
    return view("changepassword", compact('userpass')); 
}


}
