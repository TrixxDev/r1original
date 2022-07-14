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
                                <div class="form-group row">
                                    <label class="col-md-2 col-form-label" for="brand_select">Brends: </label>
                                    <select name="brand" class="form-control col-md-3" data-model="auto" id="brand_select">
                                        @foreach ($brands as $brand)
                                            <option value="{{ $brand->brand_id }}" @if (isset($tread->brand_id) && $tread->brand_id == $brand->brand_id) {{ 'selected' }} @endif >{{ $brand->title }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-2 col-form-label" for="tread_select">Modelis: </label>
                                    <select name="tread" class="form-control col-md-3" id="tread_select"></select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 preview-image">
                                @if (isset($tread))
                                  @if ($tread->image !== false)
                                      <img style="width: 300px; height: 300px; margin-bottom: 10px" src="{{ $tread->image }}">
                                  @else
                                      <img style="width: 300px; height: 300px; margin-bottom: 10px" src="{{ asset('img/p/r1-logo.svg') }}">
                                  @endif

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
                                        <th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="3" aria-label="Date registered: activate to sort column ascending" style="width: 320.609px;">Izmērs</th>
                                        <th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" aria-label="Role: activate to sort column ascending" style="width: 151.953px;">Veikala cena</th>
                                        <th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" aria-label="Status: activate to sort column ascending" style="width: 167.547px;">Akcijas cena</th>
                                        <th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" aria-label="Status: activate to sort column ascending" style="width: 167.547px;">Li</th>
                                        <th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" aria-label="Status: activate to sort column ascending" style="width: 167.547px;">Si</th>
                                        <th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" aria-label="Actions: activate to sort column ascending" style="width: 150.391px;">Kods</th>
                                        <th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" aria-label="Actions: activate to sort column ascending" style="width: 67.547px;">Degvielas ekonomija</th>
                                        <th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" aria-label="Actions: activate to sort column ascending" style="width: 67.547px;">Slapjšs segums</th>
                                        <th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" aria-label="Actions: activate to sort column ascending" style="width: 67.547px;">Skaļums</th>
                                        <th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" aria-label="Actions: activate to sort column ascending" style="width: 67.547px;">Piezīmes</th>
                                        <th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" aria-label="Username: activate to sort column ascending" style="width: 372.5px;">Artikuls</th>
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
                                                <td>{{ $tire->price1 }}</td>
                                                <td>{{ $tire->price2 }}</td>
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
                                                    <a class="btn btn-danger" href="{{ route('admin.auto.tire.destroy', $tire->tire_id) }}">
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
