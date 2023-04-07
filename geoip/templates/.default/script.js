const app = BX.BitrixVue.createApp({
	data() 
	{
		return {
			ip: null,
			showResult: false,
			showError: false
		}
	},
	methods: {
		send: function (event) {
			this.showError = false
			BX.ajax({
				url: '/',
				data: {
					ip: this.ip,
				},
				method: 'POST',
				dataType: 'json',
				timeout: 30,
				async: true,
				processData: true,
				scriptsRunFirst: true,
				emulateOnload: true,
				start: true,
				cache: false,
				onsuccess: (data) => {
					console.log(data);
					this.ip = data.ip;
					this.type = data.type;
					this.continent_code = data.continent_code;
					this.continent_name = data.continent_name;
					this.country_code = data.country_code;
					this.country_name = data.country_name;
					this.region_code = data.region_code;
					this.region_name = data.region_name;
					this.city = data.city;
					this.zip = data.zip;
					this.latitude = data.latitude;
					this.longitude = data.longitude;

					this.showResult = true

				},
				onfailure: (data, g, f, r) => {
					console.error(g);
					if (g === 400) {
						this.showError = true
					}
				},
			   }); 
		}
	},
	mounted() {},
	// language=Vue
	template: `
	<div class="container w-50 h-50">
		<div class="row align-items-center h-100">
			<form class="">
				<div class="row align-items-center">	
					<div class="col-sm my-1">
						<input v-model="ip" type="text" class="form-control" placeholder="Введите ip адрес" required>
					</div>
					<div class="col-sm my-1">
						<button v-on:click="send" type="button" class="btn btn-primary">Получить информацию</button>
					</div>
					<div class="text-danger" v-if="showError">
						Введенная строка не является ip адресом
					</div>
				</div>
			</form>
			<div class="" v-if="showResult">
				<ul class="list-group">
					<li class="list-group-item"><strong>ip адрес</strong>: {{ ip }}</li>
					<li class="list-group-item"><strong>Тип ip адреса</strong>: {{ type }}</li>
					<li class="list-group-item"><strong>Код материка</strong>: {{ continent_code }}</li>
					<li class="list-group-item"><strong>Материк</strong>: {{ continent_name }}</li>
					<li class="list-group-item"><strong>Код страны</strong>: {{ country_code }}</li>
					<li class="list-group-item"><strong>Страна</strong>: {{ country_name }}</li>
					<li class="list-group-item"><strong>Код региона</strong>: {{ region_code }}</li>
					<li class="list-group-item"><strong>Регион</strong>: {{ region_name }}</li>
					<li class="list-group-item"><strong>Город</strong>: {{ city }}</li>
					<li class="list-group-item"><strong>Почтовый индекс</strong>: {{ zip }}</li>
					<li class="list-group-item"><strong>Широта</strong>: {{ latitude }}</li>
					<li class="list-group-item"><strong>Долгота</strong>: {{ longitude }}</li>
				</ul>	
			</div>
		</div>
	</div>
	`
});