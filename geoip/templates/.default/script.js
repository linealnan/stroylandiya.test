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
	<div class="container">
		<form class="">
			<div class="form-row align-items-center">
				<div class="col-sm-3 my-1">
					<input v-model="ip" type="text" class="form-control" placeholder="Введите ip адрес" required>
					<div class="text-danger" v-if="showError">
						Введенная строка не является ip адресом
			  		</div>
				</div>
				<div class="col-auto my-1">
					<button v-on:click="send" type="button" class="btn btn-primary">Получить информацию</button>
				</div>
			</div>
		</form>
		<div class="" v-if="showResult">
			<ul class="list-group">
				<li class="list-group-item">{{ ip }}</li>
				<li class="list-group-item">{{ type }}</li>
				<li class="list-group-item">{{ continent_code }}</li>
				<li class="list-group-item">{{ continent_name }}</li>
				<li class="list-group-item">{{ country_code }}</li>
				<li class="list-group-item">{{ country_name }}</li>
				<li class="list-group-item">{{ region_code }}</li>
				<li class="list-group-item">{{ region_name }}</li>
				<li class="list-group-item">{{ city }}</li>
				<li class="list-group-item">{{ zip }}</li>
				<li class="list-group-item">{{ latitude }}</li>
				<li class="list-group-item">{{ longitude }}</li>
			</ul>	
		</div>
	</div>
	`
});