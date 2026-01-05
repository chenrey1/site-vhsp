function speedy_init() {
	$(document).on("click", "[speedy-init-url]", function() {
		var init_url = $(this).attr("speedy-init-url");
		var modal = $("#"+$(this).attr("speedy-modal"));
		var action = $(this).attr("speedy-action");
		speedy_init_modal(init_url, modal, action, $(this));
	});
}
function speedy_init_modal(init_url, modal, action, element=null) {
	$(document).trigger("onSpeedyInit", [element]);
	$.getJSON(init_url, function(data) {
		if (data.status) {
			datas = data.datas;
			data = data.data;
			modal.on('hidden.bs.modal', function (e) {
				modal.find("form").attr("action", "");
			});

			modal.find("input[speedy-init-by], textarea[speedy-init-by], select[speedy-init-by]").each(function(key, val) {
				val = $(val);
				var init_by = val.attr("speedy-init-by");
				modal_val = data[init_by];
				if (val.attr("type") == "checkbox") {
					val.attr("checked", (modal_val==1));
				} else {
					val.val(modal_val).change();
				}
			});

			modal.find("select[speedy-init-with]").each(function(key, val) {
				val = $(val);
				var init_by = val.attr("speedy-init-with");
				modal_val = datas[init_by];
				val.find('option:not(:disabled)').remove();
				$.each(modal_val, function(opt_key, opt_val) {
					var json_val = {
						value: opt_val[val.attr("speedy-init-value")],
						text : speedy_parse_variables_in_text(opt_val, val.attr("speedy-init-text")),
					};
					if (opt_val.attr) {
						$.extend( json_val, opt_val.attr );
					}

					val.append($('<option>', json_val));
				});
				val.val(data[val.attr("speedy-init-by")]).change();
				if (val.hasClass("selectpicker")) {
					val.selectpicker('refresh');
					val.selectpicker('val', data[val.attr("speedy-init-by")]);
					val.selectpicker('refresh');
				}
			});

			modal.find("form").attr("action", action);
			modal.modal('show');
			$(document).trigger("onSpeedyInitComplete", [element]);
		}
	});
}

function speedy_parse_variables_in_text(vars, text) {
	return text.replace(/{{@([^}]+)}}/g, function(match, offset, string) {
		if (!vars[offset] && offset.includes(".")) {
			var res = vars[offset.slice(0, offset.indexOf("."))];
			offset.slice(offset.indexOf('.')+1).split(".").forEach((e) => {
				res = res[e];
			});
			if (res==null) res = "";
			return res;
		}
		var res = vars[offset];
		if (res==null) res = "";
		return res;
	});
}

function create_datatable(selector, url, order, columns) {
    $(selector).DataTable({
        "order": order,
        "processing": true,
        "serverSide": true,
        "serverMethod": "post",
        "ajax": {
            "url":url,
        },
        "columns": columns,
        "language": {
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
   });
}

function sendToast(status, message) {
	var timer_name = "toast_timer"+Math.floor(Math.random() * 101);
	$("#toastArea")
		.append('<div class="toast fade show" data-delay="100">'+
			'<div class="toast-header">'+
			'<strong class="mr-auto"><i class="fa fa-globe"></i> ' + status + '</strong>' +
			'<small class="text-muted">1 Saniye Ã–nce</small>' +
			'<button type="button" class="ml-2 mb-1 close" data-dismiss="toast">&times;</button>' +
			'</div>' +
			'<div class="toast-body" style="color: #6c757d;">' + message + '</div>' +
			'<div style="height: 2px; background-color: blue; width: 100%; transition: all 3s ease 0s;" id="'+timer_name+'"></div>' +
			'</div>');
	toastTimer(timer_name, 4);
}

function toastTimer(toastId, time=10) {
	setTimeout(function() {
		if (time==-2) {
			$("#"+toastId).parent(".toast").remove();
		} else {
			var width = document.getElementById(toastId).style.width.replace("%", "");
			document.getElementById(toastId).style.width = width-(170/time)+"%";
			//console.log("toast", time)
			toastTimer(toastId, time-1);
		}
	}, 1000)
}
