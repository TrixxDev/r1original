@extends('admin.layouts.app')

@section('content')

  <div class="container-fluid">
    <div class="fade-in">

      <div class="row">
        <div class="col-md-12">
          <div class="card">
            <div class="card-header">Logi</div>
            <div class="card-body logs"></div>
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-md-12">
          <div class="card">
            <div class="card-header font-weight-bold">Sinhronizācijas - Auto</div>
              <div class="card-body">
                <div class="row">
                  <div class="col-md-3">
                    <div class="card bg-light">
                      <div class="card-header text-center font-weight-bold">Lattako</div>
                      <button class="card-body btn" id="i3-auto">Sinhronizēt</button>
                    </div>
                  </div>
                  <div class="col-md-3">
                    <div class="card bg-light">
                      <div class="card-header text-center font-weight-bold">GoodYear</div>
                      <button class="card-body btn" id="gy-auto">Sinhronizēt</button>
                    </div>
                  </div>
                  <div class="col-md-3">
                    <div class="card bg-light">
                      <div class="card-header text-center font-weight-bold">Riepu Zona</div>
                      <button class="card-body btn" id="rz-auto">Sinhronizēt</button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

      <div class="row">
        <div class="col-md-12">
          <div class="card">
            <div class="card-header font-weight-bold">Sinhronizācijas - Moto</div>
            <div class="card-body">
              <div class="row">
                <div class="col-md-3">
                  <div class="card bg-light">
                    <div class="card-header text-center font-weight-bold">Lattako</div>
                    <button class="card-body btn" id="i3-moto">Sinhronizēt</button>
                  </div>
                </div>

                <div class="col-md-3">
                  <div class="card bg-light">
                    <div class="card-header text-center font-weight-bold">Duell</div>
                    <button class="card-body btn" id="duell-moto">Sinhronizēt</button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-md-12">
          <div class="card">
            <div class="card-header font-weight-bold">Sinhronizācijas - Kvadraciklu</div>
            <div class="card-body">
              <div class="row">
                <div class="col-md-3">
                  <div class="card bg-light">
                    <div class="card-header text-center font-weight-bold">Lattako</div>
                    <button class="card-body btn" id="i3-quadr">Sinhronizēt</button>
                  </div>
                </div>

                <div class="col-md-3">
                  <div class="card bg-light">
                    <div class="card-header text-center font-weight-bold">Duell</div>
                    <button class="card-body btn" id="duell-quadr">Sinhronizēt</button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-md-12">
          <div class="card">
            <div class="card-header font-weight-bold">Sinhronizācijas - Lielās riepas</div>
            <div class="card-body">
              <div class="row">
                <div class="col-md-3">
                  <div class="card bg-light">
                    <div class="card-header text-center font-weight-bold">Lattako</div>
                    <button class="card-body btn" id="i3-big">Sinhronizēt</button>
                  </div>
                </div>

                <div class="col-md-3">
                  <div class="card bg-light">
                    <div class="card-header text-center font-weight-bold">Bohnenkamp</div>
                    <button class="card-body btn" id="starco-big">Sinhronizēt</button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>

@endsection
