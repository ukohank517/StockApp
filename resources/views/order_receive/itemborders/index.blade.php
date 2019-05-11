@extends('order_receive.index')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading"><font size="7">ボーダー設定</font></div>
                <div>上限: about 1M</div>
                <div class="panel-body">
                    @if (Session::has('flash_message'))
                    <div class="alert alert-success">{{ Session::get('flash_message') }}</div>
                    @elseif (Session::has('e_flash_message'))
                    <div class="alert alert-danger">{{ Session::get('e_flash_message') }}</div>
                    @endif

                    {{-- エラーの表示 --}}
                    @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <div class="mb10">
                        {!! Form::model($itemborders, [
                            'url' => route('order_receive::itemborders.upload'),
                            'method' => 'POST',
                            'files' => true
                            ]) !!}
                            <div class="row">
                                <div class="col-md-4">
                                    {!! Form::file('csv_file', null, ['class' => 'form-control']) !!}
                                </div>
                                <div class="col-md-8">
                                    {!! Form::submit('更新', ['class' => 'btn btn-primary']) !!}
                                    {{ link_to_route('order_receive::itemborders.download', 'ダウンロード', null, ['class' => 'btn btn-default']) }}
                                </div>
                            </div>
                            {!! Form::close() !!}
                        </div>

                        <table class="table table-striped table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th width="15%">parent_sku</th>
                                    <th width="15%">yellow_border</th>
                                    <th width="15%">red_border</th>
                                    <th width="15%">行編集</th>
                                </tr>
                            </thead>
                            @foreach($itemborders as $item)
                            <tr>
                                <td>{{ $item->parent_sku }}</td>
                                <td>{{ $item->yellow_border }}</td>
                                <td>{{ $item->red_border }}</td>
                                <td>
                                    <form method="PUT" action="#" >
                                        <div class="">
                                            <button name="id" value="{{$item->id}}"  type="submit" class="btn btn-primary btn-join">編集</button>
                                        </div>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                            {{$itemborders->links()}}<!-- ページバーを表示する。 -->
                        </table>

                    </div>
                </div>



            </div>
        </div>
    </div>
    @endsection
