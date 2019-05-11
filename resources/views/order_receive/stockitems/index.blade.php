@extends('order_receive.index')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading"><font size="7">在庫管理</font></div>
                <!-- 折り畳み展開ポインタ -->
                <div onclick="obj=document.getElementById('open').style; obj.display=(obj.display=='none')?'block':'none';">
                    <a style="cursor:pointer;">　　　　　v　　　　　</a>
                </div>
                <!--// 折り畳み展開ポインタ -->


                <script type="text/javascript">
                function confirm_allnew(){
                    window.confirm('更新すると今までのデータがすべて削除される。');
                }
            </script>



                <!-- 折り畳まれ部分 -->
                <div id="open" style="display:none;clear:both;">

                <!--ここの部分が折りたたまれる＆展開される部分になります。
                自由に記述してください。-->
                <a href="{{route('order_receive::stockitems.all_renew')}}"><button class="btn btn-join" onClick="confirm_allnew()">在庫表一括更新</button></a>
                </div>
                <!--// 折り畳まれ部分 -->


                <hr>
                <form action="#">
                    <input type="text" name="search_sku" placeholder="検索SKU" required>
                    <input type="submit" value="検索">
                </form>
                <hr>

                @if($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                @if(Session::has('success_msg'))
                <div class="alert alert-success">
                    {{Session::get('success_msg')}}
                </div>
                @endif

                <div>
                    <table class="table table-striped table-bordered table-hover">
                        {{$stockitems->links()}}
                        <thread>
                            <tr>
                                <th>parent_sku</th>
                                <th>stock_num</th>
                                <th>price</th>
                                <th>place</th>
                                <th>memo</th>
                                <th>編集</th>
                            </tr>
                        </thread>

                        @foreach($stockitems as $item)
                        <tr>
                            @if($edit_id != $item->id)
                            <td>{{$item->parent_sku}}</td>
                            <td>{{$item->stock_num}}</td>
                            <td>{{$item->price}}</td>
                            <td>{{$item->place}}</td>
                            <td>{{$item->memo}}</td>
                            <td><form method="PUT" action="{{route('order_receive::stockitems.select')}}">
                                <?php $pageidx = floor(($item->id -1) / 15)+1; ?>
                                <input type="hidden" name="page" value="{{$pageidx}}">
                                <button name="edit_id" value="{{$item->id}}" type="submit" class="btn btn-primary btn-join">編集</button>
                            </form></td>
                            @else
                            <td><input type="text" id="key_param" value="{{$item->parent_sku}}" disabled="disabled"></td>
                            <td><input type="text" name="edit_param" id="num" value="{{$item->stock_num}}"></td>
                            <td><input type="text" name="edit_param" id="price" value="{{$item->price}}"></td>
                            <td><input type="text" name="edit_param" id="place" value="{{$item->place}}"></td>
                            <td><input type="text" name="edit_param" id="memo" value="{{$item->memo}}"></td>



                            <td>
                                <input type="button" class="btn btn-join btn-primary" id="edit_confirm" value="確定">
                                <a href="{{route('order_receive::stockitems.index')}}"><button class="btn btn-join">キャンセル</button></a>

                                <input type="hidden" name="base_param" id="num" value="{{$item->stock_num}}">
                                <input type="hidden" name="base_param" id="price" value="{{$item->price}}">
                                <input type="hidden" name="base_param" id="place" value="{{$item->place}}">
                                <input type="hidden" name="base_param" id="memo" value="{{$item->memo}}">
                                <input type="hidden" id="endpoint" value="edit">

                            </td>
                            @endif
                        </tr>
                        @endforeach
                    </table>
                </div>
            </div>
        </div>
    </div>
    @endsection
