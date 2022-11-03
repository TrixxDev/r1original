<?php

  namespace App\Http\Controllers\Admin;

  use App\Helper\Image;
  use App\Http\Controllers\Controller;
  use App\Models\Autotire;
  use App\Models\Code;
  use App\Models\Moto;
  use App\Models\Quadr;
  use App\Models\User;
  use DOMDocument;
  use Illuminate\Http\Request;
  use App\Models\Service;
  use Illuminate\Support\Facades\DB;
  use Illuminate\Support\Facades\File;
  use Illuminate\Support\Facades\Hash;
  use Illuminate\Support\Str;
  use Spatie\Permission\Models\Role;

  class SettingsController extends Controller
  {

    public function services()
    {
      $services = Service::orderBy('service_id', 'DESC')->get();

      return view('admin.settings.services', compact('services'));
    }

    public function services_store(Request $request)
    {
      $service = new Service;
      $service->timestamps = false;
      $service->title = $request->title;
      $service->pdf_title = $request->pdf_title;
      if ($request->f_save == 'on') $service->f_save = 1;
      if ($service->save()) {
        return json_encode(['success' => 'Pakalpojums veiksmīgi izveidots!', 'service_id' => $service->service_id, 'service_title' => $request->title]);
      }

    }

    public function services_edit($id)
    {
      return $id;
    }

    public function services_destroy($id)
    {
      $service = Service::findOrFail($id);
      $service->delete();
      return redirect(route('admin.settings.services'))->with('success', 'Pakalpojums veiksmīgi dzēsts!');
    }

    // Administratori

    public function users()
    {

      $users = User::role(['administrators', 'moderators'])->get();

      return view('admin.settings.users', compact('users'));
    }

    public function users_create()
    {
      $roles = Role::all();

      return view('admin.settings.users_create', compact('roles'));
    }

    public function users_store(Request $request)
    {

      $user = new User;
      $user->name = strip_tags($request->name);
      $user->surname = strip_tags($request->surname);
      $user->username = strip_tags($request->username);
      $user->password = Hash::make($request->password);
      $user->email = strip_tags($request->email);

      foreach ($request->status as $role) {
        $user->assignRole($role);
      }

      $user->save();

//      dd(Hash::make($request->password));

      return redirect(route('admin.settings.users'))->withSuccess('Lietotājs veiksmīgi izveidots!');

    }

    public function users_edit($id, Request $request)
    {
      $user = User::findOrFail($id);
      $roles = Role::all();

      return view('admin.settings.users_edit', compact('user', 'roles'));
    }

    public function users_destroy($id)
    {
      $user = User::findOrFail($id);
      $user->delete();

      return redirect(route('admin.settings.users'))->withSuccess('Lietotājs veiksmīgi dzēsts!');
    }

    // Lapas

    public function pages()
    {
      $pages = DB::table('pages')->get();

      return view('admin.settings.pages', compact('pages'));
    }

    public function pages_create()
    {
      return view('admin.settings.pages_create');
    }

    public function pages_store(Request $request)
    {

      $title = $request->name;
      $slug = Str::slug($title);

      $file = $slug . '.blade.php';

      DB::table('pages')->insert([
        'title' => $title,
        'route' => $slug,
      ]);

      $html = "@extends('layouts.app')

@section('content')
<div class='container'>
    <div class='row'>
        <div class='main-content clearfix col-md-12 col-xl-10'>
            <div id='content-wrapper' class='right-column col-lg-12'>
                <section id='main'>
                    <header class='page-header'>
                        <h1>
                            $title
                        </h1>
                    </header>
                    <section id='content' class='page-content page-cms'>

                        @include('pages.components.$slug')

                    </section>
                    <footer class='page-footer'>
                        <!-- Footer content -->
                    </footer>
                </section>
            </div>
        </div>
        @include('components.right-sidebar')
    </div>
</div>

@endsection";

      File::put($_SERVER['DOCUMENT_ROOT'] . '/resources/views/pages/' . $file, $html);
      File::put($_SERVER['DOCUMENT_ROOT'] . '/resources/views/pages/components/' . $file, '');

      return redirect(route('admin.settings.pages'))->withSuccess('Lapa veiksmīgi izveidota!');
    }

    public function pages_edit($id)
    {
      $page = DB::table('pages')->where('id', $id)->first();

      $file = $page->route . '.blade.php';

      $component_file = $_SERVER['DOCUMENT_ROOT'] . '/resources/views/pages/components/' . $file;

      if (File::exists($component_file)) {
        $doc = File::get($component_file);
      } else {
        $doc = '';
      }

      return view('admin.settings.pages_edit', compact('page', 'doc'));
    }

    public function pages_update(Request $request, $id)
    {
      $page = DB::table('pages')->where('id', $id)->first();

      $title = $request->name;
      $file = $page->route . '.blade.php';
      $slug = $page->route;

      if ($request->uri && !empty($request->uri)) {
        if ($page->route !== $request->uri) {
          $file = $request->uri . '.blade.php';
          $slug = Str::slug($request->uri);

          File::delete($_SERVER['DOCUMENT_ROOT'] . '/resources/views/pages/' . $page->route . '.blade.php');
          File::delete($_SERVER['DOCUMENT_ROOT'] . '/resources/views/pages/components/' . $page->route . '.blade.php');

          $html = "@extends('layouts.app')

@section('content')
<div class='container'>
    <div class='row'>
        <div class='main-content clearfix col-md-12 col-xl-10'>
            <div id='content-wrapper' class='right-column col-lg-12'>
                <section id='main'>
                    <header class='page-header'>
                        <h1>
                            $title
                        </h1>
                    </header>
                    <section id='content' class='page-content page-cms'>

                        @include('pages.components.$slug')

                    </section>
                    <footer class='page-footer'>
                        <!-- Footer content -->
                    </footer>
                </section>
            </div>
        </div>
        @include('components.right-sidebar')
    </div>
</div>

@endsection";

          File::put($_SERVER['DOCUMENT_ROOT'] . '/resources/views/pages/' . $file, $html);
          File::put($_SERVER['DOCUMENT_ROOT'] . '/resources/views/pages/components/' . $file, html_entity_decode($request->page_content));
          DB::table('pages')->where('id', $id)->update(['route' => $request->uri]);
        }
      }

      $component_file = $_SERVER['DOCUMENT_ROOT'] . '/resources/views/pages/components/' . $file;

      $content = html_entity_decode($request->page_content);

      if (File::exists($component_file)) {
        File::delete($component_file);
        File::put($component_file, $content);
      } else {
        return back()->withError('Tāds fails neeksistē - ' . $component_file);
      }

      return redirect()->back()->withSuccess('Lapa veiksmīgi labota!');
    }

    public function pages_destroy($id)
    {
      $page = DB::table('pages')->where('id', $id)->first();

      $file = $page->route . '.blade.php';

      $view_file = $_SERVER['DOCUMENT_ROOT'] . '/resources/views/pages/' . $file;
      $component_file = $_SERVER['DOCUMENT_ROOT'] . '/resources/views/pages/components/' . $file;

      if (File::exists($view_file)) {
        File::delete($view_file);
      } else {
        return back()->withError('Tāds fails neeksistē - ' . $view_file);
      }

      if (File::exists($component_file)) {
        File::delete($component_file);
      } else {
        return back()->withError('Tāds fails neeksistē - ' . $component_file);
      }
      DB::table('pages')->where('id', $id)->delete();

      return redirect(route('admin.settings.pages'))->withSuccess('Lapa veiksmīgi dzēsta!');
    }

    // Salidzini.lv / Kurpirkt.lv XML Ģenerācijas

    public function salidzini()
    {
      set_time_limit(0);
      $tires = Autotire::with('tread')->where('visible_users', '<>', 0)->get();
      $moto = Moto::with('tread')->where('visible_users', '<>', 0)->get();
      $quadr = Quadr::with('tread')->where('visible_users', '<>', 0)->get();
      $dom = new DOMDocument();
      $dom->encoding = 'utf-8';
      $dom->xmlVersion = '1.0';
      $dom->formatOutput = true;
      $xml_file_name = $_SERVER['DOCUMENT_ROOT'] . '/xml/salidzini.xml';
      $root = $dom->createElement('root');
      foreach ($tires as $tire) {
        if (!isset($tire->tread->season)) {
          continue;
        }
        $item = $dom->createElement('item');
        $child_node_title = $dom->createElement('name', $tire->fullName);
        $item->appendChild($child_node_title);
        $child_node_title = $dom->createElement('price', $tire->offerPrice);
        $item->appendChild($child_node_title);
        $child_node_title = $dom->createElement('link', $tire->link);
        $item->appendChild($child_node_title);
        $child_node_title = $dom->createElement('image', Image::showAd('auto', $tire->make_id));
        $item->appendChild($child_node_title);
        if ($tire->tread->season === 1) {
          $child_node_title = $dom->createElement('category', 'Vasaras riepas >> R' . $tire->d3);
          $item->appendChild($child_node_title);
          $child_node_title = $dom->createElement('category_full', 'Auto preces >> Vasaras riepas >> R' . $tire->d3);
          $item->appendChild($child_node_title);
          $child_node_title = $dom->createElement('category_link', route('vasaras-riepas'));
          $item->appendChild($child_node_title);
        } else {
          $child_node_title = $dom->createElement('category', 'Ziemas riepas >> R' . $tire->d3);
          $item->appendChild($child_node_title);
          $child_node_title = $dom->createElement('category_full', 'Auto preces >> Ziemas riepas >> R' . $tire->d3);
          $item->appendChild($child_node_title);
          $child_node_title = $dom->createElement('category_link', route('ziemas-riepas'));
          $item->appendChild($child_node_title);
        }
        $child_node_title = $dom->createElement('in_stock', ($tire->quantity + $tire->getStockCount()));
        $item->appendChild($child_node_title);
        $root->appendChild($item);
        $dom->appendChild($root);
      }
      foreach ($moto as $tire) {
        $item = $dom->createElement('item');
        $child_node_title = $dom->createElement('name', $tire->fullName);
        $item->appendChild($child_node_title);
        $child_node_title = $dom->createElement('price', $tire->offerPrice);
        $item->appendChild($child_node_title);
        $child_node_title = $dom->createElement('link', $tire->link);
        $item->appendChild($child_node_title);
        $child_node_title = $dom->createElement('image', Image::showAd('moto', $tire->make_id));
        $item->appendChild($child_node_title);
        $child_node_title = $dom->createElement('category', 'Motociklu riepas');
        $item->appendChild($child_node_title);
        $child_node_title = $dom->createElement('category_full', 'Auto preces >> Motociklu riepas');
        $item->appendChild($child_node_title);
        $child_node_title = $dom->createElement('category_link', route('motociklu-riepas'));
        $item->appendChild($child_node_title);
        $child_node_title = $dom->createElement('in_stock', ($tire->quantity + $tire->getStockCount()));
        $item->appendChild($child_node_title);
        $root->appendChild($item);
        $dom->appendChild($root);
      }
      foreach ($quadr as $tire) {
        $item = $dom->createElement('item');
        $child_node_title = $dom->createElement('name', htmlspecialchars($tire->fullName));
        $item->appendChild($child_node_title);
        $child_node_title = $dom->createElement('price', $tire->offerPrice);
        $item->appendChild($child_node_title);
        $child_node_title = $dom->createElement('link', $tire->link);
        $item->appendChild($child_node_title);
        $child_node_title = $dom->createElement('image', Image::showAd('quadr', $tire->make_id));
        $item->appendChild($child_node_title);
        $child_node_title = $dom->createElement('category', 'Kvadraciklu riepas');
        $item->appendChild($child_node_title);
        $child_node_title = $dom->createElement('category_full', 'Auto preces >> Kvadraciklu riepas');
        $item->appendChild($child_node_title);
        $child_node_title = $dom->createElement('category_link', route('kvadraciklu-riepas'));
        $item->appendChild($child_node_title);
        $child_node_title = $dom->createElement('in_stock', ($tire->quantity + $tire->getStockCount()));
        $item->appendChild($child_node_title);
        $root->appendChild($item);
        $dom->appendChild($root);
      }
      $dom->save($xml_file_name);
      return $dom->saveXML();

    }

    public function kurpirkt()
    {
      $tires = Autotire::with('tread')->where('visible_users', 0)->get();
      $moto = Moto::with('tread')->where('visible_users', 0)->get();
      $quadr = Quadr::with('tread')->where('visible_users', 0)->get();
      $xml = '';
      $xml_file_name = $_SERVER['DOCUMENT_ROOT'] . '/xml/kurpirkt.xml';
      foreach ($tires as $tire) {
        if (!isset($tire->tread->season)) {
          continue;
        }
        $name = $tire->fullName;
        $price = $tire->offerPrice;
        $category = 'Auto piederumi > ' . (@$tire->tread->season == 1) ? 'Vasaras riepas' : 'Ziemas riepas';
        $category .= ' > ' . $tire->d3;
        $link = $tire->link;
        $image = Image::showAd('auto', $tire->make_id);
        $xml .= "{$name} || {$price} || {$link} || {$image} || {$category}\n";
      }
      foreach ($moto as $tire) {
        $name = $tire->fullName;
        $price = $tire->offerPrice;
        $category = 'Auto piederumi > Motociklu riepas';
        $link = $tire->link;
        $image = Image::showAd('moto', $tire->make_id);
        $xml .= "{$name} || {$price} || {$link} || {$image} || {$category}\n";
      }
      foreach ($quadr as $tire) {
        $name = $tire->fullName;
        $price = $tire->offerPrice;
        $category = 'Auto piederumi > Kvadraciklu riepas';
        $link = $tire->link;
        $image = Image::showAd('quadr', $tire->make_id);
        $xml .= "{$name} || {$price} || {$link} || {$image} || {$category}\n";
      }
      $xml .= '</root>';
      file_put_contents($xml_file_name, $xml);
      return $xml;
    }

    // Sinhronizācijas

    public function syncs()
    {

      $accrual_last_time = DB::table('sync_times')->where('name', 'accrual')->first()->updated_at;
      $i3_auto = DB::table('sync_times')->where('name', 'i3-auto')->first()->updated_at;
      $gy_auto = DB::table('sync_times')->where('name', 'gy-auto')->first()->updated_at;
      $rz_auto = DB::table('sync_times')->where('name', 'rz-auto')->first()->updated_at;
      $i3_moto = DB::table('sync_times')->where('name', 'i3-moto')->first()->updated_at;
      $duell_moto = DB::table('sync_times')->where('name', 'duell-moto')->first()->updated_at;
      $i3_quadr = DB::table('sync_times')->where('name', 'i3-quadr')->first()->updated_at;
      $duell_quadr = DB::table('sync_times')->where('name', 'duell-quadr')->first()->updated_at;
      $i3_big = DB::table('sync_times')->where('name', 'i3-big')->first()->updated_at;
      $starco_big = DB::table('sync_times')->where('name', 'starco-big')->first()->updated_at;

      return view('admin.settings.syncs',
              compact('accrual_last_time',
                'i3_auto',
                'gy_auto',
                'rz_auto',
                'i3_moto',
                'duell_moto',
                'i3_quadr',
                'duell_quadr',
                'i3_big',
                'starco_big'
              )
            );
    }

    public function codes()
    {
      $codes = Code::all();

      return view('admin.settings.codes', compact('codes'));
    }

    public function codes_create()
    {
      return view('admin.settings.codes_create');
    }

    public function codes_store(Request $request)
    {
      $request->validate([
        'name' => 'required|max:50',
        'explanation' => 'required|max:150',
      ]);

      $code = new Code;
      $code->name = $request->name;
      $code->explanation = $request->explanation;
      $code->save();

      return redirect()->route('admin.settings.codes')
                       ->with('success', 'Kods ' . $code->name . ' pievienots veiksmīgi!');

    }

    public function codes_edit($id)
    {
      $code = Code::findOrFail($id);

      return view('admin.settings.codes_edit',compact('code'));
    }

    public function codes_update(Request $request, $code)
    {
      $request->validate([
        'name' => 'required',
        'explanation' => 'required',
      ]);

      $code = Code::findOrFail($code);
      $code->name = $request->name;
      $code->explanation = $request->explanation;
      $code->save();

      //$code->update($request->all());

      return redirect()
        ->route('admin.settings.codes')
        ->withSuccess('Kods tika veiksmīgi labots!');
    }

    public function codes_destroy($id)
    {
      $code = Code::findOrFail($id);
      $code->delete();

      return redirect()->route('admin.settings.codes')
        ->with('success','Kods - ' . $code->name . ' tika veiksmīgi dzēsts!');

    }

    // Cenas

    public function prices() {

      $prices = DB::table('cart_config')->get();

      return view('admin.settings.prices', compact('prices'));

    }

    public function price_update(Request $request, $id) {

      $price = DB::table('cart_config')->where('id', $id)->first();

      $update = DB::table('cart_config')->where('id', $id)->update([
						'name' => $request->inputs['name'],
						'abbr' => $request->inputs['text'],
						'value' => $request->inputs['price']
						]);

	//dd($update, $request->inputs);

      if ($update == 1) {
	return json_encode(['success' => 'Cena veiksmīgi izlabota!']);
      } elseif ($update == 0 && $price->name == $request->inputs['name'] && $price->abbr == $request->inputs['text'] && $price->value == $request->inputs['price']) {
	return json_encode(['warning' => 'Nav labojumu!']);
      } else {
	return json_encode(['error' => 'Notika kļūda']);
      }

    }

}
