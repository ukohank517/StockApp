@extends('admin.index')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading"><font size="7">地代コード管理ページ</font></div>
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
                        {!! Form::model($zonecodes, [
                            'url' => route('admin::zonecodes.upload'),
                            'method' => 'POST',
                            'files' => true
                            ]) !!}
                            <div class="row">
                                <div class="col-md-4">
                                    {!! Form::file('csv_file', null, ['class' => 'form-control']) !!}
                                </div>
                                <div class="col-md-8">
                                    {!! Form::submit('DB更新', ['class' => 'btn btn-primary']) !!}
                                    {{ link_to_route('admin::zonecodes.download', 'ダウンロード', null, ['class' => 'btn btn-default']) }}
                                </div>
                            </div>
                            {!! Form::close() !!}
                        </div>

                        <table class="table table-striped table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th width="10%">ID</th>
                                    <th width="15%">国名</th>
                                    <th width="30%">国コード</th>
                                    <th>地帯属性</th>
                                </tr>
                            </thead>
                            @foreach($zonecodes as $item)
                            <tr>
                                <td>{{ $item->id }}</td>
                                <td>{{ $item->country }}</td>
                                <td>{{ $item->code }}</td>
                                <td>{{ $item->no }}</td>
                            </tr>
                            @endforeach
                            {{$zonecodes->links()}}<!-- ページバーを表示する。 -->
                        </table>


                        <!--  デバッグ用
                        <div> <font size="7">ページ状況(memo): </font></div>
                        <div>現在のページに表示されている件数: {{ $zonecodes->count() }}</div>
                        <div>現在のページ数: {{ $zonecodes->currentPage() }}</div>
                        <div>現在のページの最初の要素: {{ $zonecodes->firstItem() }}</div>
                        <div>次のページがあるかどうか: {{ $zonecodes->hasMorePages() }}</div>
                        <div>現在のページの最後の要素: {{ $zonecodes->lastItem() }}</div>
                        <div>最後のページ数: {{ $zonecodes->lastPage() }}</div>
                        <div>次のページのURL: {{ $zonecodes->nextPageUrl() }}</div>
                        <div>1ページに表示する件数: {{ $zonecodes->perPage() }}</div>
                        <div>前のページのURL: {{ $zonecodes->previousPageUrl() }}</div>
                        <div>合計件数: {{ $zonecodes->total() }}</div>
                        <div>指定ページのURL: {{ $zonecodes->url(4) }}</div>
                    -->
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
