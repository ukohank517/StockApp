@extends('work.index')

@section('content')

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">workspace</div>

                <div class="card-body">
                    @if (session('status'))
                    <div class="alert alert-success" role="alert">
                        {{ session('status') }}
                    </div>
                    @endif

                    @if(Session::has('full_flag'))
                    <form action="{{action('Work\WorkController@renew_box')}}" method="GET">
                        <p><input type="submit" value="新ボックスへ"></p>
                    </form>
                    <a href="print/print?box_name={{$box_name}}&page[]=greenlabel&page[]=invoice" target="_blank"><font size="6"><u>[印刷可能になりました。]</u></font></a>
                    @else
                    <form action="{{action('Work\SearchResultController@index')}}" name="フォームの名前" method="get">
                        <p><input type="text" size="50" placeholder="SKU" name="sku_token"></p>
                        <p><input type="submit" value="検索"></p>
                    </form>
                    @endif
                    <div>
                        <div class = "alert  alert-info">ボックス詳細[ボックス名:{{$box_name}}]</div>


                        <form action="{{action('Work\WorkController@delete_last_line')}}" name="最終行削除" method="GET" style="text-align:right;">
                            <p><input type="submit" value="最終行削除"></p>
                        </form>

                        @if (Session::has('flash_message'))
                        <div class="alert alert-success">{{ Session::get('flash_message') }}</div>
                        @endif


                        <table class="table table-striped table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th width="10%">No.</th>
                                    <th width="10%">行</th>
                                    <th width="15%">sku</th>
                                    <th width="10%">個数</th>
                                    <th width="15%">注文番号</th>
                                    <th width="15%">発送方法</th>
                                </tr>

                                @foreach($dealing_items as $item)
                                @if(($item->id_in_box)%2 == 0)
                                <tr bgcolor="pink">
                                    @else
                                    <tr bgcolor="lightskyblue">
                                        @endif
                                        <td>{{ $item->id_in_box }}</td>
                                        <td>{{ $item->line }}</td>
                                        <td>{{ $item->sku }}</td>
                                        <td>{{ $item->aim_num}}</td>
                                        <td>{{ $item->order_id }}</td>
                                        <td>{{ $item->sendway }}</td>
                                    </tr>
                                    @endforeach
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>



    </div>

    @endsection
