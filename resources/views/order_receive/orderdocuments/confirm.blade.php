@extends('order_receive.index')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading"><font size="7">発注書確認</font></div>
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
                        <div class="row">
                            <div class="col-md-8">
                                {{ link_to_route('order_receive::orderdocuments.download', 'ダウンロード', null, ['class' => 'btn btn-primary']) }}
                            </div>
                        </div>


                        <table class="table table-striped table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th width="10%">order_date</th>
                                    <th width="10%">doc_id</th>
                                    <th width="10%">親sku</th>
                                    <th width="10%">注文数</th>
                                    <th width="10%">仕入れ先</th>
                                    <th width="10%">仕入れ値段</th>
                                    <th width="10%">倉庫</th>
                                    <th width="10%">行編集</th>
                                </tr>
                            </thead>
                            @foreach($orderdocuments as $item)
                            <tr>
                                <td>{{ $item->order_date }}</td>
                                <td>{{ $item->doc_id }}</td>
                                <td>{{ $item->parent_sku }}</td>
                                <td>{{ $item->parent_num }}</td>
                                <td>{{ $item->supplier }}</td>
                                <td>{{ $item->price }}</td>
                                <td>{{ $item->store_place }}</td>

                                <td>
                                    <form method="PUT" action="#" >
                                        <div class="">
                                            <button name="id" value="{{$item->id}}"  type="submit" class="btn btn-primary btn-join">編集</button>
                                        </div>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                            {{$orderdocuments->links()}}<!-- ページバーを表示する。 -->
                        </table>

                    </div>
                </div>



            </div>
        </div>
    </div>
    @endsection
