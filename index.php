<!DOCTYPE html>
<html>
<head>
<title>Fe y Drogueria</title>
<!--Bootstrap-->
<link rel="stylesheet" type="text/css" href="lib/bootstrap/bootstrap-reboot.min.css">
<link rel="stylesheet" type="text/css" href="lib/bootstrap/bootstrap.min.css">
<!--Datatables-->
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs4-4.1.1/jq-3.3.1/dt-1.10.18/datatables.min.css"/>
<!--Fontawesome-->
<link rel="stylesheet" type="text/css" href="lib/fontawesome/css/all.min.css">
<link rel="stylesheet" type="text/css" href="lib/fontawesome/css/brands.min.css">
<link rel="stylesheet" type="text/css" href="lib/fontawesome/css/fontawesome.min.css">
<link rel="stylesheet" type="text/css" href="lib/fontawesome/css/regular.min.css">
<link rel="stylesheet" type="text/css" href="lib/fontawesome/css/solid.min.css">
<link rel="stylesheet" type="text/css" href="lib/fontawesome/css/svg-with-js.min.css">
<link rel="stylesheet" type="text/css" href="lib/fontawesome/css/v4-shims.min.css">
<!--Alertify-->
<link rel="stylesheet" type="text/css" href="lib/alertify/css/alertify.css">
<link rel="stylesheet" type="text/css" href="lib/alertify/css/themes/bootstrap.css">
</head>

<body>

<div class="mt-3 ml-3 btn-info p-3 d-inline-block    ">
    <form id="form_excel" enctype="multipart/form-data" method="post">
        <div>
            <label for="">Ingrese el Archivo</label>
            <input class="col-sm-12" type="file" id="i_file" name="i_file"/>
        </div>


<div class="row mt-3">
        <div class="col-sm-6">
            <label for="">Iniciar en la Columna N°</label>
            <input class="col-sm-12" type="number" min=0 id="inc" value="0" placeholder="Iniciar en n°" name="inc"/>
        </div>
        <div class="col-sm-6">
            <label for="">Pagina N°</label>
            <input class="col-sm-12" type="number" min=0 id="hoja" value="0" placeholder="hoja n°" name="hoja"/>
        </div>
</div>
        





        <footer class="row mt-3">
            <div class="col-sm-6">
                <button type="submit" id="btn_mostrar" class="col-sm-12 btn btn-primary">Mostrar Registros</button>   
            </div>
            <div class="col-sm-6">
                <button type="button" id="btn_guardar" class="col-sm-12 btn btn-success float-right">Guardar Registros en Base de datos</button>   
            </div>
        </footer>
    </form>
</div>

<div class="container-fluid mt-5">
    <table id="example" class="table table-hover table-bordered" style="width:100%">
        <thead>
            <tr id="thead_table"></tr>
        </thead>
        <tbody id="tbodycart"></tbody>
    </table>
</div>
    

<!--Jquery-->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
<!--Bootstrap-->
<script src="lib/bootstrap/bootstrap.min.js"></script>
<script src="lib/bootstrap/bootstrap.bundle.min.js"></script>
<!--Datatables-->
<script type="text/javascript" src="https://cdn.datatables.net/v/bs4-4.1.1/jq-3.3.1/dt-1.10.18/datatables.min.js"></script>
<!--Fontawesome-->
<script src="lib/fontawesome/js/all.js"></script>
<script src="lib/fontawesome/js/brands.min.js"></script>
<script src="lib/fontawesome/js/fontawesome.min.js"></script>
<script src="lib/fontawesome/js/regular.min.js"></script>
<script src="lib/fontawesome/js/solid.min.js"></script>
<script src="lib/fontawesome/js/v4-shims.min.js"></script>
<!--Alertify-->
<script src="lib/alertify/alertify.js"></script>
<!--Excel-->
<script src="lib/pdf/xlsx.full.min.js"></script>




<script>
    alertify.set('notifier','position', 'top-right')    
   
   $("form#form_excel").submit(function(e) {
    e.preventDefault();    
    let inc = $('#inc').val()
    let hoja = $('#hoja').val()
    var formData = new FormData(this);
    $.ajax({
        url: 'recibe.php',
        type: 'POST',
        data: formData,
        success: function (data) {
            fconsole(data,inc,hoja)
        },
        cache: false,
        contentType: false,
        processData: false
    });
});

    function fconsole(n,inc,hoja)
    {
        var oReq = new XMLHttpRequest();
        oReq.open("GET", "excel/"+n, true);
        oReq.responseType = "arraybuffer";

        oReq.onload = function(e) {
            var arraybuffer = oReq.response;

            /* convert data to binary string */
            var data = new Uint8Array(arraybuffer);
            var arr = new Array();
            for(var i = 0; i != data.length; ++i) arr[i] = String.fromCharCode(data[i]);
            var bstr = arr.join("");

            /* Call XLSX */
            var workbook = XLSX.read(bstr, {type:"binary"});

            /* DO SOMETHING WITH workbook HERE */
            var first_sheet_name = workbook.SheetNames[hoja];
            /* Get worksheet */
            var worksheet = workbook.Sheets[first_sheet_name];
            var obj_excel = XLSX.utils.sheet_to_json(worksheet,{raw:true, defval: ''})
            //console.log('estructura_vieja',obj_excel)
             //obj_excel = XLSX.utils.sheet_to_json(worksheet,{raw:false, header: "A", range: inc - 1, defval: ''})
             obj_excel = obj_excel.map((x)=>{
                const claves = Object.values(x);
                
                return {
                    claves 
                }
            })
            var temporal = []

            $.each(obj_excel, function(index,value){
                temporal.push(value['claves'])
            });
                arr = []
                for(i=0;i<temporal.length;i++){
                    var array = {};
                    var arra = [];
                    for(f=0;f<temporal[i].length;f++){
                        var label = temporal[0][f]
                        var content = temporal[i][f]
                        array[label] = content;
                        arra.push(array)
                        
                    }
                    arr.push(array)
                }



            table(temporal,inc,arr)
        }
        oReq.send();
    }
    function table(temporal,inc,arr)
    {
        arr.shift()
        
        let inc2 = parseInt(inc)+1
        $('#thead_table').empty()
        $('#tbodycart').empty()
        $.each(temporal[inc], function(index,value){
            $('#thead_table').append(`<th><button onclick="trash_fila(this)" class="btn btn-danger mr-4"><i class="fas fa-trash-alt"></i></button>`+value+`</th>`)
        });
        $('#thead_table').append(`<th>Editar</th>`)
        $('#thead_table').append(`<th>Eliminar</th>`)
        var tbodycart = ''
        
            for (let index = inc2; index < temporal.length; index++)
            {
                tbodycart += '<tr>'
                    $.each(temporal[index], function(index,value){
                        tbodycart += `<td><input disabled class="disab border-0" style="background:none;"  type="text" value="`+value+`"/></td>`
                    });
                    tbodycart += `<td><button id="edit" onclick="edit(this)" class="btn btn-primary"><i class="fas fa-pencil-alt"></i></button><button id="save" onclick="save(this)" class="btn btn-success d-none"><i class="fas fa-save"></i></button></td>`
                    tbodycart += `<td><button onclick="trash(this)" class="btn btn-danger"><i class="fas fa-trash-alt"></i></button></td>`
                    
                tbodycart += '</tr>'
            }
        $('#tbodycart').append(tbodycart)
        $('#example').DataTable()
        $.ajax({
            url: 'insert.php',
            type: 'POST',
            data: {arr},
            success: function (dat) {
                dat = JSON.parse(dat)
                console.log(dat)
            },
        });
    }
    function trash(v)
    {nth-child
        $(v).parents('tr').eq(0).remove();
        alertify.success('Registro removido')
    }
    function trash_fila(v)
    {
        $(v).parents('th').remove();
        console.log($(v).children())
        $('tr').find('td:nth-child(4)').remove();
        alertify.success('Fila removida')
    }
    function edit(v)
    {
        $('#example').removeClass('table-hover')
        $(v).parents('tr').eq(0).addClass('btn-info')
        $(v).parents('tr').eq(0).find(".disab").removeAttr("disabled")
        $(v).parents('tr').eq(0).find("#edit").addClass('d-none')
        $(v).parents('tr').eq(0).find("#save").removeClass('d-none')
        
    }
    function save(v)
    {
        $('#example').addClass('table-hover')
        $(v).parents('tr').eq(0).removeClass('btn-info')
        $(v).parents('tr').eq(0).find(".disab").attr("disabled","true")
        $(v).parents('tr').eq(0).find("#edit").removeClass('d-none')
        $(v).parents('tr').eq(0).find("#save").addClass('d-none')
        alertify.success('Registro modificado')
    }
</script>






</body>
</html
