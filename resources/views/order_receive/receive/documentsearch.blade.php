@extends('order_receive.index')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading"><font size="7">受注処理</font></div>
                {{--エラーの表示--}}
                @if($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <form action="{{action('OrderReceive\ReceiveController@research')}}" >
                    <input type="text" name="search_sku" placeholder="検索SKU" required>
                    <input type="text" name="search_num" placeholder="個数" >

                    @if(count($docids) != 0)
                    @foreach($docids as $docid)
                    <input type="hidden" name="search_doc[]" value="{{$docid}}" >
                    @endforeach
                    @endif

                    <input type="submit" value="追加検索">
                </form>

                <div>
                    <table class="table table-striped table-bordered table-hover">
                        {{$orderdocuments->links()}}
                        <thread>
                            <tr>
                                <th>注文番号</th>
                                <th>注文日付</th>
                                <th>仕入れ先</th>
                                <th>処理</th>
                            </tr>
                        </thread>

                        @foreach($orderdocuments as $item)
                        <tr>
                            <td>{{ $item->doc_id }}</td>
                            <td>{{ $item->order_date }}</td>
                            <td>{{ $item->supplier }}</td>
                            <td>


                                <form method="PUT" action="{{ route('order_receive::receive.detail') }}">
                                    @if(count($docids) != 0)
                                    @foreach($docids as $docid)
                                    <input type="hidden" name="search_doc[]" value="{{$docid}}" >
                                    @endforeach
                                    @endif

                                    <button name="doc_id" value="{{$item->doc_id}}" type="submit" class="btn btn-default btn-join">詳細</button>

                                </form>

                            </td>
                        </tr>
                        @endforeach

                    </table>
                    @if(count($docids) == 0)
                    <p>見つからなかった</p>
                    <a href="{{ route('order_receive::receive.index') }}">最初から検索し直す。</a>
                    @endif
                </div>

                @if(Session::has('detail_flag'))
                <div class="alert alert-success">
                    発注書ID: [{{Session::get('detail_flag')}}]
                    <form style="float:right;" method="PUT" action="{{route('order_receive::receive.receive')}}">
                            <button name="doc_id" value="{{Session::get('detail_flag')}}" type="submit" class="btn btn-primary btn-join">入荷確認</button>
                    </form>

                </div>
                <table class="table table-striped table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>親sku</th>
                            <th>注文数</th>
                            <th>仕入れ先</th>
                            <th>仕入れ値段</th>
                        </tr>
                    </thead>
                    @foreach($detailitems as $item)
                    <tr>
                        <td>{{$item->parent_sku}}</td>
                        <td>{{$item->parent_num}}</td>
                        <td>{{$item->supplier}}</td>
                        <td>{{$item->price}}</td>
                    </tr>
                    @endforeach
                </table>
                @endif

                <div>
                    <p>memo:</p>
                        仕入れ先と仕入れ値段ってどうするんだっけ？
                </div>
            </div>
        </div>
    </div>
    @endsection
<!--
参考URL:
http://corder.link/jquery-dynamic-row-add-del/
みてみてね！
-->
