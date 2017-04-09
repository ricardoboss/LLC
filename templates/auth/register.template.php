<div class="container">
	<h5>Please register</h5>
	<form method="post" action="/register">
		<div class="form-group row">
			<label for="input-email" class="col-form-label col-sm-2">E-Mail</label>
			<div class="col-sm-10">
				<input type="email" id="input-email" class="form-control" name="email" required>
			</div>
		</div>
		<div class="form-group row">
			<label for="input-username" class="col-form-label col-sm-2">Username</label>
			<div class="col-sm-10">
				<input type="text" id="input-username" class="form-control" name="username" required>
			</div>
		</div>
		<div class="form-group row">
			<label for="input-password" class="col-form-label col-sm-2">Password</label>
			<div class="col-sm-10">
				<input type="password" id="input-password" class="form-control" name="password" required>
			</div>
		</div>
		<div class="form-group row">
			<div class="offset-sm-2 col-sm-10">
				<button type="submit" class="btn btn-primary">Sign up</button>
			</div>
		</div>
	</form>
</div>