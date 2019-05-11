@extends('stock_work.index')

@section('content')


<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8" >
            <script src="{{ asset('js/stock_work/recommend.js') }}" defer></script>
            <div class="card">
                <script type="text/javascript">
                function alert_no_info(a){
                    window.alert("SKU【"+a+"】に関する情報は見つかっていません。\n 報告してください。");
                }
                </script>


                <div class="card-header">提案ページ
                    <a style="float: right;" class="btn btn-primary btn-join"href="{{route('stock_work::work')}}">TOPへ戻る</a>
                </div>

                    @if(strcmp($former_sku, "FINISH")!=0)
                    @if(count($skuinfo) == 0)
                    <div class="alert alert-danger">
                        <center>SKU: 【{{ $former_sku }}】</center>
                        <center>??????????????????????情報なし??????????????????????</center>
                        <script> alert_no_info("{{ $former_sku }}")</script>
                    </div>
                    @else
                    <div class="alert alert-success">
                        <center>SKU: 【{{ $former_sku }}】</center>
                        @foreach($skuinfo as $item)
                        <p>【{{$item->parent_sku}} :: {{$stockinfo[$item->parent_sku]["name"]}}】 × {{$item->parent_num}}/{{$stockinfo[$item->parent_sku]["num"]}} ---------- {{$stockinfo[$item->parent_sku]["place"]}}</p>
                        @endforeach
                    </div>
                    @endif
                    @endif


                <div class="card-body">
                    <div>
                        <div>
                            <input style="transform:scale(2); " type="checkbox" name="allcheck" onclick="allCheck()">　一括選択
                        </div>
                        <form action={{route('stock_work::work.deal_and_recommend')}} method="get">
                            <div>
                                @foreach($ordersheets as $items)
                                <table class="table table-striped table-bordered table-hover">
                                    <thread>
                                        <tr>
                                            <th>処理</th>
                                            <th>状態</th>
                                            <th>注文日付</th>
                                            <th>行</th>
                                            <th>SKU</th>
                                            <th>個数</th>
                                            <th>注文番号</th>
                                        </tr>
                                    </thread>
                                    @foreach($items as $item)
                                    <tr>

                                        @if($item->id == $items[0]->id)
                                        <?php $hoge= count($items); ?>
                                        <td align="center" rowspan="{{$hoge}}">
                                            <input style="transform:scale(2); " type="checkbox" name="deal_ids[]" value="{{ $item->id}}">
                                        </td>
                                        @endif
                                        @if(strcmp($item->stock_stat, "在庫") == 0)
                                        <td>在庫</td>
                                        @elseif(($item->aim_num <= $item->stock_num))
                                        <td>4F処理済</td>
                                        @else
                                        <td>4F未処理</dt>
                                        @endif
                                        <td>{{ $item->date }}</td>
                                        <td>{{ $item->line }}</td>
                                        @if(strcmp($item->sku, $former_sku) == 0)
                                        <td bgcolor="pink">{{ $item->sku }}</td>
                                        @else
                                        <td>{{ $item->sku }}</td>
                                        @endif
                                        <td>{{ $item->aim_num }}</td>
                                        <td>{{ $item->order_id }}</td>
                                    </tr>
                                    @endforeach
                                </table>
                                @endforeach
                            </div>
                            @if(strcmp($former_sku , "FINISH") == 0)
                            <p>一通り終了しました。お疲れ様です。</p>
                            <a class="btn btn-primary btn-join"href="{{route('stock_work::work')}}">戻る</a>
                            @else
                            <p><input type="hidden" name="zero_ignore" value={{$zero_ignore}}></p>
                            <p><input type="hidden" name="former_sku" value={{$former_sku}}></p>
                            @if (Session::has('print_flag'))
                            <input type="submit" class="btn btn-primary btn-join" disabled="true" value="処理して次へ">
                            @else
                            <input type="submit" class="btn btn-primary btn-join" value="処理して次へ">
                            @endif
                            @endif
                        </form>
                    </div>
                </div>

                <div class="card-header"><center>BOX【{{$box_name}}】中身一覧</center></div>
                @if (Session::has('print_flag'))
                <p><a href="../../pic/greenlabel/{{$box_name}}.pdf" target="_blank"> greenlabel[box: {{$box_name}}] </a></p>
                <p><a href="../../pic/invoice/{{$box_name}}.pdf" target=("_blank")> invoice[box: {{$box_name}}] </a></p>

                <form action = "{{route('stock_work::work.renew_box')}}">
                    <input type="hidden" name="zero_ignore" value={{$zero_ignore}}>
                    <?php
                    if(isset($_GET['former_sku'])) { $sku = $_GET['former_sku']; }
                    else $sku = null;
                    ?>
                    <input type="hidden" name="former_sku" value={{$sku}}>
                    <input type="hidden" name="to_url" value=1>
                    <input type="submit" class="btn btn-default btn-join" value = "BOX新規">
                </form>

                @else
                <div class="card-body">
                    <form action = "{{route('stock_work::work.print')}}">
                        <input type="hidden" name="zero_ignore" value={{$zero_ignore}}>
                        <?php
                        if(isset($_GET['former_sku'])) { $sku = $_GET['former_sku']; }
                        else $sku = null;
                        ?>
                        <input type="hidden" name="former_sku" value={{$sku}}>
                        <input type="hidden" name="to_url" value=1>
                        <input type="submit" class="btn btn-default btn-join" value = "印刷">
                    </form>
                    <div>
                        <table class="table table-striped table-bordered table-hover">
                            <thread>
                                <tr>
                                    <th>id</th>
                                    <th>注文日付</th>
                                    <th>行</th>
                                    <th>SKU</th>
                                    <th>個数</th>
                                    <th>注文番号</th>
                                </tr>
                            </thread>
                            @foreach($selfitem as $item)
                            <tr>
                                <td>{{ $item->id_in_box }}</td>
                                <td>{{ $item->date }}</td>
                                <td>{{ $item->line }}</td>
                                <td>{{ $item->sku }}</td>
                                <td>{{ $item->aim_num }}</td>
                                <td>{{ $item->order_id }}</td>
                            </tr>
                            @endforeach
                        </table>

                    </div>

                </div>
                @endif
            </div>
        </div>

    </div>

    @endsection
