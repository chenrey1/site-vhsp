var editor = [];
function loadProductEditModal(item) {
    item = $(item);
    var product_id = item.attr("product_id");
    var product_name = item.attr("product_name");
    var product_price = item.attr("product_price");
    var product_desc = item.attr("product_desc");
    var category_id = item.attr("category_id");
    var product_img = item.attr("product_img");
    var action = baseURL+"client/editProduct/";
    $('#modalPazarEdit form').attr("action", action+product_id);


    $('#modalPazarEdit form [name="product_name"]').val(product_name);
    $('#modalPazarEdit form [name="product_price"]').val(product_price);
    $('#modalPazarEdit form [name="product_desc"]').val(product_desc);
    $('#modalPazarEdit form #product_def_img').attr("src", product_img);

    $('#modalPazarEdit form [name="category_id"]').val(category_id).change();


    if (!editor['editor']) {
        ClassicEditor.create(document.querySelector('#editor')).then(cEditor => {
            console.log('Editor was initialized', cEditor);
            editor['editor'] = cEditor;
            cEditor.setData(product_desc);
        }).catch(error => {
            console.error(error);
        });
    } else {
        editor['editor'].setData(product_desc);
    }

    $('#modalPazarEdit').modal('show');
}

$(document).ready(function() {
    $("select[auto_select]").each(function(index) {
        var element = $(this);
        var auto_select = $(this).attr("auto_select");

        $(this).val(auto_select).change();
    });
});