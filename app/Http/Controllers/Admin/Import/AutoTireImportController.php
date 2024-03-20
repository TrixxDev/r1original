<?php

namespace App\Http\Controllers\Admin\Import;

use App\Http\Controllers\Controller;
use App\Models\Autobrand;
use App\Models\Autostock;
use App\Models\Autotire;
use App\Models\Autotread;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AutoTireImportController extends Controller
{
    public function index()
    {
        return view('admin.import.auto');
    }

    public function import(Request $request)
    {
      $out = '';

      $data = $request->rows;
      $rows = explode("\n", trim($data));

      DB::table('auto_tires')->update(['top' => 0]);

      foreach ($rows as $idx => $row) {

        $row = trim($row);
        if (($idx > -1) && ($row != '')) {
          $fields = explode("\t", $row);

          $tire = Autotire::where('article', $fields[2])->first();
          $new = false;

          if ($tire === null) {
            $tire = new Autotire();
            $new = true;
          }

          $brand = Autobrand::where('title', 'like', '%' . $fields[3] . '%')->orderBy('brand_id', 'DESC')->first();

          if ($brand === null) {
            $brand = new Autobrand();
            $brand->timestamps = false;
            $brand->title = $fields[3];
            $brand->slug = Str::slug($fields[3], '-');
            $brand->save();
            $brand_id = $brand->brand_id;
            $out .= '<p>Jauns brends: ' . ucfirst($brand->title) . '</p>';
          }

          $tread = Autotread::where('t_title', $fields[11])->where('brand_id', $brand->brand_id)->first();

          if ($tread === null) {
            $tread = new Autotread();
            $tread->timestamps = false;
            $tread->season = ($fields[0] == 'VASARA') ? 1 : 2;
            $tread->brand_id = ($brand === null) ? $brand_id : $brand->brand_id;
            $tread->t_title = $fields[11];
            $tread->slug = Str::slug($fields[11], '-');
            $tread->t_comment = '';
            $tread->t_type = 1;
            $tread->save();
            $tread_id = $tread->tread_id;
            $out .= '<p>Jauns protektora modelis: ' . ucfirst($tread->t_title) . '</p>';
          }

          $tread_id = $tread->tread_id;

          $tire->timestamps = false;
          $tire->make_id = $tread_id;

          $tire->d1 = @$fields[4];
          $tire->d2 = @$fields[5];
          $tire->d3 = @$fields[6];

          switch (@$fields[12]) {
              case 'M+S': {
                  $returnType = 1;
                  break;
              }
              case 'R': {
                  $returnType = 2;
                  break;
              }
              case 'R+': {
                  $returnType = 3;
                  break;
              }
              case 'W': {
                  $returnType = 4;
                  break;
              }
              default: {
                  $returnType = NULL;
                  break;
              }
          }

          $tire->type = $returnType;

          $tire->li = @$fields[8];
          $tire->si = @$fields[9];

          $tire->price1 = @$fields[14];
          $tire->price2 = @$fields[15];

          $tire->comment = @$fields[18];
          $tire->acomment = @$fields[25];
          $tire->code = @$fields[10];

          $tire->quantity = 0;

          $eco = trim(@$fields[22]);
          $wet = trim(@$fields[23]);
          $noise = trim(@$fields[24]);

          $tire->eco = $eco;
          $tire->wet = $wet;
          $tire->noise = $noise;

          $tire->top = (@$fields[26] == 'X') ? 0 : 1;

          $tire->article = @$fields[2];

//          dd($fields, $tire->getAttributes());

          $tire->save();
          $tire_id = $tire->tire_id;

          $i3 = @$fields[19];
          $gy = @$fields[20];
          $rz = @$fields[21];

          if ($new === false) {
            $out .= "<p>Labojam izmēru: \"{$brand->title} {$tread->t_title}\" {$fields[4]}/{$fields[5]} R{$fields[6]} (LI:{$fields[8]}, SI:{$fields[9]}, kods: {$fields[10]}) - <strong>{$fields[2]}</strong></p>";
          } else {
            $out .= "<p>Pievienojam izmēru: \"{$brand->title} {$tread->t_title}\" {$fields[4]}/{$fields[5]} R{$fields[6]} (LI:{$fields[8]}, SI:{$fields[9]}, kods: {$fields[10]}) - <strong>{$fields[2]}</strong></p>";
          }

          if (!empty($i3)) {
            $tire->addSecondaryArticle($i3, 'i3');
          }

          if (!empty($gy)) {
            $tire->addSecondaryArticle($gy, 'gy');
          }

          if (!empty($rz)) {
            $tire->addSecondaryArticle($rz, 'rz');
          }

        }
      }

      return redirect()->back()->with('out', $out);

    }
}
