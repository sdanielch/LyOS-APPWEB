<script type="text/javascript" src="<?php echo $fundamental_libs;?>jquery.simple-calendar.min.js"></script>
<script type="text/javascript" src="<?php echo $fundamental_libs;?>moment.min.js"></script>
<script type="text/javascript" src="<?php echo $fundamental_libs;?>tail.datetime-full.min.js"></script>
<script type="text/javascript" src="<?php echo $fundamental_libs;?>tail.datetime-all.min.js"></script>
<link rel="stylesheet" type="text/css" href="<?php echo $fundamental_libs;?>simple-calendar.css" />
<link rel="stylesheet" type="text/css" href="<?php echo $fundamental_libs;?>tail.datetime-default-red.min.css" />






<style>
	.close {
		z-index: 80000;
	}
	.event-container {
		border-radius: 8px;
		z-index: 8000;
		overflow: auto;
	}
	.tail-datetime-calendar {
		width: 280px !important;
		display: inline-block !important;
	}
	#datetime-demo-holder, #datetime-demo-holder2 {
		display: inline-block;
		margin: 2px;
	}
	#formcalendar {
		text-align: center;
	}

</style>
<div id="container"></div>
<br />
<div id='formcalendar' style='position: relative; background: transparent; color: black; display: block; min-height: 100px; border-radius: 8px; padding: 10px; margin: -10px; margin-top: 10px;'><h2>Crear un nuevo evento</h2><hr>

	<div id='datetime-demo-holder'><span style="display: block">Fecha y hora de inicio</span></div>
	<div id='datetime-demo-holder2'><span style="display: block">Fecha y hora final</span></div>
	<input type='text' class='form-control' id='timeone' placeholder="Selecciona la fecha y hora de inicio" disabled />
	<input type='text' class='form-control'  placeholder="Selecciona la fecha y hora de fin" id='timetwo'  disabled />
</div>

<script>
	$(document).ready(function(){

		tail.DateTime("#timeone", {
			position: "#datetime-demo-holder",
			dateFormat: "dd-mm-YYYY",
			timeFormat: "HH:ii:ss",
			animate: true,
			closeButton: true,
			today: true,
			locale: "es",
			startOpen: true,
			stayOpen: true

		});

		tail.DateTime("#timetwo", {
			position: "#datetime-demo-holder2",
			dateFormat: "dd-mm-YYYY",
			timeFormat: "HH:ii:ss",
			animate: true,
			closeButton: true,
			today: true,
			locale: "es",
			startOpen: true,
			stayOpen: true

		});




		$("#container").simpleCalendar({
			//Defaults options below
			//string of months starting from january
			months: ['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'],
			days: ['Domingo','Lunes','Martes','Miércoles','Jueves','Viernes','Sábado'],
			displayYear: true,              // Display year in header
			fixedStartDay: true,            // Week begin always by monday or by day set by number 0 = sunday, 7 = saturday, false = month always begin by first day of the month
			displayEvent: true,             // Display existing event
			disableEventDetails: false, // disable showing event details
			disableEmptyDetails: false, // disable showing empty date details
			events:  [
				// generate new event after tomorrow for one hour
				{
					startDate: new Date(new Date().setHours(new Date().getHours() + 24)).toDateString(),
					endDate: new Date(new Date().setHours(new Date().getHours() + 25)).toISOString(),
					summary: 'Visit of the Eiffel Tower'
				},
				// generate new event for yesterday at noon
				{
					startDate: new Date(new Date().setHours(new Date().getHours() - new Date().getHours() - 12, 0)).toISOString(),
					endDate: new Date(new Date().setHours(new Date().getHours() - new Date().getHours() - 11)).getTime(),
					summary: 'Restaurant'
				},
				// generate new event for the last two days
				{
					startDate: new Date(new Date().setHours(new Date().getHours() - 48)).toISOString(),
					endDate: new Date(new Date().setHours(new Date().getHours() - 24)).getTime(),
					summary: 'Visit of the Louvre'
				}
			],                     // List of events
			onInit: function (calendar) {}, // Callback after first initialization
			onMonthChange: function (month, year) {}, // Callback on month change
			onDateSelect: function (date, events) {
				console.log(date,events)
				moment.locale('es');
				console.log(moment(date).format('dddd Do MMMM, YYYY'));



			}, // Callback on date selection
			onEventSelect: function(e) {
				console.log(e)
			}, // Callback on event selection - use $(this).data('event') to access the event
			onEventCreate: function( $el ) {},          // Callback fired when an HTML event is created - see $(this).data('event')
			onDayCreate:   function( $el, d, m, y ) {}  // Callback fired when an HTML day is created   - see $(this).data('today'), .data('todayEvents')
		});
	});
</script>
