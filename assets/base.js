function sendToast(status, message) {
	var timer_name = "toast_timer"+Math.floor(Math.random() * 101);
	$("#toastArea")
		.append('<div class="toast fade show" data-delay="100">'+
			'<div class="toast-header">'+
			'<strong class="mr-auto"><i class="fa fa-globe"></i> ' + status + '</strong>' +
			'<small class="text-muted">1 Saniye Önce</small>' +
			'<button type="button" class="ml-2 mb-1 close" data-dismiss="toast">&times;</button>' +
			'</div>' +
			'<div class="toast-body" style="color: #6c757d;">' + message + '</div>' +
			'<div style="height: 2px; background-color: blue; width: 100%; transition: all 3s ease 0s;" id="'+timer_name+'"></div>' +
			'</div>');
	toastTimer(timer_name, 4);
}

//if toasttimer function not exist

if (typeof toastTimer !== 'function') {
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
}
