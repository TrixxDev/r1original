<?php

namespace App\Http\Controllers\Admin\Import;

use App\Http\Controllers\Controller;
use App\Models\Motobrand;
use App\Models\Motostock;
use App\Models\Moto;
use App\Models\Mototread;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MotoTireImportController extends Controller
{
    public function index()
    {
        return view('admin.import.moto');
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

                $tire = Moto::where('article', $fields[0])->first();
                $brand = Motobrand::where('title', $fields[1])->first();
                $tread = Mototread::where('title', $fields[11])->first();

                if ($tire === null)
                {
                    $tire = new Moto();
                }

                if ($tread === null)
                {
                    $tread = new Mototread();
                    $tread->timestamps = false;
                    if ($brand === null)
                    {
                        $brand = new Motobrand();
                        $brand->timestamps = false;
                        $brand->title = $fields[1];
                        $brand->slug = Str::slug($fields[1], '-');
                        $brand->save();
                        $brand_id = $brand->id;
                        $out.='<p>Jauns brends: '.ucfirst($brand->title).'</p>';
                    }
                    $brand_id = $brand->brand_id;
                    $tread->brand_id = $brand_id;
                    $tread->title = $fields[11];
                    $tread->slug = Str::slug($fields[11], '-');
                    $tread->comment = '';
                    $tread->save();
                    $tread_id = $tread->id;
                    $out.='<p>Jauns protektora modelis: '.ucfirst($tread->title).'</p>';
                }

                $tread_id = $tread->tread_id;

                $tire->timestamps = false;
                $tire->make_id = $tread_id;

                $tire->d1 = @$fields[2];
                $tire->sep = @$fields[4];
                $tire->d2 = @$fields[3];
                $tire->d3 = @$fields[5];

                $tire->type = @$fields[11];

                $tire->li = @$fields[7];
                $tire->si = @$fields[8];

                $tire->price1 = @$fields[12];
                $tire->price2 = @$fields[13];

                $tire->comment = @$fields[14];
                $tire->code = @$fields[9];

                $tire->quantity = 0;

                $tire->article = @$fields[0];

                $tire->save();
                $tire_id = $tire->id;

                $duell = @$fields[19];

                if ($brand->title == 0) {
                    return true;
                }

                if ($tire_id !== null) {
                    $out .= "<p>Labojam izmēru: \"{$brand->title} {$tread->title}\" {$fields[4]}/{$fields[5]} R{$fields[6]} (LI:{$fields[8]}, SI:{$fields[9]}, kods: {$fields[16]}) - <strong>{$fields[2]}</strong></p>";
                } else {
                    $out .= "<p>Pievienojam izmēru: \"{$brand->title} {$tread->title}\" {$fields[4]}/{$fields[5]} R{$fields[6]} (LI:{$fields[8]}, SI:{$fields[9]}, kods: {$fields[16]}) - <strong>{$fields[2]}</strong></p>";
                }

                $stock = Motostock::where('itype', 'duell')->where('article', $duell)->first();


                if ($stock === null)
                {
                    $stock = new Motostock();
                    if ($tire_id === null) {
                        $stock->tire_id = $tire->tire_id;
                    } else {
                        $stock->tire_id = $tire_id;
                    }
                    $stock->article = $duell;
                    $stock->quantity = 0;
                    $stock->itype = 'duell';
                    $stock->metadata = '';
                    $stock->save();
                }

            }
        }

        return $out;
    }
}
