@extends('admin.layouts.app')

@section('content')

    <div class="container-fluid">
        <div class="fade-in">
            @if (session('success'))
              <div class="alert alert-success">
                {{ session('success') }}
              </div>
            @endif
            @if (session('danger'))
              <div class="alert alert-danger">
                {{ session('danger') }}
              </div>
            @endif
            <div class="card">
                <div class="card-header"> Auto riepas
                    <div class="card-header-actions">
                    </div>
                </div>
                <div class="card-body">
                    <div id="DataTables_Table_0_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
                        <div class="row">
                            <div class="col-sm-12 col-md-6">
                                <div class="form-group row brand-settings">
                                    <label class="col-md-2 col-form-label" for="brand_select">Brends: </label>
                                    <select name="brand" class="form-control col-md-3" data-model="auto" id="brand_select">
                                        @foreach ($brands as $brand)
                                            <option value="{{ $brand->brand_id }}" @if (isset($tread->brand_id) && $tread->brand_id == $brand->brand_id) {{ 'selected' }} @endif >{{ ucwords(strtolower($brand->title)) }}</option>
                                        @endforeach
                                    </select>
                                    <form method="post" style="display: flex;">
                                      @csrf
                                      <input type="text" name="brand-name" style="width: 200px; margin-left: 10px;" disabled class="form-control brand-input">
                                      <button type="button" class="btn btn-success new-brand" style="margin-left: 10px; color: white;">Izveidot</button>
                                      <button type="button" class="btn btn-warning edit-brand" style="margin-left: 10px; color: white;">Labot</button>
                                      <button class="btn btn-danger delete-brand" name="delete-brand" value="true" style="margin-left: 10px; color: white;">Dzēst</button>
                                    </form>
                                </div>
                                <div class="form-group row make-settings">
                                    <label class="col-md-2 col-form-label" for="tread_select">Modelis: </label>
                                    <select name="tread" class="form-control col-md-3" id="tread_select" disabled></select>
                                    <form method="post" style="display: flex;">
                                      @csrf
                                      <input type="text" name="make-name" style="width: 200px; margin-left: 10px;" disabled class="form-control make-input">
                                      <button type="button" class="btn btn-success new-make" style="margin-left: 10px; color: white;">Izveidot</button>
                                      <button type="button" class="btn btn-warning edit-make" style="margin-left: 10px; color: white;">Labot</button>
                                      <button class="btn btn-danger delete-make" name="delete-make" value="true" style="margin-left: 10px; color: white;">Dzēst</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 preview-image">
                                @if (isset($tread))
{{--                                  {{ dd($tread) }}--}}
                                  {!! App\Helper\Image::showGrid('auto', $tread->tread_id, 'width: 300px; margin-bottom: 20px;') !!}

                                  @if (isset($tread->image))
                                  <form action="{{ route('admin.auto.tires.image', $tread->tread_id) }}" method="post" enctype="multipart/form-data">
                                      @csrf
                                      <div class="row">
                                          <div class="col-md-3">
                                              <input id="file-input" type="file" name="tread_image" style="margin-bottom: 10px;">
                                          </div>
                                      </div>
                                      <div class="row">
                                          <div class="col-md-3">
                                              <button class="btn btn-md btn-primary" type="submit"> Saglabāt</button>
                                              <button type="clear" class="btn btn-md btn-info"> Attīrīt</button>
                                          </div>
                                      </div>
                                  </form>
                                  @endif
                                @endif
                            </div>
{{--                            <textarea name="" id="" cols="80" rows="10"></textarea>--}}
                            <div class="tread_comment">
                              <div class="card">
                                <div class="card-body">
                                  <span class="comment-text">{!! $tread->t_comment !!}</span>
                                </div>
                              </div>
                              <button class="btn btn-warning comment-edit">Labot</button>
                            </div>
                        </div>
                        @if (isset($tread))
                        <div class="row justify-content-end tires-header">
                          <button class="btn btn-md btn-primary new_tire"><a class="text-white" href="{{ route('admin.auto.tires.create', $tread->tread_id) }}">Pievienot</a></button>
                        </div>
                        @endif
                        <div class="row">
                            <div class="col-sm-12">
                                <table class="table table-striped table-bordered datatable dataTable no-footer" id="DataTables_Table_0" role="grid" aria-describedby="DataTables_Table_0_info" style="border-collapse: collapse !important">
                                    <thead>
                                    <tr role="row">
                                        <th tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="3" aria-label="Date registered: activate to sort column ascending" style="width: 320.609px;">Izmērs</th>
                                        @if (isset($tread) && $tread->season == 2)
                                        <th rowspan="1" colspan="1" style="width: 151.953px;">Tips</th>
                                        @endif
                                        <th tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" aria-label="Role: activate to sort column ascending" style="width: 151.953px;">Veikala cena</th>
                                        <th tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" aria-label="Status: activate to sort column ascending" style="width: 167.547px;">Akcijas cena</th>
                                        <th tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" aria-label="Status: activate to sort column ascending" style="width: 167.547px;">Li</th>
                                        <th tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" aria-label="Status: activate to sort column ascending" style="width: 167.547px;">Si</th>
                                        <th tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" aria-label="Actions: activate to sort column ascending" style="width: 150.391px;">Kods</th>
                                        <th tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" aria-label="Actions: activate to sort column ascending" style="width: 67.547px;">Degvielas ekonomija</th>
                                        <th tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" aria-label="Actions: activate to sort column ascending" style="width: 67.547px;">Slapjšs segums</th>
                                        <th tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" aria-label="Actions: activate to sort column ascending" style="width: 67.547px;">Skaļums</th>
                                        <th tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" aria-label="Actions: activate to sort column ascending" style="width: 67.547px;">Piezīmes</th>
                                        <th tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" aria-label="Username: activate to sort column ascending" style="width: 372.5px;">Artikuls</th>
                                        <th tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" style="width: 322.391px;"></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @if (isset($tires))
                                        @foreach ($tires as $tire)
                                            <tr role="row" class="odd">
                                                <td>{{ $tire->d1 }}</td>
                                                <td>{{ $tire->d2 }}</td>
                                                <td>{{ $tire->d3 }}</td>
                                                <td>

                                                  @switch($tire->type)
                                                    @case(1)
                                                    <span data-toggle="tooltip">
                                                      <img src="{{asset('images/ms.png')}}" alt="ms"
                                                           title="<span>Centrāleiropas tipa ziemas riepa</span>" style="margin:0;">
                                                    </span>

                                                    @break

                                                    @case(2)
                                                    <span data-toggle="tooltip">
                                                      <img src="{{asset('images/radzeb.png')}}" alt="radzojama"
                                                           title="<span>Radžojama</span>" style="margin:0;">
                                                    </span>

                                                    @break

                                                    @case(3)
                                                    <span data-toggle="tooltip">
                                                      <img src="{{asset('images/radzea.png')}}" alt="ar radzem"
                                                           title="<span>Ar radzēm</span>" style="margin:0;">
                                                    </span>

                                                    @break

                                                    @case(4)
                                                    <span data-toggle="tooltip">
                                                      <img src="{{asset('images/parsla.png')}}" alt="skandinavijas"
                                                           title="<span>Skandināvijas tipa ziemas riepa</span>" style="margin:0;">
                                                    </span>
                                                    @break

                                                  @endswitch

                                                </td>
                                                <td>{{ $tire->price1 }}</td>
                                                <td style="color: red; font-weight: 500;">{{ $tire->price2 }}</td>
                                                <td>{{ $tire->li }}</td>
                                                <td>{{ $tire->si }}</td>
                                                <td>{{ $tire->code }}</td>
                                                <td>{{ $tire->eco }}</td>
                                                <td>{{ $tire->wet }}</td>
                                                <td>{{ $tire->noise }}</td>
                                                <td>{{ $tire->comment }}</td>
                                                <td>{{ $tire->article }}</td>
                                                <td>
                                                    <a class="btn btn-success" href="{{ route('admin.auto.tire.edit', $tire->tire_id) }}">
                                                        <svg class="c-icon">
                                                            <use xlink:href="/node_modules/@coreui/icons/sprites/free.svg#cil-description"></use>
                                                        </svg>
                                                    </a>
                                                    <a onclick="confirm('Tiešām vēlies dzēst?')" class="btn btn-danger" href="{{ route('admin.auto.tire.destroy', $tire->tire_id) }}">
                                                        <svg class="c-icon">
                                                            <use xlink:href="/node_modules/@coreui/icons/sprites/free.svg#cil-trash"></use>
                                                        </svg>
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
