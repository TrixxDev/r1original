<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Autobrand;
use App\Models\Autotire;
use App\Models\Autotread;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Intervention\Image\ImageManagerStatic as Image;
use Storage;

class AutoTireController extends Controller
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

        if ($request->post()) {
          if ($request->input('new-brand') == 'true') {
            if (empty($request->input('brand-name'))) return redirect($request->url())->with('danger', 'Sākumā jāievada brenda nosaukums!');
            $brand = Autobrand::where('title', $request->input('brand-name'))->first();
            if ($brand) return redirect($request->url())->with('danger', 'Brends ar šādu nosaukumu jau eksistē!');
            $brand = new Autobrand;
            $brand->timestamps = false;
            $brand->title = $request->input('brand-name');
            $brand->slug = Str::slug($brand->title);
            if ($brand->save()) {
              return redirect($request->url())->with('success', 'Brends ir pievienots!');
            } else {
              return redirect($request->url())->with('danger', 'Notika kļūda, brends nav pievienots!');
            }
          } else if ($request->input('edit-brand') == 'true') {
            $brand = Autobrand::where('brand_id', $request->input('brand-id'))->first();
            $brand->timestamps = false;
            if ($brand && $brand->title == $request->input('brand-name')) {
              return redirect($request->url())->with('danger', 'Brenda nosaukums nav mainīts, ievadīts tāds pats!');
            } else {
              $brand->title = $request->input('brand-name');
              $brand->slug = Str::slug($brand->title);
              if ($brand->save()) {
                return redirect($request->url())->with('success', 'Brenda nosaukums nomainīts!');
              } else {
                return redirect($request->url())->with('danger', 'Notika kļūda, brends nav mainīts!');
              }
            }
          } else if ($request->input('delete-brand') == 'true') {
            $brand = Autobrand::where('brand_id', $request->input('brand-id'))->first();
            if (!$brand) return redirect($request->url())->with('danger', 'Tāds brends neeksistē, nevaru izdzēst!');
            if ($brand->delete()) {
              redirect($request->url())->with('success', 'Brends veiksmīgi izdzēsts!');
            } else {
              redirect($request->url())->with('danger', 'Notika kļūda, brends nav izdzēsts!');
            }
          }

          if ($request->input('new-make') == 'true') {
            if (empty($request->input('make-name'))) return redirect($request->url())->with('danger', 'Sākumā jāievada modeļa nosaukums!');
            $make = Autotread::where('t_title', $request->input('make-name'))->where('brand_id', $request->input('brand-id'))->first();
            if ($make) return redirect($request->url())->with('danger', 'Modelis ar šādu nosaukumu jau eksistē!');
            $make = new Autotread;
            $make->timestamps = false;
            $make->season = $request->input('make-season');
            $make->brand_id = $request->input('brand-id');
            $make->t_title = $request->input('make-name');
            $make->slug = Str::slug($make->t_title);
            if ($make->save()) {
              return redirect($request->url())->with('success', 'Modelis ir pievienots!');
            } else {
              return redirect($request->url())->with('danger', 'Notika kļūda, modelis nav pievienots!');
            }
          }
        }

//        $tires = Autotire::with(ctread')->groupBy('make_id')->paginate($perPage);
        $brands = Autobrand::orderBy('title', 'ASC')->get();
        $treads = Autotread::orderBy('t_title', 'ASC')->get();

        return view('admin.auto_tires.index', compact('brands', 'treads'));
    }

    public function tires_search(Request $request)
    {

        if ($request->post()) {
          if ($request->input('new-brand') == 'true') {
            if (empty($request->input('brand-name'))) return redirect($request->url())->with('danger', 'Sākumā jāievada brenda nosaukums!');
            $brand = Autobrand::where('title', $request->input('brand-name'))->first();
            if ($brand) return redirect($request->url())->with('danger', 'Brends ar šādu nosaukumu jau eksistē!');
            $brand = new Autobrand;
            $brand->timestamps = false;
            $brand->title = $request->input('brand-name');
            $brand->slug = Str::slug($brand->title);
            if ($brand->save()) {
              return redirect($request->url())->with('success', 'Brends ir pievienots!');
            } else {
              return redirect($request->url())->with('danger', 'Notika kļūda, brends nav pievienots!');
            }
          } else if ($request->input('edit-brand') == 'true') {
            $brand = Autobrand::where('brand_id', $request->input('brand-id'))->first();
            $brand->timestamps = false;
//            if ($brand && $brand->title == $request->input('brand-name')) {
//              return redirect(route('admin.auto.tires'))->with('danger', 'Brenda nosaukums nav mainīts, ievadīts tāds pats!');
//            } else {
              $brand->title = $request->input('brand-name');
              $brand->slug = Str::slug($brand->title);
              if ($brand->save()) {
                return redirect($request->url())->with('success', 'Brenda nosaukums nomainīts!');
              } else {
                return redirect($request->url())->with('danger', 'Notika kļūda, brends nav mainīts!');
              }
//            }
          } else if ($request->input('delete-brand') == 'true') {
            $brand = Autobrand::where('brand_id', $request->input('brand-id'))->first();
            if (!$brand) return redirect($request->url())->with('danger', 'Tāds brends neeksistē, nevaru izdzēst!');
            if ($brand) {
              if ($brand->delete()) {
                redirect($request->url())->with('success', 'Brends veiksmīgi izdzēsts!');
              } else {
                redirect($request->url())->with('danger', 'Notika kļūda, brends nav izdzēsts!');
              }
            } else {
              redirect($request->url());
            }
          }

          if ($request->input('new-make') == 'true') {
            if (empty($request->input('make-name'))) return redirect($request->url())->with('danger', 'Sākumā jāievada modeļa nosaukums!');
            $make = Autotread::where('t_title', $request->input('make-name'))->where('brand_id', $request->input('brand-id'))->first();
            if ($make) return redirect($request->url())->with('danger', 'Modelis ar šādu nosaukumu jau eksistē!');
            $make = new Autotread;
            $make->timestamps = false;
            $make->season = $request->input('make-season');
            $make->brand_id = $request->input('brand-id');
            $make->t_title = $request->input('make-name');
            $make->slug = Str::slug($make->t_title);
            if ($make->save()) {
              return redirect($request->url())->with('success', 'Modelis ir pievienots!');
            } else {
              return redirect($request->url())->with('danger', 'Notika kļūda, modelis nav pievienots!');
            }
          } else if ($request->input('edit-make') == 'true') {
            $make = Autotread::where('brand_id', $request->input('brand-id'))->where('tread_id', $request->tread_id)->first();
            $make->timestamps = false;
//            if ($make && $make->t_title == $request->input('make-name')) {
//              return redirect($request->url())->with('danger', 'Modeļa nosaukums nav mainīts, ievadīts tāds pats!');
//            } else {
            $make->season = $request->input('make-season');
            $make->t_title = $request->input('make-name');
            $make->slug = Str::slug($make->t_title);
            if ($make->save()) {
              return redirect($request->url())->with('success', 'Modeļa nosaukums nomainīts!');
            } else {
              return redirect($request->url())->with('danger', 'Notika kļūda, modeļis nav mainīts!');
            }
//            }
          } else if ($request->input('delete-make') == 'true') {
            $make = Autotread::where('tread_id', $request->tread_id)->first();
            if (!$make) return redirect($request->url())->with('danger', 'Tāds modelis neeksistē, nevaru izdzēst!');
            if ($make->delete()) {
              redirect($request->url())->with('success', 'Modelis veiksmīgi izdzēsts!');
            } else {
              redirect($request->url())->with('danger', 'Notika kļūda, modelis nav izdzēsts!');
            }
          }
        }

        $tires = Autotire::with('tread')->where('make_id', $request->tread_id)->get();
        $tread = Autotread::where('tread_id', $request->tread_id)->first();
        $brands = Autobrand::orderBy('title', 'ASC')->get();
        $treads = Autotread::orderBy('t_title', 'ASC')->get();

        return view('admin.auto_tires.index', compact('tires', 'tread', 'brands', 'treads'));
    }

    public function tire_create($id)
    {
      $tread = Autotread::where('tread_id', $id)->first();
      $brand = Autobrand::where('brand_id', $tread->brand_id)->first();

      return view('admin.auto_tires.tires.create', compact('tread', 'brand'));
    }

    public function tire_store(Request $request, $id)
    {
        $inputs = $request->except(['_token']);

        if (!array_filter($inputs)) {
          return redirect(route('admin.auto.tires.create', $id))->with('danger', 'Visi lauki ir tukši');
        }

        $tire = new Autotire;
        $tire->timestamps = false;

        $tire->make_id = $id;
        $tire->d1 = ($request->d1 === null) ? '' : $request->d1;
        $tire->d2 = ($request->d2 === null) ? '' : $request->d2;
        $tire->d3 = ($request->d3 === null) ? '' : $request->d3;
        $tire->type = ($request->tire_type === null) ? 0 : $request->tire_type;
        $tire->li = ($request->li === null) ? '' : $request->li;
        $tire->si = ($request->si === null) ? '' : $request->si;
        $tire->price1 = ($request->price1 === null) ? '' : $request->price1;
        $tire->price2 = ($request->price2 === null) ? '' : $request->price2;
        $tire->comment = ($request->comment === null) ? '' : $request->comment;
        $tire->code = ($request->code === null) ? '' : $request->code;
        $tire->eco = ($request->eco === null) ? '' : $request->eco;
        $tire->wet = ($request->wet === null) ? '' : $request->wet;
        $tire->noise = ($request->noise === null) ? '' : $request->noise;
        $tire->article = ($request->article === null) ? '' : $request->article;
        $tire->quantity = ($request->quantity === null) ? '' : $request->quantity;
        $tire->visible_list = 1;
        $tire->visible_users = 1;
        $tire->urs_quantity = ($request->urs_quantity === null) ? '' : $request->urs_quantity;
        $tire->krs_quantity = ($request->krs_quantity === null) ? '' : $request->krs_quantity;

        $tire->save();

        return redirect(route('admin.auto.tires.search', $id))->with('success', 'Riepa veiksmīgi pievienota');

    }

    public function tire_edit($id)
    {
        $tire = Autotire::with('tread')->where('tire_id', $id)->first();

        return view('admin.auto_tires.tires.edit', compact('tire'));
    }

    public function tire_update(Request $request, $id)
    {

        $tire = Autotire::findOrFail($id);

        $tire->d1 = $request->d1;
        $tire->d2 = $request->d2;
        $tire->d3 = $request->d3;
        $tire->type = ($request->tire_type) ? $request->tire_type : 0;
        $tire->li = $request->li;
        $tire->si = $request->si;
        $tire->price1 = $request->price1;
        $tire->price2 = $request->price2;
        $tire->comment = $request->comment;
        $tire->code = $request->code;
        $tire->eco = $request->eco;
        $tire->wet = $request->wet;
        $tire->noise = $request->noise;
        $tire->article = $request->article;
        $tire->quantity = $request->quantity;
        $tire->urs_quantity = $request->urs_quantity;
        $tire->krs_quantity = $request->krs_quantity;

        $tire->save();

        return redirect(route('admin.auto.tire.edit', $id))->with('success', 'Informācija veiksmīgi atjaunota');
    }

    public function tire_destroy($id)
    {

      Autotire::where('tire_id', $id)->delete();

      return redirect()->back()->with('success', 'Riepa veiksmīgi dzēsta!');

    }

    public function tire_image(Request $request, $id)
    {
        if ($request->hasFile('tread_image')) {
            $image      = $request->file('tread_image');
            $fileName   = $id . '.' . $image->getClientOriginalExtension();
            $fileNameSmall   = $id . '-s.' . $image->getClientOriginalExtension();
            $fileNameMed   = $id . '-n.' . $image->getClientOriginalExtension();
            $fileNameLarge   = $id . '-o.' . $image->getClientOriginalExtension();
//            dd($image);
            Image::make($image->getRealPath())->save('public/storage/auto/tread/' . $fileName);
            Image::make($image->getRealPath())
              ->resize(100, 100, function($constraint) {
                $constraint->aspectRatio();
              })->save('public/storage/auto/tread/' . $fileNameSmall);
            Image::make($image->getRealPath())
              ->resize(200, 200, function($constraint) {
                $constraint->aspectRatio();
              })->save('public/storage/auto/tread/' . $fileNameMed);
            Image::make($image->getRealPath())
              ->resize(1500, 1500, function($constraint) {
                $constraint->aspectRatio();
              })->save('public/storage/auto/tread/' . $fileNameLarge);
//            Storage::putFileAs('public/auto/tread/' . $fileName, (string)$image->encode('png', 95), $fileName);
//            Storage::putFileAs('public/auto/tread/' . $fileNameSmall, (string)$imageSmall->encode('png', 95), $fileNameSmall);
//            Storage::putFileAs('public/auto/tread/' . $fileNameMed, (string)$imageMed->encode('png', 95), $fileNameMed);
//            Storage::putFileAs('public/auto/tread/' . $fileNameLarge, (string)$imageLarge->encode('png', 95), $fileNameLarge);
        }
        return redirect()->back();
    }

    public function brands_list($paginate = 10)
    {
        if (is_numeric($paginate)) {
            if (\Session::has('search')) {
                $brands = Autobrand::orderBy('brand_id', 'DESC')->where('title', 'LIKE', '%' . \Session::get('search') . '%')->paginate($paginate);
            } else {
                $brands = Autobrand::orderBy('brand_id', 'DESC')->paginate($paginate);
            }
        } else {
            \Session::remove('search');
            return redirect(route('admin.auto.brands'));
        }

        return view('admin.auto_tires.brands.index', compact('brands', 'paginate'));
    }

    public function brand_search(Request $request, $paginate = 10) {

        if ($request->search) {
            \Session::put('search', $request->search);
            $brands = Autobrand::orderBy('brand_id', 'DESC')->where('title', 'LIKE', '%' . \Session::get('search') . '%')->paginate($paginate);
        } else {
            if (\Session::has('search')) {
                $brands = Autobrand::orderBy('brand_id', 'DESC')->where('title', 'LIKE', '%' . \Session::get('search') . '%')->paginate($paginate);
            } else {
                $brands = Autobrand::orderBy('brand_id', 'DESC')->paginate($paginate);
            }
        }

        return view('admin.auto_tires.brands.index', compact('brands', 'paginate'));
    }

    public function brand_add()
    {
        return view('admin.auto_tires.brands.add');
    }

    public function brand_store(Request $request)
    {
        $brand = new Autobrand();
        $brand->timestamps = false;
        $brand->title = $request->brand_title;
        $brand->slug = \Str::slug($request->brand_title, '-');
        $brand->save();

        if ($request->hasFile('brand_image')) {
            $brand_edit = Autobrand::findOrFail($brand->brand_id);
            $brand_edit->timestamps = false;

            $image      = $request->file('brand_image');
            $fileName   = 'auto_' . $brand->brand_id . '.' . $image->getClientOriginalExtension();


            Storage::disk('public')->putFileAs('brands', $image, $fileName);
            $brand_edit->image = $fileName;
            $brand_edit->save();
        }

        return redirect(route('admin.auto.brands'));
    }

    public function brand_edit($id)
    {
        $brand = Autobrand::findOrFail($id);

        return view('admin.auto_tires.brands.edit', compact('brand'));
    }

    public function brand_update(Request $request, $id)
    {
        $brand = Autobrand::findOrFail($id);
        $brand->timestamps = false;
        $brand->title = $request->brand_title;
        $brand->slug = \Str::slug($request->brand_title, '-');

        if ($request->hasFile('brand_image')) {

            $image      = $request->file('brand_image');
            $fileName   = 'auto_' . $id . '.' . $image->getClientOriginalExtension();


            Storage::disk('public')->putFileAs('brands', $image, $fileName);
            $brand->image = $fileName;
        }

        $brand->save();

        return view('admin.auto_tires.brands.edit', compact('brand'));
    }

    public function brand_delete(Request $request, $id)
    {
        $brand = Autobrand::findOrFail($id);
        Storage::disk('public')->delete('brands/' . $brand->image);
        $brand->delete();
        return redirect()->back();
    }

    /*
     *
     *
     * Riepu modeļi
     *
     *
     */

    public function treads_list($paginate = 10)
    {


        if (is_numeric($paginate)) {
            if (\Session::has('search')) {
                $treads = Autotread::orderBy('tread_id', 'DESC')->where('t_title', 'LIKE', '%' . \Session::get('search') . '%')->paginate($paginate);
            } else {
                $treads = Autotread::orderBy('tread_id', 'DESC')->groupBy('tread_id')->paginate($paginate);
            }
        } else {
            \Session::remove('search');
            return redirect(route('admin.auto.treads'));
        }

        return view('admin.auto_tires.treads.index', compact('treads', 'paginate'));
    }

    public function treads_search(Request $request, $paginate = 10)
    {
        if ($request->search) {
            \Session::put('search', $request->search);
            $treads = Autotread::orderBy('tread_id', 'DESC')->where('t_title', 'LIKE', '%' . \Session::get('search') . '%')->paginate($paginate);
        } else {
            if (\Session::has('search')) {
                $treads = Autotread::orderBy('tread_id', 'DESC')->where('title', 'LIKE', '%' . \Session::get('search') . '%')->paginate($paginate);
            } else {
                $treads = Autotread::orderBy('tread_id', 'DESC')->paginate($paginate);
            }
        }

        return view('admin.auto_tires.treads.index', compact('treads', 'paginate'));
    }

    public function tread_add()
    {
        return view('admin.auto_tires.treads.add');
    }

    public function tread_store(Request $request)
    {
        $tread = new Autotread();
        $tread->timestamps = false;
        $tread->t_title = $request->tread_title;
        $tread->slug = \Str::slug($request->tread_title, '-');
        $tread->save();

        if ($request->hasFile('tread_image')) {
            $tread_edit = Autotread::findOrFail($tread->brand_id);
            $tread_edit->timestamps = false;

            $image      = $request->file('tread_image');
            $fileName   = 'auto_' . $tread->brand_id . '.' . $image->getClientOriginalExtension();


            Storage::disk('public')->putFileAs('tread', $image, $fileName);
            $tread_edit->image = $fileName;
            $tread_edit->save();
        }

        return redirect(route('admin.auto.treads'));
    }

    public function tread_edit($id)
    {
        $tread = Autotread::findOrFail($id);
        $brands = Autobrand::all();

        return view('admin.auto_tires.treads.edit', compact('tread', 'brands'));
    }

    public function tread_update(Request $request, $id)
    {
        $tread = Autotread::findOrFail($id);
        $tread->timestamps = false;
        $tread->title = $request->tread_title;
        $tread->slug = \Str::slug($request->tread_title, '-');
        $tread->season = $request->tread_season;
        $tread->brand_id = $request->tread_brand;
        $tread->comment = $request->tread_desc;

        if ($request->hasFile('tread_image')) {

            $image      = $request->file('tread_image');
            $fileName   = 'auto_' . $id . '.' . $image->getClientOriginalExtension();


            Storage::disk('public')->putFileAs('tread', $image, $fileName);
            $tread->image = $fileName;
        }

        $tread->save();
        $brands = Autobrand::all();

        return view('admin.auto_tires.treads.edit', compact('tread', 'brands'));
    }

    public function tread_delete()
    {
        return 'tread_delete()';
    }

    /*
     *
     *
     * Auto riepu atjaunošana (Ajax)
     *
     *
     */

    public function ajaxUpdateTreads(Request $request)
    {
        $treads = Autotread::where('brand_id', $request->brand_id)->orderBy('t_title', 'ASC')->get();
        return json_encode($treads);
    }

    public function ajaxUpdateTires()
    {
        return 123;
    }
}
