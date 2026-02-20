<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class UserController extends Controller{

    public function getUser(){
        // return "Mahesh Yadav";
        return view('user');
    }

     public function homeComman(){
        // return "Mahesh Yadav";
        return view('home');
    }

      public function aboutUser(){
        return "This is James from USA";
    }

        public function getUserName($name){
        return view('getuser',['name'=>$name]);
    }

    public function adminLogin(){
        if(View::exists('admin.login')){
        return view('admin.login');
        }
        else{
            return "View not found";
        }
    }
}