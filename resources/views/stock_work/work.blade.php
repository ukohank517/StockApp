@extends('stock_work.index') 
@section('content')

<div class="container">
  <div class="row justify-content-center">
    <div class="col-md-8">
      <div class="card">
        <div class="card-header">在庫品処理ページ</div>
        <div class="card-body">
          <form action="{{route('stock_work::work.deal_and_recommend')}}">
            <!-- 実装予定 -->
            <!-- <p><input type ="checkbox" name="zero_ignore" value={{$zero_ignore}}>　0在庫無視</p> -->
            <p><input type="hidden" name="former_sku" value={{$former_sku}}></p>
            @if(Session::has('print_flag'))
            <input type="submit" class="btn btn-primary btn-join" disabled="true" value="システムより提案"> @else
            <input type="submit" class="btn btn-primary btn-join" value="システムより提案"> @endif
          </form>
        </div>

        <div class="card-header">
          <center>BOX【{{$box_name}}】中身一覧</center>
        </div>
        @if (Session::has('print_flag'))
        <p><a href="../pic/greenlabel/{{$box_name}}.pdf" target="_blank"> greenlabel[box: {{$box_name}}] </a>
        </p>
        <p><a href="../pic/invoice/{{$box_name}}.pdf" target=( "_blank" )> invoice[box: {{$box_name}}] </a></p>

        <form action="{{route('stock_work::work.renew_box')}}">
          <input type="hidden" name="to_url" value=0>
          <input type="submit" class="btn btn-default btn-join" value="BOX新規">
        </form>

        @else
        <div class="card-body">
          <form action="{{route('stock_work::work.print')}}">
            <input type="hidden" name="to_url" value=0>
            <input type="submit" class="btn btn-default btn-join" value="印刷">
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
                  <th>取消し</th>
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
                <td>
                  <input style="transform:scale(2); " type="checkbox" name="deal_ids[]" value="{{ $item->order_id }}">
                </td>
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