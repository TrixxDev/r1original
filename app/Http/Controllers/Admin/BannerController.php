<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Bannerimage;
use Intervention\Image\ImageManagerStatic as Image;

class BannerController extends Controller
{

  public $search;
  public $paginate = 10;

  /*
   *
   *
   * Riepu brendi
   *
   *
   */

  public function __construct()
  {
  }

  public function index(Request $request)
  {
    $banners = Bannerimage::orderBy('id', 'desc')->get();
    return view('admin.settings.banners', compact('banners'));
  }

  public function upload(Request $request)
  {
    $validatedData = $request->validate([
      'formFile' => 'required|file|mimes:jpg,png,jpeg|max:2048',
    ]);

    $name = strtolower($request->file('formFile')->getClientOriginalName());
    $fileName = pathinfo($name, PATHINFO_FILENAME);
    $fileExtension = pathinfo($name, PATHINFO_EXTENSION);

//    $path = $request->file('formFile')->store('images/banners');
//    dd($image);
    $save = new Bannerimage;
    $save->name = $name;

    $save->save();

    if ($request->hasFile('formFile')) {
      $image = $request->file('formFile');

      $height = Image::make($image)->height();
      $width = Image::make($image)->width();

//      dd($width, $height);
//      if ($width != 760 && $height != 100) return redirect()->back()->with('error', 'Bildes izmēram jābūt 760px x 100px');
//      Image::make($image->getRealPath())->save('public/storage/banners/' . $name);
      Image::make($image->getRealPath())->fit(760, 100)->save('public/storage/banners/' . $save->id . '.' . $fileExtension);
    }

    return redirect()->back()->with('status', 'Banneris pievienots veiksmīgi!');
  }

  public function delete($id) {
    $stock = Bannerimage::find($id);
    $stock->delete();

    return redirect()->back()->with('success', 'Banneris dzēsts.');
  }
}
