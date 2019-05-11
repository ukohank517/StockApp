@extends('order_receive.index')

@section('content')
<div class="container">
    <div class="row">
        <div class="panel panel-default">
            <div class="panel-heading" style="z-index=100"><font size="6">初期設定に当たって</font></div>
            <div>
                <p>1．まず関係表の追加から</p>
                <p>2．そして発注書により全体更新(初期はこれで在庫を無理矢理に作ってください)</p>
            </div>
            <p> ------------ </p>
            <p>モデル作ろう。。。</p>
            <p>-</p>
            <p>レポート:</p>
            <p>黄色、赤色一覧</p>
            <p>日程再設定、基準再設定の黄色赤色一覧</p>
            <p>-</p>
            <p>発注:</p>
            <p>全部DL</p>
            <p>yellow redだけのDL</p>
            <p>指定SKUのDL</p>
            <p>-</p>
            <p>受注:</p>
            <p>skuより注文履歴検索</p>
            <p>履歴確定で更新</p>

        </div>


    </div>
</div>

@endsection
