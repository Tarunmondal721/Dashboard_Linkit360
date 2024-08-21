@extends('layouts.admin')

@section('title')
    {{ $form->name.__("'s Response") }}
@endsection

@section('action-button')
    <div class="all-button-box row d-flex justify-content-end">
        <div class="col-xl-2 col-lg-2 col-md-4 col-sm-6 col-6">
            <a href="{{ route('form_builder.index') }}" class="btn btn-xs btn-white btn-icon-only width-auto">
                <i class="fas fa-arrow-left"></i> {{__('Back')}}
            </a>
        </div>
    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped mb-0">
                            @if($form->response->count() > 0)
                                <tbody>
                                @php
                                    $first = null;
                                    $second = null;
                                    $third = null;
                                    $i = 0;
                                @endphp
                                @foreach ($form->response as $response)
                                    @php
                                        $i++;
                                            $resp = json_decode($response->response,true);
                                            if(count($resp) == 1)
                                            {
                                                $resp[''] = '';
                                                $resp[' '] = '';
                                            }
                                            elseif(count($resp) == 2)
                                            {
                                                $resp[''] = '';
                                            }
                                            $firstThreeElements = array_slice($resp, 0, 3);

                                            $thead= array_keys($firstThreeElements);
                                            $head1 = ($first != $thead[0]) ? $thead[0] : '';
                                            $head2 = (!empty($thead[1]) && $second != $thead[1]) ? $thead[1] : '';
                                            $head3 = (!empty($thead[2]) && $third != $thead[2]) ? $thead[2] : '';
                                    @endphp
                                    @if(!empty($head1) || !empty($head2) || !empty($head3) && $head3 != ' ')
                                        <tr>
                                            <th>{{ $head1 }}</th>
                                            <th>{{ $head2 }}</th>
                                            <th>{{ $head3 }}</th>
                                            <th></th>
                                        </tr>
                                    @endif
                                    @php
                                        $first =  $thead[0];
                                        $second =  $thead[1];
                                        $third =  $thead[2];
                                    @endphp
                                    <tr>
                                        @foreach(array_values($firstThreeElements) as $ans)
                                            <td>{{$ans}}</td>
                                        @endforeach
                                        <td class="Action">
                                            <span>
                                                <a href="#" data-url="{{ route('response.detail',$response->id) }}" data-ajax-popup="true" data-size="lg" data-title="{{__('Response Detail')}}" class="edit-icon bg-info" data-toggle="tooltip" data-original-title="{{__('Response Detail')}}">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            @else
                                <tbody>
                                <tr>
                                    <td class="text-center">{{__('No data available in table')}}</td>
                                </tr>
                                </tbody>
                            @endif
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
