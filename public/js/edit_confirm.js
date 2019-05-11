$(function(){

    $('#edit_confirm').click(function(){
        var url =  location.href.split("?")[0];
        //                [         [[            ]     ]     ]
        url = url.replace(new RegExp(url.split("/").pop(), "g") , $('#endpoint').val()) + location.search + '&';

        var base_param = document.getElementsByName("base_param");
        var edit_param = document.getElementsByName("edit_param");

        var id = [];
        var former_data = [];
        var data = [];

        base_param.forEach(function(elem){
            id.push(elem.id);
            former_data.push(elem.value);
        });
        edit_param.forEach(function(elem){
            data.push(elem.value);
        });

        var alert_message = 'parent_sku:[' + $('#key_param') + '] \n';
        for(var i = 0; i < id.length; i++){
            alert_message += id[i] + ' : ' + former_data[i] + ' -> ' + data[i] + '\n';
            url += id[i] + '=' + data[i] + '&';
        }

        if(confirm(alert_message)){
            document.location.href = url;
        }else{

        }

    });
});
