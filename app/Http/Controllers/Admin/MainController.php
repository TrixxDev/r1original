<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Audit;
use App\Models\Slot;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class MainController extends Controller
{

    public $models;
    public $model;
    public $param;
    public $searchBy;

    public function models()
    {
      $this->models = [
        'slots' => [
          'name' => 'App\\Models\\Slot',
          'title' => 'Slots',
          Schema::getColumnListing((new Slot)->getTable()),
          'searchBy' => [
            'audit_event' => 'Notikums',
            'vehiclePlate' => 'Mašīnas numurs',
            'ownerPhone' => 'Telefona numurs',
            // Datums, Laiks, Mašīnas numurs, Telefona numurs, Klienta vārds-uzvārds
          ],
        ],
//        'users' => [
//          'name' => 'App\\Models\\User',
//          'title' => 'Lietotāji',
//          Schema::getColumnListing((new User)->getTable()),
//          'searchBy' => [
//            'audit_time' => 'Laiks',
//            'audit_time' => 'Datums',
//            // Datums, Laiks, Mašīnas numurs, Telefona numurs, Klienta vārds-uzvārds
//          ]
//        ],
      ];

      return $this->models;
    }

    public function home()
    {
        return view('admin.home');
    }

  public function audits(Request $request)
  {
      DB::enableQueryLog();
      $models = $this->models();

      if ($request->post())
      {
        if (isset($request->params)) {
          if (empty($request->params)) return redirect(route('admin.audits'));
          $quote = "'";
          $this->model = $modelname = $request->model;
          $this->param = $param = $request->params;

          $params = explode(';', $this->model);
          $this->model = $params[0];

          $modelName = $this->models[$this->model]['name'];
          $this->searchBy = $params[1];

          if ($this->searchBy == 'audit_event') {
            $audits = Audit::where('audit_classname', $modelName)->where($this->searchBy, 'like', '%' . $this->param . '%')
              ->orderBy('audit_time', 'DESC')->orderBy('id', 'DESC')->paginate(20);
            $modelname = $this->model . ';' . $this->searchBy;
          } else {
            $audits = Audit::when($this->model, function ($query) use ($quote, $modelName) {
              $query->where('audit_classname', $modelName)->whereRaw('audit_instance LIKE ' . $quote . '%"' . $this->searchBy . '":"' . $this->param . '"%' . $quote);
              //          $query->where('audit_classname', $modelName)->whereRaw('audit_instance LIKE ' . $quote . '%"' . $this->searchBy . '":"' . $quote . '||' . $this->param . '||' . $quote . '"%' . $quote);
            })->orderBy('audit_time', 'DESC')->orderBy('id', 'DESC')->paginate(20);
          }
          return view('admin.audits.audits', compact('audits', 'models', 'modelname', 'param'));
        }

//        dd(DB::getQueryLog());

      }

      $modelname = '';
      $param = '';
      $audits = Audit::orderBy('audit_time', 'DESC')->orderBy('id', 'DESC')->paginate(20);

      return view('admin.audits.audits', compact('audits', 'models', 'modelname', 'param'));
  }

  public function audit($id)
  {
    $audit = Audit::where('id', $id)->first();

    $classname = $audit->audit_classname;

    $instance_id = $audit->audit_item;

    $instance_data = $audit->audit_instance;
    $instance_class = $audit->audit_classname;
    $instance = unserialize($instance_data);

    $row1 = Audit::where('audit_item', $instance_id)->where('id', '<', $id)->where('audit_classname', $instance_class)->orderBy('audit_time', 'DESC')->orderBy('id', 'DESC')->first();

    if ($row1) {
      $old_instance_data = $row1->audit_instance;
      $old_instance = unserialize($old_instance_data);
      if ($old_instance === false) {
	      if (class_exists($instance_class)) {
          $old_instance = new $instance_class;
	      } else {
	        $old_instance = '';
	      }
      }
    } else {
      if (class_exists($instance_class)) {
        $old_instance = new $instance_class;
      } else {
        $old_instance = '';
      }
    }

    return view('admin.audits.audit', compact('audit', 'classname', 'id', 'old_instance', 'instance', 'instance_id'));
  }

}
