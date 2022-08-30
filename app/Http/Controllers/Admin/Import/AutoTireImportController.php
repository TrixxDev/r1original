<?php

namespace App\Http\Controllers\Admin\Import;

use App\Http\Controllers\Controller;
use App\Models\Autobrand;
use App\Models\Autostock;
use App\Models\Autotire;
use App\Models\Autotread;
use Illuminate\Http\Request;
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

        foreach ($rows as $idx=>$row){

            $row = trim($row);
            if (($idx>-1)&&($row!='')){
                $fields = explode("\t",$row);

                dd($fields);

                $tire = Autotire::where('article', $fields[2])->first();
                $brand = Autobrand::where('title', $fields[3])->first();
                $tread = Autotread::where('title', $fields[11])->first();

                if ($tire === null)
                {
                    $tire = new Autotire();
                }

                if ($tread === null)
                {
                    $tread = new Autotread();
                    $tread->timestamps = false;
                    $tread->season = ($fields[0] == 'VASARA') ? 1 : 2;
                    if ($brand === null)
                    {
                        $brand = new Autobrand();
                        $brand->timestamps = false;
                        $brand->title = $fields[3];
                        $brand->slug = Str::slug($fields[3], '-');
                        $brand->save();
                        $brand_id = $brand->id;
                        $out.='<p>Jauns brends: '.ucfirst($brand->title).'</p>';
                    }
                    $brand_id = $brand->brand_id;
                    $tread->brand_id = $brand_id;
                    $tread->title = $fields[11];
                    $tread->slug = Str::slug($fields[11], '-');
                    $tread->comment = '';
                    $tread->type = 1;
                    $tread->save();
                    $tread_id = $tread->id;
                    $out.='<p>Jauns protektora modelis: '.ucfirst($tread->title).'</p>';
                }

                $tread_id = $tread->tread_id;

                $tire->timestamps = false;
                $tire->make_id = $tread_id;

                $tire->d1 = @$fields[4];
                $tire->d2 = @$fields[5];
                $tire->d3 = @$fields[6];

                $tire->li = @$fields[8];
                $tire->si = @$fields[9];

                $tire->price1 = @$fields[14];
                $tire->price2 = @$fields[15];

                $tire->comment = @$fields[16];
                $tire->code = @$fields[10];

                $tire->quantity = 0;

                $eco = trim(@$fields[20]);
                $wet = trim(@$fields[21]);
                $noise = trim(@$fields[22]);

                $tire->eco = $eco;
                $tire->wet = $wet;
                $tire->noise = $noise;

                $tire->article = @$fields[2];

                $tire->save();
                $tire_id = $tire->id;

                $i3 = @$fields[19];

                if ($tire_id !== null) {
                    $out .= "<p>Labojam izmēru: \"{$brand->title} {$tread->title}\" {$fields[4]}/{$fields[5]} R{$fields[6]} (LI:{$fields[8]}, SI:{$fields[9]}, kods: {$fields[16]}) - <strong>{$fields[2]}</strong></p>";
                } else {
                    $out .= "<p>Pievienojam izmēru: \"{$brand->title} {$tread->title}\" {$fields[4]}/{$fields[5]} R{$fields[6]} (LI:{$fields[8]}, SI:{$fields[9]}, kods: {$fields[16]}) - <strong>{$fields[2]}</strong></p>";
                }

                $stock = Autostock::where('itype', 'i3')->where('article', $i3)->first();


                if ($stock === null)
                {
                    $stock = new Autostock();
                    if ($tire_id === null) {
                        $stock->tire_id = $tire->tire_id;
                    } else {
                        $stock->tire_id = $tire_id;
                    }
                    $stock->article = $i3;
                    $stock->quantity = 0;
                    $stock->itype = 'i3';
                    $stock->metadata = '';
                    $stock->save();
                }

            }
        }

        return $out;
    }
}
