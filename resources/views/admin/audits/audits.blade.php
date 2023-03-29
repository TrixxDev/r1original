@extends('admin.layouts.app')

@section('content')

  <div class="container-fluid">
    <div class="fade-in">
      <div class="row">
        <div class="col-md-12">
          <div class="card">
            <div class="card-header">Notikumu žurnāls</div>
            <div class="card-body">
              <div class="row">
                <div class="col-12">
                  <form method="get">
                    <div class="form-row">
                      <div class="col-2">
                        <select name="model" class="form-control">
                          <option value="none" selected>Izvēlēties</option>
                            @foreach ($models as $model_name => $model)
                            <optgroup label="{{ $model['title'] }}">
                              @foreach ($model['searchBy'] as $model_col => $model_title)
                                <option @if ($modelname == $model_name . ';' . $model_col) selected @endif value="{{ $model_name }};{{ $model_col }}">{{ ucfirst($model_title) }}</option>
                              @endforeach
                            </optgroup>
                            @endforeach
                        </select>
                      </div>
                      <div class="col-3">
                        <input type="text" name="params" class="form-control" placeholder="Parametri" @if ($param) value="{{ $param }}" @endif>
                      </div>
                      <div class="col">
                        <button class="btn btn-success">Meklēt</button>
                        @if (!empty($modelname))
                          <a href="{{ route('admin.audits') }}" class="btn btn-warning">Nodzēst filtru</a>
                        @endif
                      </div>
                    </div>
                  </form>
                </div>
              </div>
              <br>
              <div class="row">
                <div class="col-md-12">
                  <table class="table table-striped table-bordered table-hover">
                    <thead>
                    <tr>
                      <th width="50">&nbsp;</th>
                      <th width="">Laiks</th>
                      <th width="">Apraksts</th>
                      <th width="">Apakšsistēma</th>
                      <th width="">Lietotājs</th>
                    </tr>
                    </thead>
                    <tbody>
                      @foreach ($audits as $audit)
                      <tr style="cursor: pointer;" onclick="window.location.href='{{ route('admin.audit', $audit->id) }}'">
                        <td></td>
                        <td>{{ $audit['audit_time'] }}</td>
                        <td>{{ trim($audit['audit_event']) }}</td>
                        <td align="center">{{ \App\Models\Audit::get_facility_name($audit['audit_facility']) }}</td>
                        <td align="center">
                          @if (App\Models\User::find($audit->audit_uid))
                            {{ App\Models\User::find($audit->audit_uid)->fullName . ' (' . $audit->audit_uid . ')' }}
                          @else
                            {{ 'Nezināms (' . $audit->audit_uid . ')' }}
                          @endif
                        </td>
                      </tr>
                      @endforeach
                    </tbody>
                  </table>
                  {{ $audits->links() }}
                </div>
              </div>
            </div>
          </div>

        </div>
      </div>
    </div>
  </div>

@endsection

