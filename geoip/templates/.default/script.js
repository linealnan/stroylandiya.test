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
			fetch('', {
				method: 'POST',
				headers: {
					"Content-Type": "application/json"
				},
				body: JSON.stringify({
					ip: this.ip,
					AJAX_MODE: 'Y'
				})
			})
			.then((response) => {
				return response.json();
			})
			.then((data) => {
				console.log(data);
				this.ip = data.UF_IP,
				this.continent_code = data.UF_CONTINENT_CODE,
				this.type = data.UF_TYPE,
				this.showResult = true
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
					<input v-model="ip" type="text" class="form-control" id="inlineFormInputName" placeholder="ip адрес">
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