@extends('work.index')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Single Print</div>

                <div class="card-body">
                    @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach($errors->all() as $error)
                            <li> {{ $error }} </li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <div>
                        <form action="{{action('Work\PrintController@single_print')}}" method="get">
                            <button type="submit">印刷データの作成</button>
                            @if(Session::has('file_exist'))
                            <a href="single_invoice.pdf" target="_blank"><font size=3><u>[インボイス生成しました]</u></font></a>
                            @endif

                        </form>


                    </div>
                </br>

                <div>
                    <div class="alert alert-info">結果ゾーン</div>
                    <div>
                        <form action="{{action('Work\PrintController@addition')}}" method="get">
                            <p><input type="text" name="month" placeholder="月"> <input type="text" name="line" placeholder="行番号"><button type="submit">追加</button>
                            </form>
                        </div>

                        @if(Session::has('addition_fail'))
                        <div class="alert alert-warning">{{ Session::get('addition_fail') }}</div>
                        @endif
                        @if(Session::has('addition_success'))
                        <div class="alert alert-success">処理しました。[Line: {{ Session::get('addition_success') }}]</div>
                        @endif
                    </div>
                    <div>
                        <table class="table table-striped table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th width="10%">id</th>
                                    <th width="15%">日付</th>
                                    <th width="15%">行番号</th>
                                </tr>

                                @foreach($items as $item)
                                @if(($item->id_in_box)%2 == 0)
                                <tr bgcolor="pink">
                                    @else
                                    <tr bgcolor="lightskyblue">
                                        @endif
                                        <td>{{ $item->id_in_box }}</td>
                                        <td>{{ $item->date }}</td>
                                        <td>{{ $item->line }}</td>
                                    </tr>
                                    @endforeach
                                </thead>
                            </table>
                        </div>
                        <form action="{{action('Work\PrintController@addition_clear')}}" method="get">
                            <p><button type="submit">内容クリア</button></p>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endsection
