@extends('order_receive.index')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading"><font size="7">発注書更新</font></div>
                <div>ファイル上限: about 1M</div>
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
                        {!! Form::model($orderdocuments, [
                            'url' => route('order_receive::orderdocuments.upload'),
                            'method' => 'POST',
                            'files' => true
                            ]) !!}

                            <div>発注書ID　<input name="doc_id" placeholder="ID" required></div>
                            <div>発注日：　<input type="date" name="order_date"  required></div>
                            <div class="row">
                                <div class="col-md-4">
                                    {!! Form::file('csv_file', null, ['class' => 'form-control']) !!}
                                </div>
                                <div class="col-md-8">
                                    {!! Form::submit('更新', ['class' => 'btn btn-primary']) !!}
                                    {{ link_to_route('order_receive::orderdocuments.download', 'ダウンロード', null, ['class' => 'btn btn-default']) }}
                                </div>
                            </div>


                            {!! Form::close() !!}
                        </div>


                    </div>
                </div>

                <hr>
                <div>
                    <table class="table table-striped table-bordered table-hover">
                        <thread>
                            <tr>
                                <th>注文番号</th>
                                <th>注文日付</th>
                                <th>処理</th>
                            </tr>
                        </thread>
                        @foreach($orderdocuments as $item)
                        <tr>
                            <td>{{ $item->doc_id }}</td>
                            <td>{{ $item->order_date }}</td>
                            <td>
                                <form method="PUT" action="{{route('order_receive::orderdocuments.detail')}}">
                                    @if(count($docids) != 0)
                                    @foreach($docids as $docid)
                                    <input type="hidden" name = "search_doc[]" value="{{$docid}}">
                                    @endforeach
                                    @endif
                                    <button name="doc_id" value="{{$item->doc_id}}" type="submit" class="btn btn-defalut btn-join">詳細</button>
                                </form>
                            </td>

                        </tr>
                        @endforeach
                        <tr>
                    </table>
                </div>

                @if(Session::has('detail_flag'))
                <div class="alert alert-success">
                    発注書ID: [{{Session::get('detail_flag')}}]
                    <form  method="PUT" action="#">
                            <button name="doc_id" value="{{Session::get('detail_flag')}}" type="submit" class="btn btn-danger btn-join">発注書削除</button>
                    </form>
                </div>
                    <table class="table table-striped table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>親sku</th>
                                <th>注文数</th>
                                <th>仕入れ先</th>
                                <th>仕入れ値段</th>
                                <th>修正</th>
                            </tr>
                        </thead>
                        @foreach($detailitems as $item)
                        <tr>
                                @if($edit_id != $item->id)
                                <td>{{$item->parent_sku}}</td>
                                <td>{{$item->parent_num}}</td>
                                <td>{{$item->supplier}}</td>
                                <td>{{$item->price}}</td>
                                <td>
                                    <form method="PUT" action="{{route('order_receive::orderdocuments.select')}}">
                                        @if(count($docids) != 0)
                                        @foreach($docids as $docid)
                                        <input type="hidden" name = "search_doc[]" value="{{$docid}}">
                                        @endforeach
                                        @endif
                                        <input type="hidden" name="select_id" value="{{$item->id}}">
                                        <button name="doc_id" value="{{$item->doc_id}}" type="submit" class="btn btn-primary btn-join">編集</button>
                                    </form>
                                </td>
                                @else
                                <td><input type="text" id="key_param"  value="{{$item->parent_sku}}" disabled="disabled"></td>
                                <td><input type="text" name="edit_param" id="num" value="{{$item->parent_num}}"></td>
                                <td><input type="text" name="edit_param" id="supplier" value="{{$item->supplier}}"></td>
                                <td><input type="text" name="edit_param" id="price" value="{{$item->price}}"></td>
                                <td>
                                    <input type="button" class="btn btn-join btn-primary" id="edit_confirm" value="確定">
                                    <input type="hidden" name="base_param" id="num" value="{{$item->parent_num}}">
                                    <input type="hidden" name="base_param" id="supplier" value="{{$item->supplier}}">
                                    <input type="hidden" name="base_param" id="price" value="{{$item->price}}">
                                    <input type="hidden" id="endpoint" value="edit">

                                    <form method="PUT" action="#">
                                        @if(count($docids) != 0)
                                        @foreach($docids as $docid)
                                        <input type="hidden" name = "search_doc[]" value="{{$docid}}">
                                        @endforeach
                                        @endif
                                        <button name="doc_id" value="{{$item->doc_id}}" type="submit" class="btn btn-join">キャンセル</button>
                                    </form>
                                </td>
                                @endif




                        </tr>
                        @endforeach
                    </table>

                </div>
                <hr>
                @endif



            </div>
        </div>
    </div>
    @endsection
