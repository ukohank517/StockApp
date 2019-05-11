@extends('admin.index')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading"><font size="7">業務DB管理ページ</font></div>
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
                        {!! Form::model($ordersheets, [
                            'url' => route('admin::ordersheets.upload'),
                            'method' => 'POST',
                            'files' => true
                            ]) !!}
                            <div class="row">
                                <div class="col-md-4">
                                    {!! Form::file('csv_file', null, ['class' => 'form-control']) !!}
                                </div>
                                <div class="col-md-8">
                                    {!! Form::submit('下に追加', ['class' => 'btn btn-primary']) !!}
                                    {{ link_to_route('admin::ordersheets.download', 'ダウンロード', null, ['class' => 'btn btn-default']) }}
                                </div>
                            </div>
                            {!! Form::close() !!}
                        </div>

                        <table class="table table-striped table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th width="15%">id</th>
                                    <th width="15%">date</th>
                                    <th width="15%">box</th>
                                    <th width="15%">sku</th>
                                    <th width="15%">line</th>
                                    <th width="15%">名前</th>
                                </tr>
                            </thead>
                            @foreach($ordersheets as $item)
                            <tr>
                                <td>{{ $item->id }}</td>
                                <td>{{ $item->date }}</td>
                                <td>{{ $item->box }}</td>
                                <td>{{ $item->sku }}</td>
                                <td>{{ $item->line }}</td>
                                <td>{{ $item->customer_name }}</td>
                            </tr>
                            @endforeach
                            {{$ordersheets->links()}}<!-- ページバーを表示する。 -->
                        </table>

                    </div>
                </div>

                <div class="alert alert-danger">デンジャラスゾーン</div>

                <form action="{{action('Admin\OrdersheetsController@deletelines')}}" method="get">
                    <div> <input type="text" name="fromidx" placeholder="id:from"> </div>
                    <div> <input type="text" name="tillidx" placeholder="id:till"> </div>
                    <div class="button"> <button type="submit"> 指定id削除 </button></div>
                </form>

            </div>
        </div>
    </div>
    @endsection
