function allCheck(){
    var boxes = document.getElementsByName("deal_ids[]");
    // チェックボックスの個数を取得する
    var cnt = boxes.length;

    for(var i=0; i<cnt; i++) {
            boxes.item(i).checked = document.getElementsByName("allcheck").item(0).checked;
    }
}
