@extends('work.index')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Print Page</div>

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
                        <form action="{{action('Work\PrintController@print')}}" method="get">
                            <p><input type="text" name="box_name" placeholder="ボックス名"></p>
                            <p><input type="checkbox" name="page[]" value="greenlabel" checked="checked"> グリーンラベル</p>
                            <p><input type="checkbox" name="page[]" value="invoice" checked="checked"> インボイス</p>
                            <button type="submit">作成</button>
                        </form>
                    </div>
                    <div>
                        <div class="alert alert-info">結果ゾーン</div>
                        @if (Session::has('greenlabel_flag'))
                        <p><a href="../pic/greenlabel/{{$box_name}}.pdf" target="_blank"> greenlabel[box: {{$box_name}}] </a></p>
                        @endif
                        @if (Session::has('invoice_flag'))
                        <p><a href="../pic/invoice/{{$box_name}}.pdf" target=("_blank")> invoice[box: {{$box_name}}] </a></p>
                        @endif
                        @if (Session::has('greenlabel_flag') || Session::has('invoice_flag'))
                        <div>
                            <div class="alert alert-info">中身一覧[box: {{$box_name}}]</div>
                            <table class="table table-striped table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th width="10%">No.</th>
                                        <th width="10%">Line</th>
                                        <th width="15%">sku</th>
                                    </tr>
                                    @foreach($items as $item)
                                    @if(($item->id_in_box) %2 == 0)
                                    <tr bgcolor="pink">
                                        @else
                                        <tr bgcolor="lightskyblue">
                                            @endif
                                            <td>{{ $item->id_in_box }}</td>
                                            <td>{{ $item->line }}</td>
                                            <td>{{ $item->sku }}</td>
                                        </tr>
                                        @endforeach
                                    </thead>
                                </table>
                            </div>
                            @endif

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endsection
