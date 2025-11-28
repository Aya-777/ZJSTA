<?php

namespace App\Http\Controllers;

abstract class Controller
{
  // index
    public function index()
    {
        // Code to list all apartments
    }
    // show
    public function show($id)
    {
        // Code to show a specific apartment
    }
    // create
    public function create(){
        // Code to show form to create a new apartment
    }
    // store
    public function store(Request $request){
        // Code to store a new apartment
    }
    // edit
    public function edit($id){
        // Code to show form to edit an apartment
    }
    // update
    public function update(Request $request, $id){
        // Code to update an apartment
    }
    // destroy
    public function destroy($id){
        // Code to delete an apartment
    }
}
