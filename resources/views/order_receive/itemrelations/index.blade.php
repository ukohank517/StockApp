@extends('order_receive.index')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading"><font size="7">親sku ⇔ 子sku ⇔ JAN</font></div>
                <div>
                    <p>見方: parent_skuA * parent_numA + parent_skuB * parent_numB = child_sku </p>
                </div>
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
                        {!! Form::model($itemrelations, [
                            'url' => route('order_receive::itemrelations.upload'),
                            'method' => 'POST',
                            'files' => true
                            ]) !!}
                            <div class="row">
                                <div class="col-md-4">
                                    {!! Form::file('csv_file', null, ['class' => 'form-control']) !!}
                                </div>
                                <div class="col-md-8">
                                    {!! Form::submit('更新', ['class' => 'btn btn-primary']) !!}
                                    {{ link_to_route('order_receive::itemrelations.download', 'ダウンロード', null, ['class' => 'btn btn-default']) }}
                                </div>
                            </div>
                            {!! Form::close() !!}
                        </div>

                        <table class="table table-striped table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th width="15%">parent_sku</th>
                                    <th width="15%">parent_num</th>
                                    <th width="15%">child_sku</th>
                                    <th width="15%">child_jan</th>
                                    <th width="15%">child_asin</th>
                                    <th width="15%">行編集</th>
                                </tr>
                            </thead>
                            @foreach($itemrelations as $item)
                            <tr>
                                <td>{{ $item->parent_sku }}</td>
                                <td>{{ $item->parent_num }}</td>
                                <td>{{ $item->child_sku }}</td>
                                <td>{{ $item->child_jan }}</td>
                                <td>{{ $item->child_asin }}</td>
                                <td>
                                    <form method="PUT" action="#" >
                                        <div class="">
                                            <button name="id" value="{{$item->id}}"  type="submit" class="btn btn-primary btn-join">編集</button>
                                        </div>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                            {{$itemrelations->links()}}<!-- ページバーを表示する。 -->
                        </table>

                    </div>
                </div>



            </div>
        </div>
    </div>
    @endsection
