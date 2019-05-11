@extends('layouts.app')

@section('content')
<link rel="stylesheet" type="text/css" href="css/home.css">



<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Dashboard</div>

                <div class="card-body">
                    @if (session('status'))
                    <div class="alert alert-success" role="alert">
                        {{ session('status') }}
                    </div>
                    @endif

                    <nav>
                        <ul>
                        @can('unknown-people')
                        <li><a href="">管理者よりアクセス権限を申請してください。</a></li>
                        @endcan

                        @can('admin-higher')
                        <a href="{{ route('admin::ordersheets') }}" class="spin-btn">作業DB管理</a>
                        <a href="{{ route('order_receive::info') }}" class="spin-btn">在庫 & 受発注</a>
                        @endcan
                        @can('user-higher')
                        <a href="{{ route('stock_work::work') }}" class="spin-btn">入出荷作業</a>
                        <a href="{{ route('work::work') }}" class="spin-btn">転送作業へ</a>
                        @endcan
                    </ul>
                </nav>


            </div>

            <div class="card">
                <div class="card-header">アップデート情報</div>
                <script type="text/javascript">
                function new_info(y,m,d,mes){

                    keep_day = 3;// この日数表示される
                    old_day = new Date(y+"/"+m+"/" +d);
                    new_day = new Date();
                    day = (new_day-old_day)/(1000*24*3600);
                    info ="&nbsp &nbsp";
                    if(day <= keep_day){
                        //info = info+ "NEW! &nbsp &nbsp ";
                        info = '&nbsp <img src="pic/material/new_icon.gif">';
                    }

                    info = "<td nowrap> &nbsp &nbsp " + y + "-" + m + "-" + d + info + " </td> <td>"  + mes + "</td>";
                    document.write(info);
                }
                </script>

                <div style="background: #ffffff; width:auto; border: 1px solid #f5f5f5; height:300px; padding-left:10px; padding-right:10px; padding-top:10px; padding-bottom:10px; overflow: scroll;">
                    <table class = "table table-striped table-bordered table-hover">
                        <thead>
                            <tr><script>new_info(2019,01,28,"インボイスの長住所の折り返し機能を見直した")</script></tr>
                            <tr><script>new_info(2018,10,29,"検索条件に商品の発送状況欄追加")</script></tr>
                            <tr><script>new_info(2018,10,25,"画面ページリフォーム(ナビゲーション追加)")</script></tr>
                            <tr><script>new_info(2018,10,23,"(1)インボイスの長住所を改行するように改善。<br> (2-1) 行番号で商品検索時、同じ注文番号の詳細も表示するように改善; <br>(2-2)商品検索時、多数ヒットする際のページネーションの表示バグを修正。")</script></tr>
                            <tr><script>new_info(2018,10,20,"商品検索ページに処理済みマークを追加")</script></tr>
                            <tr><script>new_info(2018,10,19,"グリーンラベルの商品名に個数を表示するように")</script></tr>
                            <tr> <script>new_info(2018,10,18,"印刷書類にボックス名追加")</script></tr>
                            <tr> <script>new_info(2018,10,18,"アップデート情報欄追加")</script></tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
@endsection
