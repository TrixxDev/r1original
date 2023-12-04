<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Office;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ShopController extends Controller
{

  public $status_enum = [
    1 => 'Nav pabeigts/Nav informācijas',
    2 => 'Jauns',
    9 => 'Procesā',
    3 => 'Gaidām apmaksu',
    4 => 'Gaidām preci',
    8 => 'Gaida piegādi',
    6 => 'Prece nav pieejama',
    7 => 'Klients atteicās',
    10 => 'Kļūdains pasūtījums',
    11 => 'Klients nav sazvanāms',
    5 => 'Pabeigts'
];

  public $pay_enum = [
    0 => '',
    1 => 'Apmaksa saņemšanas brīdī',
    2 => 'Bankas pārskaitījums',
    3 => 'Tiešsaistes apmaksa'
  ];

  public $filteredStatus;
  public $filteredEditor;

  public function __construct(Request $request)
  {

    $this->filteredStatus = ($request->input('admin-order-status-select')) ? $request->input('admin-order-status-select') : 0;
    $this->filteredEditor = ($request->input('admin-order-editor-select')) ? $request->input('admin-order-editor-select') : 0;
    View::share('filteredStatus', $this->filteredStatus);
    View::share('filteredEditor', $this->filteredEditor);
  }

  public function orders(Request $request)
  {
    DB::enableQueryLog();

    $status_enum = $this->status_enum;
    $pay_enum = $this->pay_enum;

    if($request->post() || $this->filteredStatus || $this->filteredEditor) {

      $orders = Order::when($this->filteredStatus, function($query) {
        $query->where('status', $this->filteredStatus);
      })->when($this->filteredEditor, function($query) {
        $query->where('edituser', $this->filteredEditor);
      })->orderBy('id', 'desc')->paginate(100)->appends($request->query());
//      dd(DB::getQueryLog());

      return view('admin.shop.index', compact('orders', 'status_enum', 'pay_enum'));
    }

    $orders = Order::orderBy('id', 'desc')->paginate(100)->appends($request->query());

    return view('admin.shop.index', compact('orders', 'status_enum', 'pay_enum'));

  }

  public function orders_print(Request $request)
  {

    $status_enum = $this->status_enum;
    $pay_enum = $this->pay_enum;

    DB::enableQueryLog();

    $time_from = $request->orders_from;
    $time_to = $request->orders_to;

    if (is_null($time_from) && is_null($time_to) || is_null($time_from) && $time_to) return Redirect::back();

    $time_to = Carbon::parse($time_to)->addDay()->format('Y-m-d');
    if (is_null($time_to)) {
      $time_to = Carbon::parse(date('d-m-Y'))->addDay()->format('Y-m-d');
    }

    $orders = Order::where('created_at','>=', $time_from)
                   ->where('created_at','<=', $time_to)
                   ->orderBy('created_at', 'ASC')
                   ->get();

    $spreadsheet = new Spreadsheet();



    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setTitle('Pasūtījumi - ' . date('Y-m-d'));

    $sheet->setCellValue('A1', 'Pasūtījuma datums');
    $sheet->setCellValue('B1', 'Klients');
    $sheet->setCellValue('C1', 'Klienta nr.');
    $sheet->setCellValue('D1', 'Klienta e-pasts.');
    $sheet->setCellValue('E1', 'Akcijas');
    $sheet->setCellValue('F1', 'Preču daudzums');
    $sheet->setCellValue('G1', 'Summa');
    $sheet->setCellValue('H1', 'Apmaksas veids');
    $sheet->setCellValue('I1', 'Pasūtījuma statuss');
    $sheet->setCellValue('J1', 'Menedžeris');
    $sheet->setCellValue('K1', 'Preču grupas');
    $sheet->setCellValue('L1', 'Piegādes adrese');

    $b = 2;

    foreach ($orders as $order) {
      $item_count = [];
      $item_sum = [];
      @$userData = json_decode(json_encode(unserialize($order->info)));
      if ($userData == false || !isset($userData->items)) continue;
      $sheet->setCellValue('A' . $b, Carbon::parse($order->created_at)->format('Y-m-d'));
      if (isset($userData->name) || isset($userData->surname)) {
        $sheet->setCellValue('B' . $b, $userData->name . ', ' . $userData->surname);
      } else {
        $sheet->setCellValue('B' . $b, 'Nav info');
      }
      $sheet->setCellValue('C' . $b, $userData->phone_number);
      $sheet->setCellValue('D' . $b, $userData->email);
      if (isset($userData->items)) {
        foreach ($userData->items as $item) {
          if (!isset($item->quantity)) continue;
          array_push($item_count, $item->quantity);
          array_push($item_sum, ($item->price * $item->quantity));
        }
      }
      $item_count = array_sum($item_count);
      $item_sum = array_sum($item_sum);
      if ($order->delivery_price > 0) {
        $item_sum = $item_sum + (int) substr($order->delivery_price, 0, -2);
      } else if ($order->fit_price > 0) {
        $item_sum = $item_sum + (int) substr($order->fit_price, 0, -2);
      }
      $sheet->setCellValue('E' . $b, (isset($userData->email_notifications)) ? 'Jā' : 'Nē');
      $sheet->setCellValue('F' . $b, $item_count);
      $sheet->setCellValue('G' . $b, $item_sum);
      $sheet->setCellValue('H' . $b, $pay_enum[$order->payment]);
      $sheet->setCellValue('I' . $b, $status_enum[$order->status]);
      if (User::find($order->edituser)) {
        $sheet->setCellValue('J' . $b, User::find($order->edituser)->fullName);
      } else {
        $sheet->setCellValue('J' . $b, 'Neviens nav veicis labojumus');
      }
      if (isset($userData->shipping_city)) {
        if ($userData->shipping_city == 1) {
          $sheet->setCellValue('L' . $b, 'Rīga, ' . $userData->shipping_address);
        } else if ($userData->shipping_city == 2) {
          $sheet->setCellValue('L' . $b, 'Salaspils, ' . $userData->shipping_address);
        } else {
          $sheet->setCellValue('L' . $b, $userData->shipping_address);
        }
      } else {
        $sheet->setCellValue('L' . $b, '');
      }

      $b++;
    }

    $sheet->setAutoFilter('A:H');
    $lastRow = $sheet->getHighestRow();
    $sheet->getStyle('A2:J' . $lastRow)->getAlignment()->setHorizontal('center');
    $cellIterator = $sheet->getRowIterator()->current()->getCellIterator();
    $cellIterator->setIterateOnlyExistingCells(true);
    foreach ($cellIterator as $cell) {
      if ($cell->getColumn() == 'I') continue;
      $sheet->getColumnDimension($cell->getColumn())->setAutoSize(true);
    }
    $sheet->getColumnDimension('D')->setWidth(33);
    $sheet->getColumnDimension('E')->setWidth(10);
    $sheet->getColumnDimension('F')->setWidth(10);
    $sheet->getColumnDimension('G')->setWidth(31);
    $sheet->getColumnDimension('H')->setWidth(45);
    $sheet->getColumnDimension('I')->setWidth(27);
    $sheet->getColumnDimension('J')->setWidth(14);

    $writer = new Xlsx($spreadsheet);
    $filename = 'pasutijumi.xlsx';

    $writer->save($filename);

    // Set the content-type:
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Content-Length: ' . filesize($filename));
    readfile($filename); // send file
    unlink($filename); // delete file
    exit;


//          dd(DB::getQueryLog());
//    return view('admin.shop.index', compact('orders', 'status_enum', 'pay_enum'));
  }

  public function order($id)
  {

    $status_enum = $this->status_enum;
    $pay_enum = $this->pay_enum;

    $order = Order::findOrFail($id);

    @$userData = json_decode(json_encode(unserialize($order->info)));
    //dd($order);
    if ($userData == false || !isset($userData->items)) { return \Redirect::to(route('admin.orders'))->with('danger', 'Nevar atvērt pasūtījumu'); }

    $offices = Office::all();

    $tires = $userData->items;

    if (property_exists($userData,'company_registration_number')){
      $hasCompanyData = true;
    }else{
      $hasCompanyData = false;
    }

    return view('admin.shop.order', compact('order', 'userData', 'tires', 'offices', 'status_enum', 'pay_enum'));

  }

  public function order_update(Request $request, $id)
  {
    $order = Order::findOrFail($id);

    $data = (object) unserialize($order->info);

//    if (strpos($request->name_suraname, ',') !== false) {
//      $names = explode(', ', $request->name_suraname);
//    } else {
//      $names = explode(' ', $request->name_suraname);
//    }

//    $data->name = $names[0];
//    $data->surname = $names[1];
//    $data->email = $request->email;

    $order->status = $request->order_status;
    $order->edituser = Auth::user()->id;

    if ($order->save()) {
      return redirect()->back()->with('success', 'Pasūtījums informācija veiksmīgi labota!');
    } else {
      return redirect()->back()->with('danger', 'Notika kļūda labojot pasūtījuma informāciju!');
    }

  }

  public function delete($id) {

    $order = Order::findOrFail($id);

    if ($order->delete()) {
	    return redirect()->route('admin.orders')
          ->with('success','Pasūtījums veiksmīgi dzēsts');
    } else {
	    return redirect()->route('admin.shop.orders')->with('danger', 'Kļūda pasūtījuma dzēšanā');
    }

  }

}
