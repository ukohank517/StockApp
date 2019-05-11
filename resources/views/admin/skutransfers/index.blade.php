@extends('admin.index')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading"><font size="7">SKUTRANSFER管理ページ</font></div>
                <div>上限: about 1M</div>
                <div class="panel-body">
                    @if (Session::has('flash_message'))
                    <div class="alert alert-success">{{ Session::get('flash_message') }}</div>
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
                        {!! Form::model($skutransfers, [
                            'url' => route('admin::skutransfers.upload'),
                            'method' => 'POST',
                            'files' => true
                            ]) !!}
                            <div class="row">
                                <div class="col-md-4">
                                    {!! Form::file('csv_file', null, ['class' => 'form-control']) !!}
                                </div>
                                <div class="col-md-8">
                                    {!! Form::submit('DB更新', ['class' => 'btn btn-primary']) !!}
                                    {{ link_to_route('admin::skutransfers.download', 'ダウンロード', null, ['class' => 'btn btn-default']) }}
                                </div>
                            </div>
                            {!! Form::close() !!}
                        </div>

                        <table class="table table-striped table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th width="10%">ID</th>
                                    <th width="15%">before</th>
                                    <th width="30%">after</th>
                                </tr>
                            </thead>
                            @foreach($skutransfers as $item)
                            <tr>
                                <td>{{ $item->id }}</td>
                                <td>{{ $item->before }}</td>
                                <td>{{ $item->after }}</td>
                            </tr>
                            @endforeach
                            {{$skutransfers->links()}}<!-- ページバーを表示する。 -->
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @endsection
