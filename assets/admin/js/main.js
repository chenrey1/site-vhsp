(function () {
	document.addEventListener('DOMContentLoaded', function () {
		const sidebarToggle = document.querySelector('#sidebarToggle');
		if (sidebarToggle) {
			sidebarToggle.addEventListener('click', function (e) {
				e.preventDefault();
				document.body.classList.toggle('sb-sidenav-toggled');
				localStorage.setItem('sb|sidebar-toggle', document.body.classList.contains('sb-sidenav-toggled'));
			});
		}
	});
})();

$(document).ready(function(){
    if (document.querySelectorAll(".dataTable").length<1) return;
    if ($.fn.dataTable.isDataTable('.dataTable')) return;
    var tabledata = {
        language: {
            "sDecimal":        ",",
            "sEmptyTable":     "Tabloda herhangi bir veri mevcut değil",
            "sInfo":           "_TOTAL_ kayıttan _START_ - _END_ arasındaki kayıtlar gösteriliyor",
            "sInfoEmpty":      "Kayıt yok",
            "sInfoFiltered":   "(_MAX_ kayıt içerisinden bulunan)",
            "sInfoPostFix":    "",
            "sInfoThousands":  ".",
            "sLengthMenu":     "Sayfada _MENU_ kayıt göster",
            "sLoadingRecords": "Yükleniyor...",
            "sProcessing":     "İşleniyor...",
            "sSearch":         "Ara:",
            "sZeroRecords":    "Eşleşen kayıt bulunamadı",
            "oPaginate": {
                "sFirst":    "İlk",
                "sLast":     "Son",
                "sNext":     "Sonraki",
                "sPrevious": "Önceki"
            },
            "oAria": {
                "sSortAscending":  ": artan sütun sıralamasını aktifleştir",
                "sSortDescending": ": azalan sütun sıralamasını aktifleştir"
            },
            "select": {
                "rows": {
                    "_": "%d kayıt seçildi",
                    "0": "",
                    "1": "1 kayıt seçildi"
                }
            }
        }
    };
    if ($(".dataTable").attr("sort_by") !== undefined) {
        var sort_type = "desc";
        if ($(".dataTable").attr("sort_type") !== undefined) {
            sort_type = $(".dataTable").attr("sort_type");
        }
        tabledata["order"] = [[ $(".dataTable").attr("sort_by"), sort_type ]];
    }
    $(".dataTable").DataTable(tabledata);
});