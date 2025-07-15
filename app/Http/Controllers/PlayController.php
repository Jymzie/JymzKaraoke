<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PlayController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $req)
    {   if($req->has('mode')){
            return DB::connection('Sample')
            ->select("SELECT * FROM Queue");
           
        }
        else{
            // return DB::connection('Sample')->select('SELECT ID, Title FROM List');

            
            $files = collect(Storage::disk('public')->allFiles('video'))
                ->filter(function ($file) {
                    return Str::endsWith($file, '.mp4');
                })
                ->map(function ($file) {
                    return ['Title' => basename($file,'.mp4')];
                });

            return $files;
        }
        
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
     * @return \Illuminate\Http\Response
     */
    public function store(Request $req)
    {
        if($req->has('content')){
            $file = $req->file('content');
            // $filePath = $file->store('video','public');
            $file->storeAs('video',$file->getClientOriginalName(),'public');
            // DB::connection('Sample')->insert("INSERT INTO
            // List (Title,Link) VALUES(?,?)",[$req->name,$filePath]);
        }
        else{   
            return DB::connection('Sample')->select("INSERT INTO 
            Queue (Title)
            VALUES (?)",[$req->Title]);
        }
        
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $req,$id)
    {
        if($id == 'del'){
            $min = DB::connection('Sample')->select("SELECT Min(No) AS Min FROM Queue")[0]->Min;
            DB::connection('Sample')->delete("DELETE FROM Queue
            WHERE No = $min");
        }
        if($id == 'clear'){
            DB::connection('Sample')->delete("TRUNCATE TABLE Queue");
        }
        if($id == 'cancel'){
            DB::connection('Sample')->delete("DELETE FROM Queue
            WHERE No = $req->no");
        }
           
    }
}
