<!-- REQUIRED SCRIPTS -->

<!-- jQuery -->
<script src="<?= base_url('assets/front-end/plugins/jquery/jquery.min.js') ?>"></script>

<!-- Bootstrap 4 -->
<script src="<?= base_url('assets/front-end/plugins/bootstrap/js/bootstrap.bundle.min.js') ?>"></script>

<!-- DataTables -->
<script type="text/javascript" src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/1.10.16/js/dataTables.bootstrap4.min.js"></script>
<!-- Glider -->
<script src="<?= base_url('assets/front-end/plugins/glider/glider.min.js') ?>"></script>
<!-- select 2 -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>
<!-- Moment JS -->
<script src="<?= base_url('assets/front-end/plugins/moment/moment.min.js') ?>"></script>
<!-- ChartJs -->
<script src="<?= base_url('assets/front-end/plugins/chart.js/chart.min.js') ?>"></script>
<!-- CK editor JS -->
<script src="<?= base_url('assets/plugins/ckeditor/ckeditor.js') ?>"></script>
<!-- Sweetalert2 -->
<script src="<?= base_url('assets/front-end/plugins/sweetalert2/sweetalert2.min.js') ?>"></script>
<!-- Ekko Lightbox -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/ekko-lightbox/5.3.0/ekko-lightbox.js" crossorigin="anonymous"></script>
<!-- JS Datepicker -->
<script src="<?= base_url('assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js') ?>"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/clockpicker/0.0.7/bootstrap-clockpicker.min.js"></script>

<!-- AdminLTE App -->
<!-- <script src="dist/js/adminlte.min.js"></script> -->

<script>
	// Custome Dropdown menu
	$("li.dropdown.nav-item a").on("click", function(evt) {
		if (!$(this).parent().hasClass('show')) {
			$(this).parent().toggleClass("show");
		}
	});

	$("body").on("click", function(e) {
		if (
			!$("li.dropdown.nav-item").is(e.target) &&
			$("li.dropdown.nav-item").has(e.target).length === 0 &&
			$(".show").has(e.target).length === 0
		) {
			$("li.dropdown.nav-item").removeClass("show");
		}
	});
</script>

<script>
	$('.input-group.date').datepicker({
		format: 'yyyy-mm-dd',
		endDate: '0d',
		autoclose: true,
		todayHighlight: true

	});
</script>


<script type="text/javascript">
	$('.clockpicker1').clockpicker();
	$('.clockpicker2').clockpicker();
</script>

<!-- My JS -->
<script src="<?= base_url('assets/front-end/dist/js/my-js.js') ?>"></script>
</body>

</html>