document.addEventListener('DOMContentLoaded', function () {
	var form = document.querySelector('.search-form form');

	if (form) {
		form.addEventListener('submit', function () {
			var btn = form.querySelector('input[type="submit"]');

			if (btn && !btn.disabled) {
				btn.disabled = true;
				btn.value = 'Vyhled\u00e1v\u00e1m...';
			}
		});
	}

	var historyItems = document.querySelectorAll('.history-item');

	for (var i = 0; i < historyItems.length; i++) {
		historyItems[i].addEventListener('click', function (e) {
			e.preventDefault();
			var keyword = this.getAttribute('data-keyword');
			var input = document.querySelector('.search-form input[type="text"]');

			if (input) {
				input.value = keyword;
				var formEl = input.closest('form');

				if (formEl) {
					formEl.submit();
				}
			}
		});
	}
});
