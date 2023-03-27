@extends('admin.layouts.app')

@section('content')

  <div class="container-fluid">
    <div class="fade-in">
      <div class="row">
        <div class="col-md-12">
          <div class="card">
            <div class="card-header">Notikuma apraksts</div>
            <div class="card-body">
              <div class="row">
                <div class="col-md-12">
                  <dl class="datalist">
                    <dt>Datums, laiks</dt>
                    <dd>{{ $audit->audit_time }}</dd>
                    <div class="c"></div>
                    <dt>Notikuma apraksts</dt>
                    <dd>{!! $audit->audit_event !!}</dd>
                    <div class="c"></div>
                    <dt>Apakšsistēma</dt>
                    <dd>{!! App\Models\Audit::get_facility_name($audit->audit_facility) !!}</dd>
                    <div class="c"></div>
                    <dt>Lietotājs</dt>
                    <dd>@if (App\Models\User::find($audit->audit_uid)) {!! App\Models\User::find($audit->audit_uid)->fullName . ' (' . $audit->audit_uid . ')' !!} @else {!! 'Nezināms (' . $audit->audit_uid . ')' !!} @endif</dd>
                    <div class="c"></div>
                    <dt>IP adrese</dt>
                    <dd>{!! $audit->audit_ip !!}</dd>
                    <div class="c"></div>
                    <dt>Pieprasījums:</dt>
                    <dd>{!! $audit->audit_url !!}</dd>
                    <div class="c"></div>
                    @if ($audit->audit_item)
                      <dt>Objekta ID</dt>
                      <dd>{!! $audit->audit_item !!}</dd>
                      <div class="c"></div>
                    @endif
                    @if ($audit->audit_subitem)
                      <dt>Apakšobjekta ID</dt>
                      <dd>{!! $audit->audit_subitem !!}</dd>
                      <div class="c"></div>
                    @endif
                    @if ($classname)
                      <dt>Objekta klase</dt>
                      <dd>{!! $classname !!}</dd>
                      <div class="c"></div>
                    @endif
                  </dl>
                  <div class="c"></div>
                  @if (($classname!='') && class_exists($classname))

                    <h2>Objekta izmaiņas: {{ $instance->_compare_get_classname().' ('.$instance_id.')' }}</h2>
                    @if ($instance!==false)
                      @php $changeList=$instance->compare($old_instance); //dd($changeList);@endphp

                      <dl class="datalist">
                        {{--                        {{ dd($changeList) }}--}}
                        @foreach ($changeList as $attribute_name => $attribute)
{{--                                                    {{ dd($changeList, $attribute) }}--}}
                          @if ($attribute!==false)
                            @php
                              if (is_array($attribute[0])) {
                                if (!empty($attribute[0])) {
                                  $newVal = array_values(array_filter($attribute[0][0]));
                                  $newVal = implode(', ', $newVal);
                                } else {
                                  $newVal = '';
                                }
                              } else {
                                $newVal = $attribute[0];
                              }
                              if (is_array($attribute[1])) {
                                if (!empty($attribute[1])) {
                                  $oldVal = array_values(array_filter($attribute[0][1]));
                                  $oldVal = implode(', ', $oldVal);
                                } else {
                                  $oldVal = '';
                                }
                              } else {
                                $oldVal = $attribute[1];
                              }

                            @endphp

                            @if ($newVal)
                              @php
                                $delta = $out = strlen($oldVal) > 100 ? substr($oldVal,0,100)."..." : $oldVal;
                                $delta .= ' <span class="red bold">-></span> '.$newVal;
                                $class = 'audit-changed';
                              @endphp
                            @else
                              @php
                                $delta = $oldVal;
                                $class = 'audit';
                              @endphp
                            @endif
                            <dt><span class="{{ $class }}">{!! trim($attribute_name) !!}</span></dt>
                            <dd>{!! $delta !!}&nbsp;</dd>
                            <div class="c"></div>
                          @endif
                        @endforeach
                      </dl>
                      <div class="c"></div>
                    @endif
                  @endif

                  @php
                    $backtraceData = $audit->audit_backtrace;
                    $backtrace = @unserialize($backtraceData);
                  @endphp

                  <table class="table table-bordered table-striped" style="width:750px">
                    <th>Fails</th>
                    <th class="last">Izsaukums</th>

                    @php $documentRoot = str_replace('\\','/',$_SERVER['DOCUMENT_ROOT']); @endphp

                    @foreach ($backtrace as $calls)
                      @php
                        $file = @str_replace('\\','/',$calls['file']);
                        $file = str_ireplace($documentRoot,'',$file);

                        if ($file){
                          $line = ' (line <b>'.@$calls['line'].'</b>)';
                        } else {
                          $line = '';
                        }

                        $function = $calls['function'];

                        //$args = implode(',',$calls['args']);
                        $args = '';
                        if (isset($calls['args'])){
                          foreach ($calls['args'] as $arg){
                            if (is_object($arg)){
                              $string = get_class($arg);
                            } else {
                              $string = (string)$arg;
                            }

                          $args.='\''.trim($string,100).'\', ';
                          }
                        }
                        $args = trim($args,', ');

                        $class = @$calls['class'];
                      @endphp
                      <tr>
                        <td>{!! $file.$line !!}</td>
                        <td class="last">{!! (($class)?$class.$calls['type']:'').$function.'('.$args.')' !!}</td>
                      </tr>
                    @endforeach

                  </table>
                </div>
              </div>
            </div>
          </div>

        </div>
      </div>
    </div>
  </div>

@endsection

