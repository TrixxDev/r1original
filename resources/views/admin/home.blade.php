@extends('admin.layouts.app')

@section('content')

    <div class="container-fluid">
        <div class="fade-in">
            <div class="row">
                <div class="col-sm-6 col-lg-3">
                    <div class="card text-white bg-primary">
                        <div class="card-body card-body pb-0 d-flex justify-content-between align-items-start">
                            <div>
                                <div class="text-value-lg">9.823</div>
                                <div>Members online</div>
                            </div>
                            <div class="btn-group">
                                <button class="btn btn-transparent dropdown-toggle p-0" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <svg class="c-icon">
                                        <use xlink:href="node_modules/@coreui/icons/sprites/free.svg#cil-settings"></use>
                                    </svg>
                                </button>
                                <div class="dropdown-menu dropdown-menu-right"><a class="dropdown-item" href="#">Action</a><a class="dropdown-item" href="#">Another action</a><a class="dropdown-item" href="#">Something else here</a></div>
                            </div>
                        </div>
                        <div class="c-chart-wrapper mt-3 mx-3" style="height:70px;">
                            <canvas class="chart" id="card-chart1" height="70"></canvas>
                        </div>
                    </div>
                </div>
                <!-- /.col-->
                <div class="col-sm-6 col-lg-3">
                    <div class="card text-white bg-info">
                        <div class="card-body card-body pb-0 d-flex justify-content-between align-items-start">
                            <div>
                                <div class="text-value-lg">9.823</div>
                                <div>Members online</div>
                            </div>
                            <div class="btn-group">
                                <button class="btn btn-transparent dropdown-toggle p-0" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <svg class="c-icon">
                                        <use xlink:href="node_modules/@coreui/icons/sprites/free.svg#cil-settings"></use>
                                    </svg>
                                </button>
                                <div class="dropdown-menu dropdown-menu-right"><a class="dropdown-item" href="#">Action</a><a class="dropdown-item" href="#">Another action</a><a class="dropdown-item" href="#">Something else here</a></div>
                            </div>
                        </div>
                        <div class="c-chart-wrapper mt-3 mx-3" style="height:70px;">
                            <canvas class="chart" id="card-chart2" height="70"></canvas>
                        </div>
                    </div>
                </div>
                <!-- /.col-->
                <div class="col-sm-6 col-lg-3">
                    <div class="card text-white bg-warning">
                        <div class="card-body card-body pb-0 d-flex justify-content-between align-items-start">
                            <div>
                                <div class="text-value-lg">9.823</div>
                                <div>Members online</div>
                            </div>
                            <div class="btn-group">
                                <button class="btn btn-transparent dropdown-toggle p-0" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <svg class="c-icon">
                                        <use xlink:href="node_modules/@coreui/icons/sprites/free.svg#cil-settings"></use>
                                    </svg>
                                </button>
                                <div class="dropdown-menu dropdown-menu-right"><a class="dropdown-item" href="#">Action</a><a class="dropdown-item" href="#">Another action</a><a class="dropdown-item" href="#">Something else here</a></div>
                            </div>
                        </div>
                        <div class="c-chart-wrapper mt-3" style="height:70px;">
                            <canvas class="chart" id="card-chart3" height="70"></canvas>
                        </div>
                    </div>
                </div>
                <!-- /.col-->
                <div class="col-sm-6 col-lg-3">
                    <div class="card text-white bg-danger">
                        <div class="card-body card-body pb-0 d-flex justify-content-between align-items-start">
                            <div>
                                <div class="text-value-lg">9.823</div>
                                <div>Members online</div>
                            </div>
                            <div class="btn-group">
                                <button class="btn btn-transparent dropdown-toggle p-0" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <svg class="c-icon">
                                        <use xlink:href="node_modules/@coreui/icons/sprites/free.svg#cil-settings"></use>
                                    </svg>
                                </button>
                                <div class="dropdown-menu dropdown-menu-right"><a class="dropdown-item" href="#">Action</a><a class="dropdown-item" href="#">Another action</a><a class="dropdown-item" href="#">Something else here</a></div>
                            </div>
                        </div>
                        <div class="c-chart-wrapper mt-3 mx-3" style="height:70px;">
                            <canvas class="chart" id="card-chart4" height="70"></canvas>
                        </div>
                    </div>
                </div>
                <!-- /.col-->
            </div>
            <!-- /.row-->
            <div class="row">
                <div class="col-sm-6 col-lg-4">
                    <div class="card">
                        <div class="card-header bg-facebook content-center">
                            <svg class="c-icon c-icon-3xl text-white my-4">
                                <use xlink:href="node_modules/@coreui/icons/sprites/brand.svg#cib-facebook-f"></use>
                            </svg>
                        </div>
                        <div class="card-body row text-center">
                            <div class="col">
                                <div class="text-value-xl">89k</div>
                                <div class="text-uppercase text-muted small">friends</div>
                            </div>
                            <div class="c-vr"></div>
                            <div class="col">
                                <div class="text-value-xl">459</div>
                                <div class="text-uppercase text-muted small">feeds</div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /.col-->
                <div class="col-sm-6 col-lg-4">
                    <div class="card">
                        <div class="card-header bg-twitter content-center">
                            <svg class="c-icon c-icon-3xl text-white my-4">
                                <use xlink:href="node_modules/@coreui/icons/sprites/brand.svg#cib-twitter"></use>
                            </svg>
                        </div>
                        <div class="card-body row text-center">
                            <div class="col">
                                <div class="text-value-xl">973k</div>
                                <div class="text-uppercase text-muted small">followers</div>
                            </div>
                            <div class="c-vr"></div>
                            <div class="col">
                                <div class="text-value-xl">1.792</div>
                                <div class="text-uppercase text-muted small">tweets</div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /.col-->
                <div class="col-sm-6 col-lg-4">
                    <div class="card">
                        <div class="card-header bg-linkedin content-center">
                            <svg class="c-icon c-icon-3xl text-white my-4">
                                <use xlink:href="node_modules/@coreui/icons/sprites/brand.svg#cib-linkedin"></use>
                            </svg>
                        </div>
                        <div class="card-body row text-center">
                            <div class="col">
                                <div class="text-value-xl">500+</div>
                                <div class="text-uppercase text-muted small">contacts</div>
                            </div>
                            <div class="c-vr"></div>
                            <div class="col">
                                <div class="text-value-xl">292</div>
                                <div class="text-uppercase text-muted small">feeds</div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /.col-->
            </div>
            <!-- /.row-->
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">Traffic & Sales</div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="c-callout c-callout-info"><small class="text-muted">New Clients</small>
                                                <div class="text-value-lg">9,123</div>
                                            </div>
                                        </div>
                                        <!-- /.col-->
                                        <div class="col-6">
                                            <div class="c-callout c-callout-danger"><small class="text-muted">Recuring Clients</small>
                                                <div class="text-value-lg">22,643</div>
                                            </div>
                                        </div>
                                        <!-- /.col-->
                                    </div>
                                    <!-- /.row-->
                                    <hr class="mt-0">
                                    <div class="progress-group mb-4">
                                        <div class="progress-group-prepend"><span class="progress-group-text">Monday</span></div>
                                        <div class="progress-group-bars">
                                            <div class="progress progress-xs">
                                                <div class="progress-bar bg-info" role="progressbar" style="width: 34%" aria-valuenow="34" aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>
                                            <div class="progress progress-xs">
                                                <div class="progress-bar bg-danger" role="progressbar" style="width: 78%" aria-valuenow="78" aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="progress-group mb-4">
                                        <div class="progress-group-prepend"><span class="progress-group-text">Tuesday</span></div>
                                        <div class="progress-group-bars">
                                            <div class="progress progress-xs">
                                                <div class="progress-bar bg-info" role="progressbar" style="width: 56%" aria-valuenow="56" aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>
                                            <div class="progress progress-xs">
                                                <div class="progress-bar bg-danger" role="progressbar" style="width: 94%" aria-valuenow="94" aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="progress-group mb-4">
                                        <div class="progress-group-prepend"><span class="progress-group-text">Wednesday</span></div>
                                        <div class="progress-group-bars">
                                            <div class="progress progress-xs">
                                                <div class="progress-bar bg-info" role="progressbar" style="width: 12%" aria-valuenow="12" aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>
                                            <div class="progress progress-xs">
                                                <div class="progress-bar bg-danger" role="progressbar" style="width: 67%" aria-valuenow="67" aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="progress-group mb-4">
                                        <div class="progress-group-prepend"><span class="progress-group-text">Thursday</span></div>
                                        <div class="progress-group-bars">
                                            <div class="progress progress-xs">
                                                <div class="progress-bar bg-info" role="progressbar" style="width: 43%" aria-valuenow="43" aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>
                                            <div class="progress progress-xs">
                                                <div class="progress-bar bg-danger" role="progressbar" style="width: 91%" aria-valuenow="91" aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="progress-group mb-4">
                                        <div class="progress-group-prepend"><span class="progress-group-text">Friday</span></div>
                                        <div class="progress-group-bars">
                                            <div class="progress progress-xs">
                                                <div class="progress-bar bg-info" role="progressbar" style="width: 22%" aria-valuenow="22" aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>
                                            <div class="progress progress-xs">
                                                <div class="progress-bar bg-danger" role="progressbar" style="width: 73%" aria-valuenow="73" aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="progress-group mb-4">
                                        <div class="progress-group-prepend"><span class="progress-group-text">Saturday</span></div>
                                        <div class="progress-group-bars">
                                            <div class="progress progress-xs">
                                                <div class="progress-bar bg-info" role="progressbar" style="width: 53%" aria-valuenow="53" aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>
                                            <div class="progress progress-xs">
                                                <div class="progress-bar bg-danger" role="progressbar" style="width: 82%" aria-valuenow="82" aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="progress-group mb-4">
                                        <div class="progress-group-prepend"><span class="progress-group-text">Sunday</span></div>
                                        <div class="progress-group-bars">
                                            <div class="progress progress-xs">
                                                <div class="progress-bar bg-info" role="progressbar" style="width: 9%" aria-valuenow="9" aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>
                                            <div class="progress progress-xs">
                                                <div class="progress-bar bg-danger" role="progressbar" style="width: 69%" aria-valuenow="69" aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- /.col-->
                                <div class="col-sm-6">
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="c-callout c-callout-warning"><small class="text-muted">Pageviews</small>
                                                <div class="text-value-lg">78,623</div>
                                            </div>
                                        </div>
                                        <!-- /.col-->
                                        <div class="col-6">
                                            <div class="c-callout c-callout-success"><small class="text-muted">Organic</small>
                                                <div class="text-value-lg">49,123</div>
                                            </div>
                                        </div>
                                        <!-- /.col-->
                                    </div>
                                    <!-- /.row-->
                                    <hr class="mt-0">
                                    <div class="progress-group">
                                        <div class="progress-group-header">
                                            <svg class="c-icon progress-group-icon">
                                                <use xlink:href="node_modules/@coreui/icons/sprites/free.svg#cil-user"></use>
                                            </svg>
                                            <div>Male</div>
                                            <div class="mfs-auto font-weight-bold">43%</div>
                                        </div>
                                        <div class="progress-group-bars">
                                            <div class="progress progress-xs">
                                                <div class="progress-bar bg-warning" role="progressbar" style="width: 43%" aria-valuenow="43" aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="progress-group mb-5">
                                        <div class="progress-group-header">
                                            <svg class="c-icon progress-group-icon">
                                                <use xlink:href="node_modules/@coreui/icons/sprites/free.svg#cil-user-female"></use>
                                            </svg>
                                            <div>Female</div>
                                            <div class="mfs-auto font-weight-bold">37%</div>
                                        </div>
                                        <div class="progress-group-bars">
                                            <div class="progress progress-xs">
                                                <div class="progress-bar bg-warning" role="progressbar" style="width: 43%" aria-valuenow="43" aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="progress-group">
                                        <div class="progress-group-header align-items-end">
                                            <svg class="c-icon progress-group-icon">
                                                <use xlink:href="node_modules/@coreui/icons/sprites/brand.svg#cib-google"></use>
                                            </svg>
                                            <div>Organic Search</div>
                                            <div class="mfs-auto font-weight-bold mfe-2">191.235</div>
                                            <div class="text-muted small">(56%)</div>
                                        </div>
                                        <div class="progress-group-bars">
                                            <div class="progress progress-xs">
                                                <div class="progress-bar bg-success" role="progressbar" style="width: 56%" aria-valuenow="56" aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="progress-group">
                                        <div class="progress-group-header align-items-end">
                                            <svg class="c-icon progress-group-icon">
                                                <use xlink:href="node_modules/@coreui/icons/sprites/brand.svg#cib-facebook-f"></use>
                                            </svg>
                                            <div>Facebook</div>
                                            <div class="mfs-auto font-weight-bold mfe-2">51.223</div>
                                            <div class="text-muted small">(15%)</div>
                                        </div>
                                        <div class="progress-group-bars">
                                            <div class="progress progress-xs">
                                                <div class="progress-bar bg-success" role="progressbar" style="width: 15%" aria-valuenow="15" aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="progress-group">
                                        <div class="progress-group-header align-items-end">
                                            <svg class="c-icon progress-group-icon">
                                                <use xlink:href="node_modules/@coreui/icons/sprites/brand.svg#cib-twitter"></use>
                                            </svg>
                                            <div>Twitter</div>
                                            <div class="mfs-auto font-weight-bold mfe-2">37.564</div>
                                            <div class="text-muted small">(11%)</div>
                                        </div>
                                        <div class="progress-group-bars">
                                            <div class="progress progress-xs">
                                                <div class="progress-bar bg-success" role="progressbar" style="width: 11%" aria-valuenow="11" aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="progress-group">
                                        <div class="progress-group-header align-items-end">
                                            <svg class="c-icon progress-group-icon">
                                                <use xlink:href="node_modules/@coreui/icons/sprites/brand.svg#cib-linkedin"></use>
                                            </svg>
                                            <div>LinkedIn</div>
                                            <div class="mfs-auto font-weight-bold mfe-2">27.319</div>
                                            <div class="text-muted small">(8%)</div>
                                        </div>
                                        <div class="progress-group-bars">
                                            <div class="progress progress-xs">
                                                <div class="progress-bar bg-success" role="progressbar" style="width: 8%" aria-valuenow="8" aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- /.col-->
                            </div>
                            <!-- /.row--><br>
                        </div>
                    </div>
                </div>
                <!-- /.col-->
            </div>
            <!-- /.row-->
        </div>
    </div>

@endsection
