<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Item;
use App\Image;
use \DB;
use \Auth;

class ItemsController extends Controller
{
  /**
  * Display a listing of the resource.
  *
  * @return \Illuminate\Http\Response
  */
  public function index()
  {
    $items = Item::all()->toArray();
    if (Auth::check()){
      $filteredItems = array();
      foreach($items as $item){
        if($item['claimed_user_id'] != auth()->user()->id){
          array_push($filteredItems,$item);
        }
      }
      $items = $filteredItems;
    }
    return view('items.index',compact('items'));
  }

  /**
  * Show the form for creating a new resource.
  *
  * @return \Illuminate\Http\Response
  */
  public function create()
  {
    return view('items.create');
  }

  /**
  * Store a newly created resource in storage.
  *
  * @param  \Illuminate\Http\Request  $request
  * @return \Illuminate\Http\Response
  */
  public function store(Request $request)
  {
    // form validation
    $item = $this->validate(request(), [
      'category' => 'required',
      'color' => 'required',
      'date_lost' => 'required',
    ]);

    $item = new Item;
    $item->category = $request->input('category');
    $item->color = $request->input('color');
    $item->date_lost = $request->input('date_lost');
    $item->details = $request->input('details');
    $item->place = $request->input('place');
    $item->claim_status = 'unclaimed';
    $item->save();
    $input=$request->all();
    $images=array();
    if ($request->hasFile('images')){
      $files=$request->file('images');
      foreach($files as $file){
        $fileNameWithExt=$file->getClientOriginalName();
        $fileName = pathinfo($fileNameWithExt, PATHINFO_FILENAME);
        $extension = $file->getClientOriginalExtension();
        $fileNameToStore = $fileName.'_'.time().'.'.$extension;
        $path = $file->storeAs('public/images',$fileNameToStore);
        $newImage = new Image;
        $newImage->image = $fileNameToStore;
        $newImage->image_item_id = $item->id;
        $newImage->save();
      }
    }
    return back()->with('success', 'Item has been added');
  }

  /**
  * Display the specified resource.
  *
  * @param  int  $id
  * @return \Illuminate\Http\Response
  */
  public function show($id)
  {
    $item = Item::find($id);
    return view('items.show',compact('item'));
  }

  /**
  * Show the form for editing the specified resource.
  *
  * @param  int  $id
  * @return \Illuminate\Http\Response
  */
  public function edit($id)
  {
      $claim = DB::table('claims')
                    ->where('id','=',$id)->first();
      $item_id = $claim->item_id;
      $user_id = $claim->user_id;
      DB::table('items')
          ->where('id',$item_id)
          ->update(['claimed_user_id' => $user_id]);
      DB::table('claims')->where('id','=',$id)->delete();
      DB::table('items')
          ->where('id',$item_id)
          ->update(['claim_status' => 'claimed']);
      return back()->with('success','The claim has been accepted');
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
  public function destroy($id)
  {
    //
  }
}
