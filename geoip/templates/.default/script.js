const app = BX.BitrixVue.createApp({
	data() 
	{
		return {
			ip: null,
			continent_code: null,
			type: null,
			showResult: false
		}
	},
	methods: {
		send: function (event) {
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
					this.ip = data.ip,
					this.type = data.type,
					this.continent_code = data.continent_code,
					this.showResult = true
				},
				onfailure: (data) => {
					console.log(data);
				},
			   }); 
		}
	},
	mounted() {},
	// language=Vue
	template: `
	<div class="container">
		<form>
			<div class="form-row align-items-center">
				<div class="col-sm-3 my-1">
					<input v-model="ip" type="text" class="form-control" id="inlineFormInputName" placeholder="Введите ip адрес">
				</div>
				<div class="col-auto my-1">
					<button v-on:click="send" type="button" class="btn btn-primary">Получить информацию</button>
				</div>
			</div>
		</form>
		<div class="" v-if="showResult">
			<ul class="list-group">
				<li class="list-group-item">{{ ip }}</li>
				<li class="list-group-item">{{ continent_code }}</li>
				<li class="list-group-item">{{ type }}</li>
			</ul>	
		</div>
	</div>
	`
});