<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DriverController extends Controller
{
    public function show(Request $request){
        //return a user and associated user models
           $user = $request->user();
           $user->load('driver');
        return $user;
    }

    public function update(Request $request){
     $request->validate([
         'year'=>'required | numeric | between:2011,2024',
         'make'=>'required',
         'model'=>'required',
         'color'=>'required',
         'license_plate'=>'required',
         'name'=>'required',
     ]);

     $user = $request->user();
     $user->update($request->only('name'));

     $user->driver()->updateOrCreate([
         'year',
         'make',
         'model',
         'color',
         'license_plate',
         'name'
     ]);

     $user->load('driver');

     return $user;

    }
}
